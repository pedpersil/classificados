<?php 
$pageTitle = "Editar Anúncio - Classificados";
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="container mt-4">
    <h2>Editar Anúncio</h2>

    <form action="<?= BASE_URL ?>/user/profile/ads/update/<?= $ad['id']; ?>" method="POST" enctype="multipart/form-data" class="card p-4 shadow" id="editAdForm">

        <!-- Campos padrão -->
        <div class="mb-3">
            <label for="title" class="form-label">Título</label>
            <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($ad['title']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Descrição</label>
            <textarea name="description" id="description" class="form-control" rows="4"><?= htmlspecialchars($ad['description']); ?></textarea>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Preço</label>
            <input type="text" name="price" id="price" class="form-control" value="<?= htmlspecialchars($ad['price']); ?>">
        </div>

        <div class="mb-3">
            <label for="category_id" class="form-label">Categoria</label>
            <select name="category_id" id="category_id" class="form-select" required>
                    <option value="">Selecione uma Categoria</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>" <?= $ad['category_id'] == $category['id'] ? 'selected' : '' ?>><?= $category['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Imagens já cadastradas -->
        <div class="mb-3">
            <label class="form-label">Imagens Atuais</label>
            <div class="d-flex flex-wrap gap-3">
                <?php if (!empty($ad['images'])): ?>
                    <?php foreach ($ad['images'] as $img): ?>
                        <div class="position-relative">
                            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($img['image_path']) ?>" class="rounded border" style="max-width: 160px; max-height: 160px;">
                            <a href="<?= BASE_URL ?>/user/profile/ads/delete-image/<?= $img['id'] ?>" onclick="return confirm('Deseja remover esta imagem?')" class="btn btn-sm btn-danger position-absolute top-0 end-0">
                                &times;
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p><em>Nenhuma imagem cadastrada.</em></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Novo envio de imagens -->
        <div class="mb-3">
            <label class="form-label">Novas Imagens</label>
            <div id="image-inputs">
                <div class="image-upload mb-2 d-flex align-items-start gap-3">
                    <div class="flex-grow-1">
                        <input type="file" name="images[]" accept="image/*" class="form-control image-compress" onchange="previewAndResizeImage(this)">
                        <img class="img-preview mt-2 d-none rounded border" style="max-width: 200px;" />
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger mt-1" onclick="removeImageInput(this)">Remover</button>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="addImageInput()">+ Adicionar outra imagem</button>
            <p class="text-muted small">Máximo 10 imagens no total.</p>
        </div>
        <button type="submit" class="btn btn-primary">Atualizar</button>
        <a href="<?= BASE_URL ?>/user/profile/ads" class="btn btn-secondary">Cancelar</a>
        <!-- Loader do envio -->
        <div id="formLoader" style="display: none; margin-top: 15px;">
            <div class="loader"></div>
        </div>
    </form>
</div>

<!-- Script do loader -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('editAdForm');
        const loader = document.getElementById('formLoader');
        const submitButton = form.querySelector('button[type="submit"]');

        form.addEventListener('submit', function () {
            loader.style.display = 'flex';
            submitButton.disabled = true;
        });
    });
    </script>

    <!-- Estilo do loader -->
    <style>
    .loader {
        border: 6px solid #f3f3f3;
        border-top: 6px solid #0d6efd;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
        margin: 0 auto;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    </style>

<!-- Scripts para manipular imagens -->
<script>
let imageCount = 1;

function addImageInput() {
    if (imageCount >= 10) return;

    const container = document.getElementById('image-inputs');
    const wrapper = document.createElement('div');
    wrapper.className = 'image-upload mb-2 d-flex align-items-start gap-3';

    wrapper.innerHTML = `
        <div class="flex-grow-1">
            <input type="file" name="images[]" accept="image/*" class="form-control image-compress" onchange="previewAndResizeImage(this)">
            <img class="img-preview mt-2 d-none rounded border" style="max-width: 200px;" />
        </div>
        <button type="button" class="btn btn-sm btn-outline-danger mt-1" onclick="removeImageInput(this)">Remover</button>
    `;
    container.appendChild(wrapper);
    imageCount++;
}

function removeImageInput(button) {
    const container = button.closest('.image-upload');
    if (container) {
        container.remove();
        imageCount--;
    }
}

function previewAndResizeImage(input) {
    const file = input.files[0];
    if (!file || !file.type.startsWith('image/')) return;

    const reader = new FileReader();
    reader.onload = function (e) {
        const img = new Image();
        img.onload = function () {
            const maxWidth = 800;
            const maxHeight = 800;

            let width = img.width;
            let height = img.height;

            if (width > maxWidth || height > maxHeight) {
                const scale = Math.min(maxWidth / width, maxHeight / height);
                width = Math.round(width * scale);
                height = Math.round(height * scale);
            }

            const canvas = document.createElement('canvas');
            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, width, height);

            canvas.toBlob(blob => {
                const newFile = new File([blob], file.name, { type: 'image/jpeg' });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(newFile);
                input.files = dataTransfer.files;

                const preview = input.parentNode.querySelector('.img-preview');
                preview.src = URL.createObjectURL(blob);
                preview.classList.remove('d-none');
            }, 'image/jpeg', 0.7);
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
