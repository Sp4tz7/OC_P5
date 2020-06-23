<?php

namespace Core;

use Application\PDO\PDOFactory;

abstract class AbstractController extends ApplicationComponent
{
    protected $action = '';
    protected $app = '';
    protected $view = '';
    protected $page = null;
    protected $managers = null;

    public function __construct(\Core\Application $app, $view, $action)
    {
        if ($app->getEnv() == 'Backend' && ! $app->getHttpRequest()->sessionExists('UserAuth')) {
            $app->getHttpResponse()->redirect('/login/');
        }

        $this->managers = new Managers('PDO', PDOFactory::getMysqlConnexion());
        $this->setApp($app);
        $this->setAction($action);
        $this->page = new Page($this->app);
        $this->setView($action);
        $this->execute();

        $userManager = $this->managers->getManagerOf('User');
        $request     = new HTTPRequest($app);
        $user        = $userManager->getUnique($request->getSession('UserAuth'));
        $this->page->addVar('user', $user);

        $this->page->getGeneratedPage();
    }

    /**
     * Set the application as object
     *
     * @param $app
     *
     */
    public function setApp(\Core\Application $app)
    {
        if (! is_object($app)) {
            throw new \InvalidArgumentException('The app name should be a valid string');
        }

        $this->app = $app;
    }

    public function setAction($action)
    {
        if (! is_string($action) || empty($action)) {
            throw new \InvalidArgumentException('The action should be a valid string');
        }

        $this->action = strtolower($action);
    }

    public function execute()
    {
        $method = 'execute'.ucfirst($this->action);

        if (! is_callable([$this, $method])) {
            throw new \RuntimeException('"'.$this->action.'" is not defined as action in view controller');
        }

        $this->$method($this->app->getHttpRequest(), $this->app->getHttpResponse());
    }

    public function setView($view)
    {
        if (! is_string($view) || empty($view)) {
            throw new \InvalidArgumentException('The view should be a valid string');
        }

        $this->view = strtolower($view);
        $this->page->setTemplateDir(APP_DIR.'Templates/'.$this->app->getEnv().'/');
        $this->page->setContentFile(strtolower($view).'.twig');
    }

    public function adminOnly()
    {
        $userManager = $this->managers->getManagerOf('User');
        $user        = $userManager->getUnique($this->app->getHttpRequest()->getSession('UserAuth'));

        if ($user->getRole() == 'MEMBER') {
            $this->app->getHttpResponse()->redirect('/admin/access-denied/');
        }
    }

    public function getPage()
    {
        return $this->page;
    }
}
