<?php

namespace Core;

class Route
{
    protected $action;
    protected $env;
    protected $app;
    protected $url;
    protected $varsNames;
    protected $vars = [];

    public function __construct($url, $env, $app, $action, array $varsNames)
    {
        $this->setUrl($url);
        $this->setEnv($env);
        $this->setApp($app);
        $this->setAction($action);
        $this->setVarsNames($varsNames);
    }

    public function setUrl($url)
    {
        if (is_string($url)) {
            $this->url = $url;
        }
    }

    public function hasVars()
    {
        return !empty($this->varsNames);
    }

    public function match($url)
    {
        if (preg_match('`^'.$this->url.'$`', $url, $matches)) {
            return $matches;
        }

        return false;

    }

    public function getAction()
    {
        return $this->action;
    }

    public function setAction($action)
    {
        if (is_string($action)) {
            $this->action = $action;
        }
    }

    public function getApp()
    {
        return $this->app;
    }

    public function setApp($app)
    {
        if (is_string($app)) {
            $this->app = $app;
        }
    }

    public function getEnv()
    {
        return $this->env;
    }

    public function setEnv($env)
    {
        if (is_string($env)) {
            $this->env = $env;
        }
    }

    public function getVars()
    {
        return $this->vars;
    }

    public function setVars(array $vars)
    {
        $this->vars = $vars;
    }

    public function getVarsNames()
    {
        return $this->varsNames;
    }

    public function setVarsNames(array $varsNames)
    {
        $this->varsNames = $varsNames;
    }
}
