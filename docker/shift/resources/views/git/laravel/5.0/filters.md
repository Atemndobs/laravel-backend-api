Add global middleware

Laravel 5 replaces *Filters* with *Middleware* and enables CSRF
protection by default. This commit updates the code to use the new
`auth` and `csrf` middleware, but disables global CSRF protection.
