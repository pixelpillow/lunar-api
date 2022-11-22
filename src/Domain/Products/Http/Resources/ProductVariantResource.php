<?php

namespace Dystcz\LunarApi\Domain\Products\Http\Resources;

use Dystcz\LunarApi\Domain\JsonApi\Http\Resources\JsonApiResource;
use Dystcz\LunarApi\Domain\Prices\Http\Resources\PriceResource;
use Illuminate\Http\Request;

class ProductVariantResource extends JsonApiResource
{
    protected function toAttributes(Request $request): array
    {
        return [
            'sku' => $this->sku,
            'ean' => $this->ean,
            ...! $this->attribute_data ? [] : $this->attribute_data->keys()->mapWithKeys(function ($key) {
                return [$key => $this->attr($key)];
            }),
        ];
    }

    protected function toRelationships(Request $request): array
    {
        return [
            'basePrices' => $this->optionalCollection(PriceResource::class, 'basePrices'),
        ];
    }
}