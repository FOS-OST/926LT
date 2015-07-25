<?php

use Phalcon\Mvc\Router;

// Create the router
$router = new Router(false);
$router->removeExtraSlashes(true);

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

//Set 404 paths
$router->notFound(array(
    "controller" => "error",
    "action"     => "show404"
));
//$router->handle();
return $router;