Shift controller inheritance

Laravel 5 replaced `app/controllers/BaseController` with
`app/Http/Controllers/Controller`. Any custom code within the 
`BaseController` class has been moved to the new `Controller` class and
all controllers have been updated to extend `Controller`.
