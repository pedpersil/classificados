<?php 
$pageTitle = "Login do Usuário - Classificados Taperoá";
require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4" style="max-width: 500px;">
    <h2>Login</h2>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['login_error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['login_error']; unset($_SESSION['login_error']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/login" class="card p-4 shadow" id="loginForm">
        <div class="mb-3">
            <label>Email:</label>
            <input type="email" name="email" required class="form-control" />
        </div>
        <div class="mb-3">
            <label>Senha:</label>
            <input type="password" name="password" required class="form-control" />
        </div>
        <!-- reCAPTCHA -->
        <div class="g-recaptcha mb-3" data-sitekey="6LcGqz0rAAAAAChFXB3izZM6gVqPlyE8dWsDolU1"></div>
        <button type="submit" class="btn btn-primary">Entrar</button>
        <!-- Loader do envio -->
        <div id="formLoader" style="display: none; margin-top: 15px;">
            <div class="loader"></div>
        </div>
        <a href="<?= BASE_URL ?>/forgot_password" class="btn btn-link">Redefinir Senha</a>
    </form>
</div>
<!-- Script do Google -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<!-- Script do loader -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('loginForm');
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
