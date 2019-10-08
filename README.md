1. Install mysql, apache, php, composer, nodejs (v11 or higher), npm

2. Synchronize server time with internet time [like this](https://linuxconfig.org/how-to-sync-time-on-ubuntu-18-04-bionic-beaver-linux)

```
sudo timedatectl set-ntp off
sudo timedatectl set-ntp on
timedatectl
```

3. Clone repository and set directory permissions

```
cd /var/www/html
sudo git clone --branch voyger https://github.com/bkartel1/laracrm
sudo chown -R webadmin:www-data /var/www/html
```
Then set permissions of directory content (also do this if Laravel gives permission error later)
```
sudo find /var/www/html -type f -exec chmod 644 {} \;
sudo find /var/www/html -type d -exec chmod 755 {} \;
cd /var/www/html/laracrm
sudo chgrp -R www-data storage bootstrap/cache
sudo chmod -R ug+rwx storage bootstrap/cache
```

4. Create database and update environment file .env with all correct parameters

5. Inside project directory, install dependencies
```
composer install
php artisan key:generate
php artisan voyager:install
php artisan migrate:fresh
php artisan db:seed --class=VoyagerDatabaseSeeder
php artisan db:seed --class=VoyagerDummyDatabaseSeeder
npm install
npm audit fix --force

```

If you have problem with composer install and php

```
sudo apt-get update
sudo apt-get upgrade
sudo add-apt-repository ppa:ondrej/php
sudo apt-get install mcrypt phpunit php7.1-mcrypt php7.1-mbstring php7.1-gd php7.1-xml php7.1-curl php7.1-mysql libapache2-mod-php7.1
```

## Apache configuration
- Setting /etc/apache2/sites-available/bitsindex.com.conf
```
<VirtualHost *:80>
    DocumentRoot /var/www/html/laracrm/public
    ServerName bitsindex.com
    Redirect "/" "https://bitsindex.com/"
    ServerAlias www.bitsindex.com
    <Directory /var/www/html/laracrm/public>
          Options -Indexes +FollowSymLinks
          AllowOverride All
          Require all granted
          DirectoryIndex index.html index.htm index.php
    </Directory>
</VirtualHost>
```
- Remove /index.php from url
```
sudo a2enmod rewrite
```

## Note for developer
There's utility `/refresh.bat` (Windows) `/refresh.sh` (Linux)  that refreshes DB and Laravel caches.

Use it with caution when Laravel doesn't reflect changes!
