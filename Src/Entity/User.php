<?php

namespace Entity;

/**
 * Class User
 * @package Entity
 */
class User extends Entity
{
    /**
     * @var
     */
    protected $id;

    /**
     * @var
     */
    protected $firstname;

    /**
     * @var
     */
    protected $lastname;

    /**
     * @var
     */
    protected $email;

    /**
     * @var
     */
    protected $nickname;

    /**
     * @var
     */
    protected $password;

    /**
     * @var
     */
    protected $register_date;

    /**
     * @var
     */
    protected $active;

    /**
     * @var
     */
    protected $token;

    /**
     * @var
     */
    protected $token_validity;

    /**
     * @var
     */
    protected $role;

    /**
     * @var
     */
    protected $show_full_name;

    /**
     * @var
     */
    protected $send_email_replay;

    /**
     * @var
     */
    protected $send_email_approve;


    /**
     * @return mixed
     */
    public function getTokenValidity()
    {
        return $this->token_validity;
    }

    /**
     * @param mixed $token_validity
     */
    public function setTokenValidity($token_validity): void
    {
        $this->token_validity = $token_validity;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token): void
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function isValid()
    {
        $notEmpty = ['firstname', 'lastname', 'nickname', 'role'];
        $isInt    = ['show_full_name', 'send_email_replay', 'send_email_replay', 'send_email_approve'];
        foreach ($notEmpty as $value) {
            if (empty($this->$value)) {
                return 'The '.ucfirst($value).' cannot be empty';
            }
            if (!is_string($this->$value)) {
                return 'The '.ucfirst($value).' should be a valid string: '.$value.' given!';
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
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            return 'The email is not valid';
        }

        return 'PASS';
    }

    /**
     * @return mixed
     */
    public function getShowFullName()
    {
        return (int)$this->show_full_name;
    }

    /**
     * @param mixed $show_full_name
     */
    public function setShowFullName($show_full_name): void
    {
        $this->show_full_name = (int)$show_full_name;
    }

    /**
     * @return mixed
     */
    public function getSendEmailReplay()
    {
        return (int)$this->send_email_replay;
    }

    /**
     * @param mixed $send_email_replay
     */
    public function setSendEmailReplay($send_email_replay): void
    {
        $this->send_email_replay = (int)$send_email_replay;
    }

    /**
     * @return mixed
     */
    public function getSendEmailApprove()
    {
        return (int)$this->send_email_approve;
    }

    /**
     * @param mixed $send_email_approve
     */
    public function setSendEmailApprove($send_email_approve): void
    {
        $this->send_email_approve = (int)$send_email_approve;
    }


    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * @param mixed $nickname
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * @return mixed
     */
    public function getRegisterDate()
    {
        return $this->register_date;
    }

    /**
     * @param mixed $register_date
     * @throws \Exception
     */
    public function setRegisterDate($register_date)
    {
        $date                = new \DateTime($register_date);
        $this->register_date = $date->format('Y-m-d');
    }

    /**
     * @return int
     */
    public function getActive()
    {
        return (int)$this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active)
    {
        $this->active = (int)$active;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }
}
