<?php

namespace Core\Form;

use Entity\Entity;

abstract class FormBuilder
{
    protected $form;

    public function __construct(Entity $entity)
    {
        $this->setForm(new Form($entity));
    }

    public function setForm(Form $form)
    {
        $this->form = $form;
    }

    abstract public function build();

    public function getForm()
    {
        return $this->form;
    }
}