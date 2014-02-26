<?php
//http://silex.sensiolabs.org/doc/providers/security.html <-- read this and come back to it.
$app->register(new Silex\Provider\SecurityServiceProvider(), [
    'security.firewalls' => [
    'login' => [
        'pattern' => '^/login$',
    ],
    'index' => [
        'pattern' => '^/(?:index)?$',
    ],
        'secured' => [
            'pattern' => '^.*$',
            'form' => [
                'login_path' => '/login',
                'check_path' => '/login_check',
                'username_parameter' => 'username',
                'password_parameter' => 'password'
            ],
            'users' => $app->share(function () use ($app) {
                return new \Fone\UserProvider($app['db']);
            }),
            //'logout' => array('logout_path' => '/logout'),
        ],
    ]
]);

$app['security.encoder.digest'] = $app->share(function ($app) {
    return new \Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder(4);
});

return $app;