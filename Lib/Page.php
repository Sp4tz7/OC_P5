<?php

namespace Core;

class Page extends ApplicationComponent
{
    protected $contentFile;
    protected $contentDir;
    protected $vars = [];
    protected $name;
    protected $app;

    public function addVar($var, $value)
    {
        if ( ! is_string($var) || is_numeric($var) || empty($var)) {
            throw new \InvalidArgumentException('The variable name should be a valid string');
        }

        $this->vars[$var] = $value;
    }

    public function getGeneratedPage()
    {
        $contentDir = ! empty($this->contentDir) ? $this->contentDir : APP_DIR.'Templates/'.$this->app->getEnv();
        if ( ! file_exists($contentDir)) {
            throw new \RuntimeException('The view '.$contentDir.' does not exist');
        }
        $loader = new \Twig\Loader\FilesystemLoader(
            [
                APP_DIR.'Templates/'.$this->app->getEnv(),
                APP_DIR.'Templates/'.$this->app->getEnv().'/Views/',
                APP_DIR.'Templates/'.$this->app->getEnv().'/Views/'.$this->app->getName().'/',
            ]
        );
        $twig   = new \Twig\Environment(
            $loader, [
                'debug' => true,
                //'cache' => APP_DIR.'/Cache',
            ]
        );
        $twig->addExtension(new \Twig\Extension\DebugExtension());

        $page = [
            'page' => [
                'title' => 'test',
            ],
        ];
        $page = array_merge($page, $this->getVars());
        echo $twig->render($this->contentFile,$page);


    }

    public function getVars()
    {
        return $this->vars;
    }

    public function setContentDir($contentDir)
    {
        if ( ! is_string($contentDir) || empty($contentDir)) {
            throw new \InvalidArgumentException('The view directory is not valid');
        }

        $this->contentDir = $contentDir;
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
        if ( ! is_string($contentFile) || empty($contentFile)) {
            throw new \InvalidArgumentException('The view name is not valid');
        }

        $this->contentFile = $contentFile;
    }
}