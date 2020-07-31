<?php

namespace Core;

use Application\Pdo\PDOFactory;

/**
 * Class AbstractController
 * @package Core
 */
abstract class AbstractController extends ApplicationComponent
{
    /**
     * @var string
     */
    protected $action = '';

    /**
     * @var string
     */
    protected $app = '';

    /**
     * @var string
     */
    protected $view = '';

    /**
     * @var Page|null
     */
    protected $page = null;

    /**
     * @var Managers|null
     */
    protected $managers = null;

    /**
     * @var FormManager|null
     */
    protected $formManager = null;


    /**
     * AbstractController constructor.
     * @param Application $app
     * @param             $view
     * @param             $action
     */
    public function __construct(\Core\Application $app, $view, $action)
    {
        if ($app->getEnv() == 'Backend' && !$app->getHttpRequest()->sessionExists('UserAuth')) {
            $app->getHttpResponse()->redirect('/login/');
        }

        $this->managers = new Managers('PDO', PDOFactory::getMysqlConnexion());
        $this->setApp($app);
        $this->setAction($action);
        $this->page        = new Page($app);
        $this->formManager = new FormManager($app);
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
        if (!is_object($app)) {
            throw new \InvalidArgumentException('The app name should be a valid string');
        }

        $this->app = $app;
    }

    /**
     * @param $action
     */
    public function setAction($action)
    {
        if (!is_string($action) || empty($action)) {
            throw new \InvalidArgumentException('The action should be a valid string');
        }

        $this->action = strtolower($action);
    }

    /**
     * @param $view
     */
    public function setView($view)
    {
        if (!is_string($view) || empty($view)) {
            throw new \InvalidArgumentException('The view should be a valid string');
        }

        $this->view = strtolower($view);
        $this->page->setTemplateDir(APP_DIR.'Templates/'.$this->app->getEnv().'/');
        $this->page->setContentFile(strtolower($view).'.twig');
    }

    public function execute()
    {
        $method = 'execute'.ucfirst($this->action);

        if (!is_callable([$this, $method])) {
            throw new \RuntimeException('"'.$this->action.'" is not defined as action in view controller');
        }

        $this->$method($this->app->getHttpRequest(), $this->app->getHttpResponse());
    }

    public function adminOnly()
    {
        $userManager = $this->managers->getManagerOf('User');
        $user        = $userManager->getUnique($this->app->getHttpRequest()->getSession('UserAuth'));

        if ($user->getRole() == 'MEMBER') {
            $this->app->getHttpResponse()->redirect('/admin/access-denied/');
        }
    }

    /**
     * @return Page|null
     */
    public function getPage()
    {
        return $this->page;
    }
}
