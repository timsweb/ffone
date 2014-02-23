<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Response;

try {

    $app = new Silex\Application();
    $app->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__ . '/../views',
    ));

    $app->register(new Silex\Provider\DoctrineServiceProvider(), array(
        'db.options' => array(
            'driver' => 'pdo_sqlite',
            'path' => __DIR__ . '../fOne.db',
        ),
    ));

    $app->register(new Silex\Provider\SecurityServiceProvider(), array(
        'security.firewalls' => [
            'unsecured' => [
             'anonymous' => true,
                ]
        ] //TODO: Errr all of this.
    ));

    $app->register(new Silex\Provider\UrlGeneratorServiceProvider());
    $app->register(new Silex\Provider\SessionServiceProvider());

    //setup
    //TODO: setup routing.

    $app->get('/', function() use($app) {
        //return "Hello World";
        return $app['twig']->render('index.twig');
    });

    $app->get('/login', function() use($app) {
       return $app['twig']->render('login.twig');
    });


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

    $app->run();
} catch (\Exception $e) {
    print_r($e);
}
