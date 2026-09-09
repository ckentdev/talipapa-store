<?php

namespace App\Http\Controllers\Customer;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Review\StoreReviewRequest;
use App\Models\Order;
use App\Models\Review;
use App\Models\StoreProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request): RedirectResponse
    {
        $order = Order::query()
            ->where('customer_id', $request->user()->id)
            ->findOrFail($request->integer('order_id'));

        abort_unless($order->status === OrderStatus::Delivered, 403, 'You can only review delivered orders.');

        [$reviewableType, $reviewableId] = match ($request->input('type')) {
            'store' => [StoreProfile::class, $order->store_profile_id],
            'rider' => [User::class, $order->rider_id],
            default => abort(422),
        };

        if ($reviewableId === null) {
            return back()->with('error', 'No rider assigned to this order yet.');
        }

        Review::query()->updateOrCreate(
            [
                'order_id' => $order->id,
                'reviewer_id' => $request->user()->id,
                'reviewable_type' => $reviewableType,
                'reviewable_id' => $reviewableId,
            ],
            [
                'rating' => $request->integer('rating'),
                'comment' => $request->input('comment'),
            ],
        );

        return back()->with('success', 'Thank you for your review!');
    }
}
