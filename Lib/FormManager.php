<?php

namespace Core;

class FormManager extends AbstractSecurityForm
{

    public function postNotEmpty($data)
    {
        foreach ($data as $item) {

            if (empty($item)) {

                return false;
            }

            return true;

        }
    }


    public function isEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;

    }

}

