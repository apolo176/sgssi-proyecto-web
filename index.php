<?php
// Cargar todas las dependencias
require_once __DIR__ . '/app/routes/RouteManager.php';
require_once __DIR__ . '/app/controllers/HomeController.php';
require_once __DIR__ . '/app/controllers/RegisterController.php';
require_once __DIR__ . '/app/controllers/LoginController.php';

$router = new RouteManager();

$router->addRoute('', function() {
    $controller = new HomeController();
    $controller->index();
});

$router->addRoute('/register', function() {
    $controller = new RegisterController();
    $controller->showForm();
});

$router->addRoute('/login', function() {
    $controller = new LoginController();
    $controller->showForm();
});

// Manejar la ruta solicitada
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$router->dispatch($request_uri);
?>
