Remove unnecessary date casts

Laravel automatically casts the `created_at` and `updated_at` properties to dates. As of Laravel 5.8, the `deleted_at` property is automatically cast to a date as well.