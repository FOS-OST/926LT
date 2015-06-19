<?php

use Phalcon\Mvc\Router;

// Create the router
$router = new Router(false);

//Set 404 paths
$router->notFound(array(
    "controller" => "error",
    "action"     => "show404"
));
$router->setDefaults(array('controller' => 'index', 'action' => 'index'));
$router->add('/books', array(
    //'module' => 'backend',
    'controller' => 'books',
    'action' => 'index',
));
//$router->handle();
return $router;