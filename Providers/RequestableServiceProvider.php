<?php

namespace Modules\Requestable\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Core\Traits\CanPublishConfiguration;
use Modules\Core\Events\BuildingSidebar;
use Modules\Core\Events\LoadingBackendTranslations;
use Illuminate\Support\Arr;
use Modules\Requestable\Events\Handlers\RegisterRequestableSidebar;

class RequestableServiceProvider extends ServiceProvider
{
    use CanPublishConfiguration;
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBindings();
        $this->app['events']->listen(BuildingSidebar::class, RegisterRequestableSidebar::class);

        $this->app['events']->listen(LoadingBackendTranslations::class, function (LoadingBackendTranslations $event) {
            $event->load('requestables', Arr::dot(trans('requestable::requestables')));
            // append translations


        });
    }

    public function boot()
    {
        $this->publishConfig('requestable', 'permissions');
        $this->publishConfig('requestable', 'config');
        //$this->publishConfig('requestable', 'requests');

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
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

    private function registerBindings()
    {
        $this->app->bind(
            'Modules\Requestable\Repositories\RequestableRepository',
            function () {
                $repository = new \Modules\Requestable\Repositories\Eloquent\EloquentRequestableRepository(new \Modules\Requestable\Entities\Requestable());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Requestable\Repositories\Cache\CacheRequestableDecorator($repository);
            }
        );
// add bindings


    }
}
