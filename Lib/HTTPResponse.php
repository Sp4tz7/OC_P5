<?php

namespace Core;

class HTTPResponse extends ApplicationComponent
{
    protected $page;

    public function redirect($location)
    {
        header('Location: '.$location);
        die();
    }

    public function redirect404()
    {
        $this->addHeader('HTTP/1.0 404 Not Found');
        $app        = new Application();
        $this->page = new Page($app);
        $this->page->set404();
        $this->send();
    }

    public function addHeader($header)
    {
        header($header);
    }

    public function setJson($data){
        return json_encode($data);
    }

    public function send()
    {
        return $this->page->getGeneratedPage();
    }

    public function killKookie($name)
    {
        $this->setCookie($name, '', time() - 360 , '/');
    }

    public function setCookie(
        $name,
        $value = '',
        $expire = 0,
        $path = null,
        $domain = null,
        $secure = false,
        $httpOnly = false
    ) {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
    }

    public function killSession($name)
    {
        unset($_SESSION[$name]);

    }

    public function setPage(Page $page)
    {
        $this->page = $page;
    }

    // Set last argument to true by default

    public function setSession($name, $value)
    {
        $_SESSION[$name] = $value;
    }
}
