<?php

namespace Dystcz\LunarApi\Domain\Carts\Http\Routing;

use Dystcz\LunarApi\Domain\Carts\Http\Controllers\CartAddressesController;
use Dystcz\LunarApi\Domain\Carts\Http\Controllers\SelectShippingOptionController;
use Dystcz\LunarApi\Routing\RouteGroup;
use LaravelJsonApi\Laravel\Facades\JsonApiRoute;
use LaravelJsonApi\Laravel\Routing\ResourceRegistrar;

class CartAddressRouteGroup extends RouteGroup
{
    public string $prefix = 'cart-addresses';

    public array $middleware = [];

    /**
     * Register routes.
     */
    public function routes(?string $prefix = null, array|string $middleware = []): void
    {
        JsonApiRoute::server('v1')
            ->prefix('v1')
            ->resources(function (ResourceRegistrar $server) {
                $server->resource($this->getPrefix(), CartAddressesController::class)
                    ->only('store', 'update');

                $server->resource($this->getPrefix(), SelectShippingOptionController::class)
                    ->only('')
                    ->actions('-actions', function ($actions) {
                        $actions->withId()->patch('select-shipping-option');
                    });
            });
    }
}
