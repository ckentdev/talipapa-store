<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterSubscribeController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:newsletter_subscribers,email'],
        ]);

        NewsletterSubscriber::query()->create([
            'email' => $validated['email'],
            'subscribed_at' => now(),
        ]);

        return back()->with('success', 'Thanks for subscribing! We will keep you posted on deals and updates.');
    }
}
