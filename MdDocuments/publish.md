# Mekodonia Home Connect - Deployment Guide

## Pre-Deployment Checklist

1. **Code Review**
   - [ ] All tests are passing
   - [ ] Environment variables are properly configured
   - [ ] Database migrations are up to date
   - [ ] Frontend assets are built for production

2. **Infrastructure Requirements**
   - Web server (Nginx/Apache)
   - PHP 8.2+
   - PostgreSQL 13+
   - Node.js 20+
   - Redis (for caching/queues, recommended)

## Deployment Options

### Option 1: Railway (Recommended)

1. **Create Railway Account**
   - Sign up at [Railway.app](https://railway.app/)
   - Install Railway CLI: `npm i -g @railway/cli`

2. **Deploy Backend**
   ```bash
   # Login to Railway
   railway login
   
   # Create new project
   railway init
   
   # Link to existing project (if created via web)
   railway link
   
   # Add PostgreSQL database
   railway add
   
   # Set environment variables
   railway env push .env.production
   
   # Deploy
   git push railway main
   ```

3. **Deploy Frontend**
   - Use Vercel or Netlify for frontend deployment
   - Set environment variables:
     ```
     VITE_API_URL=your-railway-app-url.railway.app
     VITE_APP_ENV=production
     ```

### Option 2: DigitalOcean App Platform

1. **Create DigitalOcean Account**
   - Sign up at [DigitalOcean](https://cloud.digitalocean.com/)
   - Go to App Platform

2. **Deploy from GitHub**
   - Connect your GitHub repository
   - Configure as a Web Service
   - Set build command: `npm install && npm run build`
   - Set run command: `php artisan serve --port=$PORT`
   - Add environment variables from your `.env`
   - Add PostgreSQL database
   - Deploy

### Option 3: Traditional VPS (Ubuntu 22.04)

1. **Server Setup**
   ```bash
   # Update packages
   sudo apt update && sudo apt upgrade -y
   
   # Install required software
   sudo apt install -y nginx postgresql postgresql-contrib php8.2-fpm php8.2-pgsql \
       php8.2-curl php8.2-mbstring php8.2-xml php8.2-zip nodejs npm
   
   # Install Composer
   curl -sS https://getcomposer.org/installer | php
   sudo mv composer.phar /usr/local/bin/composer
   ```

2. **Database Setup**
   ```bash
   sudo -u postgres createuser --interactive
   sudo -u postgres createdb mekodonia_prod
   ```

3. **Deploy Application**
   ```bash
   # Clone repository
   git clone https://github.com/yourusername/mekodonia-connect.git /var/www/mekodonia
   cd /var/www/mekodonia
   
   # Install dependencies
   composer install --optimize-autoloader --no-dev
   npm install && npm run build
   
   # Set permissions
   sudo chown -R www-data:www-data /var/www/mekodonia
   sudo chmod -R 755 /var/www/mekodonia/storage
   
   # Configure environment
   cp .env.example .env
   nano .env  # Edit with production values
   php artisan key:generate
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   
   # Run migrations
   php artisan migrate --force
   ```

4. **Configure Nginx**
   ```nginx
   server {
       listen 80;
       server_name yourdomain.com;
       root /var/www/mekodonia/public;

       add_header X-Frame-Options "SAMEORIGIN";
       add_header X-Content-Type-Options "nosniff";

       index index.php;

       charset utf-8;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location = /favicon.ico { access_log off; log_not_found off; }
       location = /robots.txt  { access_log off; log_not_found off; }

       error_page 404 /index.php;

       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
           include fastcgi_params;
       }

       location ~ /\.(?!well-known).* {
           deny all;
       }
   }
   ```

## Post-Deployment Tasks

1. **Configure Queue Worker**
   ```bash
   # For systemd service
   sudo nano /etc/systemd/system/mekodonia-worker.service
   
   # [Unit]
   # Description=Mekodonia Queue Worker
   # After=network.target
   
   # [Service]
   # User=www-data
   # Group=www-data
   # Restart=on-failure
   # ExecStart=/usr/bin/php /var/www/mekodonia/artisan queue:work --sleep=3 --tries=3 --max-time=3600
   # 
   # [Install]
   # WantedBy=multi-user.target
   
   sudo systemctl enable mekondia-worker
   sudo systemctl start mekondia-worker
   ```

2. **Schedule Tasks**
   ```bash
   # Add to crontab
   crontab -e
   
   # Run scheduler every minute
   * * * * * cd /var/www/mekodonia && php artisan schedule:run >> /dev/null 2>&1
   ```

3. **Monitoring**
   - Set up monitoring (e.g., Laravel Horizon, Laravel Telescope)
   - Configure log rotation
   - Set up error tracking (e.g., Sentry, Bugsnag)

## Environment Variables

Required environment variables for production:

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
APP_KEY=base64:...

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=mekodonia_prod
DB_USERNAME=mekodonia
DB_PASSWORD=secure_password

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@yourdomain.com
MAIL_FROM_NAME="Mekodonia Home Connect"

# For production, use a real payment processor
PAYMENT_PROCESSOR=telebirr
TELEBIRR_API_KEY=your_telebirr_api_key
```

## Backups

1. **Database Backups**
   ```bash
   # Daily backup script
   pg_dump -U mekondia mekondia_prod > /backups/mekodonia-db-$(date +%Y%m%d).sql
   ```

2. **File Backups**
   ```bash
   # Backup storage directory
   tar -czf /backups/mekodonia-files-$(date +%Y%m%d).tar.gz /var/www/mekodonia/storage/app/public
   ```

## Maintenance Mode

```bash
# Enable maintenance mode
php artisan down --secret="1630540a-246b-4b66-afa1-dd72a4c43515"

# Disable maintenance mode
php artisan up
```

## Troubleshooting

Common issues and solutions:

1. **500 Server Error**
   - Check Laravel logs: `tail -f storage/logs/laravel.log`
   - Verify file permissions
   - Check if .env is properly configured

2. **Database Connection Issues**
   - Verify database credentials in .env
   - Check if PostgreSQL is running: `sudo systemctl status postgresql`
   - Check connection: `psql -U mekondia -d mekondia_prod`

3. **Asset Loading Issues**
   - Run `npm run build`
   - Check Nginx/Apache configuration
  
## Security Considerations

1. **File Permissions**
   ```bash
   # Only storage and bootstrap/cache need write permissions
   sudo chown -R www-data:www-data /var/www/mekodonia/storage
   sudo chown -R www-data:www-data /var/www/mekodonia/bootstrap/cache
   ```

2. **HTTPS**
   - Use Let's Encrypt for free SSL certificates
   - Configure HTTP/2 for better performance

3. **Rate Limiting**
   - Configure rate limiting in `app/Http/Kernel.php`
   - Consider Cloudflare for DDoS protection

## Scaling

1. **Vertical Scaling**
   - Upgrade server resources (CPU, RAM)
   - Enable OPcache for PHP

2. **Horizontal Scaling**
   - Set up load balancing
   - Use Redis for session and cache
   - Consider database read replicas

## Monitoring and Maintenance

1. **Log Management**
   - Set up log rotation
   - Consider centralized logging (e.g., Papertrail, Loggly)

2. **Performance Monitoring**
   - New Relic
   - Blackfire.io
   - Laravel Telescope (for development/staging)

3. **Regular Updates**
   - Keep PHP, Node.js, and dependencies updated
   - Regularly check for security updates

## Support

For additional support, contact:
- Email: support@mekodonia.org
- Phone: +251-XXX-XXXXXX
