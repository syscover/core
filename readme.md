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

**2 - Register service provider, on file config/app.php add to providers array**
```
/*
 * Pulsar Application Service Providers...
 */
Syscover\Core\CoreServiceProvider::class,
```

**3 - You must register JWTAuthServiceProvider above AppServiceProvider**
```
Tymon\JWTAuth\Providers\JWTAuthServiceProvider::class,
```

**4 - Register services provider third party**
```
/*
 * Third party
 */
Barryvdh\Cors\ServiceProvider::class,
```

**5 - Register JWT Alias in aliases array**
```
'JWTAuth' => Tymon\JWTAuth\Facades\JWTAuth::class,
'JWTFactory' => Tymon\JWTAuth\Facades\JWTFactory::class
```

**6 - Publish elements from JWT provider and CORS**
```
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\JWTAuthServiceProvider"
php artisan vendor:publish --provider="Barryvdh\Cors\ServiceProvider"
```

**7 - Generate new JWT key**
```
php artisan jwt:generate
```

**8 - Config middleware for JWT and CORS in app/Http/Kernel.php**
```
protected $middleware = [
    ...
    \Barryvdh\Cors\HandleCors::class,
];

protected $routeMiddleware = [
    ...
    'jwt.auth' => \Tymon\JWTAuth\Middleware\GetUserFromToken::class,
    'jwt.refresh' => \Tymon\JWTAuth\Middleware\RefreshToken::class,
];
```

**9 - Config env file with follows properties**
```
API_DEBUG=true
API_VERSION=v1
API_NAME=PULSAR
API_PREFIX=api
```

**10 - Config config/cors.php with this values**
```
'supportsCredentials' => false,
'allowedOrigins' => ['*'],
'allowedHeaders' => ['*'],
'allowedMethods' => ['*'],
'exposedHeaders' => ['Authorization'],
'maxAge' => 0,
```

**11 - Set base lang application in .env file**
```
BASE_LANG=en
```

**12 - Execute publish command**
```
php artisan vendor:publish
```