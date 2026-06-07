# Docker Setup Guide

This project is now ready for Docker deployment. Below are the key files and their purposes.

## New Files Created

### Docker Configuration
- **Dockerfile** - Multi-stage build for production-optimized image
- **.dockerignore** - Excludes unnecessary files from Docker build
- **docker-compose.yml** - Local development environment with all services

### Docker Service Configuration
- **docker/nginx.conf** - Nginx web server configuration
- **docker/default.conf** - Nginx site configuration for Laravel
- **docker/php-fpm.conf** - PHP FastCGI Process Manager configuration
- **docker/supervisor.conf** - Process manager configuration (PHP-FPM, Nginx, Queue)
- **docker/start.sh** - Startup script that handles migrations and caching

### Deployment Configuration
- **render.yaml** - Infrastructure-as-code for Render deployment
- **RENDER_DEPLOYMENT.md** - Complete deployment guide
- **.env.example** - Updated with production-ready environment variables

## Local Development

### Using Docker Compose

Start all services (app, MySQL, Redis):
```bash
docker-compose up -d
```

Run migrations:
```bash
docker-compose exec app php artisan migrate
```

Access the application at http://localhost:8000

View logs:
```bash
docker-compose logs -f app
```

Stop services:
```bash
docker-compose down
```

### Without Docker (Traditional)

If you prefer local PHP:
```bash
composer install
npm install && npm run production
php artisan key:generate
php artisan migrate
php artisan serve
```

## Deployment to Render

### Quick Start

1. Push your code to GitHub (with the new Docker files)
2. Go to render.com and create account
3. Click "New +" → "Web Service" → Connect your GitHub repo
4. Set environment to "Docker"
5. Add environment variables from `.env.example`
6. Click "Create Web Service"

### Environment Variables to Set

Required:
- `APP_KEY` - Generate: `php artisan key:generate --show`
- `APP_URL` - Your Render app URL
- `DB_*` - Database credentials
- `REDIS_*` - Redis credentials
- `AWS_*` - AWS S3 credentials (optional but recommended)

See `RENDER_DEPLOYMENT.md` for complete list.

### Additional Services on Render

After creating the web service, add:

1. **Worker Service** (for background jobs)
   - Same repo, Docker environment
   - Same environment variables
   - Start command: `php /app/artisan queue:work --sleep=3 --tries=3 --timeout=90`

2. **Cron Job** (for scheduler)
   - Schedule: `* * * * *`
   - Command: `sh -c 'php /app/artisan schedule:run'`

## Verification Checklist

Before deploying:

- [ ] Dockerfile builds successfully: `docker build -t laravel-timetable .`
- [ ] Docker Compose works: `docker-compose up -d`
- [ ] Migrations run: `docker-compose exec app php artisan migrate`
- [ ] All environment variables documented in `.env.example`
- [ ] APP_KEY is not empty
- [ ] Database connection works
- [ ] Redis (if used) is accessible
- [ ] S3 bucket (if used) is configured

## Troubleshooting

### Build Fails
```bash
# Check Dockerfile syntax
docker build --no-cache -t laravel-timetable .

# View detailed errors
docker build -t laravel-timetable . 2>&1 | tail -50
```

### Container Won't Start
```bash
# View container logs
docker logs <container-id>

# Try interactive shell
docker run -it laravel-timetable /bin/sh
```

### Database Connection Issues
```bash
# Check if MySQL/PostgreSQL is running and accessible
docker-compose exec db mysql -u root -p -e "SHOW DATABASES;"
```

### Permission Issues
```bash
# Fix storage permissions
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

## File Structure After Setup

```
Laravel-School-Timetable-Calendar/
├── docker/
│   ├── nginx.conf
│   ├── default.conf
│   ├── php-fpm.conf
│   ├── supervisor.conf
│   └── start.sh
├── Dockerfile
├── .dockerignore
├── docker-compose.yml
├── render.yaml
├── RENDER_DEPLOYMENT.md
├── DOCKER_SETUP.md (this file)
└── ... (other Laravel files)
```

## Next Steps

1. Test locally with Docker Compose
2. Review RENDER_DEPLOYMENT.md for detailed Render setup
3. Create Render account and configure services
4. Set all required environment variables
5. Deploy and monitor logs

For more information:
- Docker: https://docs.docker.com
- Laravel: https://laravel.com/docs
- Render: https://render.com/docs
