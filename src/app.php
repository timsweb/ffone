<?php
date_default_timezone_set('Europe/London');
$app = new Fone\Application();

$app['userConfig'] = $app->share(function() use($app) {
    $fileData = parse_ini_file(__DIR__ . '/../etc/config.ini');
    $config = [];
    foreach ($fileData as $key => $val) {
        $path = explode('.', $key);
        $pointer = &$config;
        while($p = array_shift($path)) {
            if (count($path) === 0) {
                $pointer[$p] = $val;
                continue;
            } elseif (!isset($pointer[$p])) {
                $pointer[$p] = [];
            }
            $pointer = &$pointer[$p];
        }

    }
    return $config;
});

$app['bfClient'] = $app->share(function() use ($app){
    return new \Fone\Betting\Client($app['userConfig']['betfair']);
});

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../views',
));

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_sqlite',
        'path'   => __DIR__ . '/../fone.db',
    ),
));

$mappers = ['Driver' => 'drivers', 'Round' => 'rounds', 'Team' => 'teams', 'User' => 'users', 'UserTeam' => 'userTeams', 'RoundResult' => 'roundResults'];
foreach ($mappers as $mapper => $tableName) {
    $diKey = lcfirst($mapper) . 'Mapper';
    $app[$diKey] = $app->share(function() use ($app, $mapper, $tableName) {
        $class = '\Fone\Mapper\\' . $mapper;
        $model = '\Fone\Model\\' . $mapper;
        return new $class($app, $app['db'], $tableName, $model);
    });
}

$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());

return $app;