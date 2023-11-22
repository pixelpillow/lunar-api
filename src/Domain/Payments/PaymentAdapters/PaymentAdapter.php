<?php

namespace Dystcz\LunarApi\Domain\Payments\PaymentAdapters;

use BadMethodCallException;
use Dystcz\LunarApi\Domain\Transactions\Actions\CreateTransaction;
use Dystcz\LunarApi\Domain\Transactions\Data\TransactionData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Lunar\Models\Cart;
use Lunar\Models\Transaction;

abstract class PaymentAdapter
{
    protected Cart $cart;

    /**
     * Register payment adapter.
     */
    public static function register(): void
    {
        $adapter = new static();

        App::make(PaymentAdaptersRegister::class)
            ->add($adapter->getDriver(), static::class);
    }

    /**
     * Get payment driver.
     */
    abstract public function getDriver(): string;

    /**
     * Get payment type.
     */
    abstract public function getType(): string;

    /**
     * Create payment intent.
     *
     * @param  array<string,mixed>  $meta */
    abstract public function createIntent(Cart $cart, array $meta = []): PaymentIntent;

    /**
     * Handle incoming webhook call.
     */
    abstract public function handleWebhook(Request $request): JsonResponse;

    /**
     * Prepare transaction data.
     *
     * @param  array<string,mixed>  $data
     */
    protected function prepareTransactionData(PaymentIntent $paymentIntent, array $data = []): TransactionData
    {
        return (new TransactionData(
            type: 'intent',
            order_id: $this->cart->draftOrder->id,
            driver: $this->getDriver(),
            amount: $paymentIntent->amount,
            success: $paymentIntent->status === 'succeeded',
            reference: $paymentIntent->id,
            status: $paymentIntent->status,
            card_type: $this->getType(),
        ))->when(
            ! empty($data),
            fn ($data) => $data->mergeData($data),
        );
    }

    /**
     * Create transaction for payment intent.
     *
     * @param  array<string,mixed>  $data
     *
     * @throws BadMethodCallException
     */
    public function createTransaction(PaymentIntent $paymentIntent, array $data = []): Transaction
    {
        $this->validateCart();

        $transactionData = $this->prepareTransactionData($paymentIntent, $data);

        return (new CreateTransaction)($transactionData);
    }

    /**
     * Validate cart.
     *
     * @throws BadMethodCallException
     */
    protected function validateCart(): void
    {
        if ($this->cart->hasCompletedOrders()) {
            throw new BadMethodCallException('Cannot create transaction for completed order.');
        }

        if (! $this->cart->draftOrder) {
            throw new BadMethodCallException('Cart has no order.');
        }
    }
}
