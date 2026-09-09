<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function getOrCreateCart(?User $user = null, ?string $sessionId = null): Cart
    {
        if ($user !== null) {
            return Cart::query()->firstOrCreate(['user_id' => $user->id]);
        }

        if ($sessionId !== null) {
            return Cart::query()->firstOrCreate(
                ['session_id' => $sessionId],
                ['user_id' => null],
            );
        }

        throw new \InvalidArgumentException('Either a user or session ID is required.');
    }

    public function addItem(Cart $cart, Product $product, int $quantity = 1): CartItem
    {
        $this->validateStock($product, $quantity);

        $existingItem = $cart->items()->where('product_id', $product->id)->first();

        if ($existingItem !== null) {
            return $this->updateQuantity($cart, $existingItem, $existingItem->quantity + $quantity);
        }

        return $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit_price' => $product->price,
        ]);
    }

    public function updateQuantity(Cart $cart, CartItem $item, int $quantity): CartItem
    {
        if ($item->cart_id !== $cart->id) {
            throw new \InvalidArgumentException('Cart item does not belong to this cart.');
        }

        if ($quantity <= 0) {
            $this->removeItem($cart, $item);

            return $item;
        }

        $product = $item->product ?? Product::query()->findOrFail($item->product_id);
        $this->validateStock($product, $quantity);

        $item->update(['quantity' => $quantity]);

        return $item->fresh();
    }

    public function removeItem(Cart $cart, CartItem $item): void
    {
        if ($item->cart_id !== $cart->id) {
            throw new \InvalidArgumentException('Cart item does not belong to this cart.');
        }

        $item->delete();
    }

    public function mergeGuestCart(string $sessionId, User $user): Cart
    {
        return DB::transaction(function () use ($sessionId, $user): Cart {
            $guestCart = Cart::query()
                ->where('session_id', $sessionId)
                ->whereNull('user_id')
                ->with('items.product')
                ->first();

            $userCart = $this->getOrCreateCart($user);

            if ($guestCart === null) {
                return $userCart;
            }

            foreach ($guestCart->items as $guestItem) {
                $existingItem = $userCart->items()->where('product_id', $guestItem->product_id)->first();

                if ($existingItem !== null) {
                    $this->updateQuantity(
                        $userCart,
                        $existingItem,
                        $existingItem->quantity + $guestItem->quantity,
                    );
                } else {
                    $this->addItem($userCart, $guestItem->product, $guestItem->quantity);
                }
            }

            $guestCart->items()->delete();
            $guestCart->delete();

            return $userCart->fresh(['items.product']);
        });
    }

    public function clearCart(Cart $cart): void
    {
        $cart->items()->delete();
    }

    public function getItemCount(Cart $cart): int
    {
        return (int) $cart->items()->sum('quantity');
    }

    protected function validateStock(Product $product, int $quantity): void
    {
        if (! $product->is_available) {
            throw ValidationException::withMessages([
                'product' => ["{$product->name} is currently unavailable."],
            ]);
        }

        if ($product->stock < $quantity) {
            throw ValidationException::withMessages([
                'quantity' => ["Only {$product->stock} unit(s) of {$product->name} are available."],
            ]);
        }
    }
}
