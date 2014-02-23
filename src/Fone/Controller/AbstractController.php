<?php

namespace Fone\Controller;

abstract class AbstractController
{

    /**
     *
     * @param \Silex\Application $app
     * @return \Doctrine\DBAL\Connection
     */
    protected function _getDb(\Silex\Application $app)
    {
        return $app['db'];
    }
}