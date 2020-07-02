<?php

namespace Config;

define('APP_DIR', __DIR__.'/../');
define('SITE_URL', 'https://ocp5.magneticlab.ch');
define('TIME_ZONE', 'Europe/Zurich');
define('PASSWORD_MIN_LENGTH', 6);
define('SITE_NAME', 'MY Blog');

final class config
{

    public static function getDbSettings()
    {
        return [
            'host'     => 'localhost',
            'dbname'   => 'php_blog',
            'username' => 'root',
            'password' => 'ishkaspatz7',
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
