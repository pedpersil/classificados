<?php 
$pageTitle = "Meu Perfil - Classificados";
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="container mt-5 mb-5" style="max-width: 900px;">
    <h2>Meu Perfil</h2>
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <div class="card shadow-lg border-0">
        <div class="card-body p-4">
    
            <!-- Foto e nome -->
            <div class="text-center mb-4">
                <img src="<?= BASE_URL ?>/<?= $userInfo['photo_url'] ?? 'assets/profiles/default.png'; ?>" alt="Foto de perfil" class="rounded-circle shadow" width="120" height="120">
                <h3 class="mt-3"><?= htmlspecialchars($userInfo['name']) ?></h3>
            </div>

            <!-- Informações divididas em colunas -->
            <div class="row g-4">

                <!-- Coluna 1 -->
                <div class="col-md-6">
                    <h5 class="border-bottom pb-2">Contato</h5>
                    <p><strong>Email:</strong> <?= htmlspecialchars($userInfo['email'] ?? 'Não informado') ?></p>
                    <p><strong>Telefone:</strong> <?= htmlspecialchars($userInfo['phone'] ?? 'Não informado') ?></p>
                    <p><strong>Sexo:</strong> 
                        <?php
                            echo match($userInfo['gender'] ?? '') {
                                'male' => 'Masculino',
                                'female' => 'Feminino',
                                'other' => 'Outro',
                                default => 'Não informado',
                            };
                        ?>
                    </p>
                    <p><strong>Data de Nascimento:</strong> 
                        <?= !empty($userInfo['birth_date']) ? date('d/m/Y', strtotime($userInfo['birth_date'])) : 'Não informada' ?>
                    </p>
                </div>

                <!-- Coluna 2 -->
                <div class="col-md-6">
                    <h5 class="border-bottom pb-2">Localização</h5>
                    <p><strong>Endereço:</strong> <?= htmlspecialchars($userInfo['address'] ?? 'Não informado') ?></p>
                    <p><strong>Cidade:</strong> <?= htmlspecialchars($userInfo['city'] ?? 'Não informado') ?></p>
                    <p><strong>Estado:</strong> <?= htmlspecialchars($userInfo['state'] ?? 'Não informado') ?></p>
                    <p><strong>CEP:</strong> <?= htmlspecialchars($userInfo['zip_code'] ?? 'Não informado') ?></p>
                </div>

                <!-- Documentos -->
                <div class="col-md-6">
                    <h5 class="border-bottom pb-2">Documentos</h5>
                    <p><strong>RG:</strong> <?= htmlspecialchars($userInfo['rg'] ?? 'Não informado') ?></p>
                    <p><strong>CPF:</strong> <?= htmlspecialchars($userInfo['cpf'] ?? 'Não informado') ?></p>
                </div>

                <!-- Datas -->
                <div class="col-md-6">
                    <h5 class="border-bottom pb-2">Registro</h5>
                    <p><strong>Criado em:</strong> <?= date('d/m/Y H:i', strtotime($userInfo['created_at'] ?? 'now')) ?></p>
                    <?php if (!empty($userInfo['updated_at'])): ?>
                        <p><strong>Atualizado em:</strong> <?= date('d/m/Y H:i', strtotime($userInfo['updated_at'])) ?></p>
                    <?php endif; ?>
                </div>

                <div class="text-center mt-3">
                    <a href="<?= BASE_URL ?>/user/profile/edit" class="btn btn-outline-primary">
                        <i class="bi bi-pencil"></i> Editar Perfil
                    </a>
                </div>

            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
