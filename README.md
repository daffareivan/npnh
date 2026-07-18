# NPNHCREATIVE

**NPNHCREATIVE** is a Laravel-powered SaaS for converting audio files into Roblox-ready OGG assets. Users upload audio, apply speed/pitch presets (e.g. `2.3x`, `2.5x`, `2.7x` nightcore-style speed-ups with dB amplification), and download or push the converted file straight to Roblox via built-in OAuth integration. Access to conversions and downloads is metered through a credit system backed by subscription plans and Midtrans payments.

## ✨ Key Features

* 🎧 **Audio Conversion Pipeline** — Upload audio, process it asynchronously with a headless-Chromium (Web Audio API) engine (speed/pitch, bass boost, format), and track job status/progress in real time.
* 🎛️ **Conversion Presets** — Admin-managed speed/amplify presets applied per upload.
* 🎮 **Roblox Integration** — Connect a Roblox account via OAuth, and upload converted audio directly as a Roblox asset.
* 💳 **Credit System** — Downloads and conversions consume credits; balances, history, and admin-configurable costs are tracked per user.
* 📦 **Subscription Plans & Payments** — Plan-based subscriptions with checkout, manual/gateway payment confirmation, invoices, and [Midtrans](https://midtrans.com/) payment gateway integration (including webhook handling).
* 👥 **Community** — User reviews, comments, helpful votes, badges, and reports on the homepage, moderated from the admin panel.
* 🔐 **Authentication** — Email/password auth plus Google OAuth login, email verification, and password reset flows.
* 🛠️ **Admin Dashboard** — Manage users, conversions, queue/activity monitoring, credit settings, subscription plans/orders/transactions, community moderation, and app-wide settings.
* 🎨 **Tailwind CSS v4 + Alpine.js UI** — Responsive, dark-mode-ready dashboard UI (built on the TailAdmin Laravel component set).

## 🧱 Tech Stack

* **Laravel 12** (PHP 8.4)
* **Tailwind CSS v4** + **Alpine.js** + **Vite**
* **Node.js + Puppeteer (headless Chromium)** for audio conversion — see `audio-engine/` (no ffmpeg dependency)
* **spatie/laravel-permission** for roles & permissions
* **laravel/socialite** for Google OAuth
* **midtrans/midtrans-php** for payments
* **Pest** for testing

## 📋 Requirements

* **PHP 8.4+** with the extensions Laravel requires
* **Composer**
* **Node.js 18+** and **npm** — also used to run the `audio-engine/` conversion engine (`cd audio-engine && npm install` once; downloads a bundled Chromium via Puppeteer)
* **Database** — MySQL (default) or any Laravel-supported driver
* A queue worker running (`CONVERTER_QUEUE`, default `audio-conversion`) to process conversion jobs

## 🚀 Installation

### 1. Install dependencies

```bash
composer install
npm install
cd audio-engine && npm install && cd ..
```

The `audio-engine` install downloads a bundled Chromium via Puppeteer (~300MB) — this is what the conversion pipeline uses instead of ffmpeg. On a minimal Ubuntu server, headless Chromium needs a handful of shared libraries that aren't installed by default (`libnss3`, `libatk1.0-0`, `libgbm1`, `libasound2`, etc.) — install them with:

```bash
sudo apt-get install -y libnss3 libatk1.0-0 libatk-bridge2.0-0 libcups2 libdrm2 \
  libxkbcommon0 libxcomposite1 libxdamage1 libxfixes3 libxrandr2 libgbm1 libasound2
```

### 2. Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

Set at minimum:

```env
APP_NAME=NPNHCREATIVE

DB_CONNECTION=mysql
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

NODE_BINARY=node
AUDIO_ENGINE_PATH=/absolute/path/to/audio-engine/engine.mjs

# Optional integrations
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=

MIDTRANS_MERCHANT_ID=
MIDTRANS_CLIENT_KEY=
MIDTRANS_SERVER_KEY=
```

### 3. Set up the database

```bash
php artisan migrate
php artisan db:seed   # optional: sample data (plans, presets, settings, etc.)
```

### 4. Link storage

```bash
php artisan storage:link
```

## 🏃 Running the Application

Start everything (server, queue worker, log tail, Vite) in one go:

```bash
composer run dev
```

App will be available at [http://localhost:8000](http://localhost:8000).

Or run services individually:

```bash
php artisan serve
php artisan queue:listen        # required for audio conversion jobs
npm run dev
```

### Production build

```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

## 🧪 Testing

```bash
composer run test
# or
php artisan test
php artisan test --filter=SomeTest
```

## 📁 Project Structure

```
converter/
├── app/
│   ├── DTO/                    # Data transfer objects (upload, Roblox)
│   ├── Enums/                  # ConversionStatus and other enums
│   ├── Http/Controllers/
│   │   ├── Admin/              # Admin panel (users, credits, subscriptions, community, settings)
│   │   ├── App/                # Authenticated app (converter, Roblox, subscription, community)
│   │   ├── Auth/                # Sign in/up, Google OAuth, password reset, email verification
│   │   └── Converter/           # Public converter page + API
│   ├── Jobs/                    # ProcessAudioConversion queue job
│   ├── Models/                  # AudioFile, ConversionJob/Preset, Plan, Order, Subscription, ...
│   ├── Services/
│   │   ├── Roblox/              # OAuth, account, asset, token, user services
│   │   ├── ConverterService.php # Upload/convert/download/delete pipeline
│   │   ├── CreditService.php    # Credit balance & deduction
│   │   ├── NodeAudioConverter.php # Headless-Chromium conversion engine bridge (audio-engine/)
│   │   ├── MidtransService.php / PaymentService.php / SubscriptionService.php
│   │   └── CommunityService.php
│   └── Repositories/            # AudioFileRepository, etc.
├── config/converter.php         # Converter-specific configuration
├── database/migrations/         # Users, audio files, credits, plans, community, ...
├── resources/views/
│   ├── app/                     # Authenticated user views (converter, integrations, community)
│   ├── pages/admin/              # Admin panel views
│   └── layouts/                  # App/auth/sidebar layouts
├── routes/web.php               # All application routes
└── docs/                        # Additional project notes
```

## 🔑 Access Control

Routes are protected with role/permission middleware (`role:user|admin`, `permission:converter.upload|converter.convert`, `active`, `verified.config`) powered by `spatie/laravel-permission`. Admin-only routes live under `/admin` and require the `admin` role.

## License

MIT — see [LICENSE](./LICENSE).
