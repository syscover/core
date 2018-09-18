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

Register middleware group sessions on file app/Http/Kernel.php add to middlewareGroups array**
```
...
'sessions' => [
    \App\Http\Middleware\EncryptCookies::class,
    \Illuminate\Session\Middleware\StartSession::class,
],
...

```

**X - And execute migrations**
```
php artisan migrate
```

**X - Execute command to create encryption keys fot laravel passport**
```
php artisan passport:install
```

**X - Add Passport::routes method within the boot method of your AuthServiceProvider**

This method will register the routes necessary to issue access tokens and revoke access tokens, clients, and personal access tokens
```
<?php namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];
    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        
        Passport::routes();  // add laravel passport routes
    }
}
```


**2 - Don't forget to register CORS in your server, the following example is for apache server**
```
Header add Access-Control-Allow-Origin "*"
Header add Access-Control-Allow-Headers "authorization, origin, x-requested-with, content-type"
Header add Access-Control-Expose-Headers "authorization"
Header add Access-Control-Allow-Methods "PUT, GET, POST, DELETE, OPTIONS"
```

**3 - You may need to extend both the PHP memory on your server as well as the upload limit**
```
php_value post_max_size 1000M
php_value upload_max_filesize 1000M
php_value memory_limit 256M
```


**5 - Register GraphQl custom scalar types**

In file app\Providers\AppServiceProvider.php Include this imports
```
use Syscover\Core\GraphQL\ScalarTypes\ObjectType;
use Syscover\Core\GraphQL\ScalarTypes\AnyType;
```

inside register method, set this code to register custom scalar types
```
$this->app->singleton(ObjectType::class, function ($app) {
    return new ObjectType();
});

$this->app->singleton(AnyType::class, function ($app) {
    return new AnyType();
});
```

**6 - create link to storage folder**
```
php artisan storage:link
```

**7 - Execute publish command**
```
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
php artisan vendor:publish --provider="Folklore\GraphQL\ServiceProvider"
php artisan vendor:publish --provider="Syscover\Core\CoreServiceProvider"
```

**8 - Set GraphQl middleware**

In config/graphql.php replace 'middleware' => [] by
```
'middleware' => ['auth:api'],
```

and replace 'error_formatter' => [\Folklore\GraphQL\GraphQL::class, 'formatError'], by
```
'error_formatter' => [\Syscover\Core\GraphQL\Services\GraphQL::class, 'formatError'],
```

**9 - Add scss**
In file in resources/assets/sass/app.scss you can add utilities scss files
```
// Material
@import "../../../vendor/syscover/pulsar-core/src/assets/scss/material/elevations";

// Partials
@import "../../../vendor/syscover/pulsar-core/src/assets/scss/partials/forms";
@import "../../../vendor/syscover/pulsar-core/src/assets/scss/partials/typography";
@import "../../../vendor/syscover/pulsar-core/src/assets/scss/partials/helpers";
@import "../../../vendor/syscover/pulsar-core/src/assets/scss/partials/cookies-consent";
@import "../../../vendor/syscover/pulsar-core/src/assets/scss/partials/vue";
```

if you use Laravel Mix set this code
```
mix
    .styles([
        ...
        'vendor/syscover/pulsar-core/src/assets/vendor/bootstrap/css/bootstrap.min.css',
        ...
    ], 'public/css/all.css')
    .sass([
        ...
        'vendor/syscover/pulsar-core/src/assets/scss/app.scss',
        ...
    ], 'public/css/app.css')
    .scripts([
        ...
        'vendor/syscover/pulsar-core/src/assets/vendor/jquery/jquery-3.3.1.min.js',
        'vendor/syscover/pulsar-core/src/assets/vendor/polyfill/array.prototype.find.js',
        'vendor/syscover/pulsar-core/src/assets/vendor/polyfill/array.prototype.foreach.js',
        'vendor/syscover/pulsar-core/src/assets/vendor/bootstrap/js/bootstrap.min.js',
        'vendor/syscover/pulsar-core/src/assets/vendor/territories/js/jquery.territories.js',
        'vendor/syscover/pulsar-core/src/assets/vendor/check-postal-code/jquery.check-postal-code.js',
        'vendor/syscover/pulsar-core/src/assets/vendor/jquery-validation/jquery.validate.min.js',
        'vendor/syscover/pulsar-core/src/assets/vendor/jquery-validation/additional-methods.min.js',
        'vendor/syscover/pulsar-core/src/assets/vendor/fontawesome/svg-with-js/js/fontawesome-all.js',
        'vendor/syscover/pulsar-core/src/assets/vendor/js-cookie/src/js.cookie.js',
        'vendor/syscover/pulsar-core/src/assets/vendor/cookie-consent/cookie-consent.js'
        ...
    ], 'public/js/all.js')
```



