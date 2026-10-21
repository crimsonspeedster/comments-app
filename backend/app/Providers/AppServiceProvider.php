<?php

namespace App\Providers;

use App\Models\Review;
use App\Observers\ReviewObserver;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle7\Client as GuzzleAdapter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerElasticsearch();
        $this->configureRoutes();
        $this->observeHandle();
    }

    protected function registerElasticsearch(): void
    {
        $this->app->singleton(
            Client::class,
            function (): Client {
                $guzzle = new GuzzleClient([
                    'verify' => config('elasticsearch.ca_cert'),
                ]);

                $httpClient = new GuzzleAdapter($guzzle);

                $builder = ClientBuilder::create()
                    ->setHosts([
                        config('elasticsearch.host'),
                    ])
                    ->setBasicAuthentication(
                        config('elasticsearch.username'),
                        config('elasticsearch.password'),
                    )
                    ->setHttpClient($httpClient);

                return $builder->build();
            },
        );
    }

    protected function observeHandle(): void
    {
        Review::observe(ReviewObserver::class);
    }

    protected function configureRoutes (): void
    {
        Route::middleware('api')
            ->prefix('api')
            ->group(base_path('routes/api.php'));

        Route::middleware('web')
            ->group(base_path('routes/web.php'));
    }
}
