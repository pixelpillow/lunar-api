<?php

use Dystcz\LunarApi\Domain\Orders\Models\Order;
use Dystcz\LunarApi\Tests\Stubs\Users\User;
use Dystcz\LunarApi\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

it('can update order by its owner', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();

    $this->actingAs($user);

    /** @var Order $order */
    $order = Order::factory()->for($user)->create();

    $data = [
        'type' => 'orders',
        'id' => (string) $order->getRouteKey(),
        'attributes' => [
            'notes' => 'This is a note.',
        ],
    ];

    $response = $this
        ->jsonApi()
        ->expects('orders')
        ->withData($data)
        ->patch('http://localhost/api/v1/orders/'.$order->getRouteKey());

    $response->assertFetchedOne($order);
})->group('orders');

it('cannot update order by other user', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();

    $this->actingAs($user);

    /** @var Order $order */
    $order = Order::factory()->create();

    $data = [
        'type' => 'orders',
        'id' => (string) $order->getRouteKey(),
        'attributes' => [
            'notes' => 'This is a note.',
        ],
    ];

    $response = $this
        ->jsonApi()
        ->expects('orders')
        ->withData($data)
        ->patch('http://localhost/api/v1/orders/'.$order->getRouteKey());

    $response->assertErrorStatus([
        'detail' => 'Unauthenticated.',
    ]);
})->group('orders')->todo();
