<?php

namespace Core;

/**
 * Class Router
 * @package Core
 */
class Router
{
    /**
     *
     */
    const NO_ROUTE = 1;
    /**
     * @var array
     */
    protected $routes = [];

    /**
     * @param Route $route
     */
    public function addRoute(Route $route)
    {
        if (!in_array($route, $this->routes)) {
            $this->routes[] = $route;
        }
    }

    /**
     * @param $url
     * @return mixed
     */
    public function getRoute($url)
    {
        foreach ($this->routes as $route) {
            // if route match to the url
            if (($varsValues = $route->match($url)) !== false) {
                if ($route->hasVars()) {
                    $varsNames = $route->getVarsNames();
                    $listVars  = [];

                    // Build new array with key/value
                    // (Key = variable name, value = his value)
                    foreach ($varsValues as $key => $match) {
                        if ($key !== 0) {
                            $listVars[$varsNames[$key - 1]] = $match;
                        }
                    }

                    $route->setVars($listVars);
                }

                return $route;
            }
        }

        throw new \RuntimeException(' No route match this URL', self::NO_ROUTE);
    }
}
