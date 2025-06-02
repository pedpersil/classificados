<?php 
$pageTitle = "Meus Anúncios - Classificados";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-4">
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <h2>Meus Anúncios</h2>
    <a href="<?= BASE_URL ?>/user/profile/ads/create" class="btn btn-success mb-3">Novo Anúncio</a>
    
        <table class="table table-bordered table-hover table-rounded shadow bg-white" id="adsTable">
            <thead class="table-dark">
                <tr>
                    <th>Imagem</th>
                    <th>Título</th>
                    <th>Status</th>
                    <th>Preço</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="adsTableBody">
                <!-- Dados dos anúncios serão carregados via AJAX -->
            </tbody>
        </table>
    
    <br>
    <!-- Navegação da páginação -->
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

    // Função para carregar anúncios com base na página
    function loadAds(page) {
        $.ajax({
            url: "<?= BASE_URL ?>/user/profile/ads/fetch", // Rota para buscar os anúncios
            method: "GET",
            data: {
                page: page,
                recordsPerPage: recordsPerPage
            },
            dataType: 'json', // Definido para JSON
            success: function(response) {
                if (response.adsHtml && response.paginationHtml) {
                    // Exibe os anúncios na tabela
                    $('#adsTableBody').html(response.adsHtml);

                    // Atualiza a navegação da páginação
                    $('#paginationNav .pagination').html(response.paginationHtml);
                }
            },
            error: function(xhr, status, error) {
                console.log("Erro no carregamento dos anúncios:", error);
            }
        });
    }

    // Carregar os anúncios da primeira página
    loadAds(currentPage);

    // Ação de navegação para próxima página
    $(document).on('click', '.page-link', function() {
        const page = $(this).data('page');
        currentPage = page;
        loadAds(page);
    });
});
</script>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
