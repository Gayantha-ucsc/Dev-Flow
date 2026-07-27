SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `User`;
CREATE TABLE `User` (
    user_id                     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name                        VARCHAR(150) NOT NULL,
    email                       VARCHAR(190) NOT NULL,
    password_hash               VARCHAR(255) NOT NULL,
    profile_picture             VARCHAR(255) NULL,
    is_admin                    BOOLEAN NOT NULL DEFAULT FALSE,
    is_active                   BOOLEAN NOT NULL DEFAULT TRUE,
    is_temp                     BOOLEAN NOT NULL DEFAULT FALSE,
    is_temp_password_changed    BOOLEAN NOT NULL DEFAULT FALSE,
    created_at                  TIMESTAMP NOT NULL,   -- set by trg_user_bi
    updated_at                  TIMESTAMP NOT NULL,   -- set by trg_user_bi / trg_user_bu

    UNIQUE KEY uq_user_email (email)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `RolePermission`;
CREATE TABLE `RolePermission` (
    role_permission_id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role                        ENUM('manager','team_lead','designer','developer','client') NOT NULL,  -- roles
    permission_key              VARCHAR(100) NOT NULL,
    is_allowed                  BOOLEAN NOT NULL DEFAULT TRUE,
    updated_by                  INT UNSIGNED NOT NULL,
    updated_at                  TIMESTAMP NOT NULL,   -- set by trg_roleperm_bi / trg_roleperm_bu

    UNIQUE KEY uq_role_permission (role, permission_key),
    CONSTRAINT fk_roleperm_updated_by FOREIGN KEY (updated_by) REFERENCES `User`(user_id)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `WorkflowTemplate`;
CREATE TABLE `WorkflowTemplate` (
    workflow_template_id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name                        VARCHAR(150) NOT NULL,
    description                 TEXT NULL,
    created_by                  INT UNSIGNED NOT NULL,
    is_system_default           BOOLEAN NOT NULL DEFAULT FALSE,
    created_at                  TIMESTAMP NOT NULL,   -- set by trg_workflowtemplate_bi

    CONSTRAINT fk_template_created_by FOREIGN KEY (created_by) REFERENCES `User`(user_id)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `Project`;
CREATE TABLE `Project` (
    project_id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name                        VARCHAR(150) NOT NULL,
    description                 TEXT NULL,
    created_by                  INT UNSIGNED NOT NULL,
    workflow_template_id        INT UNSIGNED NULL,
    deadline                    DATE NULL,
    status                      ENUM('active','archived','closed') NOT NULL DEFAULT 'active',
    created_at                  TIMESTAMP NOT NULL,   -- set by trg_project_bi
    updated_at                  TIMESTAMP NOT NULL,   -- set by trg_project_bi / trg_project_bu

    CONSTRAINT fk_project_created_by FOREIGN KEY (created_by) REFERENCES `User`(user_id),
    CONSTRAINT fk_project_template FOREIGN KEY (workflow_template_id) REFERENCES `WorkflowTemplate`(workflow_template_id)
) ENGINE=InnoDB;

-- ProjectMember
DROP TABLE IF EXISTS `ProjectMember`;
CREATE TABLE `ProjectMember` (
    project_member_id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id                  INT UNSIGNED NOT NULL,
    user_id                     INT UNSIGNED NOT NULL,
    role                        ENUM('manager','team_lead','designer','developer','client') NOT NULL,
    added_by                    INT UNSIGNED NULL,
    is_active                   BOOLEAN NOT NULL DEFAULT TRUE,
    joined_at                   TIMESTAMP NOT NULL,   -- set by trg_projectmember_bi

    UNIQUE KEY uq_project_member (project_id, user_id),
    CONSTRAINT fk_pm_project FOREIGN KEY (project_id) REFERENCES `Project`(project_id),
    CONSTRAINT fk_pm_user FOREIGN KEY (user_id) REFERENCES `User`(user_id),
    CONSTRAINT fk_pm_added_by FOREIGN KEY (added_by) REFERENCES `ProjectMember`(project_member_id)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `TemplateStage`;
CREATE TABLE `TemplateStage` (
    template_stage_id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name                        VARCHAR(150) NOT NULL,
    description                 TEXT NULL
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `TemplateStageMap`;
CREATE TABLE `TemplateStageMap` (
    template_stage_map_id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    workflow_template_id        INT UNSIGNED NOT NULL,
    template_stage_id           INT UNSIGNED NOT NULL,
    sequence_order              INT NOT NULL,

    UNIQUE KEY uq_tsm_template_stage (workflow_template_id, template_stage_id),
    UNIQUE KEY uq_tsm_template_order (workflow_template_id, sequence_order),
    CONSTRAINT fk_tsm_template FOREIGN KEY (workflow_template_id) REFERENCES `WorkflowTemplate`(workflow_template_id),
    CONSTRAINT fk_tsm_stage FOREIGN KEY (template_stage_id) REFERENCES `TemplateStage`(template_stage_id)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `Stage`;
CREATE TABLE `Stage` (
    stage_id                    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id                  INT UNSIGNED NOT NULL,
    template_stage_id           INT UNSIGNED NULL,
    name                        VARCHAR(150) NOT NULL,
    sequence_order              INT NOT NULL,
    status                      ENUM('not_started','in_progress','pending_completion','completed') NOT NULL DEFAULT 'not_started',
    auto_trigger_fired          BOOLEAN NOT NULL DEFAULT FALSE,
    auto_trigger_fired_at       TIMESTAMP NULL,
    created_at                  TIMESTAMP NOT NULL,     -- set by trg_stage_bi
    completed_at                TIMESTAMP NULL,         -- set by trg_stage_bu when status -> 'completed'

    UNIQUE KEY uq_stage_order (project_id, sequence_order),
    CONSTRAINT fk_stage_project FOREIGN KEY (project_id) REFERENCES `Project`(project_id),
    CONSTRAINT fk_stage_template_stage FOREIGN KEY (template_stage_id) REFERENCES `TemplateStage`(template_stage_id)
) ENGINE=InnoDB;

-- TASK DOMAIN
DROP TABLE IF EXISTS `Task`;
CREATE TABLE `Task` (
    task_id                     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    stage_id                    INT UNSIGNED NOT NULL,
    name                        VARCHAR(150) NOT NULL,
    description                 TEXT NULL,
    created_by                  INT UNSIGNED NOT NULL,
    status                      ENUM('locked','not_started','in_progress','pending_review','approved','rejected','blocked','overdue') NOT NULL DEFAULT 'locked',
    blocked_reason              TEXT NULL,
    acknowledged                BOOLEAN NOT NULL DEFAULT FALSE,
    task_type                   VARCHAR(50) NULL,
    deadline                    DATE NULL,
    created_at                  TIMESTAMP NOT NULL,   -- set by trg_task_bi
    updated_at                  TIMESTAMP NOT NULL,   -- set by trg_task_bi / trg_task_bu

    CONSTRAINT fk_task_stage FOREIGN KEY (stage_id) REFERENCES `Stage`(stage_id),
    CONSTRAINT fk_task_created_by FOREIGN KEY (created_by) REFERENCES `ProjectMember`(project_member_id)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `TaskAssignment`;
CREATE TABLE `TaskAssignment` (
    task_assignment_id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_id                     INT UNSIGNED NOT NULL,
    user_id                     INT UNSIGNED NOT NULL,
    assigned_by                 INT UNSIGNED NOT NULL,
    assigned_at                 TIMESTAMP NOT NULL,   -- set by trg_taskassignment_bi

    UNIQUE KEY uq_task_assignment (task_id, user_id),
    CONSTRAINT fk_ta_task FOREIGN KEY (task_id) REFERENCES `Task`(task_id),
    CONSTRAINT fk_ta_user FOREIGN KEY (user_id) REFERENCES `ProjectMember`(project_member_id),
    CONSTRAINT fk_ta_assigned_by FOREIGN KEY (assigned_by) REFERENCES `ProjectMember`(project_member_id)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `TaskDependency`;
CREATE TABLE `TaskDependency` (
    task_dependency_id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_id                     INT UNSIGNED NOT NULL,
    depends_on_task_id          INT UNSIGNED NOT NULL,
    set_by                      INT UNSIGNED NOT NULL,
    created_at                  TIMESTAMP NOT NULL,   -- set by trg_taskdependency_bi

    UNIQUE KEY uq_task_dependency (task_id, depends_on_task_id),
    CONSTRAINT fk_td_task FOREIGN KEY (task_id) REFERENCES `Task`(task_id),
    CONSTRAINT fk_td_depends_on FOREIGN KEY (depends_on_task_id) REFERENCES `Task`(task_id),
    CONSTRAINT fk_td_set_by FOREIGN KEY (set_by) REFERENCES `ProjectMember`(project_member_id),
    CONSTRAINT chk_td_no_self_dependency CHECK (task_id <> depends_on_task_id)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `ProgressNote`;
CREATE TABLE `ProgressNote` (
    progress_note_id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_id                     INT UNSIGNED NOT NULL,
    user_id                     INT UNSIGNED NOT NULL,
    content                     TEXT NOT NULL,
    created_at                  TIMESTAMP NOT NULL,   -- set by trg_progressnote_bi

    CONSTRAINT fk_pn_task FOREIGN KEY (task_id) REFERENCES `Task`(task_id),
    CONSTRAINT fk_pn_user FOREIGN KEY (user_id) REFERENCES `ProjectMember`(project_member_id)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `ApprovalRecord`;
CREATE TABLE `ApprovalRecord` (
    approval_record_id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_id                     INT UNSIGNED NULL,
    stage_id                    INT UNSIGNED NULL,
    project_id                  INT UNSIGNED NULL,
    decided_by                  INT UNSIGNED NOT NULL,
    decision                    ENUM('approved','rejected','changes_requested') NOT NULL,
    feedback                    TEXT NULL,
    decided_at                  TIMESTAMP NOT NULL,   -- set by trg_approvalrecord_bi

    CONSTRAINT fk_ar_task FOREIGN KEY (task_id) REFERENCES `Task`(task_id),
    CONSTRAINT fk_ar_stage FOREIGN KEY (stage_id) REFERENCES `Stage`(stage_id),
    CONSTRAINT fk_ar_project FOREIGN KEY (project_id) REFERENCES `Project`(project_id),
    CONSTRAINT fk_ar_decided_by FOREIGN KEY (decided_by) REFERENCES `ProjectMember`(project_member_id),
    CONSTRAINT chk_ar_exactly_one_target CHECK (
        (CASE WHEN task_id IS NOT NULL THEN 1 ELSE 0 END) +
        (CASE WHEN stage_id IS NOT NULL THEN 1 ELSE 0 END) +
        (CASE WHEN project_id IS NOT NULL THEN 1 ELSE 0 END) = 1
    )
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `RevisionRound`;
CREATE TABLE `RevisionRound` (
    revision_round_id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_id                     INT UNSIGNED NOT NULL,
    round_number                INT NOT NULL,
    approval_record_id          INT UNSIGNED NULL,
    started_at                  TIMESTAMP NOT NULL,     -- set by trg_revisionround_bi
    closed_at                   TIMESTAMP NULL,         -- set by trg_revisionround_bu when approval_record_id is attached

    UNIQUE KEY uq_revision_round (task_id, round_number),
    CONSTRAINT fk_rr_task FOREIGN KEY (task_id) REFERENCES `Task`(task_id),
    CONSTRAINT fk_rr_approval FOREIGN KEY (approval_record_id) REFERENCES `ApprovalRecord`(approval_record_id)
) ENGINE=InnoDB;

-- COMMENT DOMAIN
DROP TABLE IF EXISTS `Comment`;
CREATE TABLE `Comment` (
    comment_id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_id                     INT UNSIGNED NULL,
    stage_id                    INT UNSIGNED NULL,
    user_id                     INT UNSIGNED NOT NULL,
    parent_comment_id           INT UNSIGNED NULL,
    content                     TEXT NOT NULL,
    created_at                  TIMESTAMP NOT NULL,   -- set by trg_comment_bi
    updated_at                  TIMESTAMP NOT NULL,   -- set by trg_comment_bi / trg_comment_bu

    CONSTRAINT fk_comment_task FOREIGN KEY (task_id) REFERENCES `Task`(task_id),
    CONSTRAINT fk_comment_stage FOREIGN KEY (stage_id) REFERENCES `Stage`(stage_id),
    CONSTRAINT fk_comment_user FOREIGN KEY (user_id) REFERENCES `ProjectMember`(project_member_id),
    CONSTRAINT fk_comment_parent FOREIGN KEY (parent_comment_id) REFERENCES `Comment`(comment_id),
    CONSTRAINT chk_comment_exactly_one_target CHECK (
        (CASE WHEN task_id IS NOT NULL THEN 1 ELSE 0 END) +
        (CASE WHEN stage_id IS NOT NULL THEN 1 ELSE 0 END) = 1
    )
) ENGINE=InnoDB;

-- APPROVAL & HANDOFF DOMAIN
DROP TABLE IF EXISTS `StageChangeProposal`;
CREATE TABLE `StageChangeProposal` (
    stage_change_proposal_id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    stage_id                    INT UNSIGNED NOT NULL,
    proposed_by                 INT UNSIGNED NOT NULL,
    proposed_name               VARCHAR(150) NOT NULL,
    reason                      TEXT NULL,
    status                      ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    reviewed_by                 INT UNSIGNED NULL,
    feedback                    TEXT NULL,
    created_at                  TIMESTAMP NOT NULL,   -- set by trg_stagechangeproposal_bi
    reviewed_at                 TIMESTAMP NULL,        -- set by trg_stagechangeproposal_bu when status leaves 'pending'

    CONSTRAINT fk_scp_stage FOREIGN KEY (stage_id) REFERENCES `Stage`(stage_id),
    CONSTRAINT fk_scp_proposed_by FOREIGN KEY (proposed_by) REFERENCES `ProjectMember`(project_member_id),
    CONSTRAINT fk_scp_reviewed_by FOREIGN KEY (reviewed_by) REFERENCES `ProjectMember`(project_member_id)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `JointApprovalRequest`;
CREATE TABLE `JointApprovalRequest` (
    joint_approval_request_id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    stage_id                    INT UNSIGNED NULL,
    project_id                  INT UNSIGNED NULL,
    chatroom_id                 INT UNSIGNED NULL,
    manager_id                  INT UNSIGNED NOT NULL,
    manager_decision            ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    manager_decided_at          TIMESTAMP NULL,   -- set by trg_jar_bu when manager_decision changes
    team_lead_id                INT UNSIGNED NULL,          -- nullable: project may have no Team Lead yet
    team_lead_decision          ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    team_lead_decided_at        TIMESTAMP NULL,   -- set by trg_jar_bu when team_lead_decision changes
    status                      ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    created_at                  TIMESTAMP NOT NULL,   -- set by trg_jar_bi

    CONSTRAINT fk_jar_stage FOREIGN KEY (stage_id) REFERENCES `Stage`(stage_id),
    CONSTRAINT fk_jar_project FOREIGN KEY (project_id) REFERENCES `Project`(project_id),
    CONSTRAINT fk_jar_manager FOREIGN KEY (manager_id) REFERENCES `ProjectMember`(project_member_id),
    CONSTRAINT fk_jar_team_lead FOREIGN KEY (team_lead_id) REFERENCES `ProjectMember`(project_member_id),
    CONSTRAINT chk_jar_exactly_one_target CHECK (
        (CASE WHEN stage_id IS NOT NULL THEN 1 ELSE 0 END) +
        (CASE WHEN project_id IS NOT NULL THEN 1 ELSE 0 END) +
        (CASE WHEN chatroom_id IS NOT NULL THEN 1 ELSE 0 END) = 1
    )
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `MemberApprovalRequest`;
CREATE TABLE `MemberApprovalRequest` (
    member_approval_request_id  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id                     INT UNSIGNED NOT NULL,
    project_id                  INT UNSIGNED NOT NULL,
    role                        ENUM('manager','team_lead','designer','developer','client') NOT NULL,
    requested_by                INT UNSIGNED NOT NULL,
    requested_by_role           ENUM('manager','team_lead') NOT NULL,
    approval_required_role      ENUM('manager','team_lead') NULL,
    status                      ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    decided_by                  INT UNSIGNED NULL,
    feedback                    TEXT NULL,
    created_at                  TIMESTAMP NOT NULL,     -- set by trg_memberapprovalrequest_bi
    decided_at                  TIMESTAMP NULL,         -- set by trg_memberapprovalrequest_bu when status leaves 'pending'

    CONSTRAINT fk_mar_user FOREIGN KEY (user_id) REFERENCES `User`(user_id),
    CONSTRAINT fk_mar_project FOREIGN KEY (project_id) REFERENCES `Project`(project_id),
    CONSTRAINT fk_mar_requested_by FOREIGN KEY (requested_by) REFERENCES `ProjectMember`(project_member_id),
    CONSTRAINT fk_mar_decided_by FOREIGN KEY (decided_by) REFERENCES `ProjectMember`(project_member_id)
) ENGINE=InnoDB;

-- CHAT DOMAIN
DROP TABLE IF EXISTS `ChatRoom`;
CREATE TABLE `ChatRoom` (
    chatroom_id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_type                 ENUM('project','stage','task','client') NOT NULL,
    project_id                INT UNSIGNED NOT NULL,
    stage_id                  INT UNSIGNED NULL,
    task_id                   INT UNSIGNED NULL,
    created_at                TIMESTAMP NOT NULL,   -- set by trg_chatroom_bi

    UNIQUE KEY uq_chatroom_stage (stage_id),
    UNIQUE KEY uq_chatroom_task (task_id),
    CONSTRAINT fk_chatroom_project FOREIGN KEY (project_id) REFERENCES `Project`(project_id),
    CONSTRAINT fk_chatroom_stage FOREIGN KEY (stage_id) REFERENCES `Stage`(stage_id),
    CONSTRAINT fk_chatroom_task FOREIGN KEY (task_id) REFERENCES `Task`(task_id)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `Message`;
CREATE TABLE `Message` (
    message_id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    chatroom_id               INT UNSIGNED NOT NULL,
    user_id                   INT UNSIGNED NOT NULL,
    content                   TEXT NOT NULL,
    sent_at                   TIMESTAMP NOT NULL,   -- set by trg_message_bi

    CONSTRAINT fk_message_chatroom FOREIGN KEY (chatroom_id) REFERENCES `ChatRoom`(chatroom_id),
    CONSTRAINT fk_message_user FOREIGN KEY (user_id) REFERENCES `ProjectMember`(project_member_id)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `ChatRoomMember`;
CREATE TABLE `ChatRoomMember` (
    chatroom_member_id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    chatroom_id                 INT UNSIGNED NOT NULL,
    user_id                     INT UNSIGNED NOT NULL,
    added_by                    INT UNSIGNED NULL,
    added_at                    TIMESTAMP NOT NULL,   -- set by trg_chatroommember_bi

    UNIQUE KEY uq_chatroom_member (chatroom_id, user_id),
    CONSTRAINT fk_crm_chatroom FOREIGN KEY (chatroom_id) REFERENCES `ChatRoom`(chatroom_id),
    CONSTRAINT fk_crm_user FOREIGN KEY (user_id) REFERENCES `ProjectMember`(project_member_id),
    CONSTRAINT fk_crm_added_by FOREIGN KEY (added_by) REFERENCES `ProjectMember`(project_member_id)
) ENGINE=InnoDB;

-- NOTIFICATION DOMAIN

DROP TABLE IF EXISTS `Notification`;
CREATE TABLE `Notification` (
    notification_id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id                     INT UNSIGNED NOT NULL,
    type                        VARCHAR(100) NOT NULL,
    message                     TEXT NOT NULL,
    task_id                     INT UNSIGNED NULL,
    stage_id                    INT UNSIGNED NULL,
    project_id                  INT UNSIGNED NULL,
    chatroom_id                 INT UNSIGNED NULL,
    payment_milestone_id        INT UNSIGNED NULL,
    is_read                     BOOLEAN NOT NULL DEFAULT FALSE,
    created_at                  TIMESTAMP NOT NULL,   -- set by trg_notification_bi

    CONSTRAINT fk_notif_user FOREIGN KEY (user_id) REFERENCES `User`(user_id),
    CONSTRAINT fk_notif_task FOREIGN KEY (task_id) REFERENCES `Task`(task_id),
    CONSTRAINT fk_notif_stage FOREIGN KEY (stage_id) REFERENCES `Stage`(stage_id),
    CONSTRAINT fk_notif_project FOREIGN KEY (project_id) REFERENCES `Project`(project_id),
    CONSTRAINT fk_notif_chatroom FOREIGN KEY (chatroom_id) REFERENCES `ChatRoom`(chatroom_id),
    CONSTRAINT chk_notif_at_most_one_target CHECK (
        (CASE WHEN task_id IS NOT NULL THEN 1 ELSE 0 END) +
        (CASE WHEN stage_id IS NOT NULL THEN 1 ELSE 0 END) +
        (CASE WHEN project_id IS NOT NULL THEN 1 ELSE 0 END) +
        (CASE WHEN chatroom_id IS NOT NULL THEN 1 ELSE 0 END) +
        (CASE WHEN payment_milestone_id IS NOT NULL THEN 1 ELSE 0 END) <= 1
    )
) ENGINE=InnoDB;

-- PAYMENT DOMAIN
DROP TABLE IF EXISTS `PaymentMilestone`;
CREATE TABLE `PaymentMilestone` (
    payment_milestone_id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id                  INT UNSIGNED NOT NULL,
    stage_id                    INT UNSIGNED NULL,
    created_by                  INT UNSIGNED NOT NULL,
    description                 VARCHAR(255) NOT NULL,
    amount                      DECIMAL(12,2) NOT NULL,
    due_date                    DATE NULL,
    status                      ENUM('pending','requested','paid') NOT NULL DEFAULT 'pending',
    created_at                  TIMESTAMP NOT NULL,   -- set by trg_paymentmilestone_bi

    CONSTRAINT fk_pmilestone_project FOREIGN KEY (project_id) REFERENCES `Project`(project_id),
    CONSTRAINT fk_pmilestone_stage FOREIGN KEY (stage_id) REFERENCES `Stage`(stage_id),
    CONSTRAINT fk_pmilestone_created_by FOREIGN KEY (created_by) REFERENCES `ProjectMember`(project_member_id)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `Payment`;
CREATE TABLE `Payment` (
    payment_id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payment_milestone_id        INT UNSIGNED NOT NULL,
    gateway_reference           VARCHAR(190) NOT NULL,
    amount_paid                 DECIMAL(12,2) NOT NULL,
    status                      ENUM('initiated','completed','failed') NOT NULL DEFAULT 'initiated',
    paid_at                     TIMESTAMP NULL,         -- set by trg_payment_bu when status -> 'completed'
    created_at                  TIMESTAMP NOT NULL,     -- set by trg_payment_bi

    UNIQUE KEY uq_payment_milestone (payment_milestone_id),
    CONSTRAINT fk_payment_milestone FOREIGN KEY (payment_milestone_id) REFERENCES `PaymentMilestone`(payment_milestone_id)
) ENGINE=InnoDB;

-- SYSTEM DOMAIN
DROP TABLE IF EXISTS `ActivityLog`;
CREATE TABLE `ActivityLog` (
    activity_log_id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id                     INT UNSIGNED NOT NULL,     -- who performed the action
    action                      VARCHAR(150) NOT NULL,
    task_id                     INT UNSIGNED NULL,
    stage_id                    INT UNSIGNED NULL,
    project_id                  INT UNSIGNED NULL,
    user_target_id              INT UNSIGNED NULL,
    details                     TEXT NULL,
    created_at                  TIMESTAMP NOT NULL,         -- set by trg_activitylog_bi

    CONSTRAINT fk_alog_user FOREIGN KEY (user_id) REFERENCES `User`(user_id),
    CONSTRAINT fk_alog_task FOREIGN KEY (task_id) REFERENCES `Task`(task_id),
    CONSTRAINT fk_alog_stage FOREIGN KEY (stage_id) REFERENCES `Stage`(stage_id),
    CONSTRAINT fk_alog_project FOREIGN KEY (project_id) REFERENCES `Project`(project_id),
    CONSTRAINT fk_alog_user_target FOREIGN KEY (user_target_id) REFERENCES `User`(user_id),
    CONSTRAINT chk_alog_at_most_one_target CHECK (
        (CASE WHEN task_id IS NOT NULL THEN 1 ELSE 0 END) +
        (CASE WHEN stage_id IS NOT NULL THEN 1 ELSE 0 END) +
        (CASE WHEN project_id IS NOT NULL THEN 1 ELSE 0 END) +
        (CASE WHEN user_target_id IS NOT NULL THEN 1 ELSE 0 END) <= 1
    )
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `SystemSettings`;
CREATE TABLE `SystemSettings` (
    system_settings_id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    settings_key                 VARCHAR(100) NOT NULL,
    settings_value               TEXT NOT NULL,
    description                  TEXT NULL,
    updated_by                   INT UNSIGNED NOT NULL,
    updated_at                   TIMESTAMP NOT NULL,    -- set by trg_systemsettings_bi / trg_systemsettings_bu

    UNIQUE KEY uq_settings_key (settings_key),
    CONSTRAINT fk_settings_updated_by FOREIGN KEY (updated_by) REFERENCES `User`(user_id)
) ENGINE=InnoDB;

-- DEFERRED FOREIGN KEYS
ALTER TABLE `JointApprovalRequest`
    ADD CONSTRAINT fk_jar_chatroom FOREIGN KEY (chatroom_id) REFERENCES `ChatRoom`(chatroom_id);

ALTER TABLE `Notification`
    ADD CONSTRAINT fk_notif_payment_milestone FOREIGN KEY (payment_milestone_id) REFERENCES `PaymentMilestone`(payment_milestone_id);

SET FOREIGN_KEY_CHECKS = 1;



-- TRIGGERS — AUTOMATIC TIMESTAMP HANDLING

DELIMITER $$

-- ---------- User ----------
CREATE TRIGGER trg_user_bi BEFORE INSERT ON `User`
FOR EACH ROW BEGIN
    SET NEW.created_at = NOW();
    SET NEW.updated_at = NOW();
END$$

CREATE TRIGGER trg_user_bu BEFORE UPDATE ON `User`
FOR EACH ROW BEGIN
    SET NEW.updated_at = NOW();
END$$

-- ---------- RolePermission ----------
CREATE TRIGGER trg_roleperm_bi BEFORE INSERT ON `RolePermission`
FOR EACH ROW BEGIN
    SET NEW.updated_at = NOW();
END$$

CREATE TRIGGER trg_roleperm_bu BEFORE UPDATE ON `RolePermission`
FOR EACH ROW BEGIN
    SET NEW.updated_at = NOW();
END$$

-- ---------- WorkflowTemplate ----------
CREATE TRIGGER trg_workflowtemplate_bi BEFORE INSERT ON `WorkflowTemplate`
FOR EACH ROW BEGIN
    SET NEW.created_at = NOW();
END$$

-- ---------- Project ----------
CREATE TRIGGER trg_project_bi BEFORE INSERT ON `Project`
FOR EACH ROW BEGIN
    SET NEW.created_at = NOW();
    SET NEW.updated_at = NOW();
END$$

CREATE TRIGGER trg_project_bu BEFORE UPDATE ON `Project`
FOR EACH ROW BEGIN
    SET NEW.updated_at = NOW();
END$$

-- ---------- ProjectMember ----------
CREATE TRIGGER trg_projectmember_bi BEFORE INSERT ON `ProjectMember`
FOR EACH ROW BEGIN
    SET NEW.joined_at = NOW();
END$$

-- ---------- Stage ----------
CREATE TRIGGER trg_stage_bi BEFORE INSERT ON `Stage`
FOR EACH ROW BEGIN
    SET NEW.created_at = NOW();
END$$

CREATE TRIGGER trg_stage_bu BEFORE UPDATE ON `Stage`
FOR EACH ROW BEGIN
    IF NEW.status = 'completed' AND OLD.status <> 'completed' THEN
        SET NEW.completed_at = NOW();
    END IF;
    IF NEW.auto_trigger_fired = TRUE AND OLD.auto_trigger_fired = FALSE THEN
        SET NEW.auto_trigger_fired_at = NOW();
    END IF;
END$$

-- ---------- Task ----------
CREATE TRIGGER trg_task_bi BEFORE INSERT ON `Task`
FOR EACH ROW BEGIN
    SET NEW.created_at = NOW();
    SET NEW.updated_at = NOW();
END$$

CREATE TRIGGER trg_task_bu BEFORE UPDATE ON `Task`
FOR EACH ROW BEGIN
    SET NEW.updated_at = NOW();
END$$

-- ---------- TaskAssignment ----------
CREATE TRIGGER trg_taskassignment_bi BEFORE INSERT ON `TaskAssignment`
FOR EACH ROW BEGIN
    SET NEW.assigned_at = NOW();
END$$

-- ---------- TaskDependency ----------
CREATE TRIGGER trg_taskdependency_bi BEFORE INSERT ON `TaskDependency`
FOR EACH ROW BEGIN
    SET NEW.created_at = NOW();
END$$

-- ---------- ProgressNote ----------
CREATE TRIGGER trg_progressnote_bi BEFORE INSERT ON `ProgressNote`
FOR EACH ROW BEGIN
    SET NEW.created_at = NOW();
END$$

-- ---------- ApprovalRecord ----------
CREATE TRIGGER trg_approvalrecord_bi BEFORE INSERT ON `ApprovalRecord`
FOR EACH ROW BEGIN
    SET NEW.decided_at = NOW();
END$$

-- ---------- RevisionRound ----------
CREATE TRIGGER trg_revisionround_bi BEFORE INSERT ON `RevisionRound`
FOR EACH ROW BEGIN
    SET NEW.started_at = NOW();
END$$

CREATE TRIGGER trg_revisionround_bu BEFORE UPDATE ON `RevisionRound`
FOR EACH ROW BEGIN
    IF NEW.approval_record_id IS NOT NULL AND OLD.approval_record_id IS NULL THEN
        SET NEW.closed_at = NOW();
    END IF;
END$$

-- ---------- Comment ----------
CREATE TRIGGER trg_comment_bi BEFORE INSERT ON `Comment`
FOR EACH ROW BEGIN
    SET NEW.created_at = NOW();
    SET NEW.updated_at = NOW();
END$$

CREATE TRIGGER trg_comment_bu BEFORE UPDATE ON `Comment`
FOR EACH ROW BEGIN
    SET NEW.updated_at = NOW();
END$$

-- ---------- StageChangeProposal ----------
CREATE TRIGGER trg_stagechangeproposal_bi BEFORE INSERT ON `StageChangeProposal`
FOR EACH ROW BEGIN
    SET NEW.created_at = NOW();
END$$

CREATE TRIGGER trg_stagechangeproposal_bu BEFORE UPDATE ON `StageChangeProposal`
FOR EACH ROW BEGIN
    IF NEW.status <> 'pending' AND OLD.status = 'pending' THEN
        SET NEW.reviewed_at = NOW();
    END IF;
END$$

-- ---------- JointApprovalRequest ----------
CREATE TRIGGER trg_jar_bi BEFORE INSERT ON `JointApprovalRequest`
FOR EACH ROW BEGIN
    SET NEW.created_at = NOW();
END$$

CREATE TRIGGER trg_jar_bu BEFORE UPDATE ON `JointApprovalRequest`
FOR EACH ROW BEGIN
    IF NEW.manager_decision <> 'pending' AND OLD.manager_decision = 'pending' THEN
        SET NEW.manager_decided_at = NOW();
    END IF;
    IF NEW.team_lead_decision <> 'pending' AND OLD.team_lead_decision = 'pending' THEN
        SET NEW.team_lead_decided_at = NOW();
    END IF;
END$$

-- ---------- MemberApprovalRequest ----------
CREATE TRIGGER trg_memberapprovalrequest_bi BEFORE INSERT ON `MemberApprovalRequest`
FOR EACH ROW BEGIN
    SET NEW.created_at = NOW();
END$$

CREATE TRIGGER trg_memberapprovalrequest_bu BEFORE UPDATE ON `MemberApprovalRequest`
FOR EACH ROW BEGIN
    IF NEW.status <> 'pending' AND OLD.status = 'pending' THEN
        SET NEW.decided_at = NOW();
    END IF;
END$$

-- ---------- ChatRoom ----------
CREATE TRIGGER trg_chatroom_bi BEFORE INSERT ON `ChatRoom`
FOR EACH ROW BEGIN
    SET NEW.created_at = NOW();
END$$

-- ---------- Message ----------
CREATE TRIGGER trg_message_bi BEFORE INSERT ON `Message`
FOR EACH ROW BEGIN
    SET NEW.sent_at = NOW();
END$$

-- ---------- ChatRoomMember ----------
CREATE TRIGGER trg_chatroommember_bi BEFORE INSERT ON `ChatRoomMember`
FOR EACH ROW BEGIN
    SET NEW.added_at = NOW();
END$$

-- ---------- Notification ----------
CREATE TRIGGER trg_notification_bi BEFORE INSERT ON `Notification`
FOR EACH ROW BEGIN
    SET NEW.created_at = NOW();
END$$

-- ---------- PaymentMilestone ----------
CREATE TRIGGER trg_paymentmilestone_bi BEFORE INSERT ON `PaymentMilestone`
FOR EACH ROW BEGIN
    SET NEW.created_at = NOW();
END$$

-- ---------- Payment ----------
CREATE TRIGGER trg_payment_bi BEFORE INSERT ON `Payment`
FOR EACH ROW BEGIN
    SET NEW.created_at = NOW();
END$$

CREATE TRIGGER trg_payment_bu BEFORE UPDATE ON `Payment`
FOR EACH ROW BEGIN
    IF NEW.status = 'completed' AND OLD.status <> 'completed' THEN
        SET NEW.paid_at = NOW();
    END IF;
END$$

-- ---------- ActivityLog ----------
CREATE TRIGGER trg_activitylog_bi BEFORE INSERT ON `ActivityLog`
FOR EACH ROW BEGIN
    SET NEW.created_at = NOW();
END$$

-- ---------- SystemSettings ----------
CREATE TRIGGER trg_systemsettings_bi BEFORE INSERT ON `SystemSettings`
FOR EACH ROW BEGIN
    SET NEW.updated_at = NOW();
END$$

CREATE TRIGGER trg_systemsettings_bu BEFORE UPDATE ON `SystemSettings`
FOR EACH ROW BEGIN
    SET NEW.updated_at = NOW();
END$$

DELIMITER ;