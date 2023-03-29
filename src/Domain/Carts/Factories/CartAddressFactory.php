<?php

namespace Dystcz\LunarApi\Domain\Carts\Factories;

use Dystcz\LunarApi\Domain\Carts\Models\CartAddress;
use Lunar\Database\Factories\CartAddressFactory as LunarCartAddressFactory;
use Lunar\Models\Country;

class CartAddressFactory extends LunarCartAddressFactory
{
    protected $model = CartAddress::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->title,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'company_name' => $this->faker->boolean ? $this->faker->company : null,
            'line_one' => $this->faker->streetName,
            'line_two' => $this->faker->boolean ? $this->faker->secondaryAddress : null,
            'line_three' => $this->faker->boolean ? $this->faker->buildingNumber : null,
            'city' => $this->faker->city,
            'state' => $this->faker->boolean ? $this->faker->state : null,
            'postcode' => $this->faker->postcode,
            'delivery_instructions' => $this->faker->boolean ? $this->faker->sentence : null,
            'contact_email' => $this->faker->safeEmail,
            'contact_phone' => $this->faker->boolean ? $this->faker->phoneNumber : null,
            'type' => 'shipping',
            'meta' => $this->faker->boolean ? ['has_dog' => 'yes'] : null,
            'country_id' => Country::first() ?? Country::factory(),
        ];
    }
}
