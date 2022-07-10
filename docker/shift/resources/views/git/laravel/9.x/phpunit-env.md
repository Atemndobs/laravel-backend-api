Use `<env>` tags for configuration

`<env>` tags have a lower precedence than system environment variables making it easier to overwrite PHPUnit configuration values in additional environments, such a CI.

Review this blog post for more details on configuration precedence when testing Laravel: https://jasonmccreary.me/articles/laravel-testing-configuration-precedence/
