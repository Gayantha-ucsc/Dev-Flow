<?php

class PaymentController extends Controller {

    // Layout data comes from the shared context; the project and role come from the real database.
    private function baseContext(string $pageTitle): array {
        $context = mockPageContext('/payment', $pageTitle);

        $rows = ProjectMember::projectsForUser(Auth::id());
        $ids  = array_values(array_unique(array_map(fn($r) => (int) $r['project_id'], $rows)));
        if (empty($ids)) {
            return $context;
        }

        $selected = (int) Session::get('current_project_id');
        if (!in_array($selected, $ids, true)) {
            $selected = $ids[0];
        }

        $roles = ProjectMember::rolesForUser($selected, Auth::id());
        $role  = in_array('manager', $roles, true) ? 'manager'
               : (in_array('team_lead', $roles, true) ? 'team_lead' : ($roles[0] ?? null));

        $context['currentProjectId']   = $selected;
        $context['currentProjectName'] = Project::findById($selected)['name'] ?? '';
        $context['activeRole']         = $role;
        return $context;
    }

    private function back(): void {
        header('Location: ' . url('payment'));
        exit;
    }

    // Manager of this project only, project still open, valid CSRF token.
    private function requireManager(): array {
        $context = $this->baseContext('Payment');
        $project = Project::findById((int) $context['currentProjectId']);

        if ($context['activeRole'] !== 'manager' || !$project) {
            Session::flash('error', 'Only the Manager can change the payment schedule.');
            $this->back();
        }
        if ($project['status'] === 'closed') {
            Session::flash('error', 'This project is closed. The payment schedule is read-only.');
            $this->back();
        }
        if (!verifyCsrf()) {
            Session::flash('error', 'Your session expired. Please try again.');
            $this->back();
        }
        return $context;
    }

    // Returns [stageId|null, description, amount, dueDate] or flashes an error and redirects.
    private function readMilestoneInput(int $projectId): array {
        $description = trim($_POST['description'] ?? '');
        $amount      = round((float) ($_POST['amount'] ?? 0), 2);
        $dueDate     = trim($_POST['due_date'] ?? '');
        $stageId     = (int) ($_POST['stage_id'] ?? 0) ?: null;

        $error = null;
        if ($description === '' || strlen($description) > 255) {
            $error = 'Enter a description (255 characters or fewer).';
        } elseif (!($amount > 0) || $amount > 9999999999.99) {
            $error = 'Enter an amount greater than zero.';
        } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dueDate) || !strtotime($dueDate)) {
            $error = 'Choose a valid due date.';
        } elseif ($stageId !== null && !Stage::belongsToProject($stageId, $projectId)) {
            $error = 'That stage does not belong to this project.';
        }

        if ($error !== null) {
            Session::flash('error', $error);
            $this->back();
        }
        return [$stageId, $description, $amount, $dueDate];
    }

    // Loads a milestone from the URL, scoped to the current project and still pending.
    private function findPendingOr_flash(int $projectId): array {
        $m = PaymentMilestone::findById((int) (Router::$params['id'] ?? 0));
        if (!$m || (int) $m['project_id'] !== $projectId) {
            Session::flash('error', 'Milestone not found.');
            $this->back();
        }
        if ($m['status'] !== 'pending') {
            Session::flash('error', 'Only pending milestones can be changed.');
            $this->back();
        }
        return $m;
    }

    // POST /payment/milestones
    public function store(): void {
        $context   = $this->requireManager();
        $projectId = (int) $context['currentProjectId'];
        [$stageId, $desc, $amount, $due] = $this->readMilestoneInput($projectId);

        $member = DB::getInstance()->query(
            "SELECT project_member_id FROM ProjectMember WHERE project_id = ? AND user_id = ? AND role = 'manager' AND is_active = 1",
            [$projectId, Auth::id()]
        );
        PaymentMilestone::create($projectId, $stageId, (int) $member[0]['project_member_id'], $desc, $amount, $due);

        Session::flash('success', 'Milestone added.');
        $this->back();
    }

    // POST /payment/milestones/:id/update
    public function update(): void {
        $context   = $this->requireManager();
        $projectId = (int) $context['currentProjectId'];
        $m = $this->findPendingOr_flash($projectId);
        [$stageId, $desc, $amount, $due] = $this->readMilestoneInput($projectId);

        PaymentMilestone::update((int) $m['payment_milestone_id'], $stageId, $desc, $amount, $due);
        Session::flash('success', 'Milestone updated.');
        $this->back();
    }

    // POST /payment/milestones/:id/delete
    public function destroy(): void {
        $context = $this->requireManager();
        $m = $this->findPendingOr_flash((int) $context['currentProjectId']);

        PaymentMilestone::delete((int) $m['payment_milestone_id']);
        Session::flash('success', 'Milestone removed.');
        $this->back();
    }

    // POST /payment/milestones/:id/request
    public function request(): void {
        $context = $this->requireManager();
        $m = $this->findPendingOr_flash((int) $context['currentProjectId']);

        PaymentMilestone::markRequested((int) $m['payment_milestone_id']);
        Session::flash('success', 'Payment requested. The client has been notified.');
        $this->back();
    }

    private function denyUnlessLead(?string $role): void {
        if (in_array($role, ['manager', 'team_lead'], true)) {
            return;
        }

        Session::flash('error', 'You do not have access to payments for this project.');
        header('Location: ' . url('dashboard'));
        exit;
    }

    private function projectStatus(?int $projectId): string {
        return Project::findById((int) $projectId)['status'] ?? 'active';
    }

    private function projectMilestones(int $projectId): array {
        return array_map(function (array $r) {
            $payment = null;
            if ($r['payment_status'] !== null) {
                $payment = [
                    'reference'  => $r['gateway_reference'],
                    'amountPaid' => (float) $r['amount_paid'],
                    'status'     => $r['payment_status'],
                    'paidAt'     => $r['paid_at'],
                ];
            }
            return [
                'id'          => (int) $r['payment_milestone_id'],
                'description' => $r['description'],
                'stage'       => $r['stage_name'] ?? '',
                'stageId'     => $r['stage_id'] !== null ? (int) $r['stage_id'] : '',
                'amount'      => (float) $r['amount'],
                'dueDate'     => $r['due_date'],
                'status'      => $r['status'],
                'payment'     => $payment,
            ];
        }, PaymentMilestone::listByProject($projectId));
    }

    private function stageOptions(int $projectId): array {
        return array_map(
            fn($st) => ['id' => (int) $st['stage_id'], 'name' => $st['name']],
            Stage::listByProject($projectId)
        );
    }

    private function dueInfo(array $milestone, DateTimeImmutable $today): array {
        if ($milestone['status'] === 'paid' && !empty($milestone['payment']['paidAt'])) {
            return [
                'label' => 'Paid on ' . date('M j, Y', strtotime($milestone['payment']['paidAt'])),
                'tone'  => 'success',
            ];
        }

        if (empty($milestone['dueDate'])) {
            return ['label' => '', 'tone' => ''];
        }

        $days = (int) $today->diff(new DateTimeImmutable($milestone['dueDate']))->format('%r%a');
        $unit = fn(int $n) => $n . ($n === 1 ? ' day' : ' days');

        return match (true) {
            $days < 0   => ['label' => 'Overdue by ' . $unit(-$days), 'tone' => 'danger'],
            $days === 0 => ['label' => 'Due today', 'tone' => 'warning'],
            $days <= 7  => ['label' => 'Due in ' . $unit($days), 'tone' => 'warning'],
            default     => ['label' => '', 'tone' => ''],
        };
    }

    private function decorate(array $milestones): array {
        $today = new DateTimeImmutable('today');

        return array_map(function (array $m) use ($today) {
            $meta = paymentStatusMeta($m['status']);
            $m['statusLabel'] = $meta['label'];
            $m['statusTone']  = $meta['tone'];
            $m['due']         = $this->dueInfo($m, $today);
            return $m;
        }, $milestones);
    }

    // Amounts are summed as integer cents to avoid float drift
    private function summarize(array $milestones): array {
        $scheduled    = 0;
        $collected    = 0;
        $outstanding  = 0;
        $notRequested = 0;
        $counts       = ['pending' => 0, 'requested' => 0, 'paid' => 0];

        foreach ($milestones as $m) {
            $cents = (int) round($m['amount'] * 100);
            $scheduled += $cents;
            $counts[$m['status']]++;

            if ($m['status'] === 'paid') {
                $collected += $cents;
            } elseif ($m['status'] === 'requested') {
                $outstanding += $cents;
            } else {
                $notRequested += $cents;
            }
        }

        return [
            'scheduled'        => $scheduled / 100,
            'requested'        => ($collected + $outstanding) / 100,
            'collected'        => $collected / 100,
            'outstanding'      => $outstanding / 100,
            'notRequested'     => $notRequested / 100,
            'percentCollected' => $scheduled > 0 ? (int) round($collected / $scheduled * 100) : 0,
            'count'            => count($milestones),
            'pendingCount'     => $counts['pending'],
            'requestedCount'   => $counts['requested'],
            'paidCount'        => $counts['paid'],
        ];
    }

    private function ledgerGroup(array $milestone): string {
        $recordStatus = $milestone['payment']['status'] ?? null;

        if ($recordStatus === 'completed') {
            return 'completed';
        }
        if ($recordStatus === 'failed') {
            return 'failed';
        }
        if ($recordStatus === 'initiated' || $milestone['status'] === 'requested') {
            return 'awaiting';
        }
        return 'not_requested';
    }

    private function ledgerMeta(string $group, ?string $recordStatus): array {
        if ($group === 'awaiting') {
            return $recordStatus === 'initiated'
                ? ['tone' => 'info', 'label' => 'In progress']
                : ['tone' => 'warning', 'label' => 'Awaiting payment'];
        }

        return match ($group) {
            'completed' => ['tone' => 'success', 'label' => 'Completed'],
            'failed'    => ['tone' => 'danger', 'label' => 'Failed'],
            default     => ['tone' => 'neutral', 'label' => 'Not requested'],
        };
    }

    // Completed payments first (newest on top), then failed, awaiting, and not yet requested by due date
    private function buildLedger(array $milestones): array {
        $rank = ['completed' => 0, 'failed' => 1, 'awaiting' => 2, 'not_requested' => 3];

        $rows = array_map(function (array $m) {
            $group = $this->ledgerGroup($m);
            $meta  = $this->ledgerMeta($group, $m['payment']['status'] ?? null);

            $m['group']      = $group;
            $m['groupLabel'] = $meta['label'];
            $m['groupTone']  = $meta['tone'];
            $m['canRetry']   = $group === 'failed' && $m['status'] === 'requested';
            return $m;
        }, $milestones);

        usort($rows, function (array $a, array $b) use ($rank) {
            if ($rank[$a['group']] !== $rank[$b['group']]) {
                return $rank[$a['group']] <=> $rank[$b['group']];
            }
            if ($a['group'] === 'completed') {
                return strcmp($b['payment']['paidAt'], $a['payment']['paidAt']);
            }
            return strcmp($a['dueDate'] ?? '', $b['dueDate'] ?? '');
        });

        return $rows;
    }

    private function ledgerCounts(array $ledger): array {
        $counts = ['all' => count($ledger), 'completed' => 0, 'failed' => 0, 'awaiting' => 0, 'not_requested' => 0];
        foreach ($ledger as $row) {
            $counts[$row['group']]++;
        }
        return $counts;
    }

    private function renderClientSchedule(array $context): void {
        $milestones = $this->projectMilestones((int) $context['currentProjectId']);
        usort($milestones, fn(array $a, array $b) => strcmp($a['dueDate'] ?? '', $b['dueDate'] ?? ''));

        $labels = ['pending' => 'Upcoming', 'requested' => 'Payment due', 'paid' => 'Paid'];
        $rows   = array_map(function (array $m) use ($labels) {
            $m['statusLabel'] = $labels[$m['status']];
            $m['hasFailedAttempt'] = ($m['payment']['status'] ?? null) === 'failed';
            return $m;
        }, $this->decorate($milestones));

        $this->render('payment/client', array_merge($context, [
            'summary'    => $this->summarize($milestones),
            'milestones' => $rows,
        ]));
    }

    public function index(): void {
        $context = $this->baseContext('Payment');
        $role    = $context['activeRole'];

        if ($role === 'client') {
            $this->renderClientSchedule($context);
            return;
        }

        $this->denyUnlessLead($role);

        $projectId  = (int) $context['currentProjectId'];
        $isClosed   = $this->projectStatus($projectId) === 'closed';
        $milestones = $this->projectMilestones($projectId);

        $this->render('payment/overview', array_merge($context, [
            'canManage'    => $role === 'manager' && !$isClosed,
            'isClosed'     => $isClosed,
            'summary'      => $this->summarize($milestones),
            'milestones'   => $this->decorate($milestones),
            'stageOptions' => $this->stageOptions($projectId),
        ]));
    }

    public function history(): void {
        $context = $this->baseContext('Payment History');
        $role    = $context['activeRole'];

        if ($role === 'client') {
            header('Location: ' . url('payment'));
            exit;
        }

        $this->denyUnlessLead($role);

        $ledger = $this->buildLedger($this->projectMilestones((int) $context['currentProjectId']));

        $this->render('payment/history', array_merge($context, [
            'ledger' => $ledger,
            'counts' => $this->ledgerCounts($ledger),
        ]));
    }
}