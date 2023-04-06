<?php

namespace Dystcz\LunarApi\Domain\Carts\Http\Controllers;

use Dystcz\LunarApi\Controller;
use Dystcz\LunarApi\Domain\Carts\JsonApi\V1\CartAddressSchema;
use Dystcz\LunarApi\Domain\Carts\Models\CartAddress;
use Illuminate\Http\Request;
use LaravelJsonApi\Core\Responses\DataResponse;

class AttachShippingOptionController extends Controller
{
    public function attachShippingOption(
        CartAddressSchema $schema,
        Request $request,
        CartAddress $cartAddress,
    ): DataResponse {
        $this->authorize('update', $cartAddress);

        $cartAddress->update(['shipping_option' => $request->data['attributes']['shipping_option']]);

        $model = $schema
            ->repository()
            ->queryOne($cartAddress)
            ->withRequest($request)
            ->first();

        return DataResponse::make($model)
                ->didntCreate();
    }
}