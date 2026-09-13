<?php

return [

    7 => [
        'needsReview' => [
            [
                'type'        => 'task',
                'title'       => 'Homepage Design',
                'meta'        => 'Ready for your feedback',
                'description' => 'The initial high-fidelity mockups for the main landing page, incorporating the new warm neutral color palette and fluid branding elements. Please review the hero section flow.',
                'href'        => '/client-portal/reviews',
            ],
            [
                'type'        => 'stage',
                'title'       => 'Stage 2 Architecture',
                'meta'        => 'Ready for approval',
                'description' => 'Proposed database schemas and API endpoints for the user authentication flow. Focuses on robust security measures while maintaining low latency across global regions.',
                'href'        => '/client-portal/reviews',
            ],
        ],

        'payment' => [
            'status'  => 'due',
            'amount'  => 500.00,
            'dueDate' => '2026-12-15',
        ],

        'hero' => [
            'type'    => 'final_delivery_ready',
            'heading' => 'Your project is ready for final delivery',
            'body'    => 'All core development and testing phases have been completed successfully. The final build artifacts, design source files, and comprehensive documentation are prepared and awaiting your final sign-off.',
        ],
    ],

    8 => [
        'needsReview' => [],
        'payment'     => null,
        'hero'        => null,
    ],

];