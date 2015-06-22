<?php

defined('APP_PATH') || define('APP_PATH', realpath('.'));
defined('PHALCONDEBUG') || define('PHALCONDEBUG', 1);

return new \Phalcon\Config(array(
    'database' => array(
        'adapter'     => 'mysql',
        'host'        => 'localhost',
        'username'    => 'root',
        'password'    => '',
        'dbname'      => 'nt_ebook',
        'charset'     => 'utf8',
    ),
    'application' => array(
        'controllersDir' => APP_PATH . '/app/controllers/',
        'modelsDir'      => APP_PATH . '/app/models/',
        'migrationsDir'  => APP_PATH . '/app/migrations/',
        'viewsDir'       => APP_PATH . '/app/views/',
        'pluginsDir'     => APP_PATH . '/app/plugins/',
        'libraryDir'     => APP_PATH . '/app/library/',
<<<<<<< HEAD
        'cacheDir'       => APP_PATH . '/app/storage/cache/',
=======
        'cacheDir'       => APP_PATH . '/app/cache/',
        'debugDir'       => APP_PATH . '/app/vendor/PDW/',
>>>>>>> 0cf219134a410494dbcbbc782d5f4e18dd7dd6ba
        'baseUri'        => '/',
    )
));
