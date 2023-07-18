<?php

namespace Dystcz\LunarApi\Domain\PaymentOptions\JsonApi\V1;

use Dystcz\LunarApi\Domain\PaymentOptions\Entities\PaymentOption;
use LaravelJsonApi\Core\Schema\Schema;
use LaravelJsonApi\NonEloquent\Fields\Attribute;
use LaravelJsonApi\NonEloquent\Fields\ID;

class PaymentOptionSchema extends Schema
{
    /**
     * Whether resources of this type have a self link.
     */
    protected bool $selfLink = false;

    /**
     * {@inheritDoc}
     */
    public static string $model = PaymentOption::class;

    /**
     * {@inheritDoc}
     */
    public function fields(): iterable
    {
        return [
            ID::make(),
            Attribute::make('name'),
            Attribute::make('driver'),
            Attribute::make('default'),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function authorizable(): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function repository(): PaymentOptionRepository
    {
        return PaymentOptionRepository::make()
            ->withServer($this->server)
            ->withSchema($this);
    }
}
