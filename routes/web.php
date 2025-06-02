<?php
use Core\Router;

$router = new Router();

// Página inicial pública, exibição de todos os anuncios
Router::get('/', 'HomeController@index');

// Exibição individual do anuncio
Router::get('/ads/{id}', 'AdController@show');

Router::get('/ads/search/for', 'AdController@search');

// Login
Router::get('/login', 'AuthController@loginForm');
Router::post('/login', 'AuthController@login');

// Logout para ambos Admin e User
Router::get('/logout', 'AuthController@logout');

// Valida o email do novo usuario.
Router::get('/verify_email', 'AuthController@verifyEmail');

// Admin Dashboard
Router::get('/admin', 'DashboardController@index');

// Área administrativa - o Admin administra os usuários
Router::get('/admin/users', 'UserController@index');
// Acrescentar a rota para deletar user

Router::get('/admin/users/fetch', 'UserController@fetch');

// Exibe a lista de categorias
Router::get('/admin/categories/fetch', 'CategoryController@fetch');

// Área administrativa - o Admin administra as Categorias
Router::get('/admin/categories', 'CategoryController@index');
Router::get('/admin/categories/create', 'CategoryController@createForm');
Router::post('/admin/categories/store', 'CategoryController@store');
Router::get('/admin/categories/edit/{id}', 'CategoryController@editForm');
Router::post('/admin/categories/update/{id}', 'CategoryController@update');
Router::get('/admin/categories/delete/{id}', 'CategoryController@delete');

Router::get('/admin/users/toggle/{id}', 'UserController@toggleStatus');
Router::get('/admin/users/delete/{id}', 'UserController@delete');

Router::get('/admin/profile/password', 'UserController@changePasswordForm');
Router::post('/admin/profile/password/update', 'UserController@updatePassword');

// Registrar novo Usuario
Router::get('/user/create', 'UserController@createForm');
Router::post('/user', 'UserController@store');

// Perfil do usuário
Router::get('/user/profile', 'ProfileController@index');

// Perfil do usuário - Editar e Update
Router::get('/user/profile/edit', 'ProfileController@editForm');
Router::post('/user/profile/update', 'ProfileController@update');

// Anúncios do usuário - Exibição / Criação / Edição / Delete
Router::get('/user/profile/ads', 'AdController@index');
Router::get('/user/profile/ads/create', 'AdController@createForm');
Router::post('/user/profile/ads', 'AdController@store');
Router::get('/user/profile/ads/edit/{id}', 'AdController@editForm');
Router::post('/user/profile/ads/update/{id}', 'AdController@update');
Router::get('/user/profile/ads/toggle/{id}', 'AdController@toggleStatus');
Router::get('/user/profile/ads/delete/{id}', 'AdController@delete');

Router::get('/user/profile/ads/fetch', 'ProfileController@fetch');

Router::get('/user/profile/password', 'UserController@changePasswordForm');
Router::post('/user/profile/password/update', 'UserController@updatePassword');

// Deletando Imagens
Router::get('/user/profile/ads/delete-image/{id}', 'AdImageController@delete');

// Alteração de senha do usuário
Router::get('/user/password', 'UserController@showChangePasswordForm');
Router::post('/user/password', 'UserController@changePassword');

// Exclusão de conta do usuário
Router::get('/user/delete', 'UserController@showDeleteAccountForm');
Router::post('/user/delete', 'UserController@deleteAccount');

// Links
Router::get('/about', 'HomeController@about');
Router::get('/privacy-policy', 'HomeController@privacyPolicy');
Router::get('/contact', 'HomeController@contact');

// Votação
Router::post('/ads/rate', 'RatingController@vote');

Router::post('/ads/message', 'MessageController@store');
Router::get('/ads/messages/{id}', 'MessageController@fetch');
Router::post('/ads/message/delete', 'MessageController@delete');

Router::get('/forgot_password', 'AuthController@forgotPasswordForm');
Router::post('/forgot_password/send', 'AuthController@sendResetLink');

Router::get('/reset_password', 'AuthController@showResetForm');
Router::post('/reset_password/save', 'AuthController@saveNewPassword');
