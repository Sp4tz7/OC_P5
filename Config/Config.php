<?php

namespace Config;

define('APP_DIR', __DIR__.'/../');
define('SITE_URL', 'https://ocp5.magneticlab.ch');

final class config
{

    public static function getDbSettings()
    {
        return [
            'host'     => 'localhost',
            'dbname'   => 'php_blog',
            'username' => 'root',
            'password' => '',
        ];
    }

    public static function getSmtpSettings()
    {
        return [
            'host'          => 'mail.infomaniak.com',
            'port'          => 587,
            'username'      => 'test@magneticlab.ch',
            'password'      => '4nQQSWg-cfRR',
            'mail_from'     => ['mail' => 'test@magneticlab.ch', 'name' => 'MyBlog'],
            'mail_reply_to' => ['mail' => 'test@magneticlab.ch', 'name' => 'MyBlog'],
        ];
    }
}
