<?php
session_start();

define('APP_DIR', __DIR__.'/../');
require_once APP_DIR.'vendor/autoload.php';
require_once APP_DIR.'Lib/SplClassLoader.php';

$coreLoader = new SplClassLoader('Core', APP_DIR.'');
$coreLoader->register();

$AppLoader = new SplClassLoader('Application', APP_DIR.'');
$AppLoader->register();

$ControllerLoader = new SplClassLoader('Controller', APP_DIR.'Src');
$ControllerLoader->register();

$ControllerLoader = new SplClassLoader('Model', APP_DIR.'Src');
$ControllerLoader->register();

$ControllerLoader = new SplClassLoader('Entity', APP_DIR.'Src');
$ControllerLoader->register();

$ControllerLoader = new SplClassLoader('FormBuilder', APP_DIR.'Src');
$ControllerLoader->register();

try {
    $application = new \Core\Application();
    $application->init();

} catch (Exception $exception) {
    echo $exception->getMessage();
}