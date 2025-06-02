<div class="container mt-4">
    <h2 class="mb-4 text-center">Resultados da busca</h2>
    <?php if (!empty($ads)): ?>
        <div class="row">
            <?php foreach ($ads as $ad): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow">
                        <img src="<?= BASE_URL ?>/<?= $ad['images'][0]['image_path'] ?? 'assets/images/no-image.png' ?>" class="card-img-top" style="height: 180px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($ad['title']) ?></h5>
                            <p class="card-text"><?= substr(strip_tags($ad['description']), 0, 100) ?>...</p>
                            <a href="<?= BASE_URL ?>/ads/<?= $ad['id'] ?>" class="btn btn-primary">Ver anúncio</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-muted">Nenhum resultado encontrado para essa busca.</p>
    <?php endif; ?>
</div>
