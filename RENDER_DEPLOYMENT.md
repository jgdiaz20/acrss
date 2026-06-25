# Render Deployment Guide — Laravel School Timetable

**Platform:** Render.com · **Runtime:** Docker (PHP 8.2 + Nginx + Supervisor)  
**Stack:** Laravel 10 · MySQL or PostgreSQL · Redis · AWS S3 (file storage)

---

## Table of Contents

1. [Prerequisites & Accounts](#1-prerequisites--accounts)
2. [Prepare Your Local Repository](#2-prepare-your-local-repository)
3. [Generate Your APP_KEY](#3-generate-your-app_key)
4. [Create a PostgreSQL Database on Render](#4-create-a-postgresql-database-on-render)
5. [Create a Redis Instance on Render](#5-create-a-redis-instance-on-render)
6. [Set Up AWS S3 for File Storage](#6-set-up-aws-s3-for-file-storage-recommended)
7. [Create the Web Service on Render](#7-create-the-web-service-on-render)
8. [Configure All Environment Variables](#8-configure-all-environment-variables)
9. [Deploy & Verify the Web Service](#9-deploy--verify-the-web-service)
10. [Create the Worker Service (Queue Jobs)](#10-create-the-worker-service-queue-jobs)
11. [Create the Cron Job (Laravel Scheduler)](#11-create-the-cron-job-laravel-scheduler)
12. [Seed the Database (First-Time Setup)](#12-seed-the-database-first-time-setup)
13. [Post-Deployment Verification](#13-post-deployment-verification)
14. [Troubleshooting](#14-troubleshooting)
15. [Updating the Application](#15-updating-the-application)
16. [Complete Environment Variable Reference](#16-complete-environment-variable-reference)

---

## 1. Prerequisites & Accounts

Before starting, make sure you have the following:

| Requirement | Details |
|---|---|
| **Render account** | https://render.com — free tier works for testing |
| **GitHub account** | Your code must be in a GitHub repository |
| **AWS account** | For S3 file storage (optional but strongly recommended) |
| **PHP 8.1+ locally** | Only needed for generating APP_KEY |
| **Git installed** | To push your code |

> **Important:** Render's **free tier** services spin down after 15 minutes of inactivity. For production, use the **Starter plan ($7/month)** or higher.

---

## 2. Prepare Your Local Repository

### 2a. Ensure all deployment files are committed

Check that these files exist in your project root:

```
Dockerfile
docker-compose.yml
render.yaml
.dockerignore
docker/
├── nginx.conf
├── default.conf
├── php-fpm.conf
├── supervisor.conf
└── start.sh
```

### 2b. Make sure `.env` is NOT committed

Your `.env` file must never be committed to git (it contains secrets). Verify it's in `.gitignore`:

```bash
# Check that .env is ignored
git status --short | grep ".env"
# Should show nothing (meaning it's properly ignored)
```

### 2c. Push your latest code to GitHub

```bash
git add .
git commit -m "Production-ready Docker deployment"
git push origin main
```

> Make sure you push to the `main` branch. This is what `render.yaml` uses by default (`branch: main`).

---

## 3. Generate Your APP_KEY

Laravel requires a unique application key for encryption. Run this locally:

```bash
php artisan key:generate --show
```

**Example output:**
```
base64:xyz1234ABC5678DEF/abcdefghijklmnopqrstuvwxyz==
```

> ⚠️ **Copy this entire string including the `base64:` prefix.** You'll paste it into Render's environment variables. Never reuse an old key.

If you don't have PHP locally, you can generate one online using:

```bash
# On any system with OpenSSL:
echo "base64:$(openssl rand -base64 32)"
```

---

## 4. Create a PostgreSQL Database on Render

Render's managed PostgreSQL is the recommended database (MySQL requires an external provider).

### Steps:

1. Go to [https://dashboard.render.com](https://dashboard.render.com)
2. Click **New +** → **PostgreSQL**
3. Fill in:
   - **Name:** `laravel-timetable-db`
   - **Database:** `laravel_timetable`
   - **User:** `laravel_user` (or any name you prefer)
   - **Region:** `Oregon (US West)` — same as your Web Service
   - **Plan:** Free (testing) or Starter ($7/mo for production)
4. Click **Create Database**
5. Wait ~1–2 minutes for provisioning

### Collect your credentials:

After creation, go to the database page and find the **Connections** section. You'll need:

| Variable | Where to find it |
|---|---|
| `DB_HOST` | "Hostname" field |
| `DB_PORT` | "Port" field (usually `5432`) |
| `DB_DATABASE` | "Database" field |
| `DB_USERNAME` | "Username" field |
| `DB_PASSWORD` | "Password" field (click the eye icon) |

> **Tip:** Render also provides an "Internal Database URL" — you can use this as a single `DATABASE_URL` variable if you modify your `config/database.php`, but using individual variables is safer and clearer.

### Using MySQL instead?

Render does **not** offer MySQL as a managed service. If you need MySQL:
- Use **PlanetScale** (free tier available): https://planetscale.com
- Use **Aiven for MySQL**: https://aiven.io
- Use **Railway's MySQL**: https://railway.app

For PlanetScale specifically:
1. Create a free database
2. Create a branch (use `main`)
3. Get the connection string from "Connect" → "Connect with" → "Laravel"
4. Note: PlanetScale doesn't support foreign key constraints by default — enable `STRICT_TRANS_TABLES` and check your migrations

---

## 5. Create a Redis Instance on Render

### Steps:

1. In Render dashboard, click **New +** → **Redis**
2. Fill in:
   - **Name:** `laravel-timetable-redis`
   - **Region:** Same as your Web Service (`Oregon`)
   - **Plan:** Free (limited, 25MB) or Starter ($10/mo)
   - **Maxmemory Policy:** `allkeys-lru` (recommended for cache)
3. Click **Create Redis**
4. Wait ~1 minute for provisioning

### Collect your credentials:

From the Redis dashboard page:

| Variable | Where to find it |
|---|---|
| `REDIS_HOST` | "Host" field under "Connection" |
| `REDIS_PORT` | "Port" field (usually `6379`) |
| `REDIS_PASSWORD` | "Password" field (click the eye icon) |

> **Note:** Render Redis instances **always have a password**. Never set `REDIS_PASSWORD` to the literal string `null` — leave it unset only if there truly is no password (which won't be the case on Render's managed Redis).

---

## 6. Set Up AWS S3 for File Storage (Recommended)

The application is configured to use S3 for uploaded files. Without it, any uploaded files will be lost on each deployment (Docker containers are ephemeral).

### Create an S3 Bucket:

1. Log in to [https://console.aws.amazon.com](https://console.aws.amazon.com)
2. Go to **S3** → **Create bucket**
3. Settings:
   - **Bucket name:** `laravel-timetable-prod` (must be globally unique)
   - **Region:** `us-east-1` (or your nearest region)
   - **Block all public access:** Uncheck this if your app serves public files directly
   - **Object Ownership:** `ACLs enabled` → `Bucket owner preferred`
4. Click **Create bucket**

### Create an IAM User for S3 access:

1. Go to **IAM** → **Users** → **Add users**
2. **Username:** `laravel-timetable-s3`
3. Select **Attach policies directly** → choose **AmazonS3FullAccess** (or create a scoped policy below)
4. Complete creation, then click the user → **Security credentials** → **Create access key**
5. Choose **Application running outside AWS** → Create
6. **Download or copy:**
   - `AWS_ACCESS_KEY_ID`
   - `AWS_SECRET_ACCESS_KEY`

### Scoped IAM Policy (More Secure):

Instead of `AmazonS3FullAccess`, use this minimal policy (replace `YOUR_BUCKET_NAME`):

```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": [
                "s3:GetObject",
                "s3:PutObject",
                "s3:DeleteObject",
                "s3:ListBucket"
            ],
            "Resource": [
                "arn:aws:s3:::YOUR_BUCKET_NAME",
                "arn:aws:s3:::YOUR_BUCKET_NAME/*"
            ]
        }
    ]
}
```

> **Don't want S3?** You can skip this and use local disk storage — but files will be lost on every redeploy. If skipping S3, set `FILESYSTEM_DRIVER=local` and `FILESYSTEM_CLOUD=local` instead.

---

## 7. Create the Web Service on Render

1. Go to [https://dashboard.render.com](https://dashboard.render.com)
2. Click **New +** → **Web Service**
3. Connect your GitHub repository:
   - Click **Connect account** (if first time)
   - Search for your repo: `Laravel-School-Timetable-Calendar`
   - Click **Connect**
4. Configure the service:

| Setting | Value |
|---|---|
| **Name** | `laravel-timetable` |
| **Region** | `Oregon (US West)` |
| **Branch** | `main` |
| **Runtime** | `Docker` |
| **Dockerfile Path** | `./Dockerfile` |
| **Docker Context** | `.` (project root) |
| **Plan** | Starter ($7/mo) or Free for testing |

5. Do **NOT** click "Create Web Service" yet — scroll down to add environment variables first.

---

## 8. Configure All Environment Variables

This is the most critical step. In the **Environment** section of your Web Service setup, add each variable below.

### Application Settings

| Key | Value | Notes |
|---|---|---|
| `APP_NAME` | `Laravel School Timetable` | Displayed in UI |
| `APP_ENV` | `production` | **Required** |
| `APP_DEBUG` | `false` | **Never set to true in production** |
| `APP_KEY` | `base64:...` | Paste the key from Step 3 |
| `APP_URL` | `https://laravel-timetable.onrender.com` | Your full Render URL (set after service is created) |

> ⚠️ `APP_URL` will contain your actual Render URL. You can set it after creating the service — use a placeholder first, then update it.

### Logging

| Key | Value |
|---|---|
| `LOG_CHANNEL` | `stderr` |

> `stderr` is required for Docker — it sends logs to Render's log viewer.

### Database Settings (PostgreSQL)

| Key | Value | Notes |
|---|---|---|
| `DB_CONNECTION` | `pgsql` | Use `mysql` if using MySQL |
| `DB_HOST` | *(from Step 4)* | Render's internal hostname |
| `DB_PORT` | `5432` | (`3306` for MySQL) |
| `DB_DATABASE` | *(from Step 4)* | Database name |
| `DB_USERNAME` | *(from Step 4)* | Database user |
| `DB_PASSWORD` | *(from Step 4)* | Database password |

### Cache, Session & Queue (Redis)

| Key | Value |
|---|---|
| `CACHE_DRIVER` | `redis` |
| `SESSION_DRIVER` | `redis` |
| `QUEUE_CONNECTION` | `redis` |
| `SESSION_LIFETIME` | `180` |
| `REDIS_CLIENT` | `phpredis` |
| `REDIS_HOST` | *(from Step 5)* |
| `REDIS_PORT` | `6379` |
| `REDIS_PASSWORD` | *(from Step 5)* |

> ⚠️ If Render Redis has a password (it always does), set `REDIS_PASSWORD` to that exact password. Do **not** type the word `null`.

### File Storage (AWS S3)

| Key | Value |
|---|---|
| `FILESYSTEM_DRIVER` | `s3` |
| `FILESYSTEM_CLOUD` | `s3` |
| `AWS_ACCESS_KEY_ID` | *(from Step 6)* |
| `AWS_SECRET_ACCESS_KEY` | *(from Step 6)* |
| `AWS_DEFAULT_REGION` | `us-east-1` (or your region) |
| `AWS_BUCKET` | *(your bucket name from Step 6)* |
| `AWS_USE_PATH_STYLE_ENDPOINT` | `false` |

> If skipping S3, set `FILESYSTEM_DRIVER=local` and `FILESYSTEM_CLOUD=local` instead (files won't persist across deploys).

### Mail (Optional)

| Key | Value |
|---|---|
| `MAIL_MAILER` | `smtp` |
| `MAIL_HOST` | e.g. `smtp.mailgun.org` |
| `MAIL_PORT` | `587` |
| `MAIL_USERNAME` | Your SMTP username |
| `MAIL_PASSWORD` | Your SMTP password |
| `MAIL_ENCRYPTION` | `tls` |
| `MAIL_FROM_ADDRESS` | `noreply@yourdomain.com` |
| `MAIL_FROM_NAME` | `Laravel School Timetable` |

> Recommended mail providers: [Mailgun](https://mailgun.com), [Postmark](https://postmarkapp.com), or [Resend](https://resend.com). All have free tiers.

---

## 9. Deploy & Verify the Web Service

### Deploy:

After adding all environment variables, click **Create Web Service**.

Render will:
1. Clone your repository
2. Build the Docker image (takes 3–8 minutes on first build)
3. Run `docker/start.sh` which:
   - Waits for DB and Redis to be ready
   - Generates `APP_KEY` if not set
   - Runs `php artisan migrate --force`
   - Caches config, routes, and views
4. Start Nginx + PHP-FPM via Supervisor

### Monitor the build:

Watch the **Deploy Log** in real time. A successful deploy looks like:

```
Starting Laravel application...
Waiting for database connection...
Waiting for DB at your-db-host:5432...
Running migrations...
Migrating: 2014_10_12_100000_create_password_resets_table
...
Migrated:  2025_11_22_000002_add_lesson_type_and_duration_to_lessons_table (32ms)
Caching configuration...
Application started successfully!
```

### Update APP_URL:

Once the service is live, copy your URL (e.g. `https://laravel-timetable.onrender.com`) and:
1. Go to the service → **Environment**
2. Update `APP_URL` to your actual URL
3. Click **Save Changes** → Render will redeploy automatically

---

## 10. Create the Worker Service (Queue Jobs)

The Worker Service processes background jobs (like email sending, data exports, etc.).

1. In Render Dashboard, click **New +** → **Web Service** (yes, Web Service — then change type)
   - Actually: click **New +** → **Background Worker**
2. Connect the **same GitHub repository**
3. Configure:

| Setting | Value |
|---|---|
| **Name** | `laravel-timetable-worker` |
| **Runtime** | `Docker` |
| **Branch** | `main` |
| **Start Command** | `php /app/artisan queue:work --sleep=3 --tries=3 --timeout=90` |

4. Add **all the same environment variables** as the Web Service (copy them)
5. Click **Create Background Worker**

> The worker uses the same Docker image as the Web Service — it just runs a different command (`queue:work` instead of `supervisord`).

---

## 11. Create the Cron Job (Laravel Scheduler)

The Cron Job runs `php artisan schedule:run` every minute, which handles all Laravel scheduled tasks.

1. In Render Dashboard, click **New +** → **Cron Job**
2. Connect the **same GitHub repository**
3. Configure:

| Setting | Value |
|---|---|
| **Name** | `laravel-timetable-scheduler` |
| **Runtime** | `Docker` |
| **Branch** | `main` |
| **Schedule** | `* * * * *` (every minute) |
| **Command** | `sh -c 'php /app/artisan schedule:run'` |

4. Add **all the same environment variables** as the Web Service
5. Click **Create Cron Job**

---

## 12. Seed the Database (First-Time Setup)

After the first successful deploy, seed the database with default roles, permissions, and an admin user.

### Option A — Via Render Shell (Recommended)

1. Go to your Web Service on Render
2. Click the **Shell** tab
3. Run:

```bash
php artisan db:seed
```

This runs the `DatabaseSeeder` which calls:
- `PermissionsTableSeeder` — Creates all ACL permissions
- `RolesTableSeeder` — Creates Admin and Teacher roles
- `PermissionRoleTableSeeder` — Assigns permissions to roles
- `UsersTableSeeder` — Creates default admin user
- `RoleUserTableSeeder` — Assigns admin role to admin user

### Option B — Via a One-Time Render Job

1. In Render Dashboard, click **New +** → **Cron Job**
2. Set the schedule to a specific time in the next few minutes (e.g. `30 14 25 6 *` = June 25 at 14:30)
3. Command: `php /app/artisan db:seed --force`
4. Add env vars, create job, let it run once, then delete the cron job

### Default Admin Credentials

After seeding, check `database/seeds/UsersTableSeeder.php` for the default admin credentials:

```php
# Check this file for the default email/password
app/database/seeds/UsersTableSeeder.php
```

> ⚠️ **Change the default admin password immediately** after first login via the admin panel → Users.

---

## 13. Post-Deployment Verification

Run through this checklist after all services are deployed:

### ✅ Web Service

- [ ] Visit your app URL — the public homepage loads
- [ ] Login page works at `/login`
- [ ] Login with admin credentials succeeds
- [ ] Admin dashboard loads at `/admin`
- [ ] Lessons/Timetable data displays correctly
- [ ] No errors in Render → Logs → Web Service

### ✅ Database

- [ ] Migrations ran (check logs for "Migrated:")
- [ ] Can create/edit/delete records via the admin panel
- [ ] No database connection errors

### ✅ Redis

- [ ] Session persists across page refreshes (you stay logged in)
- [ ] No "Redis connection refused" in logs
- [ ] Cache works (second page load is faster)

### ✅ File Storage

- [ ] File uploads work (if the app has file upload features)
- [ ] Uploaded files persist across service restarts
- [ ] No "Unable to write file" errors in logs

### ✅ Worker & Scheduler

- [ ] Worker service shows "Running" status on Render dashboard
- [ ] Cron job has run at least once (check "Last Run" timestamp)
- [ ] Queue jobs complete (check Render → Worker → Logs)

---

## 14. Troubleshooting

### App won't start / stays in "Deploying"

**Symptoms:** Deployment never becomes "Live"

**Check:**
1. Render → Web Service → **Deploy Log** — look for error messages
2. Common cause: `APP_KEY` is empty. Ensure it starts with `base64:`

```bash
# Verify in Render Shell:
php artisan about | grep "Application Key"
```

### "Class not found" or 500 error

**Symptoms:** App loads but throws errors

**Solution:** Clear and rebuild caches:

```bash
# In Render Shell:
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Database connection refused

**Symptoms:** Logs show "Connection refused" or "SQLSTATE[HY000]"

**Check:**
1. `DB_HOST` — must be the Render internal hostname, not `localhost` or `127.0.0.1`
2. `DB_PORT` — `5432` for PostgreSQL, `3306` for MySQL
3. `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` — verify against Render DB panel
4. Database service must be in the same Render region as the Web Service

### Redis connection failed

**Symptoms:** Logs show "Connection refused" at `redis:6379`

**Check:**
1. `REDIS_HOST` — must be the Render Redis internal hostname
2. `REDIS_PASSWORD` — Render Redis always requires a password; do not use the word `null`
3. `REDIS_CLIENT=phpredis` — required; the phpredis PHP extension is installed in the Docker image

### Migrations not running

**Symptoms:** Tables don't exist, app crashes on first request

**Solution:** Run manually via Render Shell:

```bash
php artisan migrate --force
```

### Files disappearing after redeploy

**Cause:** You're using `FILESYSTEM_DRIVER=local` — files stored inside the container are lost on redeploy.

**Fix:** Switch to S3 (see Step 6), then:
```bash
# In Render Shell, sync any existing local files to S3:
php artisan storage:link
```

### Session gets dropped / users logged out after redeploy

**Cause:** Sessions stored in files — these are deleted when the container restarts.

**Fix:** Confirm `SESSION_DRIVER=redis` is set correctly. Redis persists sessions across restarts.

### Queue jobs not processing

**Symptoms:** Background tasks never complete

**Check:**
1. Worker service is in **Running** state (not Stopped or Failed)
2. Worker service has the same env vars as Web Service
3. `QUEUE_CONNECTION=redis` is set
4. Check Worker logs: Render → Worker Service → Logs

### "Too many redirects" loop

**Cause:** `APP_URL` uses `http://` but Render serves `https://`, or missing trusted proxy config.

**Fix:** Ensure `APP_URL=https://your-app.onrender.com` (with `https://`).

Also add this to `app/Http/Middleware/TrustProxies.php`:

```php
protected $proxies = '*';
```

---

## 15. Updating the Application

Every time you push to the `main` branch, Render automatically rebuilds and redeploys.

### Deployment workflow:

```bash
# Make your code changes locally
git add .
git commit -m "Describe your changes"
git push origin main
```

Render will:
1. Detect the push
2. Build a new Docker image
3. Run `start.sh` → run new migrations automatically
4. Switch traffic to the new container with zero downtime

### Running migrations manually:

If you need to run migrations outside of a deploy:

```bash
# In Render Shell (Web Service → Shell tab):
php artisan migrate --force
```

### Rolling back a deployment:

In Render Dashboard → Web Service → **Deploys** tab → click any previous deploy → **Rollback to this deploy**.

> ⚠️ Rolling back code does NOT roll back database migrations. If migrations were destructive, restore from a database backup.

---

## 16. Complete Environment Variable Reference

Copy this full list into Render. Replace all `<placeholder>` values.

```env
# Application
APP_NAME=Laravel School Timetable
APP_ENV=production
APP_DEBUG=false
APP_KEY=<base64:generated-key-from-step-3>
APP_URL=https://<your-service-name>.onrender.com

# Logging (required for Docker)
LOG_CHANNEL=stderr

# Database (PostgreSQL)
DB_CONNECTION=pgsql
DB_HOST=<render-db-internal-hostname>
DB_PORT=5432
DB_DATABASE=<database-name>
DB_USERNAME=<database-user>
DB_PASSWORD=<database-password>

# Redis (Cache / Session / Queue)
CACHE_DRIVER=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=180
QUEUE_CONNECTION=redis
REDIS_CLIENT=phpredis
REDIS_HOST=<render-redis-internal-hostname>
REDIS_PORT=6379
REDIS_PASSWORD=<render-redis-password>

# File Storage (AWS S3)
FILESYSTEM_DRIVER=s3
FILESYSTEM_CLOUD=s3
AWS_ACCESS_KEY_ID=<aws-access-key>
AWS_SECRET_ACCESS_KEY=<aws-secret-key>
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=<your-s3-bucket-name>
AWS_USE_PATH_STYLE_ENDPOINT=false

# Mail (optional)
MAIL_MAILER=smtp
MAIL_HOST=<smtp-host>
MAIL_PORT=587
MAIL_USERNAME=<smtp-username>
MAIL_PASSWORD=<smtp-password>
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Laravel School Timetable"

# Broadcasts (not used in prod - set to log)
BROADCAST_DRIVER=log
```

---

## Architecture Diagram

```
Internet
    │
    ▼
Render Load Balancer (HTTPS :443)
    │
    ▼
Web Service (Docker Container)
├── Nginx (port 8000)          ← handles HTTP traffic
└── PHP-FPM (127.0.0.1:9000)  ← executes Laravel

Background Worker (Docker Container)
└── php artisan queue:work     ← processes queued jobs

Cron Job (Docker Container)
└── php artisan schedule:run   ← runs every minute

External Services:
├── Render PostgreSQL          ← persistent database
├── Render Redis               ← cache / sessions / queues
└── AWS S3                     ← persistent file storage
```

---

## Support Resources

- **Render Docs:** https://render.com/docs
- **Laravel 10 Docs:** https://laravel.com/docs/10.x
- **Laravel Queue Docs:** https://laravel.com/docs/10.x/queues
- **Laravel Scheduler Docs:** https://laravel.com/docs/10.x/scheduling
- **Render Community Discord:** https://discord.gg/render
- **AWS S3 Docs:** https://docs.aws.amazon.com/s3/

---

*Last updated: 2026-06-25*
