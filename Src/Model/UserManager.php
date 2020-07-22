<?php

namespace Model;

use Core\Manager;
use Entity\User;

abstract class UserManager extends Manager
{
    function save(User $user)
    {
        $message = $user->isValid();
        if ($message == 'PASS') {
            return $user->isNew() ? $this->add($user) : $this->modify($user);
        } else {
            return $message;
        }
    }

    abstract protected function add(User $user);

    abstract protected function modify(User $user);

    abstract public function count();

    abstract public function delete($userID);

    abstract public function getList($debut = -1, $limite = -1);

    abstract public function getUnique($userID);
}
