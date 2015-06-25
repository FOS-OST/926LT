<?php
/**
 * Services are globally registered in this file
 *
 * @var \Phalcon\Config $config
 */

use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Flash\Direct as FlashDirect;
use Phalcon\Mvc\Dispatcher as PhDispatcher;
use Phalcon\Security;
use PDW\DebugWidget;
use Books\Library\Auth\Auth;
/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
}, true);

/**
 * Setting up the view component
 */
$di->setShared('view', function () use ($config) {

    $view = new View();

    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines(array(
        '.volt' => function ($view, $di) use ($config) {

            $volt = new VoltEngine($view, $di);

            $volt->setOptions(array(
                'compiledPath' => $config->application->cacheDir,
                'compiledSeparator' => '_'
            ));

            return $volt;
        },
        '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
    ));

    return $view;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
/*$di->set('db', function () use ($config) {
    return new DbAdapter($config->database->toArray());
});*/

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->set('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Start the session the first time some component request the session service
 */
$di->setShared('session', function () {
    $session = new SessionAdapter();
    $session->start();

    return $session;
});

$di->set('flash', function(){
    $flash = new FlashDirect(array(
        'error'   => 'callout callout-danger',
        'success' => 'callout callout-info',
        'notice'  => 'callout callout-info',
        'warning' => 'callout callout-warning'
    ));
    return $flash;
});

/**
 * Loading routes from the routes.php file
 */
/*$di->set('router', function () {
    return require __DIR__ . '/routes.php';
});*/

$di->set(
    'dispatcher',
    function() use ($di) {

        $evManager = $di->getShared('eventsManager');

        $evManager->attach(
            "dispatch:beforeException",
            function($event, $dispatcher, $exception)
            {
                switch ($exception->getCode()) {
                    case PhDispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                    case PhDispatcher::EXCEPTION_ACTION_NOT_FOUND:
                        $dispatcher->forward(
                            array(
                                'controller' => 'error',
                                'action'     => 'show404',
                            )
                        );
                        return false;
                }
            }
        );
        $dispatcher = new PhDispatcher();
        $dispatcher->setEventsManager($evManager);
        return $dispatcher;
    },
    true
);

$di->set('security', function(){

    $security = new Security();

    //Set the password hashing factor to 12 rounds
    $security->setWorkFactor(12);

    return $security;
}, true);

/**
 * Custom authentication component
 */
$di->set('auth', function () {
    return new Auth();
});

/**
 * Mail service uses AmazonSES
 */
$di->set('mail', function () {
    return new Mail();
});

/**
 * Access Control List
 */
$di->set('acl', function () {
    return new Acl();
});


if (PHALCONDEBUG == true) {
    $debugWidget = new DebugWidget($di);
}
/*$application = new \Phalcon\Mvc\Application($di);
$application->registerModules(array(
    'backend' => array(
        'className' => 'EBook\Backend\Module',
        'path' => '../app/backend/Module.php'
    )
));*/

$di->set('collectionManager', function(){
    return new Phalcon\Mvc\Collection\Manager();
}, true);
$di->set('mongo', function() use ($config){
    if ($config->mongo->username!='' OR !$config->mongo->password!='') {
        $mongo = new MongoClient('mongodb://' . $config->mongo->host);
    } else {
        $mongo = new MongoClient("mongodb://" . $config->mongo->username . ":" . $config->mongo->password . "@" . $config->mongo->host, array("db" => $config->mongo->dbname));
    }
    return $mongo->selectDb($config->mongo->dbname);
}, true);

$di->set('config', $config);
