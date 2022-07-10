Migrate command registry

Laravel 5 moved registering commands from `start/artisan.php` to the
`$command` array of `app/Console/Kernel.php`.
