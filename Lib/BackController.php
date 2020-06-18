<?php

namespace Core;

use Application\PDO\PDOFactory;

abstract class BackController extends ApplicationComponent
{
    protected $action = '';
    protected $app = '';
    protected $view = '';
    protected $page = null;
    protected $managers = null;

    public function __construct(\Core\Application $app, $view, $action)
    {

        $this->managers = new Managers('PDO', PDOFactory::getMysqlConnexion());
        $this->setApp($app);
        $this->setAction($action);
        $this->page = new Page($this->app);
        $this->execute();
        $this->setView($action);
        $this->page->getGeneratedPage();

    }

    /**
     * Set the application as object
     *
     * @param $app
     *
     * @return \Core\Application
     */
    public function setApp(\Core\Application $app)
    {
        if ( ! is_object($app)) {
            throw new \InvalidArgumentException('The app name should be a valid string');
        }

        $this->app = $app;
    }

    public function setAction($action)
    {
        if ( ! is_string($action) || empty($action)) {
            throw new \InvalidArgumentException('The action should be a valid string');
        }

        $this->action = strtolower($action);
    }

    public function execute()
    {
        $method = 'execute'.ucfirst($this->action);

        if ( ! is_callable([$this, $method])) {
            throw new \RuntimeException('"'.$this->action.'" is not defined as action in view controller');
        }

        $this->$method($this->app->getHttpRequest());
    }

    public function setView($view)
    {
        if ( ! is_string($view) || empty($view)) {
            throw new \InvalidArgumentException('The view should be a valid string');
        }

        $this->view = strtolower($view);
        $this->page->setTemplateDir(APP_DIR.'Templates/'.$this->app->getEnv().'/');
        $this->page->setContentFile(strtolower($view).'.twig');
    }

    public function getPage()
    {
        return $this->page;
    }
}
