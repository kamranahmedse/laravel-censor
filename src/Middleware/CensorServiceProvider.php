<?php namespace KamranAhmed\LaravelCensor\Middleware;

use App;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Support\ServiceProvider;

class CensorServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;
    protected $package = 'kamranahmedse/laravel-censor';
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/censor.php' => config_path('censor.php')
        ], 'config');

        /** @var Kernel $kernel */
        $kernel = $this->app['Illuminate\Contracts\Http\Kernel'];
        $kernel->pushMiddleware(Censor::class);
    }
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/censor.php', 'censor');
    }
}