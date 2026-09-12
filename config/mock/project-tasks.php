<?php
// All assignees use the same "User N" placeholder convention as
// team-members.php (was previously a mix of placeholder and invented
// real-sounding names, which read as two different rosters). These are
// a separate numbering range (32+) from team-members.php's numbers,
// since nothing elsewhere ties a specific task-doer to a specific
// ProjectMember row yet - same convention, not (yet) the same identity.

return [

    1 => [ // Project Beta
        0 => [ // Requirement Gathering - completed, 4/4
            ['name' => 'Stakeholder Interviews',     'type' => 'research', 'status' => 'approved', 'assignees' => ['User Thirty-Two'], 'deadline' => '2026-02-10', 'dependsOn' => []],
            ['name' => 'Requirements Document',      'type' => 'research', 'status' => 'approved', 'assignees' => ['User Thirty-Two'], 'deadline' => '2026-02-12', 'dependsOn' => ['Stakeholder Interviews']],
            ['name' => 'Technical Feasibility Study','type' => 'research', 'status' => 'approved', 'assignees' => ['User Thirty-Three'], 'deadline' => '2026-02-14', 'dependsOn' => []],
            ['name' => 'Success Metrics Definition', 'type' => 'research', 'status' => 'approved', 'assignees' => ['User Thirty-Two'], 'deadline' => '2026-02-15', 'dependsOn' => ['Requirements Document']],
        ],
        1 => [ // Design - completed, 6/6
            ['name' => 'Wireframes',              'type' => 'design', 'status' => 'approved', 'assignees' => ['User Thirty-Four'], 'deadline' => '2026-03-01', 'dependsOn' => []],
            ['name' => 'Design System Tokens',    'type' => 'design', 'status' => 'approved', 'assignees' => ['User Thirty-Four'], 'deadline' => '2026-03-03', 'dependsOn' => []],
            ['name' => 'High-Fidelity Mockups',   'type' => 'design', 'status' => 'approved', 'assignees' => ['User Thirty-Four'], 'deadline' => '2026-03-08', 'dependsOn' => ['Wireframes']],
            ['name' => 'Component Library',       'type' => 'design', 'status' => 'approved', 'assignees' => ['User Thirty-Four'], 'deadline' => '2026-03-10', 'dependsOn' => ['Design System Tokens']],
            ['name' => 'Accessibility Review',    'type' => 'design', 'status' => 'approved', 'assignees' => ['User Thirty-Two'], 'deadline' => '2026-03-12', 'dependsOn' => ['High-Fidelity Mockups']],
            ['name' => 'Design Handoff Notes',    'type' => 'design', 'status' => 'approved', 'assignees' => ['User Thirty-Four'], 'deadline' => '2026-03-13', 'dependsOn' => ['Component Library']],
        ],
        2 => [ // Client Design Review - completed, 3/3
            ['name' => 'Client Walkthrough Session', 'type' => 'review', 'status' => 'approved', 'assignees' => ['User Thirty-Two'], 'deadline' => '2026-03-18', 'dependsOn' => []],
            ['name' => 'Revision Round 1',            'type' => 'review', 'status' => 'approved', 'assignees' => ['User Thirty-Four'], 'deadline' => '2026-03-22', 'dependsOn' => ['Client Walkthrough Session']],
            ['name' => 'Final Design Sign-off',       'type' => 'review', 'status' => 'approved', 'assignees' => ['User Thirty-Two'], 'deadline' => '2026-03-25', 'dependsOn' => ['Revision Round 1']],
        ],
        3 => [ // Development - in_progress, 6/10 (deliberately mixes every status for variety)
            ['name' => 'Database Schema',        'type' => 'backend',  'status' => 'approved',       'assignees' => ['User Thirty-Three'], 'deadline' => '2026-04-05', 'dependsOn' => []],
            ['name' => 'Auth Service',           'type' => 'backend',  'status' => 'approved',       'assignees' => ['User Thirty-Three'], 'deadline' => '2026-04-10', 'dependsOn' => ['Database Schema']],
            ['name' => 'API Gateway Setup',      'type' => 'backend',  'status' => 'approved',       'assignees' => ['User Thirty-Three'], 'deadline' => '2026-04-12', 'dependsOn' => []],
            ['name' => 'Core Dashboard UI',      'type' => 'frontend', 'status' => 'approved',       'assignees' => ['User Thirty-Four'], 'deadline' => '2026-04-15', 'dependsOn' => ['Design Handoff Notes']],
            ['name' => 'Notification Service',   'type' => 'backend',  'status' => 'approved',       'assignees' => ['User Thirty-Three'], 'deadline' => '2026-04-18', 'dependsOn' => ['Auth Service']],
            ['name' => 'Billing Integration',    'type' => 'backend',  'status' => 'approved',       'assignees' => ['User Thirty-Three'], 'deadline' => '2026-04-20', 'dependsOn' => ['API Gateway Setup']],
            ['name' => 'Settings Page',          'type' => 'frontend', 'status' => 'pending_review', 'assignees' => ['User Thirty-Four'], 'deadline' => '2026-04-22', 'dependsOn' => ['Core Dashboard UI']],
            ['name' => 'Search Functionality',   'type' => 'backend',  'status' => 'in_progress',    'assignees' => ['User Thirty-Three'], 'deadline' => '2026-04-25', 'dependsOn' => ['API Gateway Setup']],
            ['name' => 'Email Templates',        'type' => 'frontend', 'status' => 'blocked',        'assignees' => ['User Thirty-Four'], 'deadline' => '2026-04-24', 'dependsOn' => ['Notification Service']],
            ['name' => 'Mobile Responsive Pass', 'type' => 'frontend', 'status' => 'locked',         'assignees' => [],                 'deadline' => '2026-04-28', 'dependsOn' => ['Settings Page']],
        ],
        4 => [], // Testing - not started, no tasks yet
        5 => [], // Delivery and Handoff - not started, no tasks yet
    ],

    2 => [ // Project Gamma
        0 => [ // Design - completed, 5/5
            ['name' => 'User Flow Diagrams',       'type' => 'design', 'status' => 'approved', 'assignees' => ['User Thirty-Five'], 'deadline' => '2026-03-05', 'dependsOn' => []],
            ['name' => 'Wireframes',               'type' => 'design', 'status' => 'approved', 'assignees' => ['User Thirty-Five'], 'deadline' => '2026-03-08', 'dependsOn' => ['User Flow Diagrams']],
            ['name' => 'Visual Design Concepts',   'type' => 'design', 'status' => 'approved', 'assignees' => ['User Thirty-Five'], 'deadline' => '2026-03-12', 'dependsOn' => ['Wireframes']],
            ['name' => 'Design QA',                'type' => 'design', 'status' => 'approved', 'assignees' => ['User Thirty-Six'], 'deadline' => '2026-03-14', 'dependsOn' => ['Visual Design Concepts']],
            ['name' => 'Design Handoff',           'type' => 'design', 'status' => 'approved', 'assignees' => ['User Thirty-Five'], 'deadline' => '2026-03-15', 'dependsOn' => ['Design QA']],
        ],
        1 => [ // Development - completed, 8/8
            ['name' => 'Module Architecture',   'type' => 'backend',  'status' => 'approved', 'assignees' => ['User Thirty-Six'], 'deadline' => '2026-04-01', 'dependsOn' => []],
            ['name' => 'Data Layer',            'type' => 'backend',  'status' => 'approved', 'assignees' => ['User Thirty-Six'], 'deadline' => '2026-04-04', 'dependsOn' => ['Module Architecture']],
            ['name' => 'Core Module Logic',     'type' => 'backend',  'status' => 'approved', 'assignees' => ['User Thirty-Six'], 'deadline' => '2026-04-08', 'dependsOn' => ['Data Layer']],
            ['name' => 'Integration Endpoints', 'type' => 'backend',  'status' => 'approved', 'assignees' => ['User Thirty-Six'], 'deadline' => '2026-04-10', 'dependsOn' => ['Core Module Logic']],
            ['name' => 'Frontend Wiring',       'type' => 'frontend', 'status' => 'approved', 'assignees' => ['User Thirty-Seven'], 'deadline' => '2026-04-12', 'dependsOn' => ['Integration Endpoints']],
            ['name' => 'Error Handling',        'type' => 'backend',  'status' => 'approved', 'assignees' => ['User Thirty-Six'], 'deadline' => '2026-04-14', 'dependsOn' => []],
            ['name' => 'Logging & Monitoring',  'type' => 'devops',   'status' => 'approved', 'assignees' => ['User Thirty-Six'], 'deadline' => '2026-04-16', 'dependsOn' => []],
            ['name' => 'Code Review Pass',      'type' => 'backend',  'status' => 'approved', 'assignees' => ['User Thirty-Five'], 'deadline' => '2026-04-18', 'dependsOn' => ['Frontend Wiring']],
        ],
        2 => [ // Testing - in_progress, 3/7 (at-risk project - this is where the overdue/blocked tasks live)
            ['name' => 'Test Plan',            'type' => 'qa', 'status' => 'approved',    'assignees' => ['User Thirty-Five'], 'deadline' => '2026-05-01', 'dependsOn' => []],
            ['name' => 'Unit Tests',           'type' => 'qa', 'status' => 'approved',    'assignees' => ['User Thirty-Six'], 'deadline' => '2026-05-04', 'dependsOn' => ['Test Plan']],
            ['name' => 'Integration Tests',    'type' => 'qa', 'status' => 'approved',    'assignees' => ['User Thirty-Six'], 'deadline' => '2026-05-08', 'dependsOn' => ['Unit Tests']],
            ['name' => 'Regression Suite',     'type' => 'qa', 'status' => 'overdue',     'assignees' => ['User Thirty-Six'], 'deadline' => '2026-05-10', 'dependsOn' => ['Integration Tests']],
            ['name' => 'Load Testing',         'type' => 'qa', 'status' => 'blocked',     'assignees' => ['User Thirty-Six'], 'deadline' => '2026-05-12', 'dependsOn' => ['Regression Suite']],
            ['name' => 'Bug Triage',           'type' => 'qa', 'status' => 'in_progress', 'assignees' => ['User Thirty-Five'], 'deadline' => '2026-05-14', 'dependsOn' => []],
            ['name' => 'UAT Prep',             'type' => 'qa', 'status' => 'locked',      'assignees' => [],                  'deadline' => '2026-05-18', 'dependsOn' => ['Load Testing']],
        ],
        3 => [], // Delivery and Handoff - not started, no tasks yet
    ],

    3 => [ // Project Alpha - the flagship example, fully populated across all 5 stages
        0 => [ // Discovery - completed, 4/4
            ['name' => 'Stakeholder Interviews',        'type' => 'research', 'status' => 'approved', 'assignees' => ['User One'], 'deadline' => '2026-01-14', 'dependsOn' => []],
            ['name' => 'Competitive Analysis',          'type' => 'research', 'status' => 'approved', 'assignees' => ['User One'], 'deadline' => '2026-01-16', 'dependsOn' => []],
            ['name' => 'Requirements Specification',    'type' => 'research', 'status' => 'approved', 'assignees' => ['User One'], 'deadline' => '2026-01-19', 'dependsOn' => ['Stakeholder Interviews']],
            ['name' => 'Project Charter Sign-off',      'type' => 'research', 'status' => 'approved', 'assignees' => ['User Thirty-Eight'], 'deadline' => '2026-01-20', 'dependsOn' => ['Requirements Specification']],
        ],
        1 => [ // Design - completed, 6/6
            // Architecture Review fans out into two parallel design tracks
            // (Component Library + full UI Design can proceed at the same
            // time once the architecture is settled), which both then feed
            // into Responsive Design Pass - a genuine branch/merge, not a
            // single linear chain.
            ['name' => 'Auth Flow Wireframes',   'type' => 'design', 'status' => 'approved', 'assignees' => ['User Thirty-Nine'], 'deadline' => '2026-01-24', 'dependsOn' => []],
            ['name' => 'Architecture Review',    'type' => 'design', 'status' => 'approved', 'assignees' => ['User Thirty-Nine'], 'deadline' => '2026-01-26', 'dependsOn' => ['Auth Flow Wireframes']],
            ['name' => 'UI Component Library',   'type' => 'design', 'status' => 'approved', 'assignees' => ['User Thirty-Nine'], 'deadline' => '2026-01-29', 'dependsOn' => ['Architecture Review']],
            ['name' => 'UI Design',              'type' => 'design', 'status' => 'approved', 'assignees' => ['User Thirty-Nine'], 'deadline' => '2026-02-01', 'dependsOn' => ['Architecture Review']],
            ['name' => 'Responsive Design Pass', 'type' => 'design', 'status' => 'approved', 'assignees' => ['User Forty'], 'deadline' => '2026-02-03', 'dependsOn' => ['UI Component Library', 'UI Design']],
            ['name' => 'Design Sign-off',        'type' => 'design', 'status' => 'approved', 'assignees' => ['User One'], 'deadline' => '2026-02-04', 'dependsOn' => ['Responsive Design Pass']],
        ],
        2 => [ // Development - completed, 12/12
            ['name' => 'Database Schema',          'type' => 'backend',  'status' => 'approved', 'assignees' => ['User Forty-One'], 'deadline' => '2026-02-10', 'dependsOn' => []],
            ['name' => 'Auth Service',             'type' => 'backend',  'status' => 'approved', 'assignees' => ['User Forty-One'], 'deadline' => '2026-02-14', 'dependsOn' => ['Database Schema']],
            ['name' => 'API Gateway',              'type' => 'backend',  'status' => 'approved', 'assignees' => ['User Forty-Two'], 'deadline' => '2026-02-16', 'dependsOn' => []],
            ['name' => 'Dashboard UI',             'type' => 'frontend', 'status' => 'approved', 'assignees' => ['User Forty-Three'], 'deadline' => '2026-02-18', 'dependsOn' => ['UI Component Library']],
            ['name' => 'Task Board UI',            'type' => 'frontend', 'status' => 'approved', 'assignees' => ['User Forty-Three'], 'deadline' => '2026-02-20', 'dependsOn' => ['Dashboard UI']],
            ['name' => 'Notifications Service',    'type' => 'backend',  'status' => 'approved', 'assignees' => ['User Forty-One'], 'deadline' => '2026-02-22', 'dependsOn' => ['Auth Service']],
            ['name' => 'Chat Module',              'type' => 'backend',  'status' => 'approved', 'assignees' => ['User Forty-Two'], 'deadline' => '2026-02-24', 'dependsOn' => ['API Gateway']],
            ['name' => 'Payment Integration',      'type' => 'backend',  'status' => 'approved', 'assignees' => ['User Forty-Four'], 'deadline' => '2026-02-26', 'dependsOn' => []],
            ['name' => 'Reporting Module',         'type' => 'frontend', 'status' => 'approved', 'assignees' => ['User Forty-Five'], 'deadline' => '2026-02-28', 'dependsOn' => ['Task Board UI']],
            ['name' => 'Admin Panel',              'type' => 'frontend', 'status' => 'approved', 'assignees' => ['User Forty-Three'], 'deadline' => '2026-03-02', 'dependsOn' => ['Dashboard UI']],
            ['name' => 'Mobile Responsive Pass',   'type' => 'frontend', 'status' => 'approved', 'assignees' => ['User Forty-Six'], 'deadline' => '2026-03-04', 'dependsOn' => ['Admin Panel']],
            ['name' => 'Code Review & Cleanup',    'type' => 'backend',  'status' => 'approved', 'assignees' => ['User Forty-Two'], 'deadline' => '2026-03-06', 'dependsOn' => []],
        ],
        3 => [ // Testing - completed, 9/9
            ['name' => 'Test Plan',              'type' => 'qa', 'status' => 'approved', 'assignees' => ['User Forty-Six'], 'deadline' => '2026-03-10', 'dependsOn' => []],
            ['name' => 'Unit Tests',             'type' => 'qa', 'status' => 'approved', 'assignees' => ['User Forty-Six'], 'deadline' => '2026-03-12', 'dependsOn' => ['Test Plan']],
            ['name' => 'Integration Tests',      'type' => 'qa', 'status' => 'approved', 'assignees' => ['User Forty-Six'], 'deadline' => '2026-03-15', 'dependsOn' => ['Unit Tests']],
            ['name' => 'Security Audit',         'type' => 'qa', 'status' => 'approved', 'assignees' => ['User Forty-Two'], 'deadline' => '2026-03-17', 'dependsOn' => []],
            ['name' => 'Performance Testing',    'type' => 'qa', 'status' => 'approved', 'assignees' => ['User Forty-Six'], 'deadline' => '2026-03-19', 'dependsOn' => []],
            ['name' => 'Regression Suite',       'type' => 'qa', 'status' => 'approved', 'assignees' => ['User Forty-Six'], 'deadline' => '2026-03-21', 'dependsOn' => ['Integration Tests']],
            ['name' => 'Cross-Browser Testing',  'type' => 'qa', 'status' => 'approved', 'assignees' => ['User Forty-Five'], 'deadline' => '2026-03-22', 'dependsOn' => []],
            ['name' => 'UAT with Client',        'type' => 'qa', 'status' => 'approved', 'assignees' => ['User Thirty-Eight'], 'deadline' => '2026-03-25', 'dependsOn' => ['Regression Suite']],
            ['name' => 'Bug Fixes Round 1',      'type' => 'backend', 'status' => 'approved', 'assignees' => ['User Forty-One'], 'deadline' => '2026-03-27', 'dependsOn' => ['UAT with Client']],
        ],
        4 => [ // Delivery and Handoff - in_progress, 2/4
            ['name' => 'Deployment Runbook',              'type' => 'devops',   'status' => 'approved',       'assignees' => ['User Forty-Two'], 'deadline' => '2026-04-02', 'dependsOn' => []],
            ['name' => 'Production Deployment',           'type' => 'devops',   'status' => 'approved',       'assignees' => ['User Forty-Two'], 'deadline' => '2026-04-05', 'dependsOn' => ['Deployment Runbook']],
            ['name' => 'Client Training Session',         'type' => 'delivery', 'status' => 'pending_review', 'assignees' => ['User Thirty-Eight'], 'deadline' => '2026-04-08', 'dependsOn' => ['Production Deployment']],
            ['name' => 'Final Handover Documentation',    'type' => 'delivery', 'status' => 'locked',         'assignees' => [], 'deadline' => '2026-04-10', 'dependsOn' => ['Client Training Session']],
        ],
    ],

    4 => [ // Marketing Site Redesign
        0 => [ // Requirement Gathering - completed, 3/3
            ['name' => 'Client Brief Review', 'type' => 'research', 'status' => 'approved', 'assignees' => ['User Forty-Seven'], 'deadline' => '2026-05-05', 'dependsOn' => []],
            ['name' => 'Content Audit',       'type' => 'research', 'status' => 'approved', 'assignees' => ['User Forty-Eight'], 'deadline' => '2026-05-08', 'dependsOn' => ['Client Brief Review']],
            ['name' => 'Sitemap Draft',       'type' => 'research', 'status' => 'approved', 'assignees' => ['User Forty-Seven'], 'deadline' => '2026-05-10', 'dependsOn' => ['Content Audit']],
        ],
        1 => [ // Design - in_progress, 2/5
            ['name' => 'Moodboard',                 'type' => 'design', 'status' => 'approved',    'assignees' => ['User Forty-Eight'], 'deadline' => '2026-05-15', 'dependsOn' => []],
            ['name' => 'Homepage Mockup',           'type' => 'design', 'status' => 'approved',    'assignees' => ['User Forty-Eight'], 'deadline' => '2026-05-20', 'dependsOn' => ['Moodboard']],
            ['name' => 'Landing Page Templates',    'type' => 'design', 'status' => 'in_progress', 'assignees' => ['User Forty-Eight'], 'deadline' => '2026-05-25', 'dependsOn' => ['Homepage Mockup']],
            ['name' => 'CMS Component Mapping',     'type' => 'design', 'status' => 'not_started', 'assignees' => ['User Forty-Seven'], 'deadline' => '2026-05-28', 'dependsOn' => ['Landing Page Templates']],
            ['name' => 'Client Design Review',      'type' => 'review', 'status' => 'locked',      'assignees' => [], 'deadline' => '2026-06-01', 'dependsOn' => ['CMS Component Mapping']],
        ],
        2 => [], // Development - not started, no tasks yet
        3 => [], // Delivery and Handoff - not started, no tasks yet
    ],

    5 => [ // Project Delta - archived, fully completed
        0 => [ // Requirement Gathering - 5/5
            ['name' => 'Legacy System Audit',   'type' => 'research', 'status' => 'approved', 'assignees' => ['User Forty-Nine'], 'deadline' => '2025-11-10', 'dependsOn' => []],
            ['name' => 'Data Migration Plan',   'type' => 'research', 'status' => 'approved', 'assignees' => ['User Forty-Nine'], 'deadline' => '2025-11-14', 'dependsOn' => ['Legacy System Audit']],
            ['name' => 'Stakeholder Sign-off',  'type' => 'research', 'status' => 'approved', 'assignees' => ['User One'], 'deadline' => '2025-11-16', 'dependsOn' => ['Data Migration Plan']],
            ['name' => 'Risk Assessment',       'type' => 'research', 'status' => 'approved', 'assignees' => ['User Forty-Nine'], 'deadline' => '2025-11-18', 'dependsOn' => []],
            ['name' => 'Rollback Strategy',     'type' => 'research', 'status' => 'approved', 'assignees' => ['User Forty-Nine'], 'deadline' => '2025-11-20', 'dependsOn' => ['Risk Assessment']],
        ],
        1 => [ // Development - 10/10
            ['name' => 'Backup Verification',       'type' => 'devops',  'status' => 'approved', 'assignees' => ['User Forty-Nine'], 'deadline' => '2025-12-01', 'dependsOn' => []],
            ['name' => 'Migration Scripts',         'type' => 'backend', 'status' => 'approved', 'assignees' => ['User Forty-Nine'], 'deadline' => '2025-12-05', 'dependsOn' => ['Backup Verification']],
            ['name' => 'Data Validation Tooling',   'type' => 'backend', 'status' => 'approved', 'assignees' => ['User Forty-Nine'], 'deadline' => '2025-12-08', 'dependsOn' => ['Migration Scripts']],
            ['name' => 'Dry-Run Migration',         'type' => 'devops',  'status' => 'approved', 'assignees' => ['User Forty-Nine'], 'deadline' => '2025-12-12', 'dependsOn' => ['Data Validation Tooling']],
            ['name' => 'Decommission Scripts',      'type' => 'devops',  'status' => 'approved', 'assignees' => ['User Forty-Nine'], 'deadline' => '2025-12-15', 'dependsOn' => []],
            ['name' => 'Access Revocation',         'type' => 'devops',  'status' => 'approved', 'assignees' => ['User Forty-Nine'], 'deadline' => '2025-12-16', 'dependsOn' => []],
            ['name' => 'Archive Compliance Check',  'type' => 'research','status' => 'approved', 'assignees' => ['User One'], 'deadline' => '2025-12-18', 'dependsOn' => []],
            ['name' => 'Final Data Export',         'type' => 'backend', 'status' => 'approved', 'assignees' => ['User Forty-Nine'], 'deadline' => '2025-12-20', 'dependsOn' => ['Dry-Run Migration']],
            ['name' => 'Legacy Server Shutdown',    'type' => 'devops',  'status' => 'approved', 'assignees' => ['User Forty-Nine'], 'deadline' => '2025-12-22', 'dependsOn' => ['Decommission Scripts']],
            ['name' => 'Documentation Handover',    'type' => 'research','status' => 'approved', 'assignees' => ['User Forty-Nine'], 'deadline' => '2025-12-23', 'dependsOn' => []],
        ],
        2 => [ // Testing - 6/6
            ['name' => 'Migration Verification',     'type' => 'qa', 'status' => 'approved', 'assignees' => ['User Forty-Nine'], 'deadline' => '2026-01-05', 'dependsOn' => []],
            ['name' => 'Data Integrity Checks',       'type' => 'qa', 'status' => 'approved', 'assignees' => ['User Forty-Nine'], 'deadline' => '2026-01-08', 'dependsOn' => ['Migration Verification']],
            ['name' => 'Downstream System Checks',    'type' => 'qa', 'status' => 'approved', 'assignees' => ['User Forty-Nine'], 'deadline' => '2026-01-10', 'dependsOn' => []],
            ['name' => 'Performance Baseline',        'type' => 'qa', 'status' => 'approved', 'assignees' => ['User Forty-Nine'], 'deadline' => '2026-01-12', 'dependsOn' => []],
            ['name' => 'Rollback Drill',              'type' => 'qa', 'status' => 'approved', 'assignees' => ['User Forty-Nine'], 'deadline' => '2026-01-14', 'dependsOn' => []],
            ['name' => 'Sign-off Review',             'type' => 'qa', 'status' => 'approved', 'assignees' => ['User One'], 'deadline' => '2026-01-15', 'dependsOn' => ['Rollback Drill']],
        ],
        3 => [ // Delivery and Handoff - 3/3
            ['name' => 'Closure Report',           'type' => 'delivery', 'status' => 'approved', 'assignees' => ['User Forty-Nine'], 'deadline' => '2026-01-20', 'dependsOn' => []],
            ['name' => 'Client Handover Meeting',  'type' => 'delivery', 'status' => 'approved', 'assignees' => ['User One'], 'deadline' => '2026-01-22', 'dependsOn' => ['Closure Report']],
            ['name' => 'Archive & Close Project',  'type' => 'delivery', 'status' => 'approved', 'assignees' => ['User One'], 'deadline' => '2026-01-25', 'dependsOn' => ['Client Handover Meeting']],
        ],
    ],

    6 => [ // Project Epsilon - closed, delivered
        0 => [ // Discovery - 3/3
            ['name' => 'Compliance Scope Definition', 'type' => 'research', 'status' => 'approved', 'assignees' => ['User Fifty'], 'deadline' => '2025-09-05', 'dependsOn' => []],
            ['name' => 'Audit Checklist Prep',        'type' => 'research', 'status' => 'approved', 'assignees' => ['User Fifty'], 'deadline' => '2025-09-08', 'dependsOn' => ['Compliance Scope Definition']],
            ['name' => 'Kickoff with Client',         'type' => 'research', 'status' => 'approved', 'assignees' => ['User One'], 'deadline' => '2025-09-10', 'dependsOn' => ['Audit Checklist Prep']],
        ],
        1 => [ // Development (the audit work itself) - 7/7
            ['name' => 'Access Control Review',         'type' => 'security', 'status' => 'approved', 'assignees' => ['User Fifty'], 'deadline' => '2025-09-15', 'dependsOn' => []],
            ['name' => 'Network Security Scan',         'type' => 'security', 'status' => 'approved', 'assignees' => ['User Fifty'], 'deadline' => '2025-09-18', 'dependsOn' => []],
            ['name' => 'Data Handling Review',          'type' => 'security', 'status' => 'approved', 'assignees' => ['User Fifty'], 'deadline' => '2025-09-20', 'dependsOn' => ['Access Control Review']],
            ['name' => 'Vulnerability Assessment',      'type' => 'security', 'status' => 'approved', 'assignees' => ['User Fifty'], 'deadline' => '2025-09-24', 'dependsOn' => ['Network Security Scan']],
            ['name' => 'Policy Documentation Review',   'type' => 'research', 'status' => 'approved', 'assignees' => ['User One'], 'deadline' => '2025-09-26', 'dependsOn' => []],
            ['name' => 'Remediation Plan',              'type' => 'security', 'status' => 'approved', 'assignees' => ['User Fifty'], 'deadline' => '2025-09-28', 'dependsOn' => ['Vulnerability Assessment']],
            ['name' => 'Compliance Report Draft',       'type' => 'research', 'status' => 'approved', 'assignees' => ['User Fifty'], 'deadline' => '2025-10-01', 'dependsOn' => ['Remediation Plan']],
        ],
        2 => [ // Delivery and Handoff - 2/2
            ['name' => 'Final Report Sign-off',    'type' => 'delivery', 'status' => 'approved', 'assignees' => ['User One'], 'deadline' => '2025-10-05', 'dependsOn' => ['Compliance Report Draft']],
            ['name' => 'Client Closeout Meeting',  'type' => 'delivery', 'status' => 'approved', 'assignees' => ['User Fifty'], 'deadline' => '2025-10-08', 'dependsOn' => ['Final Report Sign-off']],
        ],
    ],

];