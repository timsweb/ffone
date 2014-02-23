<?php
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

//http://silex.sensiolabs.org/doc/providers/security.html <-- read this and come back to it. 
$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => [
        'unsecured' => [
         'anonymous' => true,
            ]
    ] //TODO: Errr all of this.
));

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());

return $app;