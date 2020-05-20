<?php
namespace Model;


/**
 * Class Blog
 * @package Model
 */
class Post
{
    /**
     * @var
     */
    protected $id;
    /**
     * @var
     */
    protected $title;
    /**
     * @var
     */
    protected $slug;
    /**
     * @var
     */
    protected $abstract;
    /**
     * @var
     */
    protected $content;
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
    protected $created_by;
    /**
     * @var
     */
    protected $edited_by;
    /**
     * @var
     */
    protected $active;

    /**
     * @return mixed
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = (int)$id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return mixed
     */
    public function getAbstract()
    {
        return $this->abstract;
    }

    /**
     * @param mixed $abstract
     */
    public function setAbstract($abstract): void
    {
        $this->abstract = $abstract;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content): void
    {
        $this->content = $content;
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
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * @param mixed $created_by
     */
    public function setCreatedBy($created_by): void
    {
        $this->created_by = $created_by;
    }

    /**
     * @return mixed
     */
    public function getEditedBy()
    {
        return $this->edited_by;
    }

    /**
     * @param mixed $edited_by
     */
    public function setEditedBy($edited_by): void
    {
        $this->edited_by = $edited_by;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active): void
    {
        $this->active = $active;
    }

}