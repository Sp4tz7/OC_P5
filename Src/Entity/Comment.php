<?php

namespace Entity;


/**
 * Class Comment
 * @package Entity
 */
class Comment extends Entity
{
    /**
     * @var
     */
    protected $id;

    /**
     * @var
     */
    protected $parent_id;

    /**
     * @var
     */
    protected $post_id;

    /**
     * @var
     */
    protected $post;

    /**
     * @var
     */
    protected $user_id;

    /**
     * @var
     */
    protected $author;

    /**
     * @var
     */
    protected $message;

    /**
     * @var
     */
    protected $status;

    /**
     * @var
     */
    protected $date_add;

    /**
     * @var
     */
    protected $date_edit;

    /**
     * @return mixed
     */
    public function getParentId()
    {
        return $this->parent_id;
    }

    /**
     * @param mixed $parent_id
     */
    public function setParentId($parent_id): void
    {
        $this->parent_id = $parent_id === null ? null : (int)$parent_id;
    }

    /**
     * @return string
     */
    public function isValid()
    {
        $notEmpty = ['message', 'status'];
        $isInt    = ['user_id', 'post_id'];
        foreach ($notEmpty as $value) {
            if (empty($this->$value)) {
                return 'The '.ucfirst($value).' cannot be empty';
            }
        }
        foreach ($isInt as $value) {
            $array  = explode('_', $value);
            $method = 'get';
            foreach ($array as $item) {
                $method .= ucfirst($item);
            }
            if (!is_int($this->$method())) {
                return 'The '.ucfirst($value).' is not an integer: '.$this->$method().' given!';
            }
        }

        return 'PASS';
    }

    /**
     * @return mixed
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @return mixed
     */
    public function getPostId()
    {
        return (int)$this->post_id;
    }

    /**
     * @param mixed $post_id
     */
    public function setPostId($post_id): void
    {
        $this->post_id = (int)$post_id;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return (int)$this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id): void
    {
        $this->user_id = (int)$user_id;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message): void
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getDateAdd()
    {
        return $this->date_add;
    }

    /**
     * @param mixed $date_add
     */
    public function setDateAdd($date_add): void
    {
        $date           = new \DateTime($date_add);
        $this->date_add = $date->format('Y-m-d h:i:s');
    }

    /**
     * @return mixed
     */
    public function getDateEdit()
    {
        return $this->date_edit;
    }

    /**
     * @param mixed $date_edit
     */
    public function setDateEdit($date_edit): void
    {
        $date            = new \DateTime($date_edit);
        $this->date_edit = $date->format('Y-m-d h:i:s');

    }

}
