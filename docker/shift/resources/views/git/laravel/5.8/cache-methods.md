Convert Cache TTL from minutes to seconds

To conform with PSR-16 Laravel 5.8 changed the cache expiration time from minutes to seconds. If you were passing an integer, Shift converted this to its integer equivalent in seconds. If you were passing a "minutes" variable, Shift converted this to the relative date time equivalent for readability.
