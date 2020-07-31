<?php

namespace Core;

/**
 * Class Managers
 * @package Core
 */
class Managers
{
    /**
     * @var null
     */
    protected $api = null;
    /**
     * @var null
     */
    protected $dao = null;
    /**
     * @var array
     */
    protected $managers = [];

    /**
     * Managers constructor.
     * @param $api
     * @param $dao
     */
    public function __construct($api, $dao)
    {
        $this->api = $api;
        $this->dao = $dao;
    }

    /**
     * @param $app
     * @return mixed
     */
    public function getManagerOf($app)
    {
        if (!is_string($app) || empty($app)) {
            throw new \InvalidArgumentException('The application name is not valid');
        }

        if (!isset($this->managers[$app])) {
            $manager = '\\Model\\'.$app.'Manager'.$this->api;

            $this->managers[$app] = new $manager($this->dao);
        }

        return $this->managers[$app];
    }
}
