<?php

namespace Model;

use Core\Manager;
use Entity\Post;

/**
 * Class PostManager
 * @package Model
 */
abstract class PostManager extends Manager
{
    /**
     * @param Post $post
     * @return string
     */
    public function save(Post $post)
    {
        $message = $post->isValid();
        if ($message == 'PASS') {
            return $post->isNew() ? $this->add($post) : $this->modify($post);
        } else {
            return $message;
        }
    }

    /**
     * @param Post $post
     * @return mixed
     */
    abstract protected function add(Post $post);

    /**
     * @param Post $post
     * @return mixed
     */
    abstract protected function modify(Post $post);

    /**
     * @return mixed
     */
    abstract public function count();

    /**
     * @param $postID
     * @return mixed
     */
    abstract public function delete($postID);

    /**
     * @param int $debut
     * @param int $limit
     * @return mixed
     */
    abstract public function getList($debut = -1, $limit = -1);

    /**
     * @param $postID
     * @return mixed
     */
    abstract public function getUnique($postID);

}
