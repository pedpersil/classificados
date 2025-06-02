<?php 
$pageTitle = (isset($ad['title']) ? $ad['title'] : "Página não encontrada.") ." - Classificados Taperoá";
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../Helpers/Library.php';
//$userRating = null;
?>

<div class="container mt-5">
    <a href="<?= BASE_URL ?>/" class="btn btn-secondary mb-3">← Voltar para anúncios</a>

    <div class="card shadow p-4">
        <div class="row">
            <div class="col-md-6">
                <?php if (!empty($ad['images'])): ?>
                    <div id="carouselAd" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner rounded">
                            <?php foreach ($ad['images'] as $index => $image): ?>
                                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                    <img src="<?= BASE_URL ?>/<?= $image ?>" class="d-block w-100 rounded" alt="Imagem do anúncio">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselAd" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselAd" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                <?php else: ?>
                    <img src="<?= BASE_URL ?>/assets/images/no-image.png" class="img-fluid rounded" alt="Sem imagem">
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <h3><?= htmlspecialchars($ad['title']); ?></h3>

                <div class="mb-3">
                    <strong>Avaliação média:</strong> <?= $average ?> / 5
                    <br>

                    <div id="star-rating" class="mt-2">
                        <?php
                            $userLoggedIn = !empty($_SESSION[SESSION_NAME]);
                            for ($i = 1; $i <= 5; $i++):
                                $filled = $userRating !== null && $i <= $userRating;
                                $iconClass = $filled ? 'text-warning' : 'text-secondary';
                                $disabled = $userLoggedIn ? '' : 'opacity-50';
                        ?>
                            <i class="bi bi-star-fill star <?= $iconClass ?> <?= $disabled ?>" 
                            data-value="<?= $i ?>" 
                            style="cursor:pointer; font-size: 1.5rem;"></i>
                        <?php endfor; ?>
                    </div>

                    <?php if (!$userLoggedIn): ?>
                        <small class="text-muted">Faça login para avaliar este anúncio.</small>
                    <?php else: ?>
                        <div id="rating-message" class="mt-2 text-success">
                            <?= $userRating !== false ? "Sua nota: {$userRating} estrela" . ($userRating != 1 ? 's' : '') : '' ?>
                        </div>
                    <?php endif; ?>
                </div>


                <p class="text-muted">
                    Publicado em <?= date('d/m/Y', strtotime($ad['created_at'])); ?>
                </p>

                <!-- Compartilhar nas redes sociais -->
                <div class="mb-3">
                    <a href="#" class="text-decoration-none me-2" onclick="shareOnX(event)">
                        <i class="bi bi-twitter-x" style="font-size: 1.0rem;"></i>
                    </a>
                    <a href="#" class="text-decoration-none me-2" onclick="shareOnFacebook(event)">
                        <i class="bi bi-facebook" style="font-size: 1.0rem;"></i>
                    </a>
                    <a href="#" class="text-decoration-none me-2" onclick="shareOnLinkedIn(event)">
                        <i class="bi bi-linkedin" style="font-size: 1.0rem;"></i>
                    </a>
                    <a href="#" class="text-decoration-none" onclick="shareOnWhatsApp(event)">
                        <i class="bi bi-whatsapp" style="font-size: 1.0rem;"></i>
                    </a>
                </div>

                <h5 class="text-success">R$ <?= number_format($ad['price'], 2, ',', '.'); ?></h5>
                <p><?= Library::makeLinksClickable($ad['description']); ?></p>

                <hr>
                <h5>Contato do Anunciante</h5>
                <!-- Foto de perfil do anunciante -->
                <?php
                    $photo = !empty($ad['photo_url']) ? $ad['photo_url'] : 'assets/profiles/default.png';
                ?>
                <div class="mt-3 text-left">
                    <img src="<?= BASE_URL . '/' . $photo ?>" alt="Foto de perfil" class="rounded-circle shadow" style="width: 100px; height: 100px; object-fit: cover;">
                </div>
                <br />
                <p><strong>Nome:</strong> <?= htmlspecialchars($ad['user_name']); ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($ad['email']); ?></p>
                <?= !empty($ad['phone']) ? '<p><strong>Telefone: </strong>' . htmlspecialchars($ad['phone']) . '</p>' : '' ?>
            </div>
        </div>

        <hr>
        <h5>Mensagens sobre este anúncio</h5>

        <?php if (!empty($_SESSION[SESSION_NAME])): ?>
            <form id="messageForm" class="mb-3">
                <input type="hidden" name="ad_id" value="<?= $ad['id'] ?>">
                <textarea name="message" class="form-control mb-2" rows="3" placeholder="Digite sua mensagem..."></textarea>
                <button type="submit" class="btn btn-primary">Enviar</button>
            </form>
        <?php else: ?>
            <p class="text-muted">Faça login para interagir com mensagens.</p>
        <?php endif; ?>

        <div id="messageList" class="mt-3"></div>

    </div>
</div>

<script>
    const adUrl = encodeURIComponent("<?= BASE_URL ?>/ads/<?= $ad['id'] ?>");
    const adTitle = encodeURIComponent("<?= htmlspecialchars($ad['title']) ?>");

    function shareOnX(e) {
        e.preventDefault();
        const url = `https://x.com/intent/tweet?url=${adUrl}&text=${adTitle}`;
        window.open(url, '_blank');
    }

    function shareOnFacebook(e) {
        e.preventDefault();
        const url = `https://www.facebook.com/sharer/sharer.php?u=${adUrl}`;
        window.open(url, '_blank');
    }

    function shareOnLinkedIn(e) {
        e.preventDefault();
        const url = `https://www.linkedin.com/sharing/share-offsite/?url=${adUrl}`;
        window.open(url, '_blank');
    }

    function shareOnWhatsApp(e) {
        e.preventDefault();
        const url = `https://wa.me/?text=${adTitle}%20-%20${adUrl}`;
        window.open(url, '_blank');
    }
</script>

<script>
document.querySelectorAll('.star').forEach(star => {
    star.addEventListener('click', function () {
        const rating = this.dataset.value;
        const adId = <?= $ad['id'] ?>;

        // Checa se o usuário está logado (verifica se rating-message existe)
        if (!document.getElementById('rating-message')) {
            alert("Faça login para votar.");
            return;
        }

        fetch("<?= BASE_URL ?>/ads/rate", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `ad_id=${adId}&rating=${rating}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('rating-message').innerText = `Sua nota: ${rating} estrela${rating != 1 ? 's' : ''}. Nova média: ${data.average} / 5`;

                // Atualiza visual das estrelas
                document.querySelectorAll('.star').forEach(s => {
                    const val = parseInt(s.dataset.value);
                    if (val <= rating) {
                        s.classList.remove('text-secondary');
                        s.classList.add('text-warning');
                    } else {
                        s.classList.remove('text-warning');
                        s.classList.add('text-secondary');
                    }
                });
            } else {
                alert(data.error || "Erro ao registrar nota.");
            }
        });
    });
});
</script>

<script>
function nl2br(str, isXhtml) {
    if (typeof str === 'undefined' || str === null) {
        return '';
    }

    const breakTag = isXhtml || typeof isXhtml === 'undefined' ? '<br />' : '<br>';
    return (str + '').replace(/(\r\n|\n\r|\r|\n)/g, breakTag + '$1');
}

function makeLinksClickable(text) {
    const urlPattern = /(https?:\/\/[^\s<]+|www\.[^\s<]+)/gi;
    return text.replace(urlPattern, function(url) {
        let href = url;
        if (!href.match('^https?://')) {
            href = 'https://' + href;
        }
        return `<a href="${href}" target="_blank" rel="noopener noreferrer">${url}</a>`;
    });
}

function loadMessages() {
    fetch("<?= BASE_URL ?>/ads/messages/<?= $ad['id'] ?>")
        .then(res => res.json())
        .then(data => {
            let html = '';
            data.forEach(msg => {
                const photo = msg.photo_url ? `<?= BASE_URL ?>/${msg.photo_url}` : '<?= BASE_URL ?>/assets/profiles/default.png';
                html += `
                        <div class="d-flex align-items-start mb-3 message-item" data-id="${msg.id}">
                            <img src="${photo}" class="rounded-circle me-3" style="width:50px; height:50px; object-fit:cover;">
                            <div class="w-100">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>${msg.name}</strong>
                                        <small class="text-muted d-block">${new Date(msg.created_at).toLocaleString('pt-BR')}</small>
                                    </div>
                                    ${msg.user_id == <?= $_SESSION[SESSION_NAME]['id'] ?? 0 ?> || '<?= $_SESSION[SESSION_NAME]['role'] ?? '' ?>' === 'admin'
                                        ? `<button class="btn btn-sm btn-danger delete-message" data-id="${msg.id}">Excluir</button>` 
                                        : ''}
                                </div>
                                <p class="mb-0">${makeLinksClickable(nl2br(msg.message))}</p>
                            </div>
                        </div>
                    `;
            });
            document.getElementById('messageList').innerHTML = html;
        });
}

// Carrega ao entrar na página
loadMessages();

// Envia nova mensagem
const form = document.getElementById('messageForm');
if (form) {
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(form);
        fetch("<?= BASE_URL ?>/ads/message", {
            method: 'POST',
            body: new URLSearchParams(formData)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                form.reset();
                loadMessages();
            } else {
                alert(data.error);
            }
        });
    });
}

document.addEventListener('click', function (e) {
    if (e.target.classList.contains('delete-message')) {
        const messageId = e.target.dataset.id;

        if (confirm('Deseja realmente excluir esta mensagem?')) {
            fetch("<?= BASE_URL ?>/ads/message/delete", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `message_id=${messageId}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.querySelector(`.message-item[data-id="${messageId}"]`).remove();
                } else {
                    alert(data.error || 'Erro ao excluir mensagem.');
                }
            });
        }
    }
});

</script>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
