<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\PlatformSetting;
use App\Models\Product;
use App\Models\StoreProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * @var array<string, list<OrderStatus>>
     */
    private const VALID_TRANSITIONS = [
        'pending' => [
            OrderStatus::Accepted,
            OrderStatus::Rejected,
            OrderStatus::Cancelled,
        ],
        'accepted' => [
            OrderStatus::Preparing,
            OrderStatus::Cancelled,
        ],
        'preparing' => [
            OrderStatus::ReadyForPickup,
            OrderStatus::Cancelled,
        ],
        'ready_for_pickup' => [
            OrderStatus::RiderAssigned,
            OrderStatus::Cancelled,
        ],
        'rider_assigned' => [
            OrderStatus::PickedUp,
            OrderStatus::Cancelled,
        ],
        'picked_up' => [
            OrderStatus::Delivered,
        ],
    ];

    public function __construct(
        private readonly CartService $cartService,
    ) {}

    public function createFromCart(
        Cart $cart,
        User $customer,
        Address $address,
        PaymentMethod $paymentMethod = PaymentMethod::Cod,
        ?string $notes = null,
    ): Order {
        $cart->load('items.product');

        if ($cart->items->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => ['Cart is empty.'],
            ]);
        }

        $storeProfileId = $this->resolveStoreProfileId($cart);
        $subtotal = $cart->subtotal();
        $deliveryFee = $this->calculateDeliveryFee();

        return DB::transaction(function () use (
            $cart,
            $customer,
            $address,
            $paymentMethod,
            $notes,
            $storeProfileId,
            $subtotal,
            $deliveryFee,
        ): Order {
            $order = Order::query()->create([
                'order_number' => $this->generateOrderNumber(),
                'customer_id' => $customer->id,
                'store_profile_id' => $storeProfileId,
                'address_id' => $address->id,
                'status' => OrderStatus::Pending,
                'payment_method' => $paymentMethod,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'total' => $subtotal + $deliveryFee,
                'notes' => $notes,
            ]);

            foreach ($cart->items as $item) {
                $product = Product::query()->lockForUpdate()->findOrFail($item->product_id);

                if (! $product->is_available || $product->stock < $item->quantity) {
                    throw ValidationException::withMessages([
                        'cart' => ["Insufficient stock for {$product->name}."],
                    ]);
                }

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                ]);

                $product->decrement('stock', $item->quantity);
            }

            $this->cartService->clearCart($cart);

            return $order->fresh(['items.product', 'store', 'address', 'customer']);
        });
    }

    public function updateStatus(Order $order, OrderStatus $status, array $attributes = []): Order
    {
        if (! $this->canTransition($order->status, $status)) {
            throw new \InvalidArgumentException(
                "Cannot transition order from {$order->status->value} to {$status->value}.",
            );
        }

        $order->update(array_merge(['status' => $status], $attributes));

        return $order->fresh();
    }

    public function assignRider(Order $order, User $rider): Order
    {
        if (! $rider->isRider()) {
            throw new \InvalidArgumentException('The assigned user must be a rider.');
        }

        return $this->updateStatus($order, OrderStatus::RiderAssigned, [
            'rider_id' => $rider->id,
        ]);
    }

    /**
     * @param  list<array{product_id: int, quantity: int}>  $lineItems
     */
    public function createPosSale(
        StoreProfile $store,
        User $cashier,
        array $lineItems,
        PaymentMethod $paymentMethod = PaymentMethod::Cod,
        ?string $customerName = null,
        ?string $paymentNote = null,
        string $discountType = 'none',
        float $discountValue = 0.0,
        ?float $cashTendered = null,
    ): Order {
        $address = $store->addresses()->first();

        if ($address === null) {
            throw ValidationException::withMessages([
                'address' => ['Add your store address in Store Information before using Point of Sales.'],
            ]);
        }

        if ($lineItems === []) {
            throw ValidationException::withMessages([
                'items' => ['Add at least one product to the sale.'],
            ]);
        }

        $notes = '[POS] In-store sale';
        if (filled($customerName)) {
            $notes .= ' — Customer: '.$customerName;
        }
        if (filled($paymentNote)) {
            $notes .= "\n".$paymentNote;
        }

        return DB::transaction(function () use (
            $store,
            $cashier,
            $lineItems,
            $paymentMethod,
            $address,
            $notes,
            $discountType,
            $discountValue,
            $cashTendered,
        ): Order {
            $subtotal = 0.0;
            $resolvedItems = [];

            foreach ($lineItems as $lineItem) {
                $product = Product::query()
                    ->where('store_profile_id', $store->id)
                    ->lockForUpdate()
                    ->findOrFail($lineItem['product_id']);

                $quantity = (int) $lineItem['quantity'];

                if (! $product->is_available || $product->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => ["Insufficient stock for {$product->name}."],
                    ]);
                }

                $subtotal += (float) $product->price * $quantity;
                $resolvedItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => (float) $product->price,
                ];
            }

            $discountAmount = $this->calculatePosDiscount($subtotal, $discountType, $discountValue);
            $totalDue = max(0, round($subtotal - $discountAmount, 2));

            if ($paymentMethod === PaymentMethod::Cash) {
                if ($cashTendered === null) {
                    throw ValidationException::withMessages([
                        'cash_tendered' => ['Enter the cash amount received from the customer.'],
                    ]);
                }

                if ($cashTendered + 0.001 < $totalDue) {
                    throw ValidationException::withMessages([
                        'cash_tendered' => ['Cash tendered must be at least the total due.'],
                    ]);
                }
            }

            $saleNotes = $notes;
            if ($discountAmount > 0) {
                $discountLabel = $discountType === 'percent'
                    ? number_format($discountValue, 2).'% (₱'.number_format($discountAmount, 2).')'
                    : '₱'.number_format($discountAmount, 2);
                $saleNotes .= "\nDiscount: ".$discountLabel;
            }

            if ($paymentMethod === PaymentMethod::Cash && $cashTendered !== null) {
                $changeDue = round(max(0, $cashTendered - $totalDue), 2);
                $saleNotes .= "\nCash tendered: ₱".number_format($cashTendered, 2);
                $saleNotes .= "\nChange: ₱".number_format($changeDue, 2);
            }

            $order = Order::query()->create([
                'order_number' => $this->generateOrderNumber(),
                'customer_id' => $cashier->id,
                'store_profile_id' => $store->id,
                'address_id' => $address->id,
                'status' => OrderStatus::Delivered,
                'payment_method' => $paymentMethod,
                'subtotal' => $subtotal,
                'delivery_fee' => 0,
                'total' => $totalDue,
                'notes' => $saleNotes,
            ]);

            foreach ($resolvedItems as $item) {
                $order->items()->create([
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);

                $item['product']->decrement('stock', $item['quantity']);
            }

            return $order->fresh(['items.product', 'store', 'address', 'customer']);
        });
    }

    public function calculateDeliveryFee(): float
    {
        return (float) PlatformSetting::get('delivery_fee', 50);
    }

    private function calculatePosDiscount(float $subtotal, string $discountType, float $discountValue): float
    {
        if ($subtotal <= 0 || $discountValue <= 0 || $discountType === 'none') {
            return 0.0;
        }

        $discountAmount = match ($discountType) {
            'percent' => $subtotal * min($discountValue, 100) / 100,
            'fixed' => $discountValue,
            default => 0.0,
        };

        return round(min($discountAmount, $subtotal), 2);
    }

    private function resolveStoreProfileId(Cart $cart): int
    {
        $storeIds = $cart->items
            ->pluck('product.store_profile_id')
            ->unique()
            ->values();

        if ($storeIds->count() !== 1) {
            throw ValidationException::withMessages([
                'cart' => ['All cart items must belong to the same store.'],
            ]);
        }

        return (int) $storeIds->first();
    }

    private function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        $prefix = "RM-{$date}-";

        $lastOrder = Order::query()
            ->where('order_number', 'like', $prefix.'%')
            ->orderByDesc('order_number')
            ->lockForUpdate()
            ->first();

        $sequence = $lastOrder !== null
            ? ((int) substr($lastOrder->order_number, -4)) + 1
            : 1;

        return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    private function canTransition(OrderStatus $from, OrderStatus $to): bool
    {
        $allowed = self::VALID_TRANSITIONS[$from->value] ?? [];

        return in_array($to, $allowed, true);
    }
}
