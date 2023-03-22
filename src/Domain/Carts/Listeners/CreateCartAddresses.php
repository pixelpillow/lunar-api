<?php

namespace Dystcz\LunarApi\Domain\Carts\Listeners;

use Dystcz\LunarApi\Domain\Carts\Events\CartCreated;

class CreateCartAddresses
{
    public function __construct()
    {
    }

    public function handle(CartCreated $event): void
    {
        $cart = $event->cart;

        $cart->setShippingAddress([
            'first_name' => null,
            'last_name' => null,
            'line_one' => null,
            'line_two' => null,
            'line_three' => null,
            'city' => null,
            'state' => null,
            'postcode' => null,
            'country_id' => null,
        ]);

        $cart->setBillingAddress([
            'first_name' => null,
            'last_name' => null,
            'line_one' => null,
            'line_two' => null,
            'line_three' => null,
            'city' => null,
            'state' => null,
            'postcode' => null,
            'country_id' => null,
        ]);
    }
}
