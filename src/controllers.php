<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


$app['indexController'] = new Fone\Controller\Index();

$app->get('/', function () use ($app) {
    return $app['indexController']->homepage($app);
})
->bind('homepage')
;

$app->get('/results', function() use ($app) {
    return $app['indexController']->results($app);
})->bind('results');

$app->get('/rules', function() use ($app) {
    return $app['indexController']->rules($app);
})->bind('rules');

$app->match('login', function(Request $request) use ($app) {
    return $app['indexController']->login($request, $app);
})->method('GET|POST')->bind('login');

$app->get('/logout', function() use ($app) {
    //return $app['indexController']->logout($app);
})->bind('logout');

$app->match('/change_password', function(Request $request) use ($app) {
    return $app['indexController']->changePassword($request, $app);
})->method('GET|POST')->bind('change_password');

$app->post('/login_check', function() use ($app) {
    return false;
})->bind('login_check');

$app->match('/my_team', function(Request $request) use ($app) {
    return $app['indexController']->myteam($request, $app);
})->method('GET|POST')->bind('my_team');

$app->error(function (\Exception $e, $code) {
        switch ($code) {
            case 404:
                $message = 'The requested page could not be found.';
                break;
            default:
                $message = 'We are sorry, but something went terribly wrong. ' . $e->getMessage();
        }

        return new Response($message);
    });
