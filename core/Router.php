<?php
namespace Core;

class Router
{
    private static array $routes = [];

    public static function get(string $uri, string $controller)
    {
        self::$routes['GET'][] = ['uri' => $uri, 'controller' => $controller];
    }

    public static function post(string $uri, string $controller)
    {
        self::$routes['POST'][] = ['uri' => $uri, 'controller' => $controller];
    }

    public function resolve(string $method, string $uri)
    {
        $uri = parse_url($uri, PHP_URL_PATH); // remove query strings

        $routes = self::$routes[$method] ?? [];

        foreach ($routes as $route) {
            $routeUri = $route['uri'];
            $controllerAction = $route['controller'];

            // Converte {param} para regex
            $pattern = preg_replace('#\{[\w]+\}#', '([\w-]+)', $routeUri);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Remove a correspondência completa

                [$controllerName, $methodName] = explode('@', $controllerAction);
                $controllerClass = "\\App\\Controllers\\{$controllerName}";

                if (class_exists($controllerClass)) {
                    $controller = new $controllerClass;

                    if (method_exists($controller, $methodName)) {
                        call_user_func_array([$controller, $methodName], $matches);
                        return;
                    }
                }
            }
        }

        // Se nenhuma rota for encontrada
        http_response_code(404);
        echo "404 - Página não encontrada.";
    }
}
