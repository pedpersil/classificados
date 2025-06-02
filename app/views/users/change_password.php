<?php 
$pageTitle = "Alterar Senha - Classificados";
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="container mt-4" style="max-width: 500px;">
    <h2>Alterar Senha</h2>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/user/profile/password/update" method="POST" class="card p-4 shadow" id="changePassword">
        <div class="mb-3">
            <label for="current_password" class="form-label">Senha Atual</label>
            <input type="password" name="current_password" id="current_password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="new_password" class="form-label">Nova Senha</label>
            <input type="password" name="new_password" id="new_password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirmar Nova Senha</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Salvar Nova Senha</button>
        <!-- Loader do envio -->
        <div id="formLoader" style="display: none; margin-top: 15px;">
            <div class="loader"></div>
        </div>
    </form>
</div>

<!-- Script do loader -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('changePassword');
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
