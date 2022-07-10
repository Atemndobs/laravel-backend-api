Leverage injected request object

Laravel automatically injects the current Http [request object][1] to all Controller actions and Middleware. Leveraging this object improves consistency and testability.

[1]: https://laravel.com/docs/requests#accessing-the-request