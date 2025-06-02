<?php 
$pageTitle = "Categorias - Classificados";
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="container mt-5">
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Categorias</h2>
        <a href="<?= BASE_URL ?>/admin/categories/create" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nova Categoria
        </a>
    </div>

    <table class="table table-bordered table-hover table-rounded shadow bg-white">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Nome</th>
                <th>Slug</th>
                <th>Palavras-chave</th>
                <th>Categoria Pai</th>
                <th>Ícone</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="categoriesTableBody">
            <!-- As categorias serão carregadas via AJAX -->
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

        // Função para carregar categorias com base na página
        function loadCategories(page) {
            $.ajax({
                url: "<?= BASE_URL ?>/admin/categories/fetch", // Rota para buscar as categorias
                method: "GET",
                data: {
                    page: page,
                    recordsPerPage: recordsPerPage
                },
                dataType: 'json', // Definido para JSON
                success: function(response) {
                    if (response.categoriesHtml && response.paginationHtml) {
                        // Exibe as categorias na tabela
                        $('#categoriesTableBody').html(response.categoriesHtml);

                        // Atualiza a navegação da paginação
                        $('#paginationNav .pagination').html(response.paginationHtml);
                    }
                },
                error: function(xhr, status, error) {
                    console.log("Erro no carregamento das categorias:", error);
                }
            });
        }

        // Carregar as categorias da primeira página
        loadCategories(currentPage);

        // Ação de navegação para próxima página
        $(document).on('click', '.page-link', function() {
            const page = $(this).data('page');
            currentPage = page;
            loadCategories(page);
        });
    });

</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
