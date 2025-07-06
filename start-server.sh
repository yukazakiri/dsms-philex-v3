#!/bin/bash

# Change to the project directory
cd /home/dccp/projects/dsms-philex-v3

# Install/update dependencies if needed
composer install --no-dev --optimize-autoloader
# npm ci --production
bun install --production
bun run build
# Clear and cache Laravel configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan icon:cache
# npm run build
# Start the FrankenPHP server
php artisan octane:start --server=frankenphp --host=127.0.0.1 --port=28578 --admin-port=28575 &
#php artisan queue:work redis --queue=assessments,pdf-generation --sleep=3 --tries=3 &
# php artisan nightwatch:agent &
# php artisan horizon &
wait

# php artisan serve --host=127.0.0.1 --port=28561
