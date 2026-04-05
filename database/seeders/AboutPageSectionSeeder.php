<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Database\Seeder;

class AboutPageSectionSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'about'],
            ['title' => 'About Us', 'is_published' => true]
        );

        // Don't re-seed if sections already exist
        if ($page->sections()->exists()) {
            $this->command->info('About page sections already exist — skipping.');
            return;
        }

        $sections = [
            [
                'type'        => 'hero',
                'label'       => 'Hero Section',
                'order_index' => 0,
                'is_active'   => true,
                'data'        => [
                    'title'    => 'We Build Digital Futures',
                    'subtitle' => 'Towncore connects ambitious businesses with elite freelance talent to deliver world-class digital projects.',
                ],
            ],
            [
                'type'        => 'story',
                'label'       => 'Our Story',
                'order_index' => 1,
                'is_active'   => true,
                'data'        => [
                    'content' => '<h2>How It All Started</h2><p>Towncore was born from a simple observation: great talent exists everywhere, but finding it and working with it effectively is still incredibly hard. We set out to change that.</p><p>Since our founding, we have connected hundreds of businesses with skilled freelancers, delivering projects across web development, design, marketing, and beyond. Every project we touch is treated with the same care and craftsmanship we would apply to our own work.</p><p>We believe in transparency, quality, and long-term partnerships — not one-off transactions. When you work with Towncore, you are not just hiring a freelancer; you are gaining a dedicated team that is invested in your success.</p>',
                ],
            ],
            [
                'type'        => 'timeline',
                'label'       => 'Our Journey',
                'order_index' => 2,
                'is_active'   => true,
                'data'        => [
                    'heading' => 'Our Journey',
                    'items'   => [
                        ['year' => '2020', 'label' => 'Founded',          'description' => 'Towncore launched with a mission to democratize access to top freelance talent.'],
                        ['year' => '2021', 'label' => 'First 100 Clients', 'description' => 'Reached our first hundred active clients across three continents.'],
                        ['year' => '2022', 'label' => 'Platform Upgrade',  'description' => 'Rebuilt the platform from the ground up with real-time project tracking.'],
                        ['year' => '2023', 'label' => 'Community Launch',  'description' => 'Launched WorkMyWork — a dedicated hub for our freelance community.'],
                        ['year' => '2024', 'label' => '500+ Projects',     'description' => 'Delivered over 500 successful projects worth $2M+ in total client value.'],
                    ],
                ],
            ],
            [
                'type'        => 'services',
                'label'       => 'Our Services',
                'order_index' => 3,
                'is_active'   => true,
                'data'        => [
                    'heading' => 'What We Do',
                    'intro'   => 'From concept to launch, we cover every digital discipline your business needs to thrive online.',
                ],
            ],
            [
                'type'        => 'vision',
                'label'       => 'Our Vision',
                'order_index' => 4,
                'is_active'   => true,
                'data'        => [
                    'heading' => 'A world where talent has no borders.',
                    'content' => 'We envision a future where geography is irrelevant, and where the best person for any job can always be found, trusted, and rewarded fairly. Towncore is our contribution to that future.',
                ],
            ],
            [
                'type'        => 'community',
                'label'       => 'Community Hub',
                'order_index' => 5,
                'is_active'   => true,
                'data'        => [
                    'heading'    => 'Join Our Freelance Community',
                    'content'    => 'WorkMyWork is our dedicated platform for freelancers — a place to find work, grow skills, connect with peers, and build a sustainable independent career. Join thousands of professionals who have already made it their professional home.',
                    'link_label' => 'Visit WorkMyWork',
                    'link_url'   => 'https://workmywork.towncolors.com',
                ],
            ],
            [
                'type'        => 'cta',
                'label'       => 'Call to Action',
                'order_index' => 6,
                'is_active'   => true,
                'data'        => [
                    'title'        => 'Ready to start your next project?',
                    'subtitle'     => "Tell us what you're building and we'll match you with the perfect talent within 24 hours.",
                    'button_label' => 'Start a Project',
                    'button_url'   => '/register',
                ],
            ],
        ];

        foreach ($sections as $attrs) {
            $page->sections()->create($attrs);
        }

        $this->command->info('✓ Seeded ' . count($sections) . ' default sections for the About page.');
    }
}
