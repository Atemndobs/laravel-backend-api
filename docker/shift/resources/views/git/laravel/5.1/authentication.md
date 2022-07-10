Shift authentication

Laravel 5.1 changes validation and creation of new users in the
Authentication components.

First, `AuthController` no longer depends on the `Guard` or `Registrar`
passed to the constructor.

Second, the `Registrar` service has been removed. Its methods
(`create` and `validator`) now live in the `AuthController`.