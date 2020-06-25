<?php

namespace Entity;

use Service\Service;

class Post extends Entity
{
    protected $id;
    protected $category_id;
    protected $category_name;
    protected $category_slug;
    protected $title;
    protected $slug;
    protected $abstract_content;
    protected $content;
    protected $image_url;
    protected $date_add;
    protected $date_edit;
    protected $author;
    protected $created_by;
    protected $edited_by;
    protected $active;

    /**
     * @return mixed
     */
    public function getCategorySlug()
    {
        return $this->category_slug;
    }

    /**
     * @param mixed $category_slug
     */
    public function setCategorySlug($category_slug): void
    {
        $this->category_slug = $category_slug;
    }

    /**
     * @return mixed
     */
    public function getImageUrl()
    {
        return $this->image_url;
    }

    /**
     * @param mixed $image_url
     */
    public function setImageUrl($image_url): void
    {
        $this->image_url = $image_url;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author): void
    {
        $this->author = $author;
    }

    public function isValid()
    {
        $notEmpty = ['title', 'category_id', 'slug', 'created_by'];
        $isInt    = ['created_by', 'edited_by', 'category_id', 'active'];
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
    public function getCategoryName()
    {
        return $this->category_name;
    }

    /**
     * @return mixed
     */
    public function getCategoryId()
    {
        return (int)$this->category_id;
    }

    /**
     * @param mixed $category_id
     */
    public function setCategoryId($category_id): void
    {
        $this->category_id = (int)$category_id;
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
    public function setSlug($title): void
    {
        $this->slug = Service::slugIt($title);
    }

    /**
     * @return mixed
     */
    public function getAbstractContent()
    {
        return $this->abstract_content;
    }

    /**
     * @param mixed $abstract_content
     */
    public function setAbstractContent($abstract_content): void
    {
        $this->abstract_content = $abstract_content;
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

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return (int)$this->created_by;
    }

    /**
     * @param mixed $created_by
     */
    public function setCreatedBy($created_by): void
    {
        $this->created_by = (int)$created_by;
    }

    /**
     * @return mixed
     */
    public function getEditedBy()
    {
        return (int)$this->edited_by;
    }

    /**
     * @param mixed $edited_by
     */
    public function setEditedBy($edited_by): void
    {
        $this->edited_by = (int)$edited_by;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return (int)$this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active): void
    {
        $this->active = (int)$active;
    }
}
