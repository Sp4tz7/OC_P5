<?php
const DEFAULT_APP = 'Frontend';

// If the app does not exists, we load the default app to avoid code break
if ( ! isset($_GET['app']) || ! file_exists(__DIR__.'/../Application/'.$_GET['app'])) {
    $_GET['app'] = DEFAULT_APP;
}
include_once '../Lib/Core/Route.php';
include_once '../Lib/Core/Router.php';

$router = new \Core\Router();
$xml    = new \DOMDocument;
$xml->load(__DIR__.'/../Application/'.$_GET['app'].'/Config/routes.xml');

$routes = $xml->getElementsByTagName('route');
// Parse XML files routes
foreach ($routes as $route) {
    $vars = [];

    // Check if routes has variables.
    if ($route->hasAttribute('vars')) {
        $vars = explode(',', $route->getAttribute('vars'));
    }

    // We add route to the router
    $router->addRoute(
        new \Core\Route(
            $route->getAttribute('url'),
            $route->getAttribute('module'),
            $route->getAttribute('action'),
            $vars
        )
    );
}

try {
    // We match the route to the URL
    $matchedRoute = $router->getRoute($_SERVER['REQUEST_URI']);
    // We merge the variables to the $_GET array.
    $_GET = array_merge($_GET, $matchedRoute->vars());

    /*
     * the URL "/" should show [app => Frontend] array
     * the URL "/blog-1" should show Frontend with [app => Frontend, id => 1] array
     * the URL "/admin/" should show [app => Backend] array
     * any other url should show LOAD 404
    */
    var_dump($_GET);
} catch (\RuntimeException $e) {
    if ($e->getCode() == \Core\Router::NO_ROUTE) {
        // If no route matches, the page didn't exists.
        var_dump('LOAD 404');
    }
}

