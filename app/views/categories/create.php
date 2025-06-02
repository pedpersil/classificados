<?php 
$pageTitle = "Nova Categoria - Classificados";
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="container mt-5">
    <h2 class="mb-4">Nova Categoria</h2>

    <form action="<?= BASE_URL ?>/admin/categories/store" method="POST" enctype="multipart/form-data" class="card p-4 shadow" id="categoriesCreateForm">

        <div class="mb-3">
            <label for="name" class="form-label">Nome da Categoria</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="slug" class="form-label">Slug (URL amigável)</label>
            <input type="text" name="slug" id="slug" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="parent_id" class="form-label">Categoria Pai (opcional)</label>
            <select name="parent_id" id="parent_id" class="form-select">
                <option value="">— Nenhuma (categoria principal) —</option>
                <?php foreach ($parents as $parent): ?>
                    <option value="<?= $parent['id']; ?>"><?= htmlspecialchars($parent['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="keywords" class="form-label">Palavras-chave (separadas por vírgulas)</label>
            <input type="text" name="keywords" id="keywords" class="form-control">
        </div>

        <div class="mb-3">
            <label for="icon" class="form-label">Ícone (opcional)</label>
            <input type="file" name="icon" id="icon" class="form-control" accept="image/*">
        </div>

        <div class="d-flex justify-content-between">
            <a href="<?= BASE_URL ?>/admin/categories" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
            <button type="submit" class="btn btn-success">
                <i class="bi bi-check-circle"></i> Salvar Categoria
            </button>
        </div>
        <!-- Loader do envio -->
        <div id="formLoader" style="display: none; margin-top: 15px;">
            <div class="loader"></div>
        </div>
    </form>
</div>

<!-- Script do loader -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('categoriesCreateForm');
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

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
