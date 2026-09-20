<?php
// Expects:
// $page        - current page number (1-based)
// $totalPages  - total number of pages
// $baseParams  - array of query params to preserve (e.g. filters), 'page' will be overridden
// $baseUrl     - path to paginate, e.g. '/admin/users'

$baseParams = $baseParams ?? [];

if (!function_exists('paginationUrl')) {
    function paginationUrl(string $baseUrl, array $params, int $page): string {
        $params['page'] = $page;
        $query = http_build_query(array_filter($params, fn($v) => $v !== '' && $v !== null));
        return url($baseUrl) . ($query !== '' ? '?' . $query : '');
    }
}
?>
<?php if ($totalPages > 1): ?>
<nav class="pagination" aria-label="Pagination">
    <a href="<?= paginationUrl($baseUrl, $baseParams, max(1, $page - 1)) ?>"
       class="pagination__arrow <?= $page <= 1 ? 'is-disabled' : '' ?>"
       aria-label="Previous page">
        <?= renderIcon('arrow-left') ?>
    </a>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="<?= paginationUrl($baseUrl, $baseParams, $i) ?>"
           class="pagination__page <?= $i === $page ? 'is-active' : '' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>

    <a href="<?= paginationUrl($baseUrl, $baseParams, min($totalPages, $page + 1)) ?>"
       class="pagination__arrow <?= $page >= $totalPages ? 'is-disabled' : '' ?>"
       aria-label="Next page">
        <?= renderIcon('arrow-right') ?>
    </a>
</nav>
<?php endif; ?>