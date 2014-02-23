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
