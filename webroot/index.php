<?php
//error_reporting(E_ALL);
//ini_set('display_errors', true);

require dirname(__DIR__) . '/config/bootstrap.php';

use App\Core\App;
use App\Core\Configure;

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute(['GET', 'POST'], '/', ['Profiles', 'dashboard']);
    $r->addRoute(['GET', 'POST'], '/login', ['Profiles', 'login']);
    $r->addRoute('GET',           '/logout', ['Profiles', 'logout']);
    $r->addRoute(['GET', 'POST'], '/changepasswd', ['Profiles', 'changePassword']);
    $r->addRoute(['GET', 'POST'], '/resetpasswd', ['Profiles', 'resetPassword']);

    $r->addRoute(['GET', 'POST'], '/posts[/{action}[/{param1}]]', 'Posts');
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
if (Configure::read('App.baseUrl') == '') {
    $uri = $_SERVER['REQUEST_URI'];
} else {
    $uri = substr(
        $_SERVER['REQUEST_URI'], 
        strpos($_SERVER['REQUEST_URI'], Configure::read('App.baseUrl')) + strlen(Configure::read('App.baseUrl'))
    );
}

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        header("HTTP/1.0 404 Not Found");
        echo "Route Not Found.\n";
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        header("HTTP/1.0 405 Method Not Allowed");
        echo "Route Not Allowed.\n";
        break;
    case FastRoute\Dispatcher::FOUND:
        $controllerName = $routeInfo[1];
        $vars = $routeInfo[2];
        if (is_array($controllerName)) {
            $controllerName = $routeInfo[1][0];
            $vars['action'] = $routeInfo[1][1];
        }

        App::dispatch($controllerName, $vars);

        break;
}
