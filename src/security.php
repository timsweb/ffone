<?php
//http://silex.sensiolabs.org/doc/providers/security.html <-- read this and come back to it.

/* Having problem getting logged in use on non-logged in pages.
 * Here's what I need. I think: http://stackoverflow.com/questions/18206213/display-authenticated-users-on-non-secure-anonymous-routes
 */
$app->register(new Silex\Provider\SecurityServiceProvider(), [
    'security.firewalls' => [
        'secured' => [
            'pattern' => '^.*$',
            'anonymous' => true,
            'form' => [
                'login_path' => '/login',
                'check_path' => '/login_check',
                'username_parameter' => 'username',
                'password_parameter' => 'password'
            ],
            'users' => $app->share(function () use ($app) {
                return new \Fone\Mapper\User($app['db'], 'users', '\Fone\Model\User');
            }),
            'logout' => [
                'logout_path' => '/logout',
                'invalidate_session' => false //this workaround for php < 5.4.11 in Symfony seems to do NOTHING!
            ],
        ],
    ],
    'security.access_rules' => [
        ['^/login$', 'IS_AUTHENTICATED_ANONYMOUSLY'],
        ['^/(?:index)?$', 'IS_AUTHENTICATED_ANONYMOUSLY'],
        ['^.*$', 'ROLE_USER'],
    ],
]);

/*
$app->register(new Silex\Provider\SecurityServiceProvider(), [
    'security.firewalls' => [
    'login' => [
        'pattern' => '^/login$',
        'anonymous' => true,
        'users' => $app->share(function () use ($app) {
                return new \Fone\UserProvider($app['db']);
        }),
    ],
    'index' => [
        'pattern' => '^/(?:index)?$',
        'anonymous' => true,
        'users' => $app->share(function () use ($app) {
            return new \Fone\UserProvider($app['db']);
        }),
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
            'logout' => array('logout_path' => '/logout'),
        ],
    ]
]);
*/
$app['security.encoder.digest'] = $app->share(function ($app) {
    return new \Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder(4);
});

return $app;