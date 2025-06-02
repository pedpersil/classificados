<?php if (!empty($ads)): ?>
<div id="recentAdsCarousel" class="carousel slide mb-5" data-bs-ride="carousel">
    <h2 class="mb-4 text-center">Vistos Recentemente</h2>
    <div class="carousel-inner">

        <?php
        $adsPerSlide = 4; // Quantidade de itens por slide em desktop
        $chunks = array_chunk($ads, $adsPerSlide);
        foreach ($chunks as $index => $group):
        ?>
            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                <div class="row justify-content-center">
                    <?php foreach ($group as $ad): ?>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                            <div class="card h-100 shadow">
                                <?php
                                    $image = $ad['image'] ?? 'assets/images/no-image.png';
                                ?>
                                <img src="<?= BASE_URL ?>/<?= $image ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <h5 class="card-title"><?= htmlspecialchars($ad['title']) ?></h5>
                                    <a href="<?= BASE_URL ?>/ads/<?= $ad['id'] ?>" class="btn btn-primary mt-auto">Ver Anúncio</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#recentAdsCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#recentAdsCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>
<?php endif; ?>
