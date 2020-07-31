<?php

namespace Core;

/**
 * Class Application
 * @package Core
 */
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
    protected $action;

    /**
     * @var
     */
    protected $config;

    /**
     * Application constructor.
     */
    public function __construct()
    {
        $this->httpRequest  = new HTTPRequest($this);
        $this->httpResponse = new HTTPResponse($this);
    }

    /**
     * @return bool|mixed
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
            if ($route->hasAttribute('vars')) {
                $vars = explode(',', $route->getAttribute('vars'));
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

        $this->name   = $matchedRoute->getApp();
        $this->env    = $matchedRoute->getEnv();
        $this->action = $matchedRoute->getAction();

        // Instantiate the controller.
        $controllerClass = 'Controller\\'.$matchedRoute->getEnv().'\\'.$matchedRoute->getApp().'Controller';

        return new $controllerClass($this, $matchedRoute->getApp(), $matchedRoute->getAction());

    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * @param $env
     */
    public function setEnv($env)
    {

        if (!is_string($env) || empty($env)) {
            throw new \InvalidArgumentException('The environment name should be a valid string');
        }

        $this->$env = strtolower($env);
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return strtolower($this->action);
    }

    /**
     * @return HTTPResponse
     */
    public function getHttpResponse()
    {
        return $this->httpResponse;
    }

    /**
     * @param $name
     */
    public function setAppName($name)
    {
        if (!is_string($name) || empty($name)) {
            throw new \InvalidArgumentException('The application name should be a valid string');
        }

        $this->$name = strtolower($name);
    }

    /**
     * @return HTTPRequest
     */
    public function getHttpRequest()
    {
        return $this->httpRequest;
    }

    /**
     * @return mixed|null
     */
    public function getFlash()
    {
        $flash = $this->httpRequest->getSession('flashes');
        $this->httpResponse->killSession('flashes');

        return $flash;
    }

    /**
     * @param string $type
     * @param array  $data
     */
    public function setFlash($type = 'success', array $data)
    {

        if ($this->hasFlash()) {
            $this->httpResponse->addToSession('flashes', ['message' => $data, 'type' => $type]);
        } else {
            $this->httpResponse->setSession('flashes', [['message' => $data, 'type' => $type]]);
        }

    }

    /**
     * @return bool
     */
    public function hasFlash()
    {
        return $this->httpRequest->sessionExists('flashes');
    }

}
