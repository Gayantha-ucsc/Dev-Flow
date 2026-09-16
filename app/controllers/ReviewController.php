<?php

class ReviewController extends Controller {

    private function baseContext(string $currentRoute, string $pageTitle): array {
        return mockPageContext($currentRoute, $pageTitle, ['heading' => $pageTitle]);
    }

    private function projectReviews(int $projectId): array {
        $all = require __DIR__ . '/../../config/mock/reviews.php';
        return $all[$projectId] ?? ['pending' => [], 'history' => []];
    }

    public function queue(): void {
        $projectId   = currentProjectContext()['currentProjectId'];
        $projectData = $projectId ? $this->projectReviews($projectId) : ['pending' => [], 'history' => []];

        $context = array_merge($this->baseContext('/review', 'Review & Approval'), [
            'pendingItems' => $projectData['pending'],
            'historyItems' => $projectData['history'],
        ]);

        $this->render('review/queue', $context);
    }
}