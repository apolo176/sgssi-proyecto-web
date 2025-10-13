<?php
class RouteManager
{
    private $routes = [];

    public function addRoute(string $uri, callable $callback)
    {
        $this->routes[$uri] = $callback;
    }

    public function dispatch(string $request_uri)
    {
        // Elimina cualquier parámetro de consulta y la ruta raíz si está presente
        $request_uri = strtok($request_uri, '?');
        $request_uri = rtrim($request_uri, '/');

        if (array_key_exists($request_uri, $this->routes)) {
            $callback = $this->routes[$request_uri];
            $callback();
        } else {
            http_response_code(404);
            echo "<h1>Error 404 - Página no encontrada.</h1>";
        }
    }
}
