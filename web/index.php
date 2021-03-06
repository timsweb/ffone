<?php
//based from https://github.com/dustinwhittle/Silex-SymfonyLive-2012/tree/master/Silex-Skeleton
use Symfony\Component\HttpKernel\Debug\ErrorHandler;
use Symfony\Component\HttpKernel\Debug\ExceptionHandler;
use Symfony\Component\Debug\Debug;

require_once __DIR__.'/../vendor/autoload.php';

ini_set('display_errors', 1);
error_reporting(-1);
Debug::enable();
ErrorHandler::register();
if ('cli' !== php_sapi_name()) {
    ExceptionHandler::register();
}

$app = require __DIR__.'/../src/app.php';
//require __DIR__.'/../config/dev.php';
require __DIR__.'/../src/controllers.php';
require __DIR__.'/../src/security.php';
$app->run();