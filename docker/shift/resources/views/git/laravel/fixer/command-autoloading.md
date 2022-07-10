Leverage command autoloading

Laravel [automatically loads Commands][1] within the `app/Console/Command` folder. As such, there is no need to register them within the _Console Kernel_.

[1]: https://laravel.com/docs/5.7/artisan#registering-commands