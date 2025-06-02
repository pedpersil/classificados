<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../routes/web.php';

use Core\App;
use Core\Router;

session_name(SESSION_NAME);
session_start();

// Inicializa a aplicação
$app = new App();
$app->run();
