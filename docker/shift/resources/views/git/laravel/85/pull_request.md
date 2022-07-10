This pull request includes changes from recent updates to Laravel core files. Although some of these changes may be considered optional, they are required when you use recently added features - such as the [new validation rules](https://laravel-news.com/laravel-prohibited-validation-rules), [configuration options](https://github.com/laravel-shift/laravel-8.x/compare/6f5bb4700e273ff55468ddcd127027ea4d706d6e..master#diff-950397cc5ef29707b54d0c774bd94af85836bab1a27f2f86533d832d25caf224), or [Laravel Sanctum](https://laravel.com/docs/8.x/sanctum).

Since Laravel has moved to annual release cycles, these changes sometimes reach a critical mass. When they do, you will receive an automated pull request as part of your _Shifty Plan_.

**Before merging**, you need to:

- Checkout the `{{branch}}` branch
- Review **all** pull request comments for additional changes
- Run `composer update` (if the scripts fail, add `--no-scripts`)
- Clear any config, route, or view cache
- Thoroughly test your application ([no tests?](https://laravelshift.com/laravel-test-generator), [no CI?](https://laravelshift.com/ci-generator))

If you would like more detail you may [view the full changes](https://github.com/laravel/laravel/compare/v8.0.0...v8.5.15). Be sure you're opted in to the [Shift Newsletter](https://laravelshift.com/account/preferences) to receive weekly updates for Laravel.
