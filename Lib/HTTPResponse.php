<?php

namespace Core;

/**
 * Class HTTPResponse
 * @package Core
 */
class HTTPResponse extends ApplicationComponent
{
    /**
     * @var
     */
    protected $page;

    /**
     * @param $location
     */
    public function redirect($location)
    {
        header('Location: '.$location);
        exit();
    }

    /**
     *
     */
    public function redirect404()
    {
        $this->addHeader('HTTP/1.0 404 Not Found');
        $app        = new Application();
        $this->page = new Page($app);
        $this->page->set404();
        $this->send();
    }

    /**
     * @param $header
     */
    public function addHeader($header)
    {
        header($header);
    }

    /**
     * @return mixed
     */
    public function send()
    {
        return $this->page->getGeneratedPage();
    }

    /**
     * @param $data
     * @return false|string
     */
    public function setJson($data)
    {
        return json_encode($data);
    }

    /**
     * @param $name
     */
    public function killKookie($name)
    {
        $this->setCookie($name, '', time() - 360, '/');
    }

    // Set 2 last arguments to true by default

    /**
     * @param        $name
     * @param string $value
     * @param int    $expire
     * @param null   $path
     * @param null   $domain
     * @param bool   $secure
     * @param bool   $httpOnly
     */
    public function setCookie(
        $name,
        $value = '',
        $expire = 0,
        $path = null,
        $domain = null,
        $secure = true,
        $httpOnly = true
    ) {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
    }

    /**
     * @param $name
     */
    public function killSession($name)
    {
        unset($_SESSION[$name]);
    }

    /**
     * @param Page $page
     */
    public function setPage(Page $page)
    {
        $this->page = $page;
    }


    /**
     * @param $name
     * @param $value
     */
    public function addToSession($name, $value)
    {
        array_push($_SESSION[$name], $value);
    }

    /**
     * @param $name
     * @param $value
     */
    public function setSession($name, $value)
    {
        $_SESSION[$name] = $value;
    }
}
