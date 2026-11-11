<?php

namespace Database\Seeders;

use App\Models\MembershipPlan;
use Illuminate\Database\Seeder;

class MembershipPlanSeeder extends Seeder
{
    /**
     * EXAMPLE plans with made-up prices. Replace them with the space's real plans.
     */
    private const EXAMPLE_PLANS = [
        ['name' => 'Regular', 'monthly_price_in_cents' => 5000, 'description' => 'Full access for one person.'],
        ['name' => 'Student', 'monthly_price_in_cents' => 2500, 'description' => 'Full access for one person with a student ID.'],
        ['name' => 'Family', 'monthly_price_in_cents' => 7500, 'description' => 'Full access for people living at one address.'],
    ];

    public function run(): void
    {
        foreach (self::EXAMPLE_PLANS as $examplePlan) {
            // firstOrCreate makes the seeder safe to run more than once: a plan
            // whose name already exists is left exactly as it is.
            MembershipPlan::firstOrCreate(['name' => $examplePlan['name']], $examplePlan);
        }
    }
}
