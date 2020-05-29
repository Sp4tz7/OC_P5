<?php

namespace Core;

class Application
{

    /**
     * @var HTTPRequest
     */
    protected $httpRequest;
    /**
     * @var HTTPResponse
     */
    protected $httpResponse;
    /**
     * @var
     */
    protected $env = 'Frontend';
    /**
     * @var
     */
    protected $name;
    /**
     * @var
     */
    protected $config;

    public function __construct()
    {
        $this->httpRequest  = new HTTPRequest($this);
        $this->httpResponse = new HTTPResponse($this);
        //$this->config = new Config($this);
    }

    /**
     * Initiate the router and return the controller
     *
     * @return Controller
     */
    public function init()
    {
        $router = new Router;

        $xml = new \DOMDocument;
        $xml->load(APP_DIR.'Config/routes.xml');

        $routes = $xml->getElementsByTagName('route');

        // Parse XML file routes.
        foreach ($routes as $route) {
            $vars = [];

            // Check if route has vars
            if ($route->hasAttribute('var')) {
                $vars = explode(',', $route->getAttribute('var'));
            }

            // Add route to the router.
            $router->addRoute(
                new Route(
                    $route->getAttribute('url'),
                    $route->getAttribute('env'),
                    $route->getAttribute('app'),
                    $route->getAttribute('action'),
                    $vars
                )
            );
        }

        try {
            // Check if a route match to the current URL
            $matchedRoute = $router->getRoute($this->httpRequest->requestURI());

        } catch (\RuntimeException $e) {
            if ($e->getCode() == Router::NO_ROUTE) {
                $this->httpResponse->redirect404();
                return false;
            }
        }

        // Add the matched variables to the $_GET array.
        $_GET = array_merge($_GET, $matchedRoute->getVars());

        $this->name = $matchedRoute->getApp();
        $this->env  = $matchedRoute->getEnv();

        // Instantiate the controller.
        $controllerClass = 'Controller\\'.$matchedRoute->getEnv().'\\'.$matchedRoute->getApp().'Controller';

        return new $controllerClass($this, $matchedRoute->getApp(), $matchedRoute->getAction());

    }

    /**
     * Return the name of the current application
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return the application environment
     *
     * @return string
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * @param string $env
     */
    public function setEnv($env)
    {

        if ( ! is_string($env) || empty($env)) {
            throw new \InvalidArgumentException('The environment name should be a valid string');
        }

        $this->$env = strtolower($env);
    }

    public function getHttpRequest()
    {
        return $this->httpRequest;
    }

    /**
     * @param string $name
     */
    public function setAppName($name)
    {
        if ( ! is_string($name) || empty($name)) {
            throw new \InvalidArgumentException('The application name should be a valid string');
        }

        $this->$name = strtolower($name);
    }

}
