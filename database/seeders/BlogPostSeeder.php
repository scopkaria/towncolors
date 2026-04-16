<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        // Seed default categories
        $categories = collect([
            'Technology',
            'Business Growth',
            'Digital Marketing',
            'Software Trends',
        ])->mapWithKeys(fn ($name) => [
            $name => BlogCategory::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            ),
        ]);

        $posts = [
            [
                'title' => 'How To Scope A Business Software Project Without Wasting Budget',
                'slug' => 'scope-business-software-project-without-wasting-budget',
                'meta_title' => 'How To Scope A Business Software Project Without Wasting Budget',
                'meta_description' => 'A practical framework for defining scope, timelines, risks, and technical assumptions before starting custom software development.',
                'content' => '<h2>Why scope matters</h2><p>Most software delays come from unclear assumptions. Teams begin with broad goals, but no defined workflows, no measurable outputs, and no ownership map. Scope creates decision boundaries so engineering can move quickly without rework.</p><h2>A simple scoping framework</h2><ul><li>Define the business event that starts the workflow.</li><li>Define each role that touches the process.</li><li>Define the final output and where it is stored.</li><li>Define the approval steps and exception paths.</li><li>Define reporting metrics and update cadence.</li></ul><h2>Budget guardrails</h2><p>Break implementation into phases: core workflow first, automation second, advanced reporting third. This keeps budget tied to usable outcomes and avoids overbuilding on day one.</p><h2>What to prepare before kickoff</h2><p>Bring sample forms, current spreadsheets, approval hierarchy, and top three pain points by financial impact. With these, your product team can design an implementation roadmap in days, not months.</p>',
                'published_at' => Carbon::now()->subDays(10),
                'cats' => ['Technology', 'Business Growth'],
                'tags' => ['software development', 'project scoping', 'budgeting'],
            ],
            [
                'title' => 'Cloud Hosting Checklist For Growing Teams',
                'slug' => 'cloud-hosting-checklist-for-growing-teams',
                'meta_title' => 'Cloud Hosting Checklist For Growing Teams',
                'meta_description' => 'A practical cloud readiness checklist covering security, backup, uptime, and release controls for production applications.',
                'content' => '<h2>Baseline infrastructure decisions</h2><p>Before launch, choose your deployment model, backup window, and log retention period. These foundational decisions reduce surprise outages and simplify incident response.</p><h2>Security essentials</h2><ul><li>Use role-based access with least privilege.</li><li>Rotate credentials and API keys on schedule.</li><li>Enforce HTTPS and secure session management.</li><li>Patch OS and framework dependencies monthly.</li></ul><h2>Operational readiness</h2><p>Set uptime checks, alert channels, and response ownership. Every critical service should have at least one rollback path and one tested restore path.</p><h2>Performance hygiene</h2><p>Track slow queries, cache hit rate, queue delays, and response time percentiles. Performance tuning is a process, not a one-time event.</p>',
                'published_at' => Carbon::now()->subDays(8),
                'cats' => ['Technology'],
                'tags' => ['cloud hosting', 'devops', 'security'],
            ],
            [
                'title' => 'Designing Service Pages That Convert Leads Into Real Projects',
                'slug' => 'designing-service-pages-that-convert-leads-into-real-projects',
                'meta_title' => 'Designing Service Pages That Convert Leads Into Real Projects',
                'meta_description' => 'Learn the structure of high-performing service pages: positioning, proof, process clarity, and conversion-focused CTA blocks.',
                'content' => '<h2>Lead conversion starts with clarity</h2><p>Visitors do not convert because of design alone. They convert when they quickly understand what you do, who it is for, and how to start.</p><h2>Essential page blocks</h2><ol><li>Problem statement connected to business impact.</li><li>Service outcomes with measurable benefits.</li><li>Proof section with portfolio and client context.</li><li>Process timeline with delivery phases.</li><li>Primary and secondary call-to-action.</li></ol><h2>Improve trust signals</h2><p>Add realistic duration and pricing ranges where possible. Transparent expectations reduce low-quality leads and improve client readiness.</p><h2>Conversion optimization tip</h2><p>Use one clear primary CTA per section. Too many options increase hesitation and lower completion rate.</p>',
                'published_at' => Carbon::now()->subDays(6),
                'cats' => ['Digital Marketing', 'Business Growth'],
                'tags' => ['landing pages', 'conversion', 'lead generation'],
            ],
            [
                'title' => 'Choosing Between Custom Software And Off-The-Shelf Tools',
                'slug' => 'choosing-between-custom-software-and-off-the-shelf-tools',
                'meta_title' => 'Choosing Between Custom Software And Off-The-Shelf Tools',
                'meta_description' => 'How to evaluate when custom software is justified and when existing SaaS tools are enough for your current stage.',
                'content' => '<h2>Start with workflow complexity</h2><p>If your process is highly standardized, off-the-shelf tools are usually enough. If your process creates competitive advantage and contains complex approvals, custom software becomes more valuable.</p><h2>Decision criteria</h2><ul><li>How often does your process change?</li><li>Do you need strict ownership and data controls?</li><li>Are manual workarounds costing significant time?</li><li>Will integration requirements keep expanding?</li></ul><h2>Total cost perspective</h2><p>License fees look cheaper at first, but operational friction can become expensive. Compare implementation cost against annual productivity losses and error exposure.</p><h2>Hybrid strategy</h2><p>Many teams combine both: SaaS for non-core functions, custom modules for critical workflow and reporting layers.</p>',
                'published_at' => Carbon::now()->subDays(4),
                'cats' => ['Software Trends', 'Business Growth'],
                'tags' => ['SaaS', 'custom software', 'strategy'],
            ],
            [
                'title' => 'Building A Reliable Delivery Workflow For Client Projects',
                'slug' => 'building-reliable-delivery-workflow-for-client-projects',
                'meta_title' => 'Building A Reliable Delivery Workflow For Client Projects',
                'meta_description' => 'A practical delivery system for agencies and product teams covering handover, communication cadence, and quality checkpoints.',
                'content' => '<h2>Delivery reliability is a system</h2><p>Teams that deliver consistently follow clear routines. They do not rely on heroics. They use structured checkpoints, issue ownership, and communication cadence.</p><h2>Weekly operating rhythm</h2><ul><li>Monday: priorities and blockers.</li><li>Midweek: progress and risk review.</li><li>Friday: client-facing summary and next actions.</li></ul><h2>Quality checkpoints</h2><p>Set acceptance criteria before development starts. Validate against those criteria before every handover. This avoids subjective review loops.</p><h2>Client communication</h2><p>Keep updates concise: completed work, current risks, and the exact decision needed from the client. Decision clarity reduces timeline drift.</p>',
                'published_at' => Carbon::now()->subDays(2),
                'cats' => ['Business Growth'],
                'tags' => ['project management', 'delivery', 'agency'],
            ],
            [
                'title' => 'SEO Foundations Every Business Website Needs In 2026',
                'slug' => 'seo-foundations-every-business-website-needs-in-2026',
                'meta_title' => 'SEO Foundations Every Business Website Needs In 2026',
                'meta_description' => 'A practical SEO implementation guide covering technical setup, content structure, and on-page signals for sustainable rankings.',
                'content' => '<h2>Start with technical readiness</h2><p>SEO performance starts before content. Ensure crawlable architecture, clear canonical tags, valid sitemap submission, and fast page rendering across mobile devices. These are baseline conditions, not optional enhancements.</p><h2>Content architecture that scales</h2><p>Build clear topic clusters around your core services. Each cluster should include one authoritative pillar page and supporting pages that answer specific user intent questions. Internal links should move users deeper into relevant solutions naturally.</p><h2>On-page essentials</h2><ul><li>One clear H1 per page aligned to search intent.</li><li>Specific meta titles and descriptions, not duplicated templates.</li><li>Structured headings that mirror reader decision flow.</li><li>Image optimization with meaningful alt text and compressed assets.</li><li>Schema markup where appropriate for business and article content.</li></ul><h2>Measure what matters</h2><p>Track qualified clicks, indexed pages, and lead-quality outcomes, not just impressions. SEO is valuable when it compounds trusted visibility into conversations and revenue.</p>',
                'published_at' => Carbon::now()->subDays(12),
                'cats' => ['Digital Marketing'],
                'tags' => ['SEO', 'technical SEO', 'content strategy'],
            ],
            [
                'title' => 'How To Plan A Website Redesign Without Losing Existing Traffic',
                'slug' => 'how-to-plan-a-website-redesign-without-losing-existing-traffic',
                'meta_title' => 'How To Plan A Website Redesign Without Losing Existing Traffic',
                'meta_description' => 'Learn a proven redesign migration process that protects rankings, preserves URLs, and improves conversion clarity.',
                'content' => '<h2>Redesign risk is usually migration risk</h2><p>Most traffic drops after redesign happen because URL structures, metadata, and internal links are changed without a controlled migration plan. Design changes are rarely the direct cause; broken relevance signals are.</p><h2>Pre-redesign audit checklist</h2><ul><li>Export current top pages by organic traffic and conversions.</li><li>Map every existing URL to its new URL destination.</li><li>Preserve high-performing titles and page intent where valid.</li><li>Document internal links to critical conversion pages.</li><li>Capture baseline technical metrics before launch.</li></ul><h2>Launch controls</h2><p>Implement 301 redirects, regenerate sitemap.xml, and run crawl validation immediately after release. Check index coverage and monitor Search Console warnings daily in the first two weeks.</p><h2>Post-launch optimization</h2><p>Use behavior data to improve clarity and conversion paths. Redesign should improve both visibility and business action, not aesthetics alone.</p>',
                'published_at' => Carbon::now()->subDays(11),
                'cats' => ['Digital Marketing', 'Technology'],
                'tags' => ['website redesign', 'migration', 'SEO'],
            ],
            [
                'title' => 'Content Marketing For Service Companies: A Practical Execution Model',
                'slug' => 'content-marketing-for-service-companies-practical-execution-model',
                'meta_title' => 'Content Marketing For Service Companies: A Practical Execution Model',
                'meta_description' => 'A repeatable content workflow for service businesses to attract qualified leads through educational, intent-driven publishing.',
                'content' => '<h2>Why random content fails</h2><p>Posting frequently without strategy creates activity but not demand. Service businesses need content that aligns with buyer questions at each decision stage, from problem awareness to vendor evaluation.</p><h2>Three-layer content system</h2><ol><li>Authority content: long-form guides proving expertise.</li><li>Decision content: comparisons, frameworks, and pricing context.</li><li>Trust content: case studies, process walkthroughs, and outcomes.</li></ol><h2>Editorial cadence</h2><p>Publish fewer but stronger assets. One high-quality pillar article plus two focused support pieces per month often outperforms low-depth weekly posts.</p><h2>Distribution discipline</h2><p>Repurpose each article into social snippets, email summaries, and sales enablement materials. The same core insight should support marketing, sales, and onboarding conversations.</p>',
                'published_at' => Carbon::now()->subDays(9),
                'cats' => ['Digital Marketing', 'Business Growth'],
                'tags' => ['content marketing', 'inbound', 'lead generation'],
            ],
            [
                'title' => 'Security Practices For Client Portals And File Sharing Systems',
                'slug' => 'security-practices-for-client-portals-and-file-sharing-systems',
                'meta_title' => 'Security Practices For Client Portals And File Sharing Systems',
                'meta_description' => 'A practical security checklist for protecting sensitive client files, communication channels, and account access in web portals.',
                'content' => '<h2>Client portals carry trust risk</h2><p>When users upload contracts, financial records, or strategy documents, your platform becomes a trust boundary. Security controls must be visible in architecture and operations.</p><h2>Access and identity controls</h2><ul><li>Enforce strong password policy and optional MFA.</li><li>Use role-based permissions per workspace and project.</li><li>Expire inactive sessions and revoke stale tokens.</li><li>Audit login history and suspicious access attempts.</li></ul><h2>File handling safeguards</h2><p>Validate file types, enforce size limits, and store files outside public paths. Generate signed, temporary URLs for previews/downloads rather than permanent direct links.</p><h2>Operational protections</h2><p>Maintain encryption in transit, regular backups, and incident playbooks. Security maturity is shown by preparedness, not only prevention.</p>',
                'published_at' => Carbon::now()->subDays(7),
                'cats' => ['Technology'],
                'tags' => ['security', 'client portal', 'file sharing'],
            ],
            [
                'title' => 'Choosing The Right KPIs For Software Projects',
                'slug' => 'choosing-the-right-kpis-for-software-projects',
                'meta_title' => 'Choosing The Right KPIs For Software Projects',
                'meta_description' => 'Define software KPIs that connect technical delivery to business outcomes, adoption quality, and operational efficiency.',
                'content' => '<h2>Vanity metrics create false confidence</h2><p>Teams often track commits, story points, and release counts while ignoring outcomes that matter to stakeholders. Good KPIs connect platform behavior to operational and revenue impact.</p><h2>KPI categories to track</h2><ul><li>Adoption: active users, feature usage depth, retention.</li><li>Operations: task completion time, error rates, rework reduction.</li><li>Commercial: lead conversion lift, cost-to-serve reduction, revenue velocity.</li><li>Reliability: uptime, incident frequency, mean time to recovery.</li></ul><h2>Set baseline before launch</h2><p>Measure current process performance first. Without baseline, post-launch improvements are hard to verify and harder to communicate to decision-makers.</p><h2>Review cycle</h2><p>Establish monthly KPI reviews with shared ownership between product, operations, and leadership. Metrics should guide roadmap decisions, not just reporting slides.</p>',
                'published_at' => Carbon::now()->subDays(5),
                'cats' => ['Business Growth', 'Software Trends'],
                'tags' => ['KPIs', 'metrics', 'product management'],
            ],
            [
                'title' => 'From Idea To MVP: How To Launch Faster With Lower Risk',
                'slug' => 'from-idea-to-mvp-how-to-launch-faster-with-lower-risk',
                'meta_title' => 'From Idea To MVP: How To Launch Faster With Lower Risk',
                'meta_description' => 'A practical MVP launch framework that helps founders validate value quickly without overbuilding early product scope.',
                'content' => '<h2>MVP is a learning tool, not a small final product</h2><p>The goal of an MVP is to test whether your core value proposition solves a real problem for a clear user group. It should answer critical assumptions quickly.</p><h2>Define launch assumptions</h2><ul><li>Who is the first user segment?</li><li>What is the exact problem statement?</li><li>What behavior proves product value?</li><li>What data will determine next iteration?</li></ul><h2>Scope for speed</h2><p>Build only the path needed for a user to reach value once. Delay edge-case automation, advanced analytics, and deep customization until usage validates demand.</p><h2>Post-launch loops</h2><p>Collect usage and interview insights weekly. Convert repeating friction points into roadmap priorities and keep iteration cycles short.</p>',
                'published_at' => Carbon::now()->subDay(),
                'cats' => ['Software Trends', 'Technology'],
                'tags' => ['MVP', 'startup', 'product launch'],
            ],
        ];

        foreach ($posts as $data) {
            $post = Post::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'title' => $data['title'],
                    'content' => $data['content'],
                    'meta_title' => $data['meta_title'],
                    'meta_description' => $data['meta_description'],
                    'status' => 'published',
                    'published_at' => $data['published_at'],
                ]
            );

            // Sync categories
            if (! empty($data['cats'])) {
                $catIds = collect($data['cats'])
                    ->map(fn ($name) => $categories[$name]->id ?? null)
                    ->filter();
                $post->categories()->sync($catIds);
            }

            // Sync tags
            if (! empty($data['tags'])) {
                $tagIds = collect($data['tags'])->map(
                    fn ($name) => BlogTag::firstOrCreate(
                        ['slug' => Str::slug($name)],
                        ['name' => $name]
                    )->id
                );
                $post->tags()->sync($tagIds);
            }
        }
    }
}
