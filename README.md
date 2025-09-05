# plx-track

[![Build Status](https://img.shields.io/github/actions/workflow/status/palactix/plx-track/ci.yml?branch=main&label=build&logo=github&style=flat-square)](https://github.com/palactix/plx-track/actions)
[![License](https://img.shields.io/github/license/palactix/plx-track?style=flat-square)](LICENSE)
[![Releases](https://img.shields.io/github/v/release/palactix/plx-track?style=flat-square)](https://github.com/palactix/plx-track/releases)

Boldly shrink links — track every click, learn where your audience is, and convert clicks into insight.

plx-track is an open-source link-shortening and click-tracking application with analytics. It provides short URLs, click metadata collection, and a management UI built with a Laravel-style PHP backend and a React + TypeScript frontend (Inertia). The project is maintained in the `palactix/plx-track` repository.

## Features
- Create and manage short links (custom aliases, password protection, expiration)
- Click analytics (time series, browsers, recent clicks)
- Link metadata fetching for previews
- User accounts and link ownership
- Modern frontend with Inertia, React + TypeScript, Tailwind CSS

## Tech stack
- Backend: PHP (Laravel-style project structure), Eloquent models, Services
- Frontend: React + TypeScript, Inertia.js, TanStack Query, Tailwind CSS
- UI primitives: Radix UI + custom components
- Charts: Recharts or similar (used in analytics pages)
- Testing: PHPUnit / Pest (PHP), TypeScript tooling for frontend

## Quick start (development)
Requirements:
- PHP 8.x
- Composer
- Node.js (LTS) and npm/yarn
- SQLite, MySQL or other supported DB (example uses sqlite by default)

Local setup (example using sqlite):

1. Clone the repo

```bash
git clone https://github.com/palactix/plx-track.git
cd plx-track
```

2. Install backend dependencies

```bash
composer install
cp .env.example .env
php artisan key:generate
```

3. Configure database

By default the project includes a sqlite database at `database/database.sqlite`. You can update `.env` to point to your DB.

4. Run migrations & seed (if needed)

```bash
php artisan migrate --seed
```

5. Install frontend dependencies and build

```bash
npm install
npm run dev
```

6. Serve the application

```bash
php artisan serve
```

Open the app in your browser. The Links page includes a UI to create short links and view analytics.

## Useful commands

- Start frontend dev server: `npm run dev`
- Build production assets: `npm run build`
- Run PHP migrations: `php artisan migrate`
- Run PHP tests: `./vendor/bin/pest` or `phpunit`
- Run TypeScript typecheck: `npx tsc --noEmit`

## API
The project exposes standard REST endpoints for links and analytics (see `routes/` for exact routes). Example:

- Create link: POST /links
- List links: GET /links
- Link analytics: GET /api/links/{short_code}/analytics

Note: If you rely on the API, check `routes/api.php` and `app/Http/Controllers/API` for exact request/response shapes.

## Contributing
- Fork the repository and open a pull request with concise, focused changes.
- Follow the existing code style and run linters/tests before submitting.
- Include tests for new behavior when possible.
- For larger features, open an issue first to discuss the design.

Code of conduct and contribution guidelines are intentionally lightweight for this project — open issues and PRs are welcome.

## License
This project includes a `LICENSE` file in the repository root. See that file for licensing details.

## Maintainers / Contact
- Repository: https://github.com/palactix/plx-track
- Maintainer: palactix

Thanks for checking out plx-track — contributions, bug reports and suggestions are welcome.
