<?php

namespace Model;


/**
 * Class Comment
 * @package Model
 */
class Comment
{

    /**
     * @var
     */
    protected $id;
    /**
     * @var
     */
    protected $blog_id;
    /**
     * @var
     */
    protected $user_id;
    /**
     * @var
     */
    protected $message;
    /**
     * @var
     */
    protected $date_add;
    /**
     * @var
     */
    protected $date_edit;
    /**
     * @var
     */
    protected $status;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getBlogId()
    {
        return $this->blog_id;
    }

    /**
     * @param mixed $blog_id
     */
    public function setBlogId($blog_id): void
    {
        $this->blog_id = $blog_id;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id): void
    {
        $this->user_id = $user_id;
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
    public function getDateAdd()
    {
        return $this->date_add;
    }

    /**
     * @param mixed $date_add
     */
    public function setDateAdd($date_add): void
    {
        $this->date_add = $date_add;
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
        $this->date_edit = $date_edit;
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

}