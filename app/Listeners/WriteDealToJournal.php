<?php

namespace App\Listeners;

use App\Events\ProductSold;
use App\Models\SalesLog;

class WriteDealToJournal
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ProductSold $event): void
    {
        SalesLog::create([
            'seller_id' => $event->seller->id,
            'buyer_id' => $event->buyer->id,
            'product_id' => $event->product_id,
            'quantity' => $event->quantity,
            'price' => $event->price,
        ]);
    }
}
