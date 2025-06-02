<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../app/Models/Ad.php';

$adsPerPage = 12; // Configurável
$pagesToShow = 6; // Número de páginas visíveis na paginação

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $adsPerPage;

$adModel = new \App\Models\Ad();
$totalAds = $adModel->getTotalAds();
$ads = $adModel->getAdsWithPagination($adsPerPage, $offset);
$totalPages = ceil($totalAds / $adsPerPage);

ob_start();
foreach ($ads as $ad): ?>
    <div class="col">
        <div class="card h-100 shadow">
            <?php if ($ad['image']): ?>
                <img src="<?= BASE_URL ?>/<?= $ad['image']; ?>" class="card-img-top" alt="Imagem do anúncio">
            <?php else: ?>
                <img src="<?= BASE_URL ?>/assets/images/no-image.png" class="card-img-top" alt="Sem imagem">
            <?php endif; ?>
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($ad['title']); ?></h5>
                <p class="card-text text-muted">R$ <?= number_format($ad['price'], 2, ',', '.'); ?></p>
                <a href="<?= BASE_URL ?>/ads/<?= $ad['id']; ?>" class="btn btn-primary btn-sm">Ver anúncio</a>
            </div>
        </div>
    </div>
<?php endforeach;
$adsHtml = ob_get_clean();

ob_start();

if ($totalPages > 1) {
    $half = floor($pagesToShow / 2);
    $startPage = max(1, $page - $half);
    $endPage = min($totalPages, $startPage + $pagesToShow - 1);

    if ($endPage - $startPage + 1 < $pagesToShow) {
        $startPage = max(1, $endPage - $pagesToShow + 1);
    }

    echo '<li class="page-item ' . ($page == 1 ? 'disabled' : '') . '">';
    echo '<a class="page-link" href="#" data-page="1">&laquo;</a></li>';

    echo '<li class="page-item ' . ($page == 1 ? 'disabled' : '') . '">';
    echo '<a class="page-link" href="#" data-page="' . ($page - 1) . '">&lsaquo;</a></li>';

    for ($i = $startPage; $i <= $endPage; $i++) {
        $active = ($i == $page) ? 'active' : '';
        echo '<li class="page-item ' . $active . '"><a class="page-link" href="#" data-page="' . $i . '">' . $i . '</a></li>';
    }

    echo '<li class="page-item ' . ($page == $totalPages ? 'disabled' : '') . '">';
    echo '<a class="page-link" href="#" data-page="' . ($page + 1) . '">&rsaquo;</a></li>';

    echo '<li class="page-item ' . ($page == $totalPages ? 'disabled' : '') . '">';
    echo '<a class="page-link" href="#" data-page="' . $totalPages . '">&raquo;</a></li>';
}

$paginationHtml = ob_get_clean();


header('Content-Type: application/json');
echo json_encode([
    'ads' => $adsHtml,
    'pagination' => $paginationHtml,
]);
