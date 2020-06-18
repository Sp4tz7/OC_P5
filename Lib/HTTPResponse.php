<?php

namespace Core;

class HTTPResponse extends ApplicationComponent
{
    protected $page;

    public function redirect($location)
    {
        header('Location: '.$location);
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

    public function send()
    {
        return $this->page->getGeneratedPage();
    }

    public function setPage(Page $page)
    {
        $this->page = $page;
    }

    // Set last argument to true by default
    public function setCookie(
        $name,
        $value = '',
        $expire = 0,
        $path = null,
        $domain = null,
        $secure = false,
        $httpOnly = true
    ) {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
    }
}
