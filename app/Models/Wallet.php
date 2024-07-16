<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wallet extends Model
{
    use HasFactory;

    /**
     * Returns User object
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * reduceBalance
     *
     * @param  float $summ
     * @return void
     */
    public function reduceBalance(float $summ)
    {
        $this->balance -= $summ;
        $this->save();
    }

    /**
     * expandBalance
     *
     * @param  float $summ
     * @return void
     */
    public function expandBalance(float $summ)
    {
        $this->balance += $summ;
        $this->save();
    }
}
