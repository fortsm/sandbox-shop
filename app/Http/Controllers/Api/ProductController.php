<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductSellRequest;
use App\Models\Product;
use App\Traits\HasJsonNotFoundRosource;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    use HasJsonNotFoundRosource;

    /**
     * Sell product from one user to another.
     */
    public function sell(ProductSellRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();
        $product = Product::find($id);
        $this->checkFound($product, Product::class, 'Товар');
        return $product->sell(
            $validated['from_user'],
            $validated['to_user'],
            $validated['price'],
            $validated['quantity'],
        );
    }
}
