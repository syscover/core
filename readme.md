# Pulsar Core App for Laravel

[![Total Downloads](https://poser.pugx.org/syscover/pulsar-core/downloads)](https://packagist.org/packages/syscover/pulsar-core)
[![Latest Stable Version](http://img.shields.io/github/release/syscover/pulsar-core.svg)](https://packagist.org/packages/syscover/pulsar-core)

Pulsar is an application that generates a control panel where you start creating custom solutions, provides the resources necessary for any web application.

---

## Installation

**1 - After install Laravel framework, execute on console:**
```
composer require syscover/pulsar-core
```

Register service provider, on file config/app.php add to providers array**
```
/*
 * Pulsar Application Service Providers...
 */
Syscover\Core\CoreServiceProvider::class,
```

**2 - You must register JWTAuthServiceProvider above AppServiceProvider**
```
Tymon\JWTAuth\Providers\JWTAuthServiceProvider::class,
Folklore\GraphQL\ServiceProvider::class,
```

**3 - Don't forget to register CORS in your server, the following example is for apache server**
```
Header add Access-Control-Allow-Origin "*"
Header add Access-Control-Allow-Headers "authorization, origin, x-requested-with, content-type"
Header add Access-Control-Expose-Headers "authorization"
Header add Access-Control-Allow-Methods "PUT, GET, POST, DELETE, OPTIONS"
```

**4 - You may need to extend both the PHP memory on your server as well as the upload limit**
```
php_value post_max_size 1000M
php_value upload_max_filesize 1000M
php_value memory_limit 256M
```

**5 - Register JWT Alias in aliases array on config/app.php**
```
'JWTAuth' => Tymon\JWTAuth\Facades\JWTAuth::class,
'JWTFactory' => Tymon\JWTAuth\Facades\JWTFactory::class,
'GraphQL' => Folklore\GraphQL\Support\Facades\GraphQL::class,
```

**6 - Publish elements from JWT provider**
```
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\JWTAuthServiceProvider"
```

**7 - Generate new JWT key**
```
php artisan jwt:generate
```

**8 - Config middleware for JWT in app/Http/Kernel.php**
```
protected $routeMiddleware = [
    ...
    'jwt.auth' => \Tymon\JWTAuth\Middleware\GetUserFromToken::class,
    'jwt.refresh' => \Tymon\JWTAuth\Middleware\RefreshToken::class,
];
```

**9 - Register GraphQl custom scalar types**
<br>In file app\Profiders\AppServiceProvider.php
Include this imports
```
use Syscover\Core\GraphQL\ScalarTypes\ObjectType;
use Syscover\Core\GraphQL\ScalarTypes\AnyType;
```

inside register array, set this code to register custom scalar types
```
$this->app->singleton(ObjectType::class, function ($app) {
    return new ObjectType();
});

$this->app->singleton(AnyType::class, function ($app) {
    return new AnyType();
});
```

**10 - create link to storage folder**
```
php artisan storage:link
```

**11 - Execute publish command**
```
php artisan vendor:publish --provider="Folklore\GraphQL\ServiceProvider"
php artisan vendor:publish --provider="Syscover\Core\CoreServiceProvider"
```

**12 - Register GraphQl middleware**
<br>in app/Http/Kernel.php inside routeMiddleware array add this middleware
```
'pulsar.core.graphQL' => \Syscover\Core\Middleware\GraphQL::class,
```

and in config/graphql.php replace 'middleware' => [] by
```
'middleware' => ['pulsar.core.graphQL'],
```

**13 - Register user for JWT**
<br>in config/jwt.php set this value
```
'user' => 'Syscover\Admin\Models\User',
```

