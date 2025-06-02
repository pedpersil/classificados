<?php 
$pageTitle = "Gerenciar Usuários - Classificados";
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="container mt-5">
    <h2>Gerenciar Usuários</h2>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <table class="table table-bordered table-hover table-rounded shadow bg-white">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Perfil</th>
                <th>Status</th>
                <th>Data de Cadastro</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="usersTableBody">
            <!-- Os usuários serão carregados via AJAX -->
        </tbody>
    </table>

    <!-- Navegação da paginação -->
    <nav id="paginationNav">
        <ul class="pagination justify-content-center">
            <!-- As páginas serão geradas dinamicamente com AJAX -->
        </ul>
    </nav>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    let currentPage = 1;
    const recordsPerPage = 15;

    // Função para carregar usuários com base na página
    function loadUsers(page) {
        $.ajax({
            url: "<?= BASE_URL ?>/admin/users/fetch", // Rota para buscar os usuários
            method: "GET",
            data: {
                page: page,
                recordsPerPage: recordsPerPage
            },
            dataType: 'json', // Definido para JSON
            success: function(response) {
                if (response.usersHtml && response.paginationHtml) {
                    // Exibe os usuários na tabela
                    $('#usersTableBody').html(response.usersHtml);

                    // Atualiza a navegação da paginação
                    $('#paginationNav .pagination').html(response.paginationHtml);
                }
            },
            error: function(xhr, status, error) {
                console.log("Erro no carregamento dos usuários:", error);
            }
        });
    }

    // Carregar os usuários da primeira página
    loadUsers(currentPage);

    // Ação de navegação para próxima página
    $(document).on('click', '.page-link', function() {
        const page = $(this).data('page');
        currentPage = page;
        loadUsers(page);
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
