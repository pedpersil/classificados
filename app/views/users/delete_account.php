<?php 
$pageTitle = "Excluir Conta - Classificados";
require_once __DIR__ . '/../layouts/header.php'; 
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container">
    <h2>Excluir Conta</h2>
    
    <form id="deleteAccountForm" action="<?= BASE_URL . '/user/delete' ?>" method="POST" class="card p-4 shadow">
        <p>Tem certeza de que deseja excluir sua conta? Esta ação é irreversível.</p>
        <button type="submit" class="btn btn-danger" id="deleteBtn">Excluir Conta</button>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById('deleteAccountForm');
    const deleteBtn = document.getElementById('deleteBtn');

    deleteBtn.addEventListener('click', function (e) {
        e.preventDefault(); // Impede o envio imediato do formulário

        Swal.fire({
            title: 'Tem certeza?',
            text: "Esta ação excluirá permanentemente sua conta!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit(); // Envia o formulário se confirmado
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
