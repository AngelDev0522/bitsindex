php artisan migrate:fresh
php artisan db:seed --class=VoyagerDatabaseSeeder
php artisan db:seed --class=VoyagerDummyDatabaseSeeder
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
