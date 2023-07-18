<?php

namespace Dystcz\LunarApi\Tests;

use Dystcz\LunarApi\JsonApiServiceProvider;
use Dystcz\LunarApi\LunarApiServiceProvider;
use Dystcz\LunarApi\Tests\Stubs\Carts\Modifiers\TestShippingModifier;
use Dystcz\LunarApi\Tests\Stubs\JsonApi\V1\Server;
use Dystcz\LunarApi\Tests\Stubs\Lunar\TestTaxDriver;
use Dystcz\LunarApi\Tests\Stubs\Lunar\TestUrlGenerator;
use Dystcz\LunarPaypal\LunarPaypalServiceProvider;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redis;
use LaravelJsonApi\Testing\MakesJsonApiRequests;
use LaravelJsonApi\Testing\TestExceptionHandler;
use Lunar\Base\ShippingModifiers;
use Lunar\Facades\Taxes;
use Lunar\Models\Channel;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\TaxClass;
use Lunar\Stripe\StripePaymentsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\LaravelData\LaravelDataServiceProvider;

abstract class TestCase extends Orchestra
{
    use MakesJsonApiRequests;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('auth.providers.users', [
            'driver' => 'eloquent',
            'model' => \Dystcz\LunarApi\Tests\Stubs\Users\User::class,
        ]);
        Config::set('lunar.urls.generator', TestUrlGenerator::class);
        Config::set('lunar.taxes.driver', 'test');

        Taxes::extend('test', function ($app) {
            return $app->make(TestTaxDriver::class);
        });

        Currency::factory()->create([
            'code' => 'EUR',
            'decimal_places' => 2,
        ]);

        Country::factory()->create([
            'name' => 'United Kingdom',
            'iso3' => 'GBR',
            'iso2' => 'GB',
            'phonecode' => '+44',
            'capital' => 'London',
            'currency' => 'GBP',
            'native' => 'English',
        ]);

        Channel::factory()->create([
            'default' => true,
        ]);

        CustomerGroup::factory()->create([
            'default' => true,
        ]);

        TaxClass::factory()->create();

        App::get(ShippingModifiers::class)->add(TestShippingModifier::class);

        activity()->disableLogging();

        $this->beforeApplicationDestroyed(function () {
            Redis::flushall();
        });
    }

    /**
     * @param  Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            LunarApiServiceProvider::class,
            JsonApiServiceProvider::class,

            // Laravel JsonApi
            \LaravelJsonApi\Encoder\Neomerx\ServiceProvider::class,
            \LaravelJsonApi\Laravel\ServiceProvider::class,
            \LaravelJsonApi\Spec\ServiceProvider::class,

            // Lunar core
            \Lunar\LunarServiceProvider::class,
            \Spatie\MediaLibrary\MediaLibraryServiceProvider::class,
            \Spatie\Activitylog\ActivitylogServiceProvider::class,
            \Cartalyst\Converter\Laravel\ConverterServiceProvider::class,
            \Kalnoy\Nestedset\NestedSetServiceProvider::class,
            \Spatie\LaravelBlink\BlinkServiceProvider::class,
            StripePaymentsServiceProvider::class,

            // Lunar PayPal payments
            LunarPaypalServiceProvider::class,
            LaravelDataServiceProvider::class,

            // Livewire
            \Livewire\LivewireServiceProvider::class,
        ];
    }

    /**
     * @param  Application  $app
     */
    public function getEnvironmentSetUp($app)
    {
        $app->useEnvironmentPath(__DIR__.'/..');
        $app->bootstrapWith([LoadEnvironmentVariables::class]);

        /**
         * Lunar configuration
         */
        Config::set('lunar-api.additional_servers', [
            Server::class,
        ]);
        // Set cart auto creation to true
        Config::set('lunar.cart.auto_create', true);
        // Default payment driver
        Config::set('lunar.payments.default', env('PAYMENT_DRIVER', 'stripe'));

        /**
         * App configuration
         */
        Config::set('database.default', 'sqlite');
        Config::set('database.migrations', 'migrations');

        Config::set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        Config::set('database.connections.mysql', [
            'driver' => 'mysql',
            'host' => 'mysql',
            'port' => '3306',
            'database' => 'lunar-api-testing',
            'username' => 'homestead',
            'password' => 'secret',
        ]);

        Config::set('database.redis.default', [
            'host' => env('REDIS_HOST', 'redis'),
            'password' => env('REDIS_PASSWORD', ''),
            'port' => env('REDIS_PORT', '6379'),
        ]);

        Config::set('services.stripe', [
            'public_key' => env('STRIPE_PUBLIC_KEY'),
            'key' => env('STRIPE_SECRET_KEY'),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        ]);

        Config::set('lunar.paypal', require __DIR__.'/../config/paypal.php');
    }

    /**
     * Define database migrations.
     *
     * @return void
     */
    protected function defineDatabaseMigrations()
    {
        $this->loadLaravelMigrations();

        // NOTE MySQL migrations do not play nice with Lunar testing for some reason
        // // artisan($this, 'lunar:install');
        // // artisan($this, 'vendor:publish', ['--tag' => 'lunar']);
        // // artisan($this, 'vendor:publish', ['--tag' => 'lunar.migrations']);
        //
        // // artisan($this, 'migrate', ['--database' => 'mysql']);
        //
        // $this->beforeApplicationDestroyed(
        //     fn () => artisan($this, 'migrate:rollback', ['--database' => 'mysql'])
        // );
    }

    protected function resolveApplicationExceptionHandler($app): void
    {
        $app->singleton(
            ExceptionHandler::class,
            TestExceptionHandler::class
        );
    }
}
