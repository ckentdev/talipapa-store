<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Concerns\ResolvesCart;
use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    use ResolvesCart;

    public function __construct(
        private readonly CartService $cartService,
    ) {}

    public function index(): View
    {
        $cart = $this->resolveCart()->load('items.product.store');

        return view('public.cart.index', [
            'cart' => $cart,
            'itemCount' => $this->cartService->getItemCount($cart),
        ]);
    }

    public function preview(): JsonResponse
    {
        $cart = $this->resolveCart()->load(['items.product.store']);
        $itemCount = $this->cartService->getItemCount($cart);

        return response()->json([
            'itemCount' => $itemCount,
            'subtotal' => $cart->subtotal(),
            'html' => view('components.partials.header-cart-dropdown-content', [
                'cart' => $cart,
                'cartCount' => $itemCount,
                'cartSubtotal' => $cart->subtotal(),
            ])->render(),
        ]);
    }

    public function store(AddToCartRequest $request): RedirectResponse|JsonResponse
    {
        $product = Product::query()->available()->findOrFail($request->integer('product_id'));
        $cart = $this->resolveCart();

        $this->cartService->addItem($cart, $product, $request->integer('quantity', 1));
        $cart->load(['items.product.store']);

        $message = "{$product->name} added to cart.";
        $itemCount = $this->cartService->getItemCount($cart);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'itemCount' => $itemCount,
                'subtotal' => $cart->subtotal(),
                'html' => view('components.partials.header-cart-dropdown-content', [
                    'cart' => $cart,
                    'cartCount' => $itemCount,
                    'cartSubtotal' => $cart->subtotal(),
                ])->render(),
            ]);
        }

        return back()->with('success', $message);
    }

    public function update(UpdateCartItemRequest $request, CartItem $cartItem): RedirectResponse
    {
        $cart = $this->resolveCart();
        abort_unless($cartItem->cart_id === $cart->id, 403);
        $this->cartService->updateQuantity($cart, $cartItem, $request->integer('quantity'));

        return back()->with('success', 'Cart updated.');
    }

    public function destroy(CartItem $cartItem): RedirectResponse
    {
        $cart = $this->resolveCart();
        abort_unless($cartItem->cart_id === $cart->id, 403);
        $this->cartService->removeItem($cart, $cartItem);

        return back()->with('success', 'Item removed from cart.');
    }
}
