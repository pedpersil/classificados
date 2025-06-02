<?php 
$pageTitle = "Painel Administrativo - Classificados";
require_once __DIR__ . '/../layouts/header.php'; ?>

        <!-- Conteúdo principal -->
        <div class="col-md-9 col-lg-10 p-5">
            <h1>Bem-vindo, <?= $_SESSION[SESSION_NAME]['name']; ?>!</h1>
            <p>Este é o painel administrativo do sistema de classificados.</p>
        </div>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
