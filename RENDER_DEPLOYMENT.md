# RENDER DEPLOYMENT GUIDE

This guide explains how to deploy this Laravel application to Render using Docker.

## Prerequisites

- Render account (https://render.com)
- Git repository with this code
- PostgreSQL or MySQL database on Render
- Redis for cache/session/queue
- AWS S3 bucket (optional but recommended for file uploads)

## Environment Variables Required on Render

The following environment variables must be set in Render dashboard for the Web Service:

### Application Configuration
- `APP_NAME`: Laravel
- `APP_ENV`: production
- `APP_DEBUG`: false
- `APP_KEY`: Generate with `php artisan key:generate --show` locally and paste here
- `APP_URL`: Your Render app URL (https://your-app.onrender.com)

### Database Configuration
- `DB_CONNECTION`: pgsql or mysql
- `DB_HOST`: Your database host
- `DB_PORT`: 5432 (PostgreSQL) or 3306 (MySQL)
- `DB_DATABASE`: Your database name
- `DB_USERNAME`: Your database username
- `DB_PASSWORD`: Your database password

### Redis Configuration (for cache, session, queue)
- `REDIS_HOST`: Your Redis instance host
- `REDIS_PORT`: 6379
- `REDIS_PASSWORD`: Your Redis password (if required)

### AWS S3 Configuration (for file uploads)
- `AWS_ACCESS_KEY_ID`: Your AWS access key
- `AWS_SECRET_ACCESS_KEY`: Your AWS secret key
- `AWS_DEFAULT_REGION`: us-east-1
- `AWS_BUCKET`: Your S3 bucket name
- `FILESYSTEM_DRIVER`: s3

### Mail Configuration (Optional)
- `MAIL_DRIVER`: smtp
- `MAIL_HOST`: Your SMTP host
- `MAIL_PORT`: 587
- `MAIL_USERNAME`: Your SMTP username
- `MAIL_PASSWORD`: Your SMTP password
- `MAIL_ENCRYPTION`: tls
- `MAIL_FROM_ADDRESS`: noreply@yourdomain.com

## Deployment Steps

1. **Connect Repository**
   - Go to Render dashboard and create a new Web Service
   - Connect your Git repository
   - Select the branch (main, develop, etc.)

2. **Configure Service**
   - Choose Environment: Docker
   - Region: Select appropriate region
   - Plan: Starter or higher

3. **Set Environment Variables**
   - Add all the environment variables listed above
   - Make sure `APP_KEY` is set correctly

4. **Deploy**
   - Click "Create Web Service"
   - Render will build and deploy automatically

5. **Create Additional Services**

   **Worker Service** (for queue jobs):
   - Create a new Worker service connected to the same repo
   - Same environment variables as Web Service
   - Start Command: `php /app/artisan queue:work --sleep=3 --tries=3 --timeout=90`

   **Cron Job** (for scheduler):
   - Create a new Cron Job
   - Schedule: `* * * * *` (every minute)
   - Command: `sh -c 'php /app/artisan schedule:run'`

## Troubleshooting

### App won't start
- Check logs in Render Dashboard → Logs
- Verify all required environment variables are set
- Ensure `APP_KEY` is not empty

### Migrations not running
- The `start.sh` script automatically runs migrations on startup
- Check database connection parameters

### Files are disappearing after deploy
- Make sure `FILESYSTEM_DRIVER=s3` is set
- Verify AWS credentials and bucket name

### Queue jobs not processing
- Ensure Worker service is running
- Check Worker service logs
- Verify `QUEUE_CONNECTION=redis` is set correctly

### Cache/Session issues
- Verify Redis connection parameters
- Check Redis host is accessible from your app

## Local Development with Docker

To test locally before deploying:

```bash
docker-compose up -d
docker-compose exec app php artisan migrate
docker-compose exec app php artisan seed
```

Access at http://localhost:8000

To stop:
```bash
docker-compose down
```

## File Structure

- `Dockerfile` - Multi-stage build for optimized image
- `docker/nginx.conf` - Nginx configuration
- `docker/default.conf` - Nginx site configuration
- `docker/php-fpm.conf` - PHP-FPM configuration
- `docker/supervisor.conf` - Supervisor configuration for processes
- `docker/start.sh` - Entry script for startup tasks
- `render.yaml` - Infrastructure as code for Render
- `docker-compose.yml` - Local development setup

## Support

For issues or questions about deployment, check:
- Render documentation: https://render.com/docs
- Laravel documentation: https://laravel.com/docs
- Docker documentation: https://docs.docker.com
