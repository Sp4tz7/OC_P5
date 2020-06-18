<?php
session_name('OCP5_MYBLOG');
session_start();

define('APP_DIR', __DIR__.'/../');
require_once APP_DIR.'vendor/autoload.php';

try {
    $application = new \Core\Application();
    $application->init();

} catch (Exception $exception) {
    echo $exception->getMessage();
}
