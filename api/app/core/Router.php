<?php
class Router {
    private $routes = [];

    public function add($method, $uri, $action) {
        $this->routes[] = ['method' => $method, 'uri' => $uri, 'action' => $action];
    }

    public function dispatch($uri, $method) {
        if ($uri == '' || $uri == '/') {
            $uri = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] == $method || $route['method'] == 'ANY') {
                $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_]+)', $route['uri']);
                if (preg_match('#^' . $pattern . '$#', $uri, $matches)) {
                    array_shift($matches);
                    
                    if (is_callable($route['action'])) {
                        call_user_func_array($route['action'], $matches);
                    } else if (is_array($route['action'])) {
                        $controllerName = $route['action'][0];
                        $methodName = $route['action'][1];
                        if (class_exists($controllerName)) {
                            $controller = new $controllerName();
                            call_user_func_array([$controller, $methodName], $matches);
                        } else {
                            http_response_code(500);
                            echo json_encode(['error' => "Controller $controllerName không tồn tại"]);
                        }
                    }
                    return;
                }
            }
        }
        
        http_response_code(404);
        echo json_encode(['error' => 'API Route không tìm thấy (404)']);
    }
}
?>
