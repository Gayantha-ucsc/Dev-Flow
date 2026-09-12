<?php

return [

    1 => [ // Project Beta - decided by User Seven (Team Lead)
        'approvalHistory' => [
            ['date' => '2026-03-25', 'item' => 'Final Design Sign-off', 'stage' => 'Client Design Review', 'decision' => 'approved', 'decidedBy' => 'User Seven', 'feedback' => 'Client walkthrough concerns addressed, sign-off granted.'],
            ['date' => '2026-03-13', 'item' => 'Design Handoff Notes',  'stage' => 'Design',                'decision' => 'approved', 'decidedBy' => 'User Seven', 'feedback' => 'Handoff notes are clear, development can proceed.'],
            ['date' => '2026-03-10', 'item' => 'Component Library',     'stage' => 'Design',                'decision' => 'approved', 'decidedBy' => 'User Seven', 'feedback' => 'Tokens match the design system, ready to build from.'],
            ['date' => '2026-02-15', 'item' => 'Success Metrics Definition', 'stage' => 'Requirement Gathering', 'decision' => 'approved', 'decidedBy' => 'User Seven', 'feedback' => 'Metrics agreed with stakeholders, moving to design.'],
        ],
    ],

    2 => [ // Project Gamma - decided by User Ten (Team Lead)
        'approvalHistory' => [
            ['date' => '2026-04-18', 'item' => 'Code Review Pass',   'stage' => 'Development', 'decision' => 'approved', 'decidedBy' => 'User Ten', 'feedback' => 'Clean diff, no outstanding comments.'],
            ['date' => '2026-04-01', 'item' => 'Module Architecture', 'stage' => 'Development', 'decision' => 'approved', 'decidedBy' => 'User Ten', 'feedback' => 'Approved as proposed, matches the agreed module boundaries.'],
            ['date' => '2026-03-14', 'item' => 'Design QA',           'stage' => 'Design',      'decision' => 'approved', 'decidedBy' => 'User Ten', 'feedback' => 'No visual regressions found against the mockups.'],
        ],
    ],

    3 => [ // Project Alpha - decided by User One (Manager, holding the
           // Team Lead role too per FR-2.1.2 since they created the project)
        'approvalHistory' => [
            ['date' => '2026-03-27', 'item' => 'Bug Fixes Round 1',       'stage' => 'Testing',              'decision' => 'approved', 'decidedBy' => 'User One', 'feedback' => 'Fixes verified against UAT feedback, cleared for delivery.'],
            ['date' => '2026-03-25', 'item' => 'UAT with Client',         'stage' => 'Testing',              'decision' => 'approved', 'decidedBy' => 'User One', 'feedback' => 'Client sign-off received during the UAT session, no blockers.'],
            ['date' => '2026-03-17', 'item' => 'Security Audit',          'stage' => 'Testing',              'decision' => 'approved', 'decidedBy' => 'User One', 'feedback' => 'No critical findings, two low-severity items logged for backlog.'],
            ['date' => '2026-03-06', 'item' => 'Code Review & Cleanup',   'stage' => 'Development',          'decision' => 'approved', 'decidedBy' => 'User One', 'feedback' => 'Codebase matches the style guide, nothing outstanding.'],
            ['date' => '2026-02-04', 'item' => 'Design Sign-off',         'stage' => 'Design',                'decision' => 'approved', 'decidedBy' => 'User One', 'feedback' => 'Responsive pass approved, ready for development handoff.'],
            ['date' => '2026-01-20', 'item' => 'Project Charter Sign-off','stage' => 'Discovery',            'decision' => 'approved', 'decidedBy' => 'User One', 'feedback' => 'Charter matches stakeholder requirements, cleared to start design.'],
        ],
    ],

    4 => [ // Marketing Site Redesign - decided by User Twenty-Four (Team Lead)
        'approvalHistory' => [
            ['date' => '2026-05-20', 'item' => 'Homepage Mockup',   'stage' => 'Design',                'decision' => 'approved', 'decidedBy' => 'User Twenty-Four', 'feedback' => 'On-brand and matches the approved moodboard direction.'],
            ['date' => '2026-05-10', 'item' => 'Sitemap Draft',     'stage' => 'Requirement Gathering', 'decision' => 'approved', 'decidedBy' => 'User Twenty-Four', 'feedback' => 'Structure agreed, content team signed off.'],
        ],
    ],

    5 => [ // Project Delta - archived, decided by User One
        'approvalHistory' => [
            ['date' => '2026-01-25', 'item' => 'Archive & Close Project', 'stage' => 'Delivery and Handoff', 'decision' => 'approved', 'decidedBy' => 'User One', 'feedback' => 'All decommission steps verified, safe to archive.'],
            ['date' => '2026-01-15', 'item' => 'Sign-off Review',         'stage' => 'Testing',              'decision' => 'approved', 'decidedBy' => 'User One', 'feedback' => 'Rollback drill passed, no data integrity issues found.'],
        ],
    ],

    6 => [ // Project Epsilon - closed, decided by User One
        'approvalHistory' => [
            ['date' => '2025-10-05', 'item' => 'Final Report Sign-off', 'stage' => 'Delivery and Handoff', 'decision' => 'approved', 'decidedBy' => 'User One', 'feedback' => 'Compliance report accepted by the client, ready to close out.'],
        ],
    ],

];