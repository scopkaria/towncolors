<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name'          => 'Green Essential',
                'slug'          => 'green-essential',
                'color'         => 'green',
                'price_monthly' => 19.99,
                'price_yearly'  => 199.99,
                'features'      => [
                    'Up to 3 active projects',
                    'Basic file storage (1 GB)',
                    'Client dashboard access',
                    'Email support',
                ],
                'sort_order' => 1,
            ],
            [
                'name'          => 'Blue Advantage',
                'slug'          => 'blue-advantage',
                'color'         => 'blue',
                'price_monthly' => 49.99,
                'price_yearly'  => 499.99,
                'features'      => [
                    'Up to 10 active projects',
                    'Extended file storage (10 GB)',
                    'Priority project assignment',
                    'Chat & email support',
                    'Monthly progress reports',
                ],
                'sort_order' => 2,
            ],
            [
                'name'          => 'Purple Elite',
                'slug'          => 'purple-elite',
                'color'         => 'purple',
                'price_monthly' => 99.99,
                'price_yearly'  => 999.99,
                'features'      => [
                    'Unlimited active projects',
                    'Large file storage (50 GB)',
                    'Dedicated freelancer assignment',
                    '24/7 priority support',
                    'Weekly progress reports',
                    'Custom invoice branding',
                ],
                'sort_order' => 3,
            ],
            [
                'name'          => 'Black Ultimate',
                'slug'          => 'black-ultimate',
                'color'         => 'black',
                'price_monthly' => 199.99,
                'price_yearly'  => 1999.99,
                'features'      => [
                    'Unlimited active projects',
                    'Unlimited file storage',
                    'Dedicated account manager',
                    '24/7 VIP support line',
                    'Daily progress reports',
                    'Custom invoice branding',
                    'API access',
                    'SLA guarantee',
                ],
                'sort_order' => 4,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::firstOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
