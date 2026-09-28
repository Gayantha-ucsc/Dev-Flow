<?php
// Hardcoded extra detail for demo tasks, keyed by task name. Read-only display data.
// Tasks not listed here still get a detail page built from config/mock/project-tasks.php.
return [
    'Settings Page' => [
        'description' => 'Build the account settings page: profile fields, password change and notification preferences.',
        'createdBy'   => 'Demo Team Lead',
        'notes'       => [['name' => 'User Thirty-Four', 'at' => '2026-04-18 10:15', 'text' => 'Form layout done, wiring up validation messages.']],
        'rounds'      => [['n' => 1, 'started' => '2026-04-20 09:00', 'closed' => null]],
        'reviews'     => [],
    ],
    'Core Dashboard UI' => [
        'description' => 'Implement the approved dashboard design in HTML/CSS with the widget grid.',
        'createdBy'   => 'Demo Team Lead',
        'notes'       => [['name' => 'User Thirty-Four', 'at' => '2026-04-10 14:00', 'text' => 'Grid and cards finished, checking spacing against the mockups.']],
        'rounds'      => [['n' => 1, 'started' => '2026-04-11 09:00', 'closed' => '2026-04-12 11:00'], ['n' => 2, 'started' => '2026-04-13 09:00', 'closed' => '2026-04-14 16:00']],
        'reviews'     => [
            ['decision' => 'changes_requested', 'by' => 'Demo Team Lead', 'at' => '2026-04-12 11:00', 'feedback' => 'Card spacing is inconsistent. Please follow the design tokens.'],
            ['decision' => 'approved', 'by' => 'Demo Team Lead', 'at' => '2026-04-14 16:00', 'feedback' => 'Matches the design now. Approved.'],
        ],
    ],
    'Email Templates' => [
        'description' => 'HTML email templates for notifications and password reset.',
        'createdBy'   => 'Demo Team Lead',
        'blocked'     => 'Waiting for the Notification Service to finalise its payload format.',
        'notes'       => [['name' => 'Demo Team Lead', 'at' => '2026-04-20 09:30', 'text' => 'Marked as blocked until the payload format is agreed.']],
        'rounds'      => [], 'reviews' => [],
    ],
    'Database Schema' => [
        'description' => 'Design and create the MySQL schema, constraints and seed data.',
        'createdBy'   => 'Demo Team Lead',
        'notes'       => [['name' => 'User Thirty-Three', 'at' => '2026-04-02 15:00', 'text' => 'Schema created, triggers tested.']],
        'rounds'      => [['n' => 1, 'started' => '2026-04-03 09:00', 'closed' => '2026-04-04 12:00']],
        'reviews'     => [['decision' => 'approved', 'by' => 'Demo Team Lead', 'at' => '2026-04-04 12:00', 'feedback' => 'Clean schema, constraints look right.']],
    ],
];
