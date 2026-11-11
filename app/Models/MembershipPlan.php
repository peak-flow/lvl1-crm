<?php

namespace App\Models;

use Database\Factories\MembershipPlanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'monthly_price_in_cents', 'description'])]
class MembershipPlan extends Model
{
    /** @use HasFactory<MembershipPlanFactory> */
    use HasFactory;

    /**
     * The members who are on this plan.
     */
    public function members(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * The price written for people, such as "$50.00 a month".
     */
    public function monthlyPriceForDisplay(): string
    {
        return '$'.number_format($this->monthly_price_in_cents / 100, 2).' a month';
    }
}
