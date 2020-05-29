<?php
namespace Core;

class Managers
{
    protected $api = null;
    protected $dao = null;
    protected $managers = [];

    public function __construct($api, $dao)
    {
        $this->api = $api;
        $this->dao = $dao;
    }

    public function getManagerOf($app)
    {
        if (!is_string($app) || empty($app))
        {
            throw new \InvalidArgumentException('The application name is not valid');
        }

        if (!isset($this->managers[$app]))
        {
            $manager = '\\Model\\'.$app.'Manager'.$this->api;

            $this->managers[$app] = new $manager($this->dao);
        }

        return $this->managers[$app];
    }
}
