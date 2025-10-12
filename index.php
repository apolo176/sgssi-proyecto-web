<?php
// Cargar todas las dependencias
require_once __DIR__ . '/app/routes/RouteManager.php';
require_once __DIR__ . '/app/controllers/HomeController.php';
require_once __DIR__ . '/app/controllers/UserController.php';
require_once __DIR__ . '/app/controllers/VideogameController.php';
$router = new RouteManager();

$router->addRoute('', function() {
    $controller = new HomeController();
    $controller->index();
});
/*---------REGISTER ROUTES -----------*/
$router->addRoute('/register', function() {
    $controller = new UserController();
    $controller->showRegister();
});
$router->addRoute('/doregister', function() {
    $controller = new UserController();
    $controller->processRegisterForm();
});

/*---------LOGIN ROUTES -----------*/
$router->addRoute('/login', function() {
    $controller = new UserController();
    $controller->showLogin();
});
$router->addRoute('/dologin', function() {
    $controller = new UserController();
    $controller->processLoginForm();
});

/*--------VIDEOGAMES ROUTES ----------*/
$router->addRoute('/items', function() {
    $controller = new VideogameController();
    $controller->showVideoGames();
});
$router->addRoute('/showItems', function() {
    $controller = new VideogameController();
    $controller->listarVideojuegos();
});

$router->addRoute('/add_item', function() {
    $controller = new VideogameController();
    $controller->showNewVideoGame();
});
$router->addRoute('/doAddItem', function() {
    $controller = new VideogameController();
    $controller->processForm();
});

$router->addRoute('/show_item', function() {
    $controller = new VideogameController();
    $item = $_GET['item'] ?? null;
    $controller->mostrarDetalle($item);
});

// Manejar la ruta solicitada
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$router->dispatch($request_uri);
?>
