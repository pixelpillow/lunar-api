<?php

namespace Dystcz\LunarApi\Domain\Attributes\Collections;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class AttributeCollection extends EloquentCollection
{
    /**
     * Map Attributes into groups based on AttributeGroup they are part of.
     */
    public function mapToAttributeGroups(Model $model): Collection
    {
        return $this
            // ->filter(fn ($attribute) => $model->attr($attribute->handle))
            ->groupBy(
                fn ($attribute) => $attribute->attributeGroup->handle
            )
            ->map(
                fn ($attributes) => $attributes->mapWithKeys(
                    fn ($attribute) => [
                        $attribute->handle => [
                            'name' => $attribute->translate('name'),
                            'value' => $model->attr($attribute->handle),
                        ],
                    ]
                )
            );
    }
}
