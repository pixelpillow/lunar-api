<?php

namespace Dystcz\LunarApi\Domain\Urls\JsonApi\V1;

use Dystcz\LunarApi\Domain\JsonApi\Extensions\Resource\ResourceManifest;
use Dystcz\LunarApi\Domain\JsonApi\Resources\JsonApiResource;
use Lunar\Models\Url;

class UrlResource extends JsonApiResource
{
    /**
     * Get the resource's attributes.
     *
     * @param  \Illuminate\Http\Request|null  $request
     */
    public function attributes($request): iterable
    {
        /** @var Url */
        $model = $this->resource;

        return [
            'slug' => $model->slug,
            'default' => $model->default,
            ...ResourceManifest::for(static::class)->attributes()->toResourceArray($this),
        ];
    }

    /**
     * Get the resource's relationships.
     *
     * @param  \Illuminate\Http\Request|null  $request
     */
    public function relationships($request): iterable
    {
        return [
            ...ResourceManifest::for(static::class)->relationships()->toResourceArray($this),
        ];
    }
}
