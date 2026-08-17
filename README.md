# Shar3 Backend (معهد علم شرعي)

Laravel API and Filament admin panel for the **Shar3a** Islamic educational platform. The public student website consumes the `/api/v1` JSON API; staff manage content and enrollments through Filament.

## Stack

| Component | Version |
|-----------|---------|
| PHP | 8.3+ |
| Laravel | 13 |
| Filament | 4 |
| Laravel Sanctum | 4 |
| PHPUnit | 12 |
| Laravel Pint | 1 |

## Architecture

```
app/
├── Actions/              # Focused business logic (enrollment, progress, quiz)
├── Filament/             # Admin panel resources & settings pages
├── Http/
│   ├── Controllers/Api/  # Versioned REST API (/api/v1)
│   ├── Middleware/       # EnsureStudentUser, etc.
│   └── Resources/        # API Resources (courses, public endpoints)
├── Models/
└── Support/              # Helpers (MediaUrl, Countries, AdminPanelSettings)

routes/api.php            # All public & student API routes
```

- **API versioning:** all endpoints live under `/api/v1`
- **Authentication:** Laravel Sanctum bearer tokens for students
- **Admin:** Filament at `/admin` (session-based, admin/staff roles only)
- **Roles:** `admin`, `staff`, `teacher`, `student`

## Requirements

- PHP 8.3 with extensions: `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`
- Composer 2
- MySQL 8+ (production) or SQLite (local/testing)
- Node.js (optional, only if compiling frontend assets in this repo)

## Installation

```bash
git clone https://github.com/Mahmoudhammad93/shar3_backend.git
cd shar3_backend
composer install
cp .env.example .env
php artisan key:generate
```

## Environment

Configure `.env`:

```env
APP_NAME="Shar3 Backend"
APP_URL=https://your-api-domain.test
APP_DEBUG=false          # production

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shar3
DB_USERNAME=...
DB_PASSWORD=...

FILESYSTEM_DISK=local    # or s3 in production
QUEUE_CONNECTION=database
SESSION_DRIVER=database
CACHE_STORE=database
```

Do not commit `.env` or real credentials.

## Database

```bash
php artisan migrate
php artisan db:seed
```

For a clean local setup:

```bash
php artisan migrate:fresh --seed
```

Seeders populate demo courses, teachers, FAQs, site settings, and an admin user (see `DatabaseSeeder`).

## Storage

```bash
php artisan storage:link
```

Uploaded media (course images, certificates, site logo) are stored on the configured disk. Private files are served through authenticated/signed routes where applicable.

## Running locally

```bash
php artisan serve
# API: http://127.0.0.1:8000/api/v1
# Admin: http://127.0.0.1:8000/admin
```

## Authentication

### Student API (Sanctum)

```http
POST /api/v1/auth/register
POST /api/v1/auth/login
Authorization: Bearer {token}
GET  /api/v1/auth/me
POST /api/v1/auth/logout
```

- Only users with role `student` may use the student login endpoint.
- Admin/staff must use the Filament admin login.
- Suspended students receive `403`.

### Rate limits

| Limiter | Routes |
|---------|--------|
| `auth` | register, login |
| `enrollment` | public & student enrollments |
| `quiz` | lesson quiz submission |
| `contact` | contact form, volunteer |

## Enrollment flow

```
Student submits enrollment → pending → admin review → approved | rejected
```

- Rejected enrollments can be resubmitted (returns to `pending`).
- Duplicate `(student_id, course_id)` pairs are prevented at the database level and in application logic with row locking.
- Approved/completed enrollments grant course access in the student portal.

## Student portal

Authenticated student routes (`auth:sanctum` + `student` middleware):

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/student/dashboard` | Stats & recent courses |
| GET | `/student/courses` | Enrolled courses |
| POST | `/student/enrollments` | Request enrollment |
| GET | `/student/courses/{id}` | Course detail + lessons |
| POST | `/student/lessons/{id}/progress` | Save video progress |
| POST | `/student/lessons/{id}/complete` | Complete lesson |
| GET/POST | `/student/lessons/{id}/quiz` | Quiz & submission |
| GET | `/student/assignments` | Assignments list |
| POST | `/student/assignments/{id}/submit` | Submit assignment |
| GET | `/student/grades` | Grades & certificates |
| GET | `/student/certificates/{id}` | Certificate detail |
| GET/PUT | `/student/profile` | Profile |
| GET | `/student/schedule` | Schedule |

## Public API overview

| Method | Endpoint |
|--------|----------|
| GET | `/home` |
| GET | `/settings` |
| GET | `/courses`, `/courses/{slug}` |
| GET | `/programs`, `/programs/{slug}` |
| GET | `/teachers`, `/teachers/{slug}` |
| GET | `/announcements`, `/announcements/{slug}` |
| GET | `/faqs` |
| GET | `/academic/structure` |
| POST | `/contact` |
| POST | `/enrollments` (guest enrollment) |
| POST | `/volunteer` |

## Admin panel

- URL: `/admin`
- Access: `admin` and `staff` roles (`User::canAccessPanel()`)
- Students and teachers cannot access Filament
- Manage courses, lessons, quizzes, enrollments, students, certificates, site settings, and more

## Testing

Tests use SQLite in-memory (`phpunit.xml`).

```bash
php artisan test
# or
./vendor/bin/phpunit
```

Coverage includes authentication, enrollment, courses, lesson progress, quizzes, student portal, and authorization.

## Code quality

```bash
./vendor/bin/pint          # fix style
./vendor/bin/pint --test   # check only
composer validate
```

## Deployment

1. Set `APP_ENV=production`, `APP_DEBUG=false`
2. Run `composer install --no-dev --optimize-autoloader`
3. Run migrations: `php artisan migrate --force`
4. Cache config/routes/views: `php artisan optimize`
5. Ensure queue worker is running if using queued jobs: `php artisan queue:work`
6. Configure web server document root to `public/`
7. Set up HTTPS and CORS for the frontend domain

```bash
php artisan optimize:clear   # after config changes
php artisan route:list       # verify routes
```

## Queue

`QUEUE_CONNECTION=database` is the default. Run a worker in production:

```bash
php artisan queue:work --tries=3
```

## License

MIT
