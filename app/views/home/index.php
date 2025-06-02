<?php 
$pageTitle = "Classificados Taperoá - Seu espaço para comprar, vender e anunciar na sua região.";
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="container mt-4">
    <h2 class="mb-4 text-center">Classificados Recentes</h2>
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <!-- Onde os anúncios serão carregados -->
    <div id="adsContainer" class="row row-cols-1 row-cols-md-3 g-4"></div>

    <!-- Paginação -->
    <nav class="mt-4 d-flex justify-content-center">
        <ul class="pagination" id="pagination"></ul>
    </nav>

    <br /><br /><br />
    <?php require_once __DIR__ . '/../ads/recent_carousel.php'; ?>
</div>

<script src="<?= BASE_URL ?>/assets/js/home.js"></script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
