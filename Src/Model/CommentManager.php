<?php

namespace Model;

use Core\Manager;
use Entity\Comment;

abstract class CommentManager extends Manager
{
    public function save(Comment $comment)
    {
        $message = $comment->isValid();
        if ($message == 'PASS') {
            return $comment->isNew() ? $this->add($comment) : $this->modify($comment);
        } else {
            return $message;
        }
    }

    abstract protected function add(Comment $comment);

    abstract protected function modify(Comment $comment);

    abstract public function count();

    abstract public function delete($id);

    abstract public function getList($debut = -1, $limite = -1);

    abstract public function getUnique($id);

}
