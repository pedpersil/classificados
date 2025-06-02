<?php
$pageTitle = "Redefinir Senha - Classificados Taperoá";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-5 mb-5" style="max-width: 500px;">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h4 class="mb-4 text-center text-primary">Redefinir Senha</h4>

            <?php if (!empty($_SESSION['flash_error'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></div>
            <?php endif; ?>

            <?php if (!isset($_GET['token']) || empty($_GET['token'])): ?>
                <div class="alert alert-warning">Token inválido ou ausente.</div>
            <?php else: ?>
                <form action="<?= BASE_URL ?>/reset_password/save" method="POST" id="resetPasswordForm">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token']); ?>">

                    <div class="mb-3">
                        <label for="password" class="form-label">Nova Senha</label>
                        <input type="password" class="form-control" name="password" id="password" required minlength="6" placeholder="Nova senha">
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirmar Nova Senha</label>
                        <input type="password" class="form-control" name="confirm_password" id="confirm_password" required minlength="6" placeholder="Confirme a senha">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-lock-fill me-1"></i> Redefinir Senha
                    </button>

                     <!-- Loader do envio -->
                    <div id="formLoader" style="display: none; margin-top: 15px;">
                        <div class="loader"></div>
                    </div>
                </form>
            <?php endif; ?>

            <div class="text-center mt-4">
                <a href="<?= BASE_URL ?>/login" class="text-decoration-none">← Voltar para o login</a>
            </div>
        </div>
    </div>
</div>

<!-- Script do loader -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('resetPasswordForm');
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
