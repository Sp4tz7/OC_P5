<?php

namespace Core;

class HTTPRequest extends ApplicationComponent
{
    public function getCookie($key)
    {
        return $this->cookieExists($key) ? $_COOKIE[$key] : null;
    }

    public function cookieExists($key)
    {
        return isset($_COOKIE[$key]);
    }

    public function getDataGet($key)
    {
        return $this->getExists($key) ? $_GET[$key] : null;
    }

    public function getExists($key)
    {
        return isset($_GET[$key]);
    }

    public function getSession($key)
    {
        return $this->sessionExist($key) ? $_SESSION[$key] : null;
    }

    public function sessionExist($key)
    {
        return isset($_SESSION[$key]);
    }

    public function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getDataPost($key)
    {
        return $this->postExists($key) ? $_POST[$key] : null;
    }

    public function postExists($key)
    {
        return isset($_POST[$key]);
    }

    public function requestURI()
    {
        return $_SERVER['REQUEST_URI'];
    }
}