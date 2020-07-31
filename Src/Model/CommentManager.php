<?php

namespace Model;

use Core\Manager;
use Entity\Comment;

/**
 * Class CommentManager
 * @package Model
 */
abstract class CommentManager extends Manager
{
    /**
     * @param Comment $comment
     * @return bool|string
     */
    public function save(Comment $comment)
    {
        $message = $comment->isValid();
        if ($message == 'PASS') {
            return $comment->isNew() ? $this->add($comment) : $this->modify($comment);
        } else {
            return $message;
        }
    }

    /**
     * @param Comment $comment
     * @return mixed
     */
    abstract protected function add(Comment $comment);

    /**
     * @param Comment $comment
     * @return mixed
     */
    abstract protected function modify(Comment $comment);

    /**
     * @return mixed
     */
    abstract public function count();

    /**
     * @param $commentID
     * @return mixed
     */
    abstract public function delete($commentID);

    /**
     * @param int $debut
     * @param int $limite
     * @return mixed
     */
    abstract public function getList($debut = -1, $limite = -1);

    /**
     * @param $commentID
     * @return mixed
     */
    abstract public function getUnique($commentID);

}
