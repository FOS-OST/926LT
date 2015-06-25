<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
    array(
        $config->application->controllersDir,
        $config->application->modelsDir,
        $config->application->libraryDir,
    )
)->register();

$loader->registerNamespaces(array(
    'PDW' => $config->application->debugDir,
    'Books\App\Forms' => $config->application->formsDir,
    'Books\App\Models' => $config->application->modelsDir,
    'Books\Library' => $config->application->libraryDir,
    'Phalcon' => APP_PATH.'/Library/Phalcon/',
))->register();

