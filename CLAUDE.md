# JHCSC Smart Panic Button System

Laravel 13 app + ESP32 firmware. ESP32 panic buttons POST alerts to `/api/emergency`; DRRMO and Clinic dashboards (Blade) acknowledge/dispatch/resolve them. SIM800L SMS is the offline fallback.
Device/hardware details (IPs, pins, SIM800L runbook): `AGENTS.md`.

## Commands
- Setup: `composer run setup` (then `php artisan db:seed`)
- Run dev: `composer run dev` (server :8000 + queue + vite)
- Test: `composer run test` (Pest, in-memory SQLite)
- Format: `vendor/bin/pint`
- Build assets: `npm run build`
- Firmware compile: `/home/jed/.local/bin/arduino-cli compile --fqbn esp32:esp32:esp32 esp32/Library/Library.ino`

## Key rules
- Never commit secrets (WiFi, passwords, `.env`); don't copy values from `.env` into docs.
- Incident statuses are Title Case strings: `Pending`, `Acknowledged`, `Responding`, `Resolved`; use `Incident::` constants and scopes.
- Every incident state change must write `Notification` rows and `broadcast(new EmergencyReported(...))`.
- Role is `DRRMO` (routes/controllers still named `ndrrmo`); Clinic actions must stay scoped by `clinicRelevant()`.
- Validate with `$request->validate()`; action endpoints return JSON when `wantsJson()`, else redirect back with flash.
- Add/adjust Pest tests in `tests/Feature` for any behavior change; keep `composer run test` green.
- Use `$incident->involvesClinic()` (not ad-hoc type checks) to decide if Clinic is notified/allowed; Medical with `need_clinic=false` must never notify Clinic.
- Don't touch `esp32/*.ino` pins/endpoints without checking `AGENTS.md`.
- Ops: MySQL runs in podman pod `mysql-stack`; if the app shows `SQLSTATE[2002] Connection refused`, run `podman pod restart mysql-stack`.

Read docs/SYSTEM.md before starting any task that touches unfamiliar parts of the system. After making changes, update the relevant sections of docs/SYSTEM.md and add a Change log entry.
