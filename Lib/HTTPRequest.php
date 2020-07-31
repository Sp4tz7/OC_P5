<?php

namespace Core;

/**
 * Class HTTPRequest
 * @package Core
 */
class HTTPRequest extends ApplicationComponent
{
    /**
     * @param $key
     * @return mixed|null
     */
    public function getCookie($key)
    {
        return $this->cookieExists($key) ? $_COOKIE[$key] : null;
    }

    /**
     * @param $key
     * @return bool
     */
    public function cookieExists($key)
    {
        return isset($_COOKIE[$key]);
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function getDataGet($key)
    {
        return $this->getExists($key) ? $_GET[$key] : null;
    }

    /**
     * @param $key
     * @return bool
     */
    public function getExists($key)
    {
        return isset($_GET[$key]);
    }

    /**
     * @param $name
     * @param $value
     * @return mixed|null
     */
    public function getFileData($name, $value)
    {
        return $this->fileExists($name, $value) ? $_FILES[$name][$value] : null;
    }

    /**
     * @param      $key
     * @param null $value
     * @return bool
     */
    public function fileExists($key, $value = null)
    {
        if ($value) {
            return isset($_FILES[$key][$value]);
        }

        return isset($_FILES[$key]);
    }

    /**
     * @return mixed
     */
    public function getReferrer()
    {
        return $_SERVER['HTTP_REFERER'];
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function getSession($key)
    {
        return $this->sessionExists($key) ? $_SESSION[$key] : null;
    }

    /**
     * @param $key
     * @return bool
     */
    public function sessionExists($key)
    {
        return isset($_SESSION[$key]);
    }

    /**
     * @return mixed
     */
    public function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function getDataPost($key)
    {
        return $this->postExists($key) ? $_POST[$key] : null;
    }

    /**
     * @param $key
     * @return bool
     */
    public function postExists($key)
    {
        return isset($_POST[$key]);
    }

    /**
     * @return array|null
     */
    public function getAllPost()
    {
        return isset($_POST) ? $_POST : null;
    }

    /**
     * @return mixed
     */
    public function requestURI()
    {
        return $_SERVER['REQUEST_URI'];
    }
}
