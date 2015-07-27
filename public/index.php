<?php

error_reporting(E_ALL);

define('APP_PATH', realpath('..'));
defined('PHALCONDEBUG') || define('PHALCONDEBUG', 0);
define('BASE_DIR', dirname(__DIR__));
define('APP_DIR', BASE_DIR . '/app');

try {

    date_default_timezone_set("UTC");
    /**
     * Read the configuration
     */
    $config = include APP_PATH . "/app/config/config.php";

    /**
     * Read auto-loader
     */
    include APP_PATH . "/app/config/loader.php";
    include APP_PATH . "/app/config/routes.php";

    /**
     * Read services
     */
    include APP_PATH . "/app/config/services.php";

    /**
     * Handle the request
     */
    $application = new \Phalcon\Mvc\Application($di);

    // Register the installed modules
    $application->registerModules(
        array(
            /*'frontend' => array(
                'className' => 'Olay\Frontend\Module',
                'path'      => '../app/frontend/Module.php',
            ),*/
            'backend'  => array(
                'className' => 'Books\Backend\Module',
                'path'      => '../app/backend/Module.php',
            ),
            /*'api'  => array(
                'className' => 'Books\Api\Module',
                'path'      => '../app/api/Module.php',
            )*/

        )
    );

    // Handle the request
    echo $application->handle()->getContent();

} catch (\Exception $e) {
    //debug($e->getMessage());
    //echo nl2br(htmlentities($e->getTraceAsString()));
}

function debug($strString, $exit = false) {
    print '<pre>';
    print_r($strString);
    print '</pre>';
    if($exit) exit();
}