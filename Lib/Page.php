<?php

namespace Core;

class Page extends ApplicationComponent
{
    protected $contentFile;
    protected $templateDir;
    protected $vars = [];
    protected $name;
    protected $app;

    public function addVar($var, $value)
    {
        if (!is_string($var) || is_numeric($var) || empty($var)) {
            throw new \InvalidArgumentException('The variable name should be a valid string');
        }

        $this->vars[$var] = $value;
    }

    public function getGeneratedPage()
    {
        $templateDir = !empty($this->templateDir) ? $this->templateDir : APP_DIR.'Templates/'.$this->app->getEnv();

        if (!file_exists($templateDir)) {
            throw new \RuntimeException('The view '.$templateDir.' does not exist');
        }
        $loader            = new \Twig\Loader\FilesystemLoader(
            [
                APP_DIR.'Templates/'.$this->app->getEnv(),
                APP_DIR.'Templates/'.$this->app->getEnv().'/Views/',
                $this->dirExists(APP_DIR.'Templates/'.$this->app->getEnv().'/Views/'.$this->app->getName().'/'),
            ]
        );
        $twig_array_loader =
            [
                'debug' => USE_DEBUG,
            ];
        if (USE_CACHE) {
            $twig_array_loader['cache'] = APP_DIR.'/Cache';
        }
        $twig = new \Twig\Environment(
            $loader,
            $twig_array_loader
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
            'siteVersion' => SITE_VERSION,
        ];

        $page = array_merge($page, $this->getVars());
        echo $twig->render($this->contentFile, $page);
        $this->app->getHttpResponse()->setSession('token', $token);
    }

    public function dirExists($dir)
    {
        if (is_dir($dir)) {
            return $dir;
        }

        return false;
    }

    public function getVars()
    {
        return $this->vars;
    }

    public function setTemplateDir($templateDir)
    {
        if (!is_string($templateDir) || empty($templateDir)) {
            throw new \InvalidArgumentException('The view directory is not valid');
        }

        $this->templateDir = $templateDir;
    }

    public function getName()
    {
        return $this->app->getName();
    }

    public function getEnv()
    {
        return $this->app->getEnv();
    }

    public function set404()
    {
        $this->app->setEnv('Frontend');
        $this->setContentFile('404.twig');
    }

    public function setContentFile($contentFile)
    {
        if (!is_string($contentFile) || empty($contentFile)) {
            throw new \InvalidArgumentException('The view name is not valid');
        }

        $this->contentFile = $contentFile;
    }
}
