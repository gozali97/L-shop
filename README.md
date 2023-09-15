# Asima v1

## Prerequisites 
1. Composer
2. PHP >8.1

## Installation :
1. Composer install
```
composer install
```
2. Copy and rename from `.env.example` to `.env` file
3. Filling the required data on the `.env`
3. Generate the `app_key`
```
php artisan key:generate
```
4. Run the migration
```
php artisan migrate
```
or use the fresh migration and seed
```
php artisan migrate:fresh --seed
```
5.  Serve the application
```
php artisan serve
```
## Additional info
1. Path url for the admin login
```
/admin/login
```
2. Migrate for the loggging, make sure that you're filled separate db
```
php artisan migrate --database=logging_db --path=database/migrations/db2/2023_09_08_115313_create_logs_table.php
```
3. Migrate for the loggging schedule logs, make sure that you're filled separate db
```
php artisan migrate --database=logging_db --path=database/migrations/db2/2023_09_13_120103_create_schedule_logs_table.php
```
#### Demo Account
1. Administration 

Admin Email
```
admin@gmail.com
```
Password
```
1111
```
2. Users

User Email
```
user@gmail.com
```
Password
```
1111
```



