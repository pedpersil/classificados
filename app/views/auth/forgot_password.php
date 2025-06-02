<?php
$pageTitle = "Recuperar Senha - Classificados Taperoá";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-5 mb-5" style="max-width: 500px;">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h4 class="mb-4 text-center text-primary">Esqueceu sua senha?</h4>

            <?php if (!empty($_SESSION['flash_success'])): ?>
                <div class="alert alert-success"><?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
            <?php elseif (!empty($_SESSION['flash_error'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/forgot_password/send" method="POST" id="forgotPasswordForm">
                <div class="mb-3">
                    <label for="email" class="form-label">Informe seu e-mail de cadastro e enviaremos um link para redefinir sua senha.</label>
                    <input type="email" class="form-control" name="email" id="email" required placeholder="seuemail@exemplo.com">
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-envelope-at me-1"></i> Enviar link de redefinição
                </button>

                <!-- Loader do envio -->
                <div id="formLoader" style="display: none; margin-top: 15px;">
                    <div class="loader"></div>
                </div>
            </form>

            <div class="text-center mt-4">
                <a href="<?= BASE_URL ?>/login" class="text-decoration-none">← Voltar para o login</a>
            </div>
        </div>
    </div>
</div>

<!-- Script do loader -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('forgotPasswordForm');
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
