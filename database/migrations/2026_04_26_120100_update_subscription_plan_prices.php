<?php

use App\Models\SubscriptionPlan;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $targets = [
            1 => ['monthly' => 100, 'yearly' => 1000],
            2 => ['monthly' => 300, 'yearly' => 3000],
            3 => ['monthly' => 400, 'yearly' => 4000],
            4 => ['monthly' => 600, 'yearly' => 6000],
            5 => ['monthly' => 1000, 'yearly' => 10000],
        ];

        foreach ($targets as $sortOrder => $price) {
            $plan = SubscriptionPlan::query()
                ->where('sort_order', $sortOrder)
                ->first();

            if (! $plan && $sortOrder === 5) {
                SubscriptionPlan::create([
                    'name' => 'Professional Plus',
                    'slug' => 'professional-plus',
                    'color' => 'black',
                    'price_monthly' => $price['monthly'],
                    'price_yearly' => $price['yearly'],
                    'features' => [
                        'Monthly professional delivery checklist',
                        'Dedicated admin oversight',
                        'Priority response and assignment',
                        'Advanced reporting and planning support',
                    ],
                    'is_active' => true,
                    'sort_order' => 5,
                ]);

                continue;
            }

            if ($plan) {
                $plan->update([
                    'price_monthly' => $price['monthly'],
                    'price_yearly' => $price['yearly'],
                ]);
            }
        }
    }

    public function down(): void
    {
        // No-op: avoid destructive rollback on production pricing records.
    }
};
