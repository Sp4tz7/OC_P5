<?php

namespace Core;

/**
 * Class ApplicationComponent
 * @package Core
 */
abstract class ApplicationComponent
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * ApplicationComponent constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return Application
     */
    public function getApp()
    {
        return $this->app;
    }
}
