This pull request contains changes for migrating your test suite from PHPUnit to [Pest](https://pestphp.com/) automated by the [Pest Converter](https://laravelshift.com/phpunit-to-pest-converter).

**Before merging**, you need to:

- Checkout the `{{branch}}` branch
- Review **all** of the comments below for additional changes
- Run `composer update` to install Pest with your dependencies
- Run `vendor/bin/pest` to verify the conversion
