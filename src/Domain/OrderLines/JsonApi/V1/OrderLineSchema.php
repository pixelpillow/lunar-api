<?php

namespace Dystcz\LunarApi\Domain\OrderLines\JsonApi\V1;

use Dystcz\LunarApi\Domain\JsonApi\Eloquent\Schema;
use LaravelJsonApi\Eloquent\Fields\ArrayHash;
use LaravelJsonApi\Eloquent\Fields\Map;
use LaravelJsonApi\Eloquent\Fields\Number;
use LaravelJsonApi\Eloquent\Fields\Relations\BelongsTo;
use LaravelJsonApi\Eloquent\Fields\Relations\MorphTo;
use LaravelJsonApi\Eloquent\Fields\Str;
use Lunar\Models\OrderLine;

class OrderLineSchema extends Schema
{
    /**
     * The default paging parameters to use if the client supplies none.
     */
    protected ?array $defaultPagination = ['number' => 1];

    /**
     * {@inheritDoc}
     */
    public static string $model = OrderLine::class;

    /**
     * {@inheritDoc}
     */
    public function includePaths(): iterable
    {
        return [
            'currency',

            'order',

            'purchasable',
            'purchasable.images',
            'purchasable.prices',
            'purchasable.product',
            'purchasable.product.thumbnail',

            ...parent::includePaths(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function fields(): array
    {
        return [
            $this->idField(),

            Number::make('purchasable_id'),
            Str::make('purchasable_type'),

            Str::make('type'),
            Str::make('description'),
            Str::make('option'),
            Str::make('identifier'),
            Str::make('notes'),

            Map::make('prices', [
                Number::make('unit_price', 'unitPrice')
                    ->serializeUsing(
                        static fn ($value) => $value?->decimal,
                    ),
                Number::make('unit_quantity', 'unitQuantity'),
                Number::make('quantity', 'quantity'),
                Number::make('sub_total', 'subTotal')
                    ->serializeUsing(
                        static fn ($value) => $value?->decimal,
                    ),
                Number::make('sub_total_discounted', 'subTotalDiscounted')
                    ->serializeUsing(
                        static fn ($value) => $value?->decimal,
                    ),
                Number::make('total', 'total')
                    ->serializeUsing(
                        static fn ($value) => $value?->decimal,
                    ),
                Number::make('tax_total', 'taxTotal')
                    ->serializeUsing(
                        static fn ($value) => $value?->decimal,
                    ),
                Number::make('discount_total', 'discountTotal')
                    ->serializeUsing(
                        static fn ($value) => $value?->decimal,
                    ),
                ArrayHash::make('tax_breakdown', 'taxBreakdown')
                    ->serializeUsing(
                        static fn ($value) => $value?->amounts,
                    ),
            ]),

            ArrayHash::make('meta'),

            BelongsTo::make('order')
                ->serializeUsing(
                    static fn ($relation) => $relation->withoutLinks(),
                ),

            BelongsTo::make('currency')
                ->serializeUsing(
                    static fn ($relation) => $relation->withoutLinks(),
                ),

            MorphTo::make('purchasable', 'purchasable')
                ->types('products', 'variants', 'shipping-options')
                ->serializeUsing(
                    static fn ($relation) => $relation->withoutLinks(),
                ),

            ...parent::fields(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function type(): string
    {
        return 'order-lines';
    }
}
