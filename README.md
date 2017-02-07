# laravel-etag-middleware
A Laravel middleware for adding ETags to HTTP requests to improve response times

[![Build Status](https://travis-ci.org/matthewbdaly/laravel-etag-middleware.svg?branch=master)](https://travis-ci.org/matthewbdaly/laravel-etag-middleware)

Installation
------------

Run the following command to install the package:

```bash
composer require matthewbdaly/laravel-etag-middleware
```

Then just include this in your `app/Http/Kernel.php` in the appropriate place where you want to import the middleware:

```php
\Matthewbdaly\ETagMiddleware\ETag::class
```
