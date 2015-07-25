<?php

use Phalcon\Mvc\Router;
use Phalcon\Mvc\Application;
use Phalcon\DI\FactoryDefault;

$router = new Router(false);
$router->removeExtraSlashes(true);

//$router->setDefaultModule("backend");
$router->add("/admin/login", array(
    'module'     => 'backend',
    'controller' => 'auth',
    'action'     => 'login',
));
$router->add("/admin/logout", array(
    'module'     => 'backend',
    'controller' => 'auth',
    'action'     => 'logout',
));

$router->add("/", array(
    'module'     => 'backend',
    'controller' => 'books',
    'action'     => 'index',
));

$router->add("/admin/books/:action", array(
    'module'     => 'backend',
    'controller' => 'books',
    'action'     => 1,
));

//Define a route
$router->add(
    "/admin/:controller/:action/:params",
    array(
        'module' => 'backend',
        "controller" => 1,
        "action"     => 2,
        "params"     => 3,
    )
);
//$router->handle();
return $router;