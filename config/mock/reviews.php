<?php
// 'pending'  -> tasks currently sitting at status 'pending_review', awaiting an ApprovalRecord decision from the Team Lead.
// 'history'  -> a flattened, most-recent-first feed of past ApprovalRecord rows for this project, used on the "Reviewed history" tab.

return [
    1 => [ // Project Beta
        'pending' => [
            ['id'=>20,'title'=>'Settings Page','stage'=>'Development','revisionRound'=>1,'submitter'=>['name'=>'Demo Developer','role'=>'Developer'],'submittedAt'=>date('Y-m-d H:i:s', strtotime('-3 hours')),'note'=>'Settings form with validation and saved-state feedback is ready.'],
            ['id'=>21,'title'=>'Visual style guide','stage'=>'Design','revisionRound'=>2,'submitter'=>['name'=>'Demo Designer','role'=>'Designer'],'submittedAt'=>date('Y-m-d H:i:s', strtotime('-6 hours')),'note'=>'Applied the logo blue as primary and updated type scale as requested.'],
            ['id'=>22,'title'=>'Mobile navigation mockups','stage'=>'Design','revisionRound'=>1,'submitter'=>['name'=>'Demo Designer','role'=>'Designer'],'submittedAt'=>date('Y-m-d H:i:s', strtotime('-1 day')),'note'=>'Drawer menu and footer mockups for review.'],
            ['id'=>23,'title'=>'Contact form backend','stage'=>'Development','revisionRound'=>1,'submitter'=>['name'=>'Demo Developer','role'=>'Developer'],'submittedAt'=>date('Y-m-d H:i:s', strtotime('-2 days')),'note'=>'PHP handler with server-side validation and error messages.'],
            ['id'=>24,'title'=>'Set up hosting configuration','stage'=>'Development','revisionRound'=>1,'submitter'=>['name'=>'Demo Developer','role'=>'Developer'],'submittedAt'=>date('Y-m-d H:i:s', strtotime('-3 days')),'note'=>'Staging config prepared, awaiting sign-off.'],
            [
                'id'            => 1,
                'title'         => 'Database Schema V2',
                'stage'         => 'Development',
                'revisionRound' => 1,
                'submitter'     => ['name' => 'User Eight', 'role' => 'Developer'],
                'submittedAt'   => date('Y-m-d H:i:s', strtotime('-2 hours')),
                'note'          => 'Resolved foreign key cascading constraints and indexed query parameters for tenant lookup.',
            ],
            [
                'id'            => 2,
                'title'         => 'Auth Edge Cases Documentation',
                'stage'         => 'Requirement Gathering',
                'revisionRound' => 2,
                'submitter'     => ['name' => 'User Nine', 'role' => 'Designer'],
                'submittedAt'   => date('Y-m-d H:i:s', strtotime('-4 hours')),
                'note'          => 'Updated OAuth2 callback failure states and mobile session renewal sequence per team lead notes.',
            ],
            [
                'id'            => 3,
                'title'         => 'Billing Gateway Webhooks',
                'stage'         => 'Development',
                'revisionRound' => 1,
                'submitter'     => ['name' => 'User Eight', 'role' => 'Developer'],
                'submittedAt'   => date('Y-m-d H:i:s', strtotime('-1 day -3 hours')),
                'note'          => 'Implemented exponential backoff retry handler for Stripe webhook notifications.',
            ],
            [
                'id'            => 4,
                'title'         => 'Mobile Responsive Layouts',
                'stage'         => 'Client Design Review',
                'revisionRound' => 1,
                'submitter'     => ['name' => 'User Nine', 'role' => 'Designer'],
                'submittedAt'   => '2026-08-18 10:00:00',
                'note'          => 'Verified drawer navigation and checkout flow breakpoints against Figma specifications.',
            ],
        ],
        'history' => [
            ['task'=>'Requirements document','round'=>1,'decision'=>'approved','date'=>'2026-09-16'],
            ['task'=>'Homepage wireframes','round'=>2,'decision'=>'approved','date'=>'2026-09-23'],
            ['task'=>'Homepage wireframes','round'=>1,'decision'=>'changes_requested','date'=>'2026-09-20'],
            ['task'=>'Kickoff meeting notes','round'=>1,'decision'=>'approved','date'=>'2026-09-09'],
            ['task'=>'Icon set','round'=>1,'decision'=>'rejected','date'=>'2026-09-18'],
            ['task'=>'Visual style guide','round'=>1,'decision'=>'changes_requested','date'=>'2026-09-30'],
            ['task'=>'Contact form backend','round'=>1,'decision'=>'rejected','date'=>'2026-10-01'],
            ['task'=>'Responsive navigation','round'=>1,'decision'=>'approved','date'=>'2026-10-02'],
            ['task' => 'Auth Flow Wireframes',      'round' => 1, 'decision' => 'approved',          'date' => '2026-08-14'],
            ['task' => 'Database Schema V2',        'round' => 1, 'decision' => 'rejected',          'date' => '2026-08-12'],
            ['task' => 'Billing Gateway Integration','round' => 1, 'decision' => 'changes_requested', 'date' => '2026-08-10'],
            ['task' => 'API Authentication Module',  'round' => 1, 'decision' => 'approved',          'date' => '2026-08-06'],
        ],
    ],

    2 => [ // Project Gamma
        'pending' => [
            ['id'=>50,'title'=>'Unit test coverage report','stage'=>'Testing','revisionRound'=>1,'submitter'=>['name'=>'User Twelve','role'=>'Developer'],'submittedAt'=>date('Y-m-d H:i:s', strtotime('-3 hours')),'note'=>'Coverage is now at 82 percent for the core module.'],
            ['id'=>51,'title'=>'Error handling pass','stage'=>'Development','revisionRound'=>2,'submitter'=>['name'=>'User Eleven','role'=>'Developer'],'submittedAt'=>date('Y-m-d H:i:s', strtotime('-2 days')),'note'=>'Added retry logic and user-facing error messages.'],
            [
                'id'            => 5,
                'title'         => 'Payment Gateway Integration',
                'stage'         => 'Development',
                'revisionRound' => 1,
                'submitter'     => ['name' => 'User Eleven', 'role' => 'Developer'],
                'submittedAt'   => date('Y-m-d H:i:s', strtotime('-5 hours')),
                'note'          => 'Stripe checkout wired up for the subscription tier, pending review of failure handling.',
            ],
            [
                'id'            => 6,
                'title'         => 'Onboarding Illustration Set',
                'stage'         => 'Design',
                'revisionRound' => 1,
                'submitter'     => ['name' => 'User Twelve', 'role' => 'Designer'],
                'submittedAt'   => date('Y-m-d H:i:s', strtotime('-1 day')),
                'note'          => 'First pass on the three-step onboarding illustrations, ready for feedback.',
            ],
        ],
        'history' => [
            ['task'=>'Module Architecture','round'=>1,'decision'=>'approved','date'=>'2026-08-05'],
            ['task'=>'Data Layer','round'=>1,'decision'=>'changes_requested','date'=>'2026-08-06'],
            ['task'=>'Data Layer','round'=>2,'decision'=>'approved','date'=>'2026-08-08'],
            ['task'=>'Integration Endpoints','round'=>1,'decision'=>'rejected','date'=>'2026-08-10'],
            ['task' => 'Subscription Plan Schema', 'round' => 1, 'decision' => 'approved', 'date' => '2026-08-11'],
            ['task' => 'Checkout UI Draft',        'round' => 2, 'decision' => 'approved', 'date' => '2026-08-09'],
        ],
    ],

    3 => [
        'pending' => [
            ['id'=>30,'title'=>'Final handoff checklist','stage'=>'Delivery and Handoff','revisionRound'=>1,'submitter'=>['name'=>'Demo Developer','role'=>'Developer'],'submittedAt'=>date('Y-m-d H:i:s', strtotime('-2 hours')),'note'=>'All deliverables ticked off, DNS and SSL verified.'],
            ['id'=>31,'title'=>'Client training session','stage'=>'Delivery and Handoff','revisionRound'=>2,'submitter'=>['name'=>'Demo Developer','role'=>'Developer'],'submittedAt'=>date('Y-m-d H:i:s', strtotime('-1 day')),'note'=>'Updated walkthrough slides after last feedback.'],
            ['id'=>32,'title'=>'Deployment runbook','stage'=>'Testing','revisionRound'=>1,'submitter'=>['name'=>'Demo Developer','role'=>'Developer'],'submittedAt'=>date('Y-m-d H:i:s', strtotime('-2 days')),'note'=>'Step-by-step runbook with rollback procedure.'],
        ],
        'history' => [
            ['task'=>'Final handoff checklist','round'=>1,'decision'=>'changes_requested','date'=>'2026-10-12'],
            ['task'=>'Admin panel QA','round'=>1,'decision'=>'approved','date'=>'2026-10-08'],
            ['task'=>'Backup verification','round'=>1,'decision'=>'approved','date'=>'2026-10-05'],
            ['task'=>'Client training session','round'=>1,'decision'=>'rejected','date'=>'2026-10-14'],
        ],
    ],

    4 => [
        'pending' => [
            ['id'=>40,'title'=>'Landing page layouts','stage'=>'Design','revisionRound'=>1,'submitter'=>['name'=>'Demo Developer','role'=>'Developer'],'submittedAt'=>date('Y-m-d H:i:s', strtotime('-4 hours')),'note'=>'Three layout options with notes on trade-offs.'],
            ['id'=>41,'title'=>'CMS content model','stage'=>'Design','revisionRound'=>1,'submitter'=>['name'=>'Demo Developer','role'=>'Developer'],'submittedAt'=>date('Y-m-d H:i:s', strtotime('-1 day')),'note'=>'Page types and field definitions.'],
            ['id'=>42,'title'=>'Hero banner variants','stage'=>'Design','revisionRound'=>2,'submitter'=>['name'=>'Demo Developer','role'=>'Developer'],'submittedAt'=>date('Y-m-d H:i:s', strtotime('-2 days')),'note'=>'Revised banners with smaller file sizes.'],
        ],
        'history' => [
            ['task'=>'Brand audit','round'=>1,'decision'=>'approved','date'=>'2026-10-01'],
            ['task'=>'Sitemap','round'=>1,'decision'=>'approved','date'=>'2026-10-03'],
            ['task'=>'Hero banner variants','round'=>1,'decision'=>'changes_requested','date'=>'2026-10-09'],
            ['task'=>'Footer redesign','round'=>1,'decision'=>'rejected','date'=>'2026-10-06'],
        ],
    ],

    5 => [ // Project Delta (archived)
        'pending' => [],
        'history' => [
            ['task'=>'Legacy System Audit','round'=>1,'decision'=>'approved','date'=>'2025-11-11'],
            ['task'=>'Data Migration Plan','round'=>1,'decision'=>'changes_requested','date'=>'2025-11-13'],
            ['task'=>'Data Migration Plan','round'=>2,'decision'=>'approved','date'=>'2025-11-15'],
            ['task'=>'Migration Scripts','round'=>1,'decision'=>'approved','date'=>'2025-12-06'],
            ['task'=>'Dry-Run Migration','round'=>1,'decision'=>'rejected','date'=>'2025-12-10'],
            ['task'=>'Dry-Run Migration','round'=>2,'decision'=>'approved','date'=>'2025-12-13'],
            ['task'=>'Sign-off Review','round'=>1,'decision'=>'approved','date'=>'2026-01-16'],
        ],
    ],

    6 => [ // Project Epsilon (closed)
        'pending' => [],
        'history' => [
            ['task'=>'Compliance Scope Definition','round'=>1,'decision'=>'approved','date'=>'2025-09-06'],
            ['task'=>'Vulnerability Assessment','round'=>1,'decision'=>'changes_requested','date'=>'2025-09-22'],
            ['task'=>'Vulnerability Assessment','round'=>2,'decision'=>'approved','date'=>'2025-09-25'],
            ['task'=>'Remediation Plan','round'=>1,'decision'=>'approved','date'=>'2025-09-29'],
            ['task'=>'Final Report Sign-off','round'=>1,'decision'=>'approved','date'=>'2025-10-06'],
        ],
    ],

    7 => [ // Project Beta
        'pending' => [
            ['id'=>70,'title'=>'Storefront product pages','stage'=>'Development','revisionRound'=>1,'submitter'=>['name'=>'Demo Developer','role'=>'Developer'],'submittedAt'=>date('Y-m-d H:i:s', strtotime('-5 hours')),'note'=>'Listing and detail pages built to the approved design.'],
            ['id'=>71,'title'=>'Checkout form validation','stage'=>'Development','revisionRound'=>1,'submitter'=>['name'=>'Demo Developer','role'=>'Developer'],'submittedAt'=>date('Y-m-d H:i:s', strtotime('-1 day')),'note'=>'Address and payment field validation in place.'],
        ],
        'history' => [
            ['task'=>'Homepage wireframes','round'=>1,'decision'=>'approved','date'=>'2026-09-12'],
            ['task'=>'Product page mockups','round'=>1,'decision'=>'changes_requested','date'=>'2026-09-18'],
            ['task'=>'Product page mockups','round'=>2,'decision'=>'approved','date'=>'2026-09-21'],
        ],
    ],

    8 => [ // Site Refresh
        'pending' => [
            ['id'=>80,'title'=>'Refresh homepage banners','stage'=>'Design','revisionRound'=>1,'submitter'=>['name'=>'Demo Developer','role'=>'Designer'],'submittedAt'=>date('Y-m-d H:i:s', strtotime('-6 hours')),'note'=>'Three seasonal banner options with updated hero copy.'],
        ],
        'history' => [
            ['task'=>'Content audit','round'=>1,'decision'=>'approved','date'=>'2026-09-10'],
            ['task'=>'Banner concepts','round'=>1,'decision'=>'rejected','date'=>'2026-09-24'],
        ],
    ],
];
