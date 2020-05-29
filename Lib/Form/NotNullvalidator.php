<?php

namespace Core\Form;

class NotNullValidator extends Validator
{
    public function isValid($value)
    {
        return $value != '';
    }
}
