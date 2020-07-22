<?php

namespace Model;

use Core\Manager;
use Entity\Post;

abstract class PostManager extends Manager
{
    public function save(Post $post)
    {
        $message = $post->isValid();
        if ($message == 'PASS') {
            return $post->isNew() ? $this->add($post) : $this->modify($post);
        } else {
            return $message;
        }
    }

    abstract protected function add(Post $post);

    abstract protected function modify(Post $post);

    abstract public function count();

    abstract public function delete($postID);

    abstract public function getList($debut = -1, $limit = -1);

    abstract public function getUnique($postID);

}
