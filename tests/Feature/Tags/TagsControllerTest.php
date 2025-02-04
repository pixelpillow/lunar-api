<?php

use Dystcz\LunarApi\Domain\Prices\Factories\PriceFactory;
use Dystcz\LunarApi\Domain\Products\Factories\ProductFactory;
use Dystcz\LunarApi\Domain\ProductVariants\Factories\ProductVariantFactory;
use Dystcz\LunarApi\Domain\Tags\Models\Tag;
use Dystcz\LunarApi\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

it('can list all tags', function () {
    /** @var TestCase $this */
    $models = Tag::factory()
        ->count(3)
        ->create();

    $response = $this
        ->jsonApi()
        ->expects('tags')
        ->get('/api/v1/tags');

    $response
        ->assertFetchedMany($models)
        ->assertDoesntHaveIncluded();
})->group('tags');

it('can read tag detail', function () {
    /** @var TestCase $this */
    $tag = Tag::factory()->create();

    $response = $this
        ->jsonApi()
        ->expects('tags')
        ->get('/api/v1/tags/'.$tag->getRouteKey());

    $response
        ->assertFetchedOne($tag)
        ->assertDoesntHaveIncluded();
})->group('tags');

it('can read tag detail with products included', function () {
    /** @var TestCase $this */
    $tag = Tag::factory()->create();

    $products = ProductFactory::new()
        ->has(
            ProductVariantFactory::new()->has(PriceFactory::new())->count(2),
            'variants'
        )
        ->count(3)
        ->create();

    $response = $this
        ->jsonApi()
        ->expects('tags')
        ->includePaths('taggables')
        ->get('/api/v1/tags/'.$tag->getRouteKey());

    $response
        ->assertFetchedOne($tag)
        ->assertIncluded([
            ['type' => 'taggables', 'id' => $tag->taggables[0]->getRouteKey()],
            ['type' => 'taggables', 'id' => $tag->taggables[1]->getRouteKey()],
        ]);
})->group('tags')->todo();

it('returns error response when tag doesnt exists', function () {
    /** @var TestCase $this */
    $response = $this
        ->jsonApi()
        ->expects('tags')
        ->get('/api/v1/tags/1');

    $response->assertErrorStatus([
        'status' => '404',
        'title' => 'Not Found',
    ]);
})->group('tags');
