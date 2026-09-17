<?php

class ReviewController extends Controller {

    private function baseContext(string $currentRoute, string $pageTitle): array {
        return mockPageContext($currentRoute, $pageTitle, ['heading' => $pageTitle]);
    }

    private function projectReviews(int $projectId): array {
        $all = require __DIR__ . '/../../config/mock/reviews.php';
        return $all[$projectId] ?? ['pending' => [], 'history' => []];
    }

    private function projectGates(int $projectId): array {
        $all = require __DIR__ . '/../../config/mock/joint-approvals.php';
        return $all[$projectId] ?? [];
    }

    private function decorateGates(array $gates, ?string $activeRole): array {
        return array_map(function (array $gate) use ($activeRole) {
            $parties = ['manager' => $gate['manager'], 'teamLead' => $gate['teamLead']];

            $approvedCount = 0;
            foreach ($parties as $key => $party) {
                if ($party['decision'] === 'approved') {
                    $approvedCount++;
                }
                $parties[$key]['isActionable'] = $activeRole !== null
                    && $party['roleKey'] === $activeRole
                    && $party['decision'] === 'pending';
            }

            $gate['manager']  = $parties['manager'];
            $gate['teamLead'] = $parties['teamLead'];
            $gate['approvedCount'] = $approvedCount;
            $gate['percent']       = (int) round(($approvedCount / 2) * 100);

            return $gate;
        }, $gates);
    }

    public function queue(): void {
        $context   = $this->baseContext('/review', 'Review Queue');
        $projectId = $context['currentProjectId'] ?? null;
        $activeRole = $context['activeRole'] ?? null;

        $canManualReview = $activeRole === 'team_lead';
        $canJointApprove = in_array($activeRole, ['manager', 'team_lead'], true);

        $reviewData = $projectId ? $this->projectReviews((int) $projectId) : ['pending' => [], 'history' => []];
        $gates      = $projectId ? $this->projectGates((int) $projectId) : [];

        $subtitle = $canManualReview
            ? 'Tasks submitted for your review.'
            : 'Approval gates awaiting your decision.';

        $context = array_merge($context, [
            'subtitle'        => $subtitle,
            'canManualReview' => $canManualReview,
            'canJointApprove' => $canJointApprove,
            'pendingItems'    => $canManualReview ? $reviewData['pending'] : [],
            'historyItems'    => $canManualReview ? $reviewData['history'] : [],
            'gates'           => $canJointApprove ? $this->decorateGates($gates, $activeRole) : [],
        ]);

        $this->render('review/queue', $context);
    }
}