<?php namespace Syscover\Core;

use Illuminate\Support\ServiceProvider;
use Syscover\Core\GraphQL\CoreGraphQLServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
        // register views
        $this->loadViewsFrom(__DIR__ . '/../../views', 'core');

        // register config files
        $this->publishes([
            __DIR__ . '/../../config/pulsar-core.php' => config_path('pulsar-core.php'),
        ]);

        // register GraphQL types and schema
        CoreGraphQLServiceProvider::bootGraphQLTypes();
        CoreGraphQLServiceProvider::bootGraphQLSchema();
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
        //
	}
}