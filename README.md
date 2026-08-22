# Community Forum / Laravel 11 + Blade

[Leia em português](./README.pt-BR.md)

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](./LICENSE) ![Free](https://img.shields.io/badge/price-free-brightgreen)

A community forum with both of its sides, built with Laravel 11, Blade, Alpine.js and Tailwind CSS v4: the board that readers and members see, and the moderation desk that keeps it healthy. Visitors get categories, topics with two-level threaded replies, up and down votes, a best answer that marks the topic solved, member profiles with reputation and badges, and a search that shows the matching excerpt. Moderators get a report queue that jumps straight to the reported reply, pin/close/move actions, category CRUD and member management with muting and banning. Reputation, the trending score and every badge are computed from the data rather than stored, so the numbers never drift. Six categories, twenty-two topics and sixty-two replies ship as seed data, persisted to `localStorage`, so nothing needs a backend.

Live preview: https://template.dev.br/preview/forum-laravel/

## Pages included

15 screens, covering the public site and the authenticated area:

- Home: featured search, category cards with counters, trending topics, latest topics and featured members
- Category: description, category moderators, topic list sorted by recent, most voted or unanswered, with a tag filter
- Topic: opening post, two-level threaded replies, up/down votes, best answer, reply box and related topics
- Member: reputation, badges, the topics and replies they wrote, and activity by month
- Search: results with the matching excerpt highlighted, filtered by category, tag or unanswered only
- Guidelines: community rules in sections plus an FAQ accordion
- Login and Register: standalone auth screens
- Moderation dashboard: open topics, replies today, reports in the queue, new members, an activity chart, unanswered topics and recent reports
- Topics: table with search and filters by category, status and author, plus pin, close, move and delete
- Topic detail: the topic and its replies in moderation mode — approve, hide, mark as spam or reply as staff
- Reports: queue by reason (spam, abuse, off-topic, duplicate) with resolve, dismiss and a reopenable history
- Categories: CRUD with icon, colour, description, order and posting permission
- Members: table with reputation and role, plus mute and ban with an end date
- Settings: profile, community, moderation, notifications, theme and language
- Not found: 404 page

## Tech stack

- Laravel 11 on PHP 8.2+, with Blade components and layouts
- Alpine.js 3 (plus the collapse plugin) for the interactive parts
- Tailwind CSS v4 compiled by Vite 6
- Server-side routing with named routes and mock auth middleware
- Client-side i18n with three locales (en, es, pt-BR)
- ApexCharts 5
- Web app manifest and service worker for the installable PWA

## Requirements

- PHP 8.2 or newer with Composer
- Node.js 18 or newer
- npm

## Getting started

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run dev
php artisan serve
```

No database is required: the seed data lives in `app/Support/` and the session-based auth is simulated by middleware. Demo credentials:

- `moderador@agora.com` / `demo123`

Any non-empty email and password logs in; the pair above is the account suggested on the login screen and the one the seed data belongs to.

## Build for production

```bash
npm run build
```

Compiles the assets into `public/build`. Serve the app with any PHP-capable web server.

## Project structure

```
app/
├── Http/Controllers/  SiteController, AppController, AuthController
├── Http/Middleware/   AuthenticateMock, RedirectIfAuthenticated
└── Support/           the seed data and the mock users
resources/
├── js/                app.js (Alpine stores and magics), i18n.js, locales
└── views/{layouts,components,pages}/
routes/web.php
```

## Theming and customization

Tailwind CSS v4 is configured in CSS, not in a JavaScript config file. The stylesheet imports Tailwind, declares the dark variant and defines the design tokens inside `@theme`: the Outfit font family, extra breakpoints and the brand color ramp (`--color-brand-25` through `--color-brand-950`). Semantic surface tokens use the `--tx-` prefix. Dark mode is a class on the root element, toggled at runtime and persisted. Category colours and icons are data, so adding a category is a data change rather than a template change.

## Internationalization

Three locales ship with the template: English, Spanish and Brazilian Portuguese. The browser language is detected on first load and the header switcher persists the choice. Dates, numbers and durations are formatted per locale.

## The same system in other stacks

The same system exists in five stacks, all sharing the screens and the visual identity, and all of them are free:

- React 19: https://github.com/danilo62x/forum-react
- Vue 3.5: https://github.com/danilo62x/forum-vue3
- Angular 19: https://github.com/danilo62x/forum-angular
- Static HTML: https://github.com/danilo62x/forum-html

The full catalog of free and paid templates is at https://template.dev.br

## Support this project

This template is free and MIT licensed. If it saves you time, you can support the work with a donation at https://template.dev.br/doar?template=forum-laravel

## License

[MIT](./LICENSE), copyright 2026 Danilo Quinelato.
