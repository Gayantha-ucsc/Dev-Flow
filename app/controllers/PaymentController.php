<?php

class PaymentController extends Controller {

    private function baseContext(string $pageTitle): array {
        return mockPageContext('/payment', $pageTitle);
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
        foreach (projectsListForCurrentUser() as $row) {
            if ($row['id'] === $projectId) {
                return $row['status'];
            }
        }
        return 'active';
    }

    private function projectMilestones(int $projectId): array {
        $all = require __DIR__ . '/../../config/mock/payments.php';
        return $all[$projectId] ?? [];
    }

    private function stageOptions(int $projectId): array {
        $detail = require __DIR__ . '/../../config/mock/project-detail.php';
        return array_column($detail[$projectId]['stages'] ?? [], 'name');
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