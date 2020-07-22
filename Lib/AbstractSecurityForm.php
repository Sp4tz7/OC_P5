<?php

namespace Core;


abstract class AbstractSecurityForm extends ApplicationComponent
{

    public function setCsrfToken()
    {
        if (!$this->getApp()->getHttpRequest()->sessionExists('csrf_token')) {
            $csrf_token             = bin2hex(random_bytes(32));
            $this->getApp()->getHttpResponse()->setSession('csrf_token', $csrf_token);

            return $csrf_token;
        }

        return $this->getApp()->getHttpRequest()->getSession('csrf_token');

    }

    public function setToken($length = 16, $validity = '+1 day')
    {
        $token          = bin2hex(random_bytes($length));
        $token_validity = (new \DateTime('now', new \DateTimeZone(TIME_ZONE)))->modify($validity);

        return ['token' => $token, 'validity' => $token_validity->format('Y-m-d H:i:s')];
    }

    public function compareCsrfToken()
    {
        if (isset($_POST['csrf_token'])
            and $_POST['csrf_token'] === $_SESSION['csrf_token']) {
            return true;
        }

        if (isset($_GET['csrf_token'])
            and $_GET['csrf_token'] === $_SESSION['csrf_token']) {
            return true;
        }


        return false;
    }

    public function killCsrfToken()
    {
        unset($_SESSION['csrf_token']);
    }

}
