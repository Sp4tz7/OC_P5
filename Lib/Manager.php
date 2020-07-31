<?php

namespace Core;

/**
 * Class Manager
 * @package Core
 */
abstract class Manager
{
    /**
     * @var
     */
    protected $dao;

    /**
     * Manager constructor.
     * @param $dao
     */
    public function __construct($dao)
    {
        $this->dao = $dao;
    }
}
