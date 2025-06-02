<?php 
$pageTitle = "Preencha os dados para criar a sua conta - Classificados";
require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <h2>Preencha os dados para criar sua conta</h2>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <form method="POST" id="userCreateForm" action="<?= BASE_URL ?>/user" enctype="multipart/form-data" class="card p-4 shadow">

        <!-- Foto -->
        <div class="mb-3">
            <label for="photo" class="form-label">Foto de Perfil</label>
            <input type="file" name="photo" id="photo" class="form-control" onchange="previewImage(event)">
            <div id="photoPreview" class="mt-3"></div>
            <button type="button" id="removeImageBtn" class="btn btn-danger mt-2 d-none" onclick="removeImage()">Remover Imagem</button>
        </div>

        <!-- Nome -->
        <div class="mb-3">
            <label for="name" class="form-label">Nome <span class="texto-vermelho">* Obrigatório</span></label>
            <input type="text" name="name" id="name" class="form-control" maxlength="80" required>
        </div>

        <!-- Telefone -->
        <div class="mb-3">
            <label for="phone" class="form-label">Telefone</label>
            <input type="text" name="phone" id="phone" maxlength="20" class="form-control">
        </div>

        <!-- Endereço -->
        <div class="mb-3">
            <label for="address" class="form-label">Endereço</label>
            <input type="text" name="address" id="address" maxlength="80" class="form-control">
        </div>

        <!-- Cidade -->
        <div class="mb-3">
            <label for="city" class="form-label">Cidade</label>
            <input type="text" name="city" id="city" maxlength="50" class="form-control">
        </div>

        <!-- Estado -->
        <div class="mb-3">
            <label for="state" class="form-label">Estado</label>
            <input type="text" name="state" id="state" maxlength="60" class="form-control">
        </div>

        <!-- CEP -->
        <div class="mb-3">
            <label for="zip_code" class="form-label">CEP</label>
            <input type="text" name="zip_code" id="zip_code" maxlength="10" class="form-control">
        </div>

        <!-- RG -->
        <div class="mb-3">
            <label for="rg" class="form-label">RG</label>
            <input type="text" name="rg" id="rg" maxlength="20" class="form-control">
        </div>

        <!-- CPF -->
        <div class="mb-3">
            <label for="cpf" class="form-label">CPF</label>
            <input type="text" name="cpf" id="cpf" maxlength="14" class="form-control">
            <small id="cpf-error" class="form-text text-danger" style="display: none;">CPF inválido!</small>
        </div>

        <!-- Sexo -->
        <div class="mb-3">
            <label for="gender" class="form-label">Sexo</label>
            <select name="gender" id="gender" class="form-select">
                <option value="">Escolha um gênero</option>
                <option value="male">Masculino</option>
                <option value="female">Feminino</option>
                <option value="other">Outro</option>
            </select>
        </div>

        <!-- Data Nasc -->
        <div class="mb-3">
            <label for="birth_date" class="form-label">Data de Nascimento</label>
            <input type="date" name="birth_date" id="birth_date" class="form-control">
        </div>
        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Email <span class="texto-vermelho">* Obrigatório</span></label>
            <input type="email" name="email" id="email" maxlength="50" class="form-control" required>
        </div>

        <!-- Senha -->
        <div class="mb-3">
            <label for="password" class="form-label">Senha <span class="texto-vermelho">* Obrigatório</span></label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

         <!-- Confirmar Senha -->
         <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirmar Senha <span class="texto-vermelho">* Obrigatório</span></label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
         </div>

        <button type="submit" class="btn btn-primary">Criar Novo Usuário</button>
        <!-- Loader do envio do formulário -->
        <div id="formLoader" style="display: none; margin-top: 15px;">
        <div class="loader"></div>
        </div>
    </form>

</div>

<!-- Preview final -->
<div id="photoPreview" class="mt-3"></div>

<!-- Modal -->
<div class="modal fade" id="resizeModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cortar imagem de perfil</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
      <img id="cropperImage" style="width:100%; height:auto; max-height:80vh;">
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary" onclick="cropImage()">Redimensionar Imagem</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('userCreateForm'); // ID do seu form
  const loader = document.getElementById('formLoader');

  if (form && loader) {
    form.addEventListener('submit', function () {
      loader.style.display = 'flex'; // mostra o loader
      form.querySelector('button[type="submit"]').disabled = true; // evita múltiplos envios
    });
  }
});
</script>

<script>
let cropper;

function previewImage(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
        const image = document.getElementById('cropperImage');
        image.src = e.target.result;

        image.onload = function () {
            if (cropper) cropper.destroy();
            cropper = new Cropper(image, {
                aspectRatio: 1,
                viewMode: 1,
                autoCropArea: 1,
            });

            new bootstrap.Modal(document.getElementById('resizeModal')).show();
        };
    };
    reader.readAsDataURL(file);
}

function cropImage() {
    const canvas = cropper.getCroppedCanvas({
        width: 600,
        height: 600,
    });

    canvas.toBlob(function (blob) {
        const preview = document.getElementById('photoPreview');
        const url = URL.createObjectURL(blob);

        // Atualiza preview
        preview.innerHTML = `<img src="${url}" class="rounded" width="150">`;
        document.getElementById('removeImageBtn').classList.remove('d-none');

        // Substitui o arquivo do input file
        const fileInput = document.getElementById('photo');
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(new File([blob], 'foto-perfil.jpg', { type: 'image/jpeg' }));
        fileInput.files = dataTransfer.files;

        // Fecha modal
        bootstrap.Modal.getInstance(document.getElementById('resizeModal')).hide();
    }, 'image/jpeg', 0.7);
}

function removeImage() {
    document.getElementById('photoPreview').innerHTML = '';
    document.getElementById('removeImageBtn').classList.add('d-none');
    document.getElementById('photo').value = '';
    if (cropper) cropper.destroy();
}
</script>

<script>
document.getElementById('cpf').addEventListener('input', function (e) {
    let cpf = e.target.value;

    // Remove tudo que não for número
    cpf = cpf.replace(/\D/g, '');

    // Formata o CPF no formato 000.000.000-00
    if (cpf.length <= 3) {
        cpf = cpf.replace(/(\d{1,3})/, '$1');
    } else if (cpf.length <= 6) {
        cpf = cpf.replace(/(\d{3})(\d{1,3})/, '$1.$2');
    } else if (cpf.length <= 9) {
        cpf = cpf.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
    } else if (cpf.length <= 11) {
        cpf = cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
    }

    // Atualiza o valor do campo de CPF com o formato
    e.target.value = cpf;

    // Valida o CPF
    if (cpf.length === 14 && !isCPFValido(cpf.replace(/\D/g, ''))) {
        document.getElementById('cpf-error').style.display = 'block';
    } else {
        document.getElementById('cpf-error').style.display = 'none';
    }
});

// Função que valida o CPF de acordo com o algoritmo
function isCPFValido(cpf) {
    // Elimina CPFs invalidos conhecidos
    if (/^(000|111|222|333|444|555|666|777|888|999)\d{7}$/.test(cpf)) {
        return false;
    }

    // Valida os dois dígitos verificadores
    let soma = 0;
    let peso = 10;

    for (let i = 0; i < 9; i++) {
        soma += parseInt(cpf.charAt(i)) * peso;
        peso--;
    }

    let resto = soma % 11;
    let digito1 = resto < 2 ? 0 : 11 - resto;

    if (parseInt(cpf.charAt(9)) !== digito1) {
        return false;
    }

    soma = 0;
    peso = 11;

    for (let i = 0; i < 10; i++) {
        soma += parseInt(cpf.charAt(i)) * peso;
        peso--;
    }

    resto = soma % 11;
    let digito2 = resto < 2 ? 0 : 11 - resto;

    return parseInt(cpf.charAt(10)) === digito2;
}
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
