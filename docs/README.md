# Newsly Backend Documentation

## Overview

Newsly is a comprehensive news aggregator backend built with Laravel 11. It provides APIs for a mobile news application similar to "Nabd", featuring:

- 🔐 Google Sign-In authentication
- 📰 RSS feed fetching and parsing
- 🔔 Push notifications via Firebase Cloud Messaging
- 📱 REST API for React Native mobile app
- 🎛️ Modern admin panel

## Quick Start

### Prerequisites

- PHP 8.2+
- MySQL 8.0+
- Redis
- Composer
- Node.js (for admin panel assets)

### Installation

1. **Clone and install dependencies:**

```bash
git clone <repository-url>
cd newsly-backend
composer install
```

2. **Configure environment:**

```bash
cp env.example .env
php artisan key:generate
```

3. **Update `.env` with your settings:**

```env
DB_DATABASE=newsly
DB_USERNAME=your_username
DB_PASSWORD=your_password

GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_MOBILE_CLIENT_ID_ANDROID=your-android-client-id
GOOGLE_MOBILE_CLIENT_ID_IOS=your-ios-client-id
```

4. **Run migrations and seeders:**

```bash
php artisan migrate --seed
```

5. **Start the development server:**

```bash
php artisan serve
```

### Docker Setup

```bash
docker-compose up -d
docker-compose exec app php artisan migrate --seed
```

## API Documentation

### Authentication

All authenticated endpoints require a Bearer token in the Authorization header:

```
Authorization: Bearer {token}
```

### Base URL

- Development: `http://localhost:8000/api`
- Production: `https://api.newsly.app`

### Endpoints

See `openapi.yaml` for complete API documentation or import `postman/newsly_api_collection.json` into Postman.

## Google Sign-In Setup

1. Go to [Google Cloud Console](https://console.cloud.google.com)
2. Create a new project or select existing
3. Enable Google Sign-In API
4. Create OAuth 2.0 credentials:
   - Web application (for backend)
   - Android application
   - iOS application
5. Add the client IDs to your `.env` file

## Firebase Cloud Messaging Setup

1. Go to [Firebase Console](https://console.firebase.google.com)
2. Create a new project
3. Go to Project Settings > Service Accounts
4. Generate a new private key
5. Save the JSON file to `storage/firebase-credentials.json`
6. Update `FIREBASE_CREDENTIALS` in `.env`

## Queue Workers

Start the queue worker for background jobs:

```bash
php artisan queue:work redis --sleep=3 --tries=3
```

Or use Supervisor for production (see `docker/supervisor/supervisord.conf`).

## Scheduler

Run the scheduler for RSS fetching:

```bash
php artisan schedule:work
```

Or add to crontab for production:

```
* * * * * cd /path/to/newsly && php artisan schedule:run >> /dev/null 2>&1
```

## Admin Panel

Access the admin panel at `/admin`:

- Default credentials:
  - Email: `admin@newsly.app`
  - Password: `admin123` (change this!)

## Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=AuthTest

# Run with coverage
php artisan test --coverage
```

## Project Structure

```
app/
├── Console/Commands/     # Artisan commands
├── Http/
│   ├── Controllers/
│   │   ├── Api/         # API controllers
│   │   └── Admin/       # Admin panel controllers
│   ├── Requests/        # Form requests (validation)
│   └── Resources/       # API resources
├── Jobs/                # Queue jobs
├── Models/              # Eloquent models
└── Services/            # Business logic services

config/
├── auth.php             # Authentication config
├── news.php             # App-specific config
└── services.php         # Third-party services

database/
├── factories/           # Model factories
├── migrations/          # Database migrations
└── seeders/             # Database seeders

resources/views/admin/   # Admin panel views

routes/
├── api.php              # API routes
└── web.php              # Web routes (admin)
```

## Environment Variables

| Variable | Description |
|----------|-------------|
| `GOOGLE_CLIENT_ID` | Google OAuth Web Client ID |
| `GOOGLE_MOBILE_CLIENT_ID_ANDROID` | Google OAuth Android Client ID |
| `GOOGLE_MOBILE_CLIENT_ID_IOS` | Google OAuth iOS Client ID |
| `FIREBASE_CREDENTIALS` | Path to Firebase service account JSON |
| `RSS_FETCH_INTERVAL_SECONDS` | Default interval between RSS fetches |
| `FCM_BATCH_SIZE` | Number of tokens per FCM batch |
| `QUEUE_CONNECTION` | Queue driver (redis recommended) |

## License

MIT License



