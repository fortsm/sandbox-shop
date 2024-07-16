<?php

namespace App\Traits;

use App\Models\User;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

trait HasTradable
{
    /**
     * sell the product from one user to another
     *
     * @param  int $fromUser
     * @param  int $toUser
     * @param  float $price
     * @param  int $quantity
     * @return JsonResponse
     */
    public function sell(int $fromUser, int $toUser, float $price, int $quantity): JsonResponse
    {
        try {
            DB::beginTransaction();
            $seller = User::findOrFail($fromUser);
            $buyer = User::findOrFail($toUser);
            $this->checkSellerHasProduct($seller, $quantity);
            $this->checkBuyerHasMoney($buyer, $quantity, $price);
            $this->sellerProductReduce($seller, $quantity);
            $buyer->wallet->reduceBalance($price * $quantity);
            $this->buyerProductExpand($buyer, $quantity, $price);
            $seller->wallet->expandBalance($price * $quantity);
            // log
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => "Error while selling: {$e->getMessage()}",
            ], 400));
        }
        return response()->json([
            'success'   => true,
            'message'   => "The product '{$this->name}' was successfully sold from user $fromUser to user $toUser in quantity of $quantity at a price of $price"
        ], 200);
    }

    /**
     * checkSellerHasProduct
     *
     * @param  User $seller
     * @param  int $quantity
     * @throws Exception
     * @return void
     */
    public function checkSellerHasProduct(User $seller, int $quantity): void
    {
        if (!$seller->product($this->id)->wherePivot('quantity', '>=', $quantity)->exists()) {
            throw new Exception("The seller doesn't have enough items");
        }
    }

    /**
     * checkBuyerHasMoney
     *
     * @param  User $buyer
     * @param  int $quantity
     * @param  float $price
     * @throws Exception
     * @return void
     */
    public function checkBuyerHasMoney(User $buyer, int $quantity, float $price): void
    {
        if (!($buyer->wallet->balance >= $quantity * $price)) {
            throw new Exception("The buyer doesn't have enough money");
        }
    }

    /**
     * sellerProductReduce
     *
     * @param  User $seller
     * @param  int $quantity
     * @return void
     */
    public function sellerProductReduce(User $seller, int $quantity)
    {
        $product = $seller->product($this->id)
            ->withPivot(['quantity'])
            ->first();

        $new_quantity = $product->pivot->quantity -= $quantity;

        if ($new_quantity > 0) {
            $seller->products()->updateExistingPivot(
                $this->id,
                ['quantity' => $new_quantity]
            );
        } else {
            $seller->products()->detach($this->id);
        }
    }

    /**
     * buyerProductExpand
     *
     * @param  User $buyer
     * @param  int $quantity
     * @param  float $price
     * @return void
     */
    public function buyerProductExpand(User $buyer, int $quantity, float $price)
    {
        $product = $buyer->product($this->id)
            ->withPivot(['quantity'])
            ->first();

        if ($product) {
            $new_quantity = $product->pivot->quantity += $quantity;
            $buyer->products()->updateExistingPivot(
                $this->id,
                [
                    'quantity' => $new_quantity,
                    'price' => $price,
                ]
            );
        } else {
            $buyer->products()->attach(
                $this->id,
                [
                    'quantity' => $quantity,
                    'price' => $price,
                ]
            );
        }
    }
}
