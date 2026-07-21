# NPNHCREATIVE

NPNHCREATIVE is a Laravel SaaS application for converting audio into Roblox-ready assets. Users can upload audio, apply optimized speed presets, split long files automatically, download converted results, and upload assets to Roblox using the Roblox account they connect through OAuth.

The app uses a credit-based subscription model. Credits are not consumed while experimenting with conversion settings; they are consumed when users download a result or upload a converted file to Roblox.

## Recent Updates From The Last 5 Commits

These notes are based on the latest five commits in the repository:

- `eed913e` - Finished conversions are hidden from History until the user downloads a result or uploads it to Roblox. In-progress and failed entries still appear.
- `1af7ad5` - Improved the frontend flow for automatic file splitting.
- `60894b7` - Replaced the FFmpeg conversion service with the Node/Web Audio conversion engine in `audio-engine/`.
- `0d60cd2` - Added automatic split-file conversion, per-file resources, download-all flow, split conversion jobs, and Roblox upload support for conversion files.
- `66abfed` - Added polished error pages, upload limits, localized page/error strings, and related admin/user UI updates.

## Key Features

- Audio conversion pipeline with asynchronous processing and real-time status.
- Optimized Roblox audio presets such as `2.3x`, `2.5x`, and `2.7x`.
- Automatic split-file conversion for long audio.
- Per-file result management, download logs, and download-all archive support.
- Roblox OAuth connection and per-connected-account upload flow.
- Credit system for downloads and Roblox uploads.
- Subscription plans: Free, Standard, Premium, and Custom.
- Mustika Payment gateway integration using `mustikapay-node`.
- Payment webhook processing through Laravel jobs.
- Invoices, payment history, payment transactions, and payment notifications.
- Homepage reviews, comments, badges, helpful votes, and admin moderation.
- Google OAuth login plus email/password authentication.
- Admin dashboard for users, conversions, credits, subscriptions, payments, reviews, settings, and activity.
- Theme and language preferences across the app.

## Tech Stack

- Laravel 12
- PHP 8.4+
- PostgreSQL
- Tailwind CSS v4
- Alpine.js
- Vite
- Node.js
- `mustikapay-node` for Mustika Payment API access
- `spatie/laravel-permission` for roles and permissions
- `laravel/socialite` for Google OAuth
- Pest for tests

## Audio Engine

The conversion engine lives in `audio-engine/` and is called from Laravel through `NodeAudioConverter`.

The app no longer depends on FFmpeg for the main conversion path. Instead, it uses a Node-powered Web Audio pipeline with a headless browser engine.

Install audio engine dependencies once:

```bash
cd audio-engine
npm install
cd ..
```

On a minimal Linux server, Chromium may require extra shared libraries:

```bash
sudo apt-get install -y libnss3 libatk1.0-0 libatk-bridge2.0-0 libcups2 libdrm2 \
  libxkbcommon0 libxcomposite1 libxdamage1 libxfixes3 libxrandr2 libgbm1 libasound2
```

## Installation

### 1. Clone and enter the project

```bash
git clone <repository-url> converter
cd converter
```

If you already have the project folder, start from the project root:

```bash
cd D:\converter
```

### 2. Install backend dependencies

```bash
composer install
```

### 3. Install frontend and payment bridge dependencies

The main frontend and MustikaPay Node bridge use the root `package.json`.

```bash
npm install
```

This installs `mustikapay-node`, Vite, Tailwind, Alpine, and the dashboard frontend dependencies.

### 4. Install audio engine dependencies

The conversion engine has its own Node project in `audio-engine/`.

```bash
cd audio-engine
npm install
cd ..
```

### 5. Create and configure `.env`

```bash
cp .env.example .env
php artisan key:generate
```

Set the required database, app URL, Node, audio engine, OAuth, Roblox, and MustikaPay values in `.env`.

For local development:

```env
APP_URL=http://127.0.0.1:8000
QUEUE_CONNECTION=sync
NODE_BINARY=node
AUDIO_ENGINE_PATH=D:/converter/audio-engine/engine.mjs
```

For webhook testing through ngrok, use the ngrok URL:

```env
APP_URL=https://your-ngrok-domain.ngrok-free.dev
MUSTIKA_CALLBACK_URL="${APP_URL}/payment/webhook/mustika"
ROBLOX_REDIRECT_URI="${APP_URL}/integrations/roblox/callback"
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

### 6. Prepare the database

```bash
php artisan migrate
php artisan db:seed
```

Seeders create the default plans, credit settings, navigation, admin data, and conversion presets used by the app.

### 7. Link storage

```bash
php artisan storage:link
```

### 8. Build frontend assets

For development:

```bash
npm run dev
```

For production/static assets:

```bash
npm run build
```

### 9. Clear cached config

Run this after changing `.env`:

```bash
php artisan config:clear
```

## Required Environment

Basic application setup:

```env
APP_NAME=NPNHCREATIVE
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=roblox
DB_USERNAME=postgres
DB_PASSWORD=

NODE_BINARY=node
AUDIO_ENGINE_PATH=/absolute/path/to/audio-engine/engine.mjs
AUDIO_ENGINE_TIMEOUT=900
```

For local webhook testing through ngrok, set `APP_URL` to the ngrok URL:

```env
APP_URL=https://your-ngrok-domain.ngrok-free.dev
```

## Mustika Payment

Mustika Payment is the active payment gateway. Laravel calls `mustikapay-node` through:

```text
resources/js/mustika-payment-bridge.cjs
```

Payment configuration:

```env
PAYMENT_GATEWAY=mustika
MUSTIKA_BASE_URL=https://mustikapayment.com
MUSTIKA_API_KEY=MP-xxxx
MUSTIKA_MERCHANT_ID=
MUSTIKA_CALLBACK_SECRET=
MUSTIKA_CALLBACK_URL="${APP_URL}/payment/webhook/mustika"
MUSTIKA_RETURN_URL="${APP_URL}/payment/success"
MUSTIKA_CANCEL_URL="${APP_URL}/payment/failed"
MUSTIKA_ENV=sandbox
MUSTIKA_RESOLVED_IP=
```

Webhook endpoint:

```text
POST /payment/webhook/mustika
```

For local development you can keep queue processing synchronous:

```env
QUEUE_CONNECTION=sync
```

For production, use a real queue worker:

```bash
php artisan queue:work
```

If local DNS has issues resolving `mustikapayment.com`, leave `MUSTIKA_RESOLVED_IP` empty first. Only set it if your environment requires a manual resolved IP.

## Roblox Integration

Roblox uploads use the Roblox account connected by the current user:

```env
ROBLOX_UPLOAD_AUTH=oauth
ROBLOX_CLIENT_ID=
ROBLOX_CLIENT_SECRET=
ROBLOX_REDIRECT_URI="${APP_URL}/integrations/roblox/callback"
ROBLOX_SCOPES="openid profile asset:read asset:write"
```

These are not needed for per-connected-account uploads and should usually stay empty:

```env
ROBLOX_OPEN_CLOUD_CREATOR_USER_ID=
ROBLOX_OPEN_CLOUD_CREATOR_GROUP_ID=
```

Only use fixed creator IDs if you intentionally switch back to API-key based upload for one fixed user or group.

## Google OAuth

```env
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

In Google Cloud Console, add:

```text
Authorized JavaScript origin: APP_URL
Authorized redirect URI: APP_URL/auth/google/callback
```

## Running The App

### Local Development

Start Laravel:

```bash
php artisan serve
```

Start Vite in a second terminal:

```bash
npm run dev
```

If `QUEUE_CONNECTION=sync`, you do not need a queue worker for local webhook testing.

If you use `database`, `redis`, or another queue driver, start a worker:

```bash
php artisan queue:work
```

Or use the Composer dev script:

```bash
composer run dev
```

## Production Build

Install optimized PHP dependencies:

```bash
composer install --optimize-autoloader --no-dev
```

Build assets:

```bash
npm install
cd audio-engine && npm install && cd ..
npm run build
```

Run database setup:

```bash
php artisan migrate --force
php artisan storage:link
```

Cache Laravel config:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

For production payments, make sure:

- `APP_URL` uses HTTPS.
- Mustika webhook URL is set to `/payment/webhook/mustika`.
- The server public IP is whitelisted in MustikaPay if required.
- A queue worker is running if `QUEUE_CONNECTION` is not `sync`.

Example queue worker command:

```bash
php artisan queue:work --tries=3 --timeout=900
```

On a VPS, run the queue worker with Supervisor or a similar process manager.

## Testing

```bash
php artisan test
```

Frontend build:

```bash
npm run build
```

## Project Structure

```text
app/
  Http/Controllers/
    Admin/
    App/
    Auth/
    Converter/
  Jobs/
    ProcessAudioConversion.php
    SplitAudioConversionJob.php
    ProcessPaymentWebhook.php
  Models/
  Services/
    Payment/
      PaymentGatewayInterface.php
      PaymentManager.php
      MustikaPaymentService.php
    Roblox/
    NodeAudioConverter.php
    AudioSplitService.php
    CreditService.php
    SubscriptionService.php
audio-engine/
config/
  converter.php
  payment.php
resources/
  js/
    mustika-payment-bridge.cjs
  views/
routes/
  web.php
  api.php
```

## Payment Flow

1. User chooses a plan.
2. Laravel creates an order.
3. `PaymentService` resolves the active gateway through `PaymentManager`.
4. `MustikaPaymentService` calls the Node bridge.
5. The Node bridge calls MustikaPay through `mustikapay-node`/Axios.
6. MustikaPay sends webhook to `/payment/webhook/mustika`.
7. `ProcessPaymentWebhook` updates the transaction.
8. Successful payment activates the subscription, adds credits, creates invoice data, and sends notifications.

## Credit Rules

Credits are consumed only for:

- Downloading converted audio.
- Uploading converted audio to Roblox.

Credits are not consumed for:

- Uploading source audio.
- Converting audio.
- Previewing audio.
- Changing playback speed or preset.
- Login/logout.

## Access Control

Routes use role and permission middleware powered by `spatie/laravel-permission`.

Admin routes live under:

```text
/admin
```

User app routes live under:

```text
/app
```

## License

MIT
