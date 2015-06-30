<?php

defined('APP_PATH') || define('APP_PATH', realpath('.'));
defined('PHALCONDEBUG') || define('PHALCONDEBUG', 0);

return new \Phalcon\Config(array(
    /*'database' => array(
        'adapter'     => 'mysql',
        'host'        => 'localhost',
        'username'    => 'root',
        'password'    => '',
        'dbname'      => 'nt_ebook',
        'charset'     => 'utf8',
    ),*/
    'mongo'  => array(
        'host'     => 'localhost:27017',
        'username' => '',
        'password' => '',
        'dbname'   => 'ebooks',
    ),
    'application' => array(
        'controllersDir' => APP_PATH . '/app/controllers/',
        'modelsDir'      => APP_PATH . '/app/models/',
        'migrationsDir'  => APP_PATH . '/app/migrations/',
        'viewsDir'       => APP_PATH . '/app/views/',
        'formsDir'       => APP_PATH . '/app/forms/',
        'pluginsDir'     => APP_PATH . '/app/plugins/',
        'libraryDir'     => APP_PATH . '/app/library/',
        'cacheDir'       => APP_PATH . '/app/storage/cache/',
        'debugDir'       => APP_PATH . '/app/vendor/PDW/',
        'baseUri'        => '/',
    )
));
