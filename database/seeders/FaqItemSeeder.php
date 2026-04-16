<?php

namespace Database\Seeders;

use App\Models\FaqItem;
use Illuminate\Database\Seeder;

class FaqItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['category' => 'Services', 'question' => 'What services do you offer?', 'answer' => 'We provide website development, custom software, mobile apps, cloud operations, SEO, media production, and digital growth support.', 'sort_order' => 1],
            ['category' => 'Services', 'question' => 'Can you customize an existing system?', 'answer' => 'Yes. We can extend, modernize, or re-architect existing systems while keeping data continuity and business operations intact.', 'sort_order' => 2],
            ['category' => 'Process', 'question' => 'How long does delivery take?', 'answer' => 'Small projects may take a few weeks, while larger systems are delivered in phases. Timeline is agreed after scope and workflow mapping.', 'sort_order' => 1],
            ['category' => 'Process', 'question' => 'Do you offer post-launch support?', 'answer' => 'Yes. We provide maintenance plans, release support, monitoring, and optimization after launch.', 'sort_order' => 2],
            ['category' => 'Cloud', 'question' => 'Do you provide hosting and backups?', 'answer' => 'Yes. We host websites and software platforms with backup, security hardening, and uptime monitoring workflows.', 'sort_order' => 1],
            ['category' => 'Commerce', 'question' => 'How does software purchase work?', 'answer' => 'Choose a product in Shop, select a payment method, submit your request, and our team confirms onboarding and delivery steps.', 'sort_order' => 1],
        ];

        foreach ($items as $item) {
            FaqItem::updateOrCreate(
                ['question' => $item['question']],
                $item + ['is_active' => true]
            );
        }
    }
}
