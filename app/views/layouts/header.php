<?php
$user = $_SESSION[SESSION_NAME] ?? null;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Meu Anúncio Local' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?= BASE_URL ?>/assets/images/favicon.png" type="image/x-icon">
    <meta name="author" content="Pedro Silva Tech" />

    <?php 
        

    // Monta meta tags dinâmicas
    $metaDescription = !empty($ad['description'])
        ? mb_strimwidth(strip_tags($ad['description']), 0, 160, '...')
        : "Confira este anúncio no Classificados Taperoá. Produtos, serviços e oportunidades na sua cidade.";

    $metaKeywords = implode(', ', array_filter([
        $ad['title'] ?? '',
        $ad['category']['name'] ?? '',
        $ad['parent_category']['name'] ?? '',
        $ad['category']['keywords'] ?? '',
        $ad['parent_category']['keywords'] ?? '',
        'anúncio', 'comprar', 'vender', 'Taperoá', 'Classificados Taperoá'
    ]));

    ?>

    <meta name="description" content="<?= !empty($ad) ? htmlspecialchars($metaDescription) : 'Classificados Taperoá é a plataforma ideal para você divulgar seus produtos, serviços e oportunidades na região. Cadastre-se grátis, confirme seu e-mail e comece a anunciar para toda Taperoá e arredores!' ?>">
    <meta name="keywords" content="<?= !empty($ad) ? htmlspecialchars($metaKeywords) : 'classificados Taperoá, anúncios Taperoá, vender em Taperoá, serviços Taperoá, produtos locais, classificados online, divulgar em Taperoá, anunciar grátis, artesanato Taperoá, comércio local Taperoá' ?>">

    <?php
    $mainImage = (isset($ad) && !empty($ad['images']) && isset($ad['images'][0]))
        ? (
            (isset($ad['images'][0]['id']) && !empty($ad['images'][0]['id']))
                ? BASE_URL . '/assets/images/logo-og.jpg'
                : BASE_URL . '/' . $ad['images'][0]
        )
        : BASE_URL . '/assets/images/logo-og.jpg';


    $shortDescription = (isset($ad) && !empty($ad['description']))
        ? mb_strimwidth(strip_tags($ad['description']), 0, 160, '...')
        : 'Classificados Taperoá é a plataforma ideal para você divulgar seus produtos, serviços e oportunidades na região.';

    $adTitle = (isset($ad) && !empty($ad['title']))
        ? $ad['title'] . ' - Classificados Taperoá'
        : 'Classificados Taperoá - Seu espaço para comprar, vender e anunciar na sua região.';
    ?>

    <!-- Meta OG para compartilhamento -->
    <meta property="og:title" content="<?= htmlspecialchars($adTitle) ?>" />
    <meta property="og:description" content="<?= htmlspecialchars($shortDescription) ?>" />
    <meta property="og:image" content="<?= $mainImage ?>" />
    <meta property="og:url" content="<?= !empty($ad) ? BASE_URL . '/ads/' . $ad['id'] : BASE_URL ?>" />
    <meta property="og:type" content="article" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:image" content="<?= $mainImage ?>" />
    <meta name="twitter:title" content="<?= htmlspecialchars($adTitle) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($metaDescription) ?>">
    <meta name="twitter:image" content="<?= $mainImage ?>">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap Bundle (inclui Modal + Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Adicionando bibliotecas para manipulação de imagens -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pica/7.0.0/pica.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    <!-- Cropper.js -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">

    <!-- Bootstrap, jQuery e Cropper.js -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    
    <script>
        const BASE_URL = "<?= BASE_URL ?>";
    </script>

    <script src="<?= BASE_URL ?>/assets/js/search.js"></script>

    <style>
    body {
        background-color: #f9fafa; /* fundo claro e limpo */
        background-image: none; /* banner removido para tema claro */
        color: #212529;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .navbar {
        background-color: #1e629c !important;
        border-bottom: 1px solid #dee2e6;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
    }

    .nav-link {
        color:rgb(237, 238, 238) !important;
        font-weight: 500;
        transition: color 0.3s ease, border-bottom 0.3s ease;
        border-bottom: 2px solid transparent;
    }

    .nav-link:hover {
        color: #fdfdfd !important; /* azul padrão bootstrap */
        border-bottom: 2px solid #fdfdfd;
    }

    .navbar-brand strong {
        color: #0d6efd;
    }

    .nav-item {
        margin: 2px;
        padding: 0px;  
    }
    
    .btn-outline-light, .btn-outline-success {
        color: #0d6efd;
        border-color: #0d6efd;
        background-color: #fff;
        transition: all 0.3s ease-in-out;
    }

    .btn-outline-light:hover,
    .btn-outline-success:hover {
        background-color: #0d6efd;
        color: #fff;
    }

    .btn-outline-secondary:hover {
        background-color: #6c757d;
        color: #fff;
    }

    .btn-outline-primary:hover {
        background-color: #0d6efd;
        color: #fff;
    }

    .profile-pic {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #dee2e6;
    }

    .logo-img {
        height: 64px;
        margin-right: 10px;
    }

    .icon {
        height: 26px;
    }

    @media (max-width: 768px) {
        .logo-img {
            height: 48px;
        }
    }

    h2 {
        color: #0d6efd;
    }

    .dropdown-menu {
        border-radius: 0.5rem;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    }

    /* Loader */
    #pageLoader {
        background: rgba(255, 255, 255, 0.9);
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 1;
        transition: opacity 0.5s ease;
    }

    /* Loader circular */
    #searchLoader {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 20px;
    }

    .loader {
    border: 6px solid #f3f3f3;
    border-top: 6px solid #0d6efd;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
    }

    /* Animação de rotação */
    @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
    }

    #searchResultsContainer {
    min-height: 10px;
    background-color: #f9fbfa;
    z-index: 10;
    }
    

    #searchResultsContainer.loaded {
    opacity: 1;
    }

    /* Estilo geral para caixas e painéis */
    .card, .shadow {
        border-radius: 0.75rem;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }

    .name {
        color: #fdfdfd;
    }

    .booton-link {
        color: #0d6efd;
    }

    .texto-vermelho {
        color: red;
        font-size: 10px;
    }
    .table-rounded {
        border-radius: 12px; /* Altere o valor conforme necessário */
        overflow: hidden; /* Garante que as bordas arredondadas não sejam cortadas */
        border: 1px solid #ddd; /* Adiciona uma borda leve */
    }

    .search-highlight {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        transition: background-color 0.3s ease;
    }

</style>

</head>
<body>

<!-- Loader -->
<div id="pageLoader">
    <div class="loader"></div> <!-- Animação de carregamento -->
</div>


<script>
window.addEventListener('load', function () {
    const loader = document.getElementById('pageLoader');
    if (loader) {
        loader.style.transition = "opacity 0.5s ease";
        loader.style.opacity = "0";  // Suaviza a transição de fade out
        setTimeout(() => loader.remove(), 500);  // Remove o loader após a animação
    }
});
</script>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>/">
            <img src="<?= BASE_URL ?>/assets/images/logo-3.png" class="logo-img" alt="Meu Anúncio Local - Seu espaço para comprar, vender e anunciar na sua região.">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menuNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/"><i class="bi bi-house-door me-1"></i>Início</a></li>

                <?php if (isset($user['role']) && $user['role'] === 'user'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>Minha Conta
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/user/profile"><i class="bi bi-person-lines-fill me-1"></i> Meu Perfil</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/user/profile/edit"><i class="bi bi-pencil-square"></i> Editar Perfil</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/user/profile/ads"><i class="bi bi-megaphone me-1"></i> Meus Anúncios</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/user/profile/password"><i class="bi bi-shield-lock"></i> Alterar Senha</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/user/delete"><i class="bi bi-x-circle-fill me-1 text-danger"></i> Excluir Conta</a></li>
                        </ul>
                    </li>
                <?php elseif (isset($user['role']) && $user['role'] === 'admin'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-shield-lock-fill me-1"></i>Administração
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin"><i class="bi bi-speedometer2 me-1"></i>Painel Admin</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/users"><i class="bi bi-people me-1"></i>Gerenciar Usuários</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/categories"><i class="bi bi-tags me-1"></i>Gerenciar Categorias</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/profile/password"><i class="bi bi-shield-lock"></i> Alterar Senha</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
                        
            

            <ul class="navbar-nav mb-2 mb-lg-0">
                <?php if ($user): ?>
                    <li class="nav-item d-flex align-items-center me-2 text-dark fw-semibold">
                        <img src="<?= BASE_URL ?>/<?= $userInfo['photo_url'] ?? 'assets/profiles/default.png'; ?>" class="profile-pic me-2" alt="Foto de perfil">
                        <span class="name"><?= htmlspecialchars($user['name']) ?></span>
                    </li>
                    <li class="nav-item me-2">
                        <a href="<?= BASE_URL ?>/logout">
                            <img src="<?= BASE_URL ?>/assets/images/logout.png" class="icon" alt="Sair">
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item me-2">
                        <a href="<?= BASE_URL ?>/user/create">
                        <img src="<?= BASE_URL ?>/assets/images/new.png" class="icon" alt="Crie a sua conta">
                        </a>
                    </li>
                    <li class="nav-item me-2">
                        <a href="<?= BASE_URL ?>/login">
                        <img src="<?= BASE_URL ?>/assets/images/login.png" class="icon" alt="Entrar"></a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<!-- Bloco de busca destacado -->
<div class="search-highlight py-4 px-3 shadow-sm">
  <div class="container">
    <form class="row justify-content-center gx-2 gy-2" id="searchForm">
    <div class="col-12 col-md-6">
        <input class="form-control" type="search" name="term" id="searchInput" placeholder="Buscar anúncios..." aria-label="Buscar">
    </div>
    <div class="col-auto">
        <button class="btn btn-outline-primary w-100" type="submit">
        <i class="bi bi-search me-1"></i> Buscar
        </button>
    </div>
    </form>
  </div>
</div>
<!-- Loader da busca -->
<div id="searchLoader" style="display: none;" class="text-center my-3">
  <div class="loader"></div>
</div>
<!-- Resultados da busca -->
<div id="searchResultsContainer" class="mt-3"></div>
