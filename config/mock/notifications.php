<?php

// 'items'  -> short list used by the navbar bell dropdown (kept small on purpose)
// 'feed'   -> richer list used by the full /notifications page
//
// Each feed item:
//   category    'tasks' | 'approvals' | 'chat' | 'payments'
//   icon        renderIcon() name
//   tone        drives the icon circle color (success, warning, info, primary, pink, danger, neutral)
//   message     plain text, **wrapped like this** renders as <strong>
//   context     optional small tag shown under the message (e.g. a stage name)
//   contextTone optional tone for that tag, defaults to link-style text
//   quote       optional quoted snippet (comment/feedback text)
//   quoteTone   optional tone, 'danger' renders the quote/label in the warning color
//   href        where the row links to
//   is_read     bool
//   created_at  datetime string, used for day-grouping and relative time

return [
    'unreadCount' => 4,
    'items' => [
        ['message' => 'Your task "Homepage Design" was approved', 'icon' => 'circle-check', 'is_read' => false, 'created_at' => '12m ago', 'href' => '/tasks'],
        ['message' => '3 tasks are now unlocked in Stage: Development', 'icon' => 'lock', 'is_read' => false, 'created_at' => '45m ago', 'href' => '/tasks'],
        ['message' => 'Priya Nair mentioned you in #client-feedback', 'icon' => 'chat', 'is_read' => false, 'created_at' => '3h ago', 'href' => '/chat'],
        ['message' => 'Payment milestone "Initial Sprint Delivery" was requested', 'icon' => 'payment', 'is_read' => true, 'created_at' => 'Yesterday', 'href' => '/payment'],
    ],

    'feed' => [
        [
            'category'   => 'approvals',
            'icon'       => 'circle-check',
            'tone'       => 'success',
            'message'    => 'Your task **"Homepage Design"** was approved by **Sam Osei**.',
            'href'       => '/tasks',
            'is_read'    => false,
            'created_at' => date('Y-m-d H:i:s', strtotime('-12 minutes')),
        ],
        [
            'category'   => 'tasks',
            'icon'       => 'lock',
            'tone'       => 'warning',
            'message'    => '**3 tasks** are now unlocked in **Stage: Development** following API Authentication approval.',
            'href'       => '/tasks',
            'is_read'    => false,
            'created_at' => date('Y-m-d H:i:s', strtotime('-45 minutes')),
        ],
        [
            'category'   => 'tasks',
            'icon'       => 'user-plus',
            'tone'       => 'info',
            'message'    => 'You were assigned to **"Tenant Isolation Middleware"** by **Alex Chen**.',
            'context'    => 'Backend Architecture',
            'href'       => '/tasks',
            'is_read'    => false,
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours')),
        ],
        [
            'category'   => 'chat',
            'icon'       => 'chat',
            'tone'       => 'pink',
            'message'    => '**Priya Nair** mentioned you in **#client-feedback**.',
            'quote'      => 'Could you check the hero contrast on mobile?',
            'href'       => '/chat',
            'is_read'    => false,
            'created_at' => date('Y-m-d H:i:s', strtotime('-3 hours')),
        ],
        [
            'category'   => 'approvals',
            'icon'       => 'flag',
            'tone'       => 'primary',
            'message'    => '**Stage 2: Design** advanced to Pending Completion &mdash; joint sign-off is ready.',
            'href'       => '/review',
            'is_read'    => true,
            'created_at' => date('Y-m-d H:i:s', strtotime('-5 hours')),
        ],
        [
            'category'   => 'approvals',
            'icon'       => 'circle-alert',
            'tone'       => 'danger',
            'message'    => '**"Database Schema V2"** &mdash; changes requested by **Sam Osei**.',
            'quote'      => 'Need explicit error codes defined for expired refresh tokens.',
            'context'    => 'Re-review required',
            'contextTone' => 'danger',
            'href'       => '/review',
            'is_read'    => true,
            // Fixed clock times (rather than "-N hours") so these stay inside
            // yesterday's calendar day regardless of what time it is right now.
            'created_at' => date('Y-m-d', strtotime('-1 day')) . ' 16:15:00',
        ],
        [
            'category'   => 'payments',
            'icon'       => 'payment',
            'tone'       => 'warning',
            'message'    => 'Payment milestone **"Initial Sprint Delivery ($500.00)"** was requested for **Client Portal Redesign**.',
            'href'       => '/payment',
            'is_read'    => true,
            'created_at' => date('Y-m-d', strtotime('-1 day')) . ' 14:30:00',
        ],
        [
            'category'   => 'tasks',
            'icon'       => 'circle-alert',
            'tone'       => 'danger',
            'message'    => '**"Auth Edge Cases Documentation"** is past deadline and has been escalated to the Manager.',
            'href'       => '/tasks',
            'is_read'    => true,
            'created_at' => date('Y-m-d', strtotime('-1 day')) . ' 10:00:00',
        ],
    ],
];