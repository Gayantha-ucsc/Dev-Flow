<?php

class AdminAuditController extends Controller {
    private const PER_PAGE = 8;

    public function index(): void {
        $allEntries = require __DIR__ . '/../../../config/mock/audit-log.php';

        $search     = trim((string) ($_GET['q'] ?? ''));
        $actor      = $_GET['actor'] ?? 'all';
        $actionType = $_GET['action_type'] ?? 'all';
        $targetType = $_GET['target_type'] ?? 'all';
        $dateFrom   = $_GET['date_from'] ?? '';
        $dateTo     = $_GET['date_to'] ?? '';
        $page       = max(1, (int) ($_GET['page'] ?? 1));

        $actors = array_values(array_unique(array_column($allEntries, 'actor')));
        sort($actors);

        $filtered = array_values(array_filter($allEntries, function ($entry) use ($search, $actor, $actionType, $targetType, $dateFrom, $dateTo) {
            if ($search !== '') {
                $needle   = strtolower($search);
                $haystack = strtolower($entry['action'] . ' ' . $entry['details'] . ' ' . ($entry['target'] ?? ''));
                if (!str_contains($haystack, $needle)) {
                    return false;
                }
            }

            if ($actor !== 'all' && $entry['actor'] !== $actor) {
                return false;
            }
            if ($actionType !== 'all' && $entry['action_type'] !== $actionType) {
                return false;
            }
            if ($targetType !== 'all' && $entry['target_type'] !== $targetType) {
                return false;
            }

            $entryDate = substr($entry['created_at'], 0, 10);
            if ($dateFrom !== '' && $entryDate < $dateFrom) {
                return false;
            }
            if ($dateTo !== '' && $entryDate > $dateTo) {
                return false;
            }

            return true;
        }));

        $totalCount = count($filtered);
        $totalPages = max(1, (int) ceil($totalCount / self::PER_PAGE));
        $page       = min($page, $totalPages);
        $offset     = ($page - 1) * self::PER_PAGE;

        $this->render('admin/audit/index', mockPageContext('/admin/audit', 'Audit Log', [
            'heading'      => 'Audit Log',
            'isAdminMode'  => true,
            'entries'      => array_slice($filtered, $offset, self::PER_PAGE),
            'actors'       => $actors,
            'totalCount'   => $totalCount,
            'allCount'     => count($allEntries),
            'page'         => $page,
            'totalPages'   => $totalPages,
            'rangeStart'   => $totalCount === 0 ? 0 : $offset + 1,
            'rangeEnd'     => min($offset + self::PER_PAGE, $totalCount),
            'filters'      => [
                'q'           => $search,
                'actor'       => $actor,
                'action_type' => $actionType,
                'target_type' => $targetType,
                'date_from'   => $dateFrom,
                'date_to'     => $dateTo,
            ],
        ]));
    }
}