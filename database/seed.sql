-- DEFAULT ADMINISTRATOR ACCOUNT
INSERT INTO `User` (name, username, email, password_hash, profile_picture, is_admin, is_active, is_temp, is_temp_password_changed)
VALUES ('System Administrator', 'admin', 'admin@example.com', '$2y$10$iD4BtQ.ARm.sDz/MELlXxumn7VWLgusUpHIbcD9A/BcYFvP6RMhJe', NULL, TRUE, TRUE, FALSE, FALSE);

-- DEFAULT ROLE PERMISSION MATRIX
INSERT INTO `RolePermission` (role, permission_key, is_allowed, updated_by) VALUES
-- Manager
('manager', 'project.create',            TRUE, 1),
('manager', 'project.archive',           TRUE, 1),
('manager', 'project.close',             TRUE, 1),
('manager', 'member.add',                TRUE, 1),
('manager', 'member.remove',             TRUE, 1),
('manager', 'stage.customize',           TRUE, 1),
('manager', 'stage.approve_completion',  TRUE, 1),
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

-- DEFAULT WORKFLOW TEMPLATES AND STAGES

INSERT INTO `WorkflowTemplate` (name, description, created_by, is_system_default)
VALUES
('Standard Web Development', 'A generic default workflow for a typical client web development project.', 1, TRUE),
('Simple Build and Review', 'A shorter, lightweight workflow for smaller projects with fewer review stages.', 1, TRUE);

-- Reusable named stages (shared across templates via TemplateStageMap)
INSERT INTO `TemplateStage` (name, description) VALUES
('Requirement Gathering', 'Collecting and documenting project requirements.'),
('Design', 'Producing design work for client review.'),
('Client Design Review', 'Client reviews and provides feedback on design work.'),
('Revision', 'Incorporating feedback and making requested changes.'),
('Final Design Approval', 'Design is finalized and approved before development begins.'),
('Development', 'Implementation of approved designs and features.'),
('Testing', 'Quality assurance and bug fixing.'),
('Client Final Review', 'Client reviews the completed work before delivery.'),
('Delivery and Handoff', 'Final delivery of the completed project to the client.');

-- Template 1: Standard Web Development (uses all 9 stages, in order)
INSERT INTO `TemplateStageMap` (workflow_template_id, template_stage_id, sequence_order) VALUES
(1, 1, 1),  -- Requirement Gathering
(1, 2, 2),  -- Design
(1, 3, 3),  -- Client Design Review
(1, 4, 4),  -- Revision
(1, 5, 5),  -- Final Design Approval
(1, 6, 6),  -- Development
(1, 7, 7),  -- Testing
(1, 8, 8),  -- Client Final Review
(1, 9, 9);  -- Delivery and Handoff

-- Template 2: Simple Build and Review (reuses some of the same named stages, shorter order)
INSERT INTO `TemplateStageMap` (workflow_template_id, template_stage_id, sequence_order) VALUES
(2, 1, 1),  -- Requirement Gathering
(2, 6, 2),  -- Development
(2, 7, 3),  -- Testing
(2, 8, 4),  -- Client Final Review
(2, 9, 5);  -- Delivery and Handoff

-- BASELINE SYSTEM SETTINGS
INSERT INTO `SystemSettings` (settings_key, settings_value, description, updated_by) VALUES
('notification_poll_interval_seconds', '15', 'How often (in seconds) the client polls for new notifications.', 1),
('chat_poll_interval_seconds',         '10', 'How often (in seconds) the client polls for new chat messages.', 1),
('task_overdue_escalation_hours',      '48', 'Hours after a task becomes overdue before the Manager is escalated to.', 1),
('session_timeout_minutes',           '60', 'Minutes of inactivity before a user session is invalidated.', 1);