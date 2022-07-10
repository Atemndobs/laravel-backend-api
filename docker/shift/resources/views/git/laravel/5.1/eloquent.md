Shift Eloquent

Laravel 5.1 introduces a few changes to Eloquent:

- `create` can now be called without parameters. The default for the
  `$attributes` parameter is an empty array.
- `parent::find()` should not be called directly. Instead, call `find()`
  on the Eloquent query builder: `static::query()->find()`
