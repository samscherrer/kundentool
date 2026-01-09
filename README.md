# Kundentool

Ein schlankes, aber produktionsfähiges Kundenbetreuungs‑Tool auf Basis von Laravel, MySQL und Blade.

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

**Admin User:**
- `ADMIN_EMAIL` und `ADMIN_PASSWORD` in der `.env` setzen, anschließend `php artisan db:seed`.

## Einladungen

```bash
php artisan app:create-invite --organization=ID --email=kunde@example.com --role=customer_admin
```

Der Link wird auf der Konsole angezeigt. Tokens werden nur gehasht gespeichert.

## Entscheidungen / Annahmen

- Bei Offertposition‑Freigabe wird standardmäßig ein Auftrag mit Status `active` erstellt, falls keiner existiert. Für jede freigegebene Position wird zusätzlich automatisch ein Task erstellt (Standard = aktiv). Dies entspricht dem Standard‑Workflow und kann später per UI‑Toggle ergänzt werden.
- Ohne Build‑Pipeline wird die Review‑Logik via `/public/js/review.js` ausgeliefert; die PDF‑Preview lädt nur die erste Seite (MVP).

## Screenshots

Screenshots können über den Browser genutzt werden, wenn die App lokal läuft (Bootstrap UI).
