Convert `env()` to `config()`

Laravel [recommends][1] only using `env()` within configuration files and using `config()` everywhere else. Doing so allows you to improve performance by running `php artisan config:cache`.

[1]: https://laravel.com/docs/5.7/configuration#configuration-caching