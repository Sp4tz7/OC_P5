<?php

namespace Core;

/**
 * Class FormManager
 * @package Core
 */
class FormManager extends AbstractSecurityForm
{
    /**
     * @param $data
     * @return bool
     */
    public function postNotEmpty($data)
    {
        foreach ($data as $item) {

            if (empty($item)) {

                return false;
            }

            return true;

        }
    }

    /**
     * @param $email
     * @return bool
     */
    public function isEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;

    }
}
