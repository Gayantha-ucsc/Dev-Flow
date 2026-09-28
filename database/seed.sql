
-- USERS
INSERT INTO `User` (user_id, name, username, email, password_hash, profile_picture, is_admin, is_active, is_temp, is_temp_password_changed) VALUES
(1, 'System Administrator', 'admin',          'admin@example.com',          '$2y$10$iD4BtQ.ARm.sDz/MELlXxumn7VWLgusUpHIbcD9A/BcYFvP6RMhJe', NULL, TRUE,  TRUE, FALSE, FALSE),
(2, 'Demo User',            'demo',           'demo@example.com',           '$2y$10$iD4BtQ.ARm.sDz/MELlXxumn7VWLgusUpHIbcD9A/BcYFvP6RMhJe', NULL, FALSE, TRUE, FALSE, FALSE),
(3, 'Demo Manager',         'demo.manager',   'demo.manager@example.com',   '$2y$10$iD4BtQ.ARm.sDz/MELlXxumn7VWLgusUpHIbcD9A/BcYFvP6RMhJe', NULL, FALSE, TRUE, FALSE, FALSE),
(4, 'Demo Team Lead',       'demo.teamlead',  'demo.teamlead@example.com',  '$2y$10$iD4BtQ.ARm.sDz/MELlXxumn7VWLgusUpHIbcD9A/BcYFvP6RMhJe', NULL, FALSE, TRUE, FALSE, FALSE),
(5, 'Demo Developer',       'demo.developer', 'demo.developer@example.com', '$2y$10$iD4BtQ.ARm.sDz/MELlXxumn7VWLgusUpHIbcD9A/BcYFvP6RMhJe', NULL, FALSE, TRUE, FALSE, FALSE),
(6, 'Demo Client',          'demo.client',    'demo.client@example.com',    '$2y$10$iD4BtQ.ARm.sDz/MELlXxumn7VWLgusUpHIbcD9A/BcYFvP6RMhJe', NULL, FALSE, TRUE, TRUE,  TRUE),
(7, 'Demo Designer',        'demo.designer',  'demo.designer@example.com',  '$2y$10$iD4BtQ.ARm.sDz/MELlXxumn7VWLgusUpHIbcD9A/BcYFvP6RMhJe', NULL, FALSE, TRUE, FALSE, FALSE);

-- ROLE PERMISSION MATRIX
INSERT INTO `RolePermission` (role, permission_key, is_allowed, updated_by) VALUES
-- Manager
('manager', 'project.create',            TRUE, 1),
('manager', 'project.archive',           TRUE, 1),
('manager', 'project.close',             TRUE, 1),
('manager', 'member.add',                TRUE, 1),
('manager', 'member.remove',             TRUE, 1),
('manager', 'stage.customize',           TRUE, 1),
('manager', 'stage.approve_completion',  TRUE, 1),
('manager', 'stage.propose_change',      TRUE, 1),
('manager', 'task.create',               TRUE, 1),
('manager', 'task.assign',               TRUE, 1),
('manager', 'task.review',               TRUE, 1),
('manager', 'payment.manage',            TRUE, 1),
('manager', 'report.view',               TRUE, 1),

-- Team Lead
('team_lead', 'task.create',             TRUE, 1),
('team_lead', 'task.assign',             TRUE, 1),
('team_lead', 'task.review',             TRUE, 1),
('team_lead', 'stage.propose_change',    TRUE, 1),
('team_lead', 'stage.approve_completion',TRUE, 1),
('team_lead', 'member.add',              TRUE, 1),

-- Designer
('designer', 'task.view_assigned',       TRUE, 1),
('designer', 'task.update_status',       TRUE, 1),
('designer', 'comment.create',           TRUE, 1),

-- Developer
('developer', 'task.view_assigned',      TRUE, 1),
('developer', 'task.update_status',      TRUE, 1),
('developer', 'comment.create',          TRUE, 1),

-- Client
('client', 'portal.view_progress',        TRUE, 1),
('client', 'portal.approve_deliverable',  TRUE, 1),
('client', 'portal.reject_deliverable',   TRUE, 1),
('client', 'payment.pay_milestone',       TRUE, 1);

-- WORKFLOW TEMPLATES AND REUSABLE STAGES (created by admin, system defaults)
INSERT INTO `WorkflowTemplate` (workflow_template_id, name, description, created_by, is_system_default) VALUES
(1, 'Standard Web Development', 'A generic default workflow for a typical client web development project.', 1, TRUE),
(2, 'Simple Build and Review',  'A shorter, lightweight workflow for smaller projects with fewer review stages.', 1, TRUE);

INSERT INTO `TemplateStage` (template_stage_id, name, description) VALUES
(1, 'Requirement Gathering', 'Collecting and documenting project requirements.'),
(2, 'Design',                'Producing design work for client review.'),
(3, 'Client Design Review',  'Client reviews and provides feedback on design work.'),
(4, 'Revision',              'Incorporating feedback and making requested changes.'),
(5, 'Final Design Approval', 'Design is finalized and approved before development begins.'),
(6, 'Development',           'Implementation of approved designs and features.'),
(7, 'Testing',               'Quality assurance and bug fixing.'),
(8, 'Client Final Review',   'Client reviews the completed work before delivery.'),
(9, 'Delivery and Handoff',  'Final delivery of the completed project to the client.');

-- Template 1: all 9 stages, in order
INSERT INTO `TemplateStageMap` (workflow_template_id, template_stage_id, sequence_order) VALUES
(1, 1, 1), (1, 2, 2), (1, 3, 3), (1, 4, 4), (1, 5, 5),
(1, 6, 6), (1, 7, 7), (1, 8, 8), (1, 9, 9);

-- Template 2: shorter order reusing some of the same stages
INSERT INTO `TemplateStageMap` (workflow_template_id, template_stage_id, sequence_order) VALUES
(2, 1, 1), (2, 6, 2), (2, 7, 3), (2, 8, 4), (2, 9, 5);

-- SYSTEM SETTINGS
INSERT INTO `SystemSettings` (settings_key, settings_value, description, updated_by) VALUES
('notification_poll_interval_seconds', '15', 'How often (in seconds) the client polls for new notifications.', 1),
('chat_poll_interval_seconds',         '10', 'How often (in seconds) the client polls for new chat messages.', 1),
('task_overdue_escalation_hours',      '48', 'Hours after a task becomes overdue before the Manager is escalated to.', 1),
('session_timeout_minutes',            '60', 'Minutes of inactivity before a user session is invalidated.', 1);

-- PROJECTS
INSERT INTO `Project` (project_id, name, description, created_by, workflow_template_id, deadline, status) VALUES
(1, 'DevFlow Demo Project', 'Client website build used to demonstrate stage and task management end to end.', 2, 1,    '2027-01-31', 'active'),
(2, 'Project Gamma',        'Module Implementation',                 3, NULL, '2026-11-30', 'active'),
(3, 'Project Alpha',        'Client Website Delivery',               2, NULL, '2026-10-24', 'active'),
(4, 'Site Redesign',        'Landing Page + CMS',                    3, NULL, '2026-11-05', 'active'),
(5, 'Project Delta',        'Legacy Database Decommissioning',       2, NULL, '2026-07-01', 'archived'),
(6, 'Project Epsilon',      'Security Audit & Compliance Review Q2', 2, NULL, '2026-06-15', 'closed'),
(7, 'Project Beta',         'Client Website Delivery',               3, NULL, '2026-12-20', 'active'),
(8, 'Site Refresh',         'Quarterly Content Refresh',             3, NULL, '2027-01-10', 'active');

-- PROJECT MEMBERS (one role per user per project)
INSERT INTO `ProjectMember` (project_member_id, project_id, user_id, role, added_by, is_active) VALUES
-- Project 1: demo is Manager
(1,  1, 2, 'manager',   NULL, TRUE),
(2,  1, 4, 'team_lead', 1,    TRUE),
(3,  1, 7, 'designer',  1,    TRUE),
(4,  1, 5, 'developer', 1,    TRUE),
(5,  1, 6, 'client',    1,    TRUE),
-- Project 2: demo is Developer
(6,  2, 3, 'manager',   NULL, TRUE),
(7,  2, 4, 'team_lead', 6,    TRUE),
(8,  2, 2, 'developer', 6,    TRUE),
(9,  2, 6, 'client',    6,    TRUE),
-- Project 3: demo is Manager
(10, 3, 2, 'manager',   NULL, TRUE),
(11, 3, 4, 'team_lead', 10,   TRUE),
(12, 3, 5, 'developer', 10,   TRUE),
(13, 3, 6, 'client',    10,   TRUE),
-- Project 4: demo is Team Lead
(14, 4, 3, 'manager',   NULL, TRUE),
(15, 4, 2, 'team_lead', 14,   TRUE),
(16, 4, 5, 'developer', 14,   TRUE),
(17, 4, 6, 'client',    14,   TRUE),
-- Project 5 (archived): demo is Manager
(18, 5, 2, 'manager',   NULL, TRUE),
(19, 5, 5, 'developer', 18,   TRUE),
-- Project 6 (closed): demo is Manager
(20, 6, 2, 'manager',   NULL, TRUE),
(21, 6, 4, 'team_lead', 20,   TRUE),
-- Project 7: demo is Client
(22, 7, 3, 'manager',   NULL, TRUE),
(23, 7, 4, 'team_lead', 22,   TRUE),
(24, 7, 5, 'developer', 22,   TRUE),
(25, 7, 2, 'client',    22,   TRUE),
-- Project 8: demo is Client
(26, 8, 3, 'manager',   NULL, TRUE),
(27, 8, 5, 'developer', 26,   TRUE),
(28, 8, 2, 'client',    26,   TRUE);

-- STAGES
INSERT INTO `Stage` (stage_id, project_id, template_stage_id, name, sequence_order, status) VALUES
(1,  1, 1, 'Requirement Gathering', 1, 'completed'),
(2,  1, 2, 'Design',                2, 'in_progress'),
(3,  1, 3, 'Client Design Review',  3, 'not_started'),
(4,  1, 4, 'Revision',              4, 'not_started'),
(5,  1, 5, 'Final Design Approval', 5, 'not_started'),
(6,  1, 6, 'Development',           6, 'in_progress'),
(7,  1, 7, 'Testing',               7, 'not_started'),
(8,  1, 8, 'Client Final Review',   8, 'not_started'),
(9,  1, 9, 'Delivery and Handoff',  9, 'not_started');

INSERT INTO `Stage` (stage_id, project_id, template_stage_id, name, sequence_order, status) VALUES
-- Project 2
(10, 2, 1, 'Requirement Gathering', 1, 'completed'),
(11, 2, 6, 'Development',           2, 'completed'),
(12, 2, 7, 'Testing',               3, 'in_progress'),
(13, 2, 9, 'Delivery and Handoff',  4, 'not_started'),
-- Project 3
(14, 3, 1, 'Requirement Gathering', 1, 'completed'),
(15, 3, 2, 'Design',                2, 'completed'),
(16, 3, 6, 'Development',           3, 'completed'),
(17, 3, 7, 'Testing',               4, 'completed'),
(18, 3, 9, 'Delivery and Handoff',  5, 'in_progress'),
-- Project 4
(19, 4, 1, 'Requirement Gathering', 1, 'completed'),
(20, 4, 2, 'Design',                2, 'in_progress'),
(21, 4, 6, 'Development',           3, 'not_started'),
(22, 4, 9, 'Delivery and Handoff',  4, 'not_started'),
-- Project 5 (archived)
(23, 5, 1, 'Requirement Gathering', 1, 'completed'),
(24, 5, 6, 'Development',           2, 'completed'),
(25, 5, 7, 'Testing',               3, 'completed'),
(26, 5, 9, 'Delivery and Handoff',  4, 'completed'),
-- Project 6 (closed)
(27, 6, 1, 'Requirement Gathering', 1, 'completed'),
(28, 6, 7, 'Testing',               2, 'completed'),
(29, 6, 9, 'Delivery and Handoff',  3, 'completed'),
-- Project 7
(30, 7, 1, 'Requirement Gathering', 1, 'completed'),
(31, 7, 2, 'Design',                2, 'completed'),
(32, 7, 6, 'Development',           3, 'in_progress'),
(33, 7, 7, 'Testing',               4, 'not_started'),
(34, 7, 9, 'Delivery and Handoff',  5, 'not_started'),
-- Project 8
(35, 8, 1, 'Requirement Gathering', 1, 'completed'),
(36, 8, 2, 'Design',                2, 'in_progress'),
(37, 8, 6, 'Development',           3, 'not_started'),
(38, 8, 9, 'Delivery and Handoff',  4, 'not_started');

-- TASKS
INSERT INTO `Task` (task_id, stage_id, name, description, created_by, status, blocked_reason, acknowledged, task_type, deadline) VALUES
(1,  1, 'Kickoff meeting notes',        'Record decisions and action items from the client kickoff call.',            2, 'approved',       NULL, TRUE,  'documentation', '2026-09-08'),
(2,  1, 'Requirements document',        'Functional requirements agreed with the client.',                             2, 'approved',       NULL, TRUE,  'documentation', '2026-09-15'),
(3,  2, 'Homepage wireframes',          'Low-fidelity wireframes for the homepage and navigation.',                    1, 'approved',       NULL, TRUE,  'design',        '2026-09-22'),
(4,  2, 'Visual style guide',           'Colors, typography and component styles for the client site.',                1, 'pending_review', NULL, TRUE,  'design',        '2026-10-06'),
(5,  2, 'Mobile navigation mockups',    'Mockups for the mobile menu and footer.',                                     1, 'in_progress',    NULL, TRUE,  'design',        '2026-10-12'),
(6,  2, 'Icon set',                     'Custom icon set for the services section.',                                   1, 'not_started',    NULL, FALSE, 'design',        '2026-10-20'),
(7,  3, 'Present homepage design',      'Walk the client through the approved homepage wireframes.',                   1, 'not_started',    NULL, FALSE, 'review',        '2026-10-14'),
(8,  6, 'Build homepage',               'Implement the approved homepage design in HTML/CSS.',                         2, 'locked',         NULL, FALSE, 'frontend',      '2026-11-15'),
(9,  6, 'Contact form backend',         'PHP handler and validation for the contact form.',                            2, 'not_started',    NULL, FALSE, 'backend',       '2026-11-25'),
(10, 6, 'Responsive navigation',        'Build the mobile navigation from the approved mockups.',                      2, 'locked',         NULL, FALSE, 'frontend',      '2026-11-20'),
(11, 7, 'Cross-browser testing',        'Check the built pages in Chrome, Firefox, Edge and Safari.',                  2, 'locked',         NULL, FALSE, 'testing',       '2026-12-05'),
(12, 7, 'Accessibility audit',          'Review contrast, keyboard navigation and alt text.',                          2, 'blocked',        'Waiting for the final colour palette from the style guide.', FALSE, 'testing', '2026-12-10'),
(13, 6, 'Set up hosting configuration', 'Prepare server configuration for the staging environment.',                   2, 'overdue',        NULL, TRUE,  'devops',        '2026-09-20');

-- Other projects: a small task set each so their boards are not empty.
INSERT INTO `Task` (task_id, stage_id, name, description, created_by, status, acknowledged, task_type, deadline) VALUES
-- Project 2 (demo is the developer, member 8)
(14, 12, 'Regression test suite',       'Automated regression checks for the new module.',              7,  'in_progress',    TRUE,  'testing',  '2026-10-10'),
(15, 12, 'Fix export formatting bug',   'CSV export drops the last column on large files.',             7,  'pending_review', TRUE,  'backend',  '2026-10-05'),
(16, 13, 'Release notes',               'Summarise the module changes for the client.',                 7,  'locked',         FALSE, 'documentation', '2026-11-20'),
-- Project 3
(17, 18, 'Final handoff checklist',     'Confirm every deliverable is ready for handoff.',              11, 'in_progress',    TRUE,  'review',   '2026-10-20'),
(18, 18, 'Client training session',     'Walk the client team through the admin area.',                 11, 'not_started',    FALSE, 'training', '2026-10-22'),
-- Project 4 (demo is the team lead, member 15)
(19, 20, 'Landing page layouts',        'Three layout options for the new landing page.',               15, 'in_progress',    TRUE,  'design',   '2026-10-15'),
(20, 20, 'CMS content model',           'Define the page types and fields for the CMS.',                15, 'not_started',    FALSE, 'backend',  '2026-10-25'),
-- Project 7
(21, 32, 'Storefront product pages',    'Build the product listing and detail pages.',                  23, 'in_progress',    TRUE,  'frontend', '2026-11-01'),
(22, 32, 'Checkout form validation',    'Validate address and payment details before submit.',          23, 'not_started',    FALSE, 'backend',  '2026-11-12'),
-- Project 8
(23, 36, 'Refresh homepage banners',    'Update the seasonal banners and hero copy.',                   26, 'in_progress',    TRUE,  'design',   '2026-10-18');

-- TASK ASSIGNMENTS (task_id, ProjectMember id, assigned_by)
INSERT INTO `TaskAssignment` (task_id, user_id, assigned_by) VALUES
(1,  2, 1), (2,  2, 1),
(3,  3, 1), (4,  3, 1), (5,  3, 1),
(7,  1, 1),
(8,  4, 1), (9,  4, 1), (10, 4, 1),
(11, 4, 2), (13, 4, 2),
(14, 8, 7), (15, 8, 7),
(17, 12, 10), (18, 12, 10),
(19, 16, 15), (20, 16, 15),
(21, 24, 23),
(23, 27, 26);

-- TASK DEPENDENCIES (task_id is locked until depends_on_task_id is approved)
INSERT INTO `TaskDependency` (task_id, depends_on_task_id, set_by) VALUES
(8,  3, 1),   -- Build homepage needs the approved wireframes
(8,  4, 1),   -- ...and the style guide (still pending review, so it stays locked)
(10, 5, 1),   -- Responsive navigation needs the mobile mockups
(11, 8, 2);   -- Cross-browser testing needs the built homepage

-- APPROVAL HISTORY (immutable) AND REVISION ROUNDS for Project 1
INSERT INTO `ApprovalRecord` (approval_record_id, task_id, decided_by, decision, feedback) VALUES
(1, 1, 2, 'approved',          'Notes are complete and match the call.'),
(2, 2, 2, 'approved',          'Requirements confirmed with the client.'),
(3, 3, 2, 'changes_requested', 'Navigation is too crowded. Please simplify the header.'),
(4, 3, 2, 'approved',          'Simplified header works well. Approved.');

INSERT INTO `RevisionRound` (task_id, round_number) VALUES
(1, 1), (2, 1), (3, 1), (3, 2), (4, 1);

-- Attaching the approval closes each round (closed_at is set by trg_revisionround_bu)
UPDATE `RevisionRound` SET approval_record_id = 1 WHERE task_id = 1 AND round_number = 1;
UPDATE `RevisionRound` SET approval_record_id = 2 WHERE task_id = 2 AND round_number = 1;
UPDATE `RevisionRound` SET approval_record_id = 3 WHERE task_id = 3 AND round_number = 1;
UPDATE `RevisionRound` SET approval_record_id = 4 WHERE task_id = 3 AND round_number = 2;

-- PROGRESS NOTES AND COMMENTS
INSERT INTO `ProgressNote` (task_id, user_id, content) VALUES
(5, 3, 'Drawer menu layout done, working on the footer links next.'),
(9, 4, 'Reviewed the form fields, starting on server-side validation.');

INSERT INTO `Comment` (comment_id, task_id, stage_id, user_id, parent_comment_id, content) VALUES
(1, 4, NULL, 2, NULL, 'Please use the brand blue from the logo as the primary colour.'),
(2, 4, NULL, 3, 1,    'Done, the palette now uses the logo blue.');

INSERT INTO `Comment` (comment_id, task_id, stage_id, user_id, parent_comment_id, content) VALUES
(3, NULL, 2, 1, NULL, 'Design stage should wrap up by mid October so development can start on time.');

-- PAYMENT MILESTONES AND PAYMENTS
INSERT INTO `PaymentMilestone` (payment_milestone_id, project_id, stage_id, created_by, description, amount, due_date, status) VALUES
-- Project 1 (demo is Manager, member 1)
(1, 1, 1, 1, 'Kickoff deposit',        4000.00, '2026-10-05', 'paid'),
(2, 1, 2, 1, 'Design approval',        6000.00, '2026-10-31', 'requested'),
(3, 1, 6, 1, 'Development milestone',  8000.00, '2026-12-15', 'pending'),
(4, 1, 9, 1, 'Final delivery payment', 5000.00, '2027-01-25', 'pending'),
-- Project 3 (demo is Manager, member 10)
(5, 3, 14, 10, 'Kickoff deposit',      3000.00, '2026-08-01', 'paid'),
(6, 3, 18, 10, 'Final delivery payment', 4500.00, '2026-10-24', 'requested'),
-- Project 7 (demo is Client, Manager is member 22)
(7, 7, 30, 22, 'Kickoff deposit',      2500.00, '2026-09-01', 'paid'),
(8, 7, 32, 22, 'Development milestone', 5000.00, '2026-11-15', 'requested'),
(9, 7, 34, 22, 'Final payment',        3500.00, '2026-12-20', 'pending');

INSERT INTO `Payment` (payment_milestone_id, gateway_reference, amount_paid, status, paid_at) VALUES
(1, 'GW-261005-4821', 4000.00, 'completed', '2026-10-05 10:42:00'),
(5, 'GW-260801-1173', 3000.00, 'completed', '2026-08-01 09:15:00'),
(7, 'GW-260901-3390', 2500.00, 'completed', '2026-09-01 14:05:00');
