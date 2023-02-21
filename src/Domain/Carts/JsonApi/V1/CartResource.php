<?php

namespace Dystcz\LunarApi\Domain\Carts\JsonApi\V1;

use Dystcz\LunarApi\Domain\Carts\Models\Cart;
use Dystcz\LunarApi\Domain\JsonApi\Extensions\Resource\ResourceManifest;
use Dystcz\LunarApi\Domain\JsonApi\Resources\JsonApiResource;

class CartResource extends JsonApiResource
{
    /**
     * Get the resource's attributes.
     *
     * @param  \Illuminate\Http\Request|null  $request
     * @return iterable
     */
    public function attributes($request): iterable
    {
        /** @var Cart $model */
        $model = $this->resource;

        if ($model->relationLoaded('lines')) {
            $model->calculate();
        }

        return [
            'prices' => [
                'sub_total' => $model->subTotal?->decimal,
                'total' => $model->total?->decimal,
                'shipping_total' => $model->shippingTotal?->decimal,
                'tax_total' => $model->taxTotal?->decimal,
                'cart_discount_amount' => $model->cartDiscountAmount?->decimal,
                'discount_total' => $model->discountTotal?->decimal,
                'tax_breakdown' => $model->taxBreakdown,
            ],

            ...ResourceManifest::for(static::class)->attributes()->toResourceArray($this),
        ];
    }

    /**
     * Get the resource's relationships.
     *
     * @param  \Illuminate\Http\Request|null  $request
     * @return iterable
     */
    public function relationships($request): iterable
    {
        /** @var Cart $cart */
        $model = $this->resource;

        return [
            $this->relation('lines'),

            ...ResourceManifest::for(static::class)->relationships()->toResourceArray($this),
        ];
    }
}
