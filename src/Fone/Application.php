<?php

namespace Fone;
/**
 *
 */
class Application extends \Silex\Application
{
    use \Silex\Application\UrlGeneratorTrait;
    use \Silex\Application\SecurityTrait;
    use \Silex\Application\SwiftmailerTrait;
}