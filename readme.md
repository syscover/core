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
 * DH2 Application Service Providers...
 */
Syscover\Core\CoreServiceProvider::class,
```

**2 - Execute publish command**
```
php artisan vendor:publish --provider="Syscover\Admin\AdminServiceProvider"
```

**3 - And execute migrations**
```
php artisan migrate
```

**4 - Execute command to create encryption keys fot laravel passport**
```
php artisan passport:install
```

**5 - Add Passport::routes method within the boot method of your AuthServiceProvider**

This method will register the routes necessary to issue access tokens and revoke access tokens, clients, and personal access tokens
```
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
```

**6 - Don't forget to register CORS in your server, the following example is for apache server**
```
Header add Access-Control-Allow-Origin "*"
Header add Access-Control-Allow-Headers "authorization, origin, x-requested-with, content-type"
Header add Access-Control-Expose-Headers "authorization"
Header add Access-Control-Allow-Methods "PUT, GET, POST, DELETE, OPTIONS"
```

**7 - You may need to extend both the PHP memory on your server as well as the upload limit**
```
php_value post_max_size 1000M
php_value upload_max_filesize 1000M
php_value memory_limit 256M
```

**8 - create link to storage folder**
```
php artisan storage:link
```

**9 - Execute publish command**
```
php artisan vendor:publish --provider="Nuwave\Lighthouse\Providers\LighthouseServiceProvider"
php artisan vendor:publish --provider="Syscover\Core\CoreServiceProvider"
```

**10 - Set GraphQl middleware**

In config/lighthouse.php add to route => middleware array
```
'middleware' => ['api', 'client'],
```

**11 - Consumption of the API from localhost**
To consume API resources from your own domain you can use the following route.
```
https://yourdomian.com/graphql/localhost
```
You will need to send CSRF token in your requests to verify that you make the requests from laravel.


**12 - Add scss**
In file in resources/assets/sass/app.scss you can add utilities scss files
```
// Material
@import "../../../vendor/syscover/pulsar-core/src/resources/sass/material/elevations";

// Partials
@import "../../../vendor/syscover/pulsar-core/src/resources/sass/partials/forms";
@import "../../../vendor/syscover/pulsar-core/src/resources/sass/partials/typography";
@import "../../../vendor/syscover/pulsar-core/src/resources/sass/partials/helpers";
@import "../../../vendor/syscover/pulsar-core/src/resources/sass/partials/cookies-consent";
@import "../../../vendor/syscover/pulsar-core/src/resources/sass/partials/vue";
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
        'vendor/syscover/pulsar-core/src/resources/vendor/polyfill/array.prototype.find.js',
        'vendor/syscover/pulsar-core/src/resources/vendor/polyfill/array.prototype.foreach.js',
        'vendor/syscover/pulsar-core/src/resources/vendor/territories/js/jquery.territories.js',
        'vendor/syscover/pulsar-core/src/resources/vendor/check-postal-code/jquery.check-postal-code.js',
        'vendor/syscover/pulsar-core/src/resources/vendor/jquery-validation/jquery.validate.min.js',
        'vendor/syscover/pulsar-core/src/resources/vendor/jquery-validation/additional-methods.min.js',
        'vendor/syscover/pulsar-core/src/resources/vendor/js-cookie/src/js.cookie.js',
        'vendor/syscover/pulsar-core/src/resources/vendor/cookie-consent/cookie-consent.js'
        ...
    ], 'public/js/all.js')
```



