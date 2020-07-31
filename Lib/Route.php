<?php

namespace Core;

/**
 * Class Route
 * @package Core
 */
class Route
{
    /**
     * @var
     */
    protected $action;

    /**
     * @var
     */
    protected $env;

    /**
     * @var
     */
    protected $app;

    /**
     * @var
     */
    protected $url;

    /**
     * @var
     */
    protected $varsNames;

    /**
     * @var array
     */
    protected $vars = [];

    /**
     * Route constructor.
     * @param       $url
     * @param       $env
     * @param       $app
     * @param       $action
     * @param array $varsNames
     */
    public function __construct($url, $env, $app, $action, array $varsNames)
    {
        $this->setUrl($url);
        $this->setEnv($env);
        $this->setApp($app);
        $this->setAction($action);
        $this->setVarsNames($varsNames);
    }

    /**
     * @param $url
     */
    public function setUrl($url)
    {
        if (is_string($url)) {
            $this->url = $url;
        }
    }

    /**
     * @return bool
     */
    public function hasVars()
    {
        return !empty($this->varsNames);
    }

    /**
     * @param $url
     * @return bool
     */
    public function match($url)
    {
        if (preg_match('`^'.$this->url.'$`', $url, $matches)) {
            return $matches;
        }

        return false;

    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param $action
     */
    public function setAction($action)
    {
        if (is_string($action)) {
            $this->action = $action;
        }
    }

    /**
     * @return mixed
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @param $app
     */
    public function setApp($app)
    {
        if (is_string($app)) {
            $this->app = $app;
        }
    }

    /**
     * @return mixed
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
        if (is_string($env)) {
            $this->env = $env;
        }
    }

    /**
     * @return array
     */
    public function getVars()
    {
        return $this->vars;
    }

    /**
     * @param array $vars
     */
    public function setVars(array $vars)
    {
        $this->vars = $vars;
    }

    /**
     * @return mixed
     */
    public function getVarsNames()
    {
        return $this->varsNames;
    }

    /**
     * @param array $varsNames
     */
    public function setVarsNames(array $varsNames)
    {
        $this->varsNames = $varsNames;
    }
}
