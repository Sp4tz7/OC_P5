<?php

namespace Core;


abstract class AbstractSecurityForm extends ApplicationComponent
{

    public function setCsrfToken()
    {
        if (!$this->getApp()->getHttpRequest()->sessionExists('csrf_token')) {
            $csrf_token = bin2hex(random_bytes(32));
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
        if ($this->getApp()->getHttpRequest()->postExists('csrf_token')
            and $this->getApp()->getHttpRequest()->getDataPost('csrf_token') === $this->getApp()->getHttpRequest()->getSession('csrf_token')) {
            return true;
        }

        if ($this->getApp()->getHttpRequest()->getExists('csrf_token')
            and $this->getApp()->getHttpRequest()->getDataGet('csrf_token') === $this->getApp()->getHttpRequest()->getSession('csrf_token')) {
            return true;
        }


        return false;
    }

    public function killCsrfToken()
    {
        $this->getApp()->getHttpResponse()->killSession('csrf_token');
    }

}
