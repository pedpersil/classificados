<?php
namespace Core;

class App
{
    public function run()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        $uri = str_replace(BASE_PATH, '', $uri);

        $router = new Router();
        $router->resolve($method, $uri);
    }
}
