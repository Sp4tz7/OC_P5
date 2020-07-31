<?php

namespace Model;

use Core\Manager;
use Entity\User;

/**
 * Class UserManager
 * @package Model
 */
abstract class UserManager extends Manager
{
    /**
     * @param User $user
     * @return string
     */
    public function save(User $user)
    {
        $message = $user->isValid();
        if ($message == 'PASS') {
            return $user->isNew() ? $this->add($user) : $this->modify($user);
        } else {
            return $message;
        }
    }

    /**
     * @param User $user
     * @return mixed
     */
    abstract protected function add(User $user);

    /**
     * @param User $user
     * @return mixed
     */
    abstract protected function modify(User $user);

    /**
     * @return mixed
     */
    abstract public function count();

    /**
     * @param $userID
     * @return mixed
     */
    abstract public function delete($userID);

    /**
     * @param int $debut
     * @param int $limite
     * @return mixed
     */
    abstract public function getList($debut = -1, $limite = -1);

    /**
     * @param $userID
     * @return mixed
     */
    abstract public function getUnique($userID);
}
