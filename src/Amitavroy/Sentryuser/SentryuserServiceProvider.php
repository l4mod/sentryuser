<?php namespace Amitavroy\Sentryuser;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Validator;

class SentryuserServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('amitavroy/sentryuser');
        include __DIR__ . '/../../routes.php';

        // registering my custom validator
        $this->app['validator']->resolver(function($translator, $data, $rules, $messages)
        {
            return new CustomValidation($translator, $data, $rules, $messages);
        });
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->app['rebase'] = $this->app->share(function($app)
            {
                return new ReBaseApp;
            });

        $this->commands('rebase');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
