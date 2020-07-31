<?php

namespace Core;

/**
 * Class Page
 * @package Core
 */
class Page extends ApplicationComponent
{
    /**
     * @var
     */
    protected $contentFile;

    /**
     * @var
     */
    protected $templateDir;

    /**
     * @var array
     */
    protected $vars = [];

    /**
     * @var
     */
    protected $name;

    /**
     * @var
     */
    protected $app;

    /**
     * @param $var
     * @param $value
     */
    public function addVar($var, $value)
    {
        if (!is_string($var) || is_numeric($var) || empty($var)) {
            throw new \InvalidArgumentException('The variable name should be a valid string');
        }

        $this->vars[$var] = $value;
    }

    /**
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function getGeneratedPage()
    {
        $templateDir = !empty($this->templateDir) ? $this->templateDir : APP_DIR.'Templates/'.$this->app->getEnv();

        if (!file_exists($templateDir)) {
            throw new \RuntimeException('The view '.$templateDir.' does not exist');
        }
        $loader = new \Twig\Loader\FilesystemLoader(
            [
                APP_DIR.'Templates/'.$this->app->getEnv(),
                APP_DIR.'Templates/'.$this->app->getEnv().'/Views/',
                $this->dirExists(APP_DIR.'Templates/'.$this->app->getEnv().'/Views/'.$this->app->getName().'/'),
            ]
        );
        $twig   = new \Twig\Environment(
            $loader,
            [
                'debug' => true,
                //'cache' => APP_DIR.'/Cache',
            ]
        );
        $twig->addExtension(new \Twig\Extension\DebugExtension());
        $twig->addExtension(new \Twig\Extra\String\StringExtension());

        $form       = new FormManager($this->app);
        $csrf_token = $form->setCsrfToken();
        $token      = $form->setToken();
        $page       = [
            'page' => [
                'body_id' => $this->app->getAction(),
            ],
            'flashes' => $this->app->hasFlash() ? $this->app->getFlash() : false,
            'csrf_token' => $csrf_token,
            'token' => $token,
            'siteName' => SITE_NAME,
        ];

        $page = array_merge($page, $this->getVars());
        echo $twig->render($this->contentFile, $page);
        $this->app->getHttpResponse()->setSession('token', $token);
    }

    /**
     * @param $dir
     * @return bool
     */
    public function dirExists($dir)
    {
        if (is_dir($dir)) {
            return $dir;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getVars()
    {
        return $this->vars;
    }

    /**
     * @param $templateDir
     */
    public function setTemplateDir($templateDir)
    {
        if (!is_string($templateDir) || empty($templateDir)) {
            throw new \InvalidArgumentException('The view directory is not valid');
        }

        $this->templateDir = $templateDir;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->app->getName();
    }

    /**
     * @return string
     */
    public function getEnv()
    {
        return $this->app->getEnv();
    }

    /**
     *
     */
    public function set404()
    {
        $this->app->setEnv('Frontend');
        $this->setContentFile('404.twig');
    }

    /**
     * @param $contentFile
     */
    public function setContentFile($contentFile)
    {
        if (!is_string($contentFile) || empty($contentFile)) {
            throw new \InvalidArgumentException('The view name is not valid');
        }

        $this->contentFile = $contentFile;
    }
}
