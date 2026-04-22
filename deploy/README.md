# Deploy templates

Two equivalent ways to run `php artisan telegram:poll` as a resilient background process. Pick one.

## Supervisor (typical on Ubuntu/Debian, Laravel Forge, shared hosting)

```bash
sudo apt install -y supervisor
sudo cp deploy/supervisor/twint-telegram.conf /etc/supervisor/conf.d/
# edit paths + user inside the file
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start twint-telegram
sudo supervisorctl status twint-telegram
# logs:
tail -f /var/log/supervisor/twint-telegram.log
```

Stop / restart:
```bash
sudo supervisorctl stop twint-telegram
sudo supervisorctl restart twint-telegram
```

## Systemd (modern Linux, no extra package needed)

```bash
sudo cp deploy/systemd/twint-telegram.service /etc/systemd/system/
# edit WorkingDirectory, User, ExecStart paths inside the file
sudo systemctl daemon-reload
sudo systemctl enable --now twint-telegram
sudo systemctl status twint-telegram
# logs:
sudo journalctl -u twint-telegram -f
# (or)
tail -f /var/log/twint-telegram.log
```

Stop / restart:
```bash
sudo systemctl stop twint-telegram
sudo systemctl restart twint-telegram
```

## What both give you

- **Boot persistence:** после `reboot` процесс поднимется сам.
- **Crash recovery:** если бот упал (rate-limit, сетевой сбой, Telegram 500) — перезапуск через 5 сек.
- **Logs:** stdout + stderr в один файл.
- **Graceful stop:** SIGTERM с таймаутом 15 сек; Nutgram корректно завершает текущий `getUpdates`.

## Reverb (WebSocket-сервер)

Тот же паттерн. Скопируйте `twint-telegram.*` → `twint-reverb.*`, поменяйте команду на `php artisan reverb:start` и опишите логи. Оба демона (бот + Reverb) должны быть запущены одновременно для полноценной работы.
