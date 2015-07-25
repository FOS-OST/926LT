<?php
namespace Books\Backend;

use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Loader;
use Phalcon\Crypt;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Books\Backend\Libraries\Auth\Auth as AdminAuth;
use Phalcon\Mvc\Dispatcher\Exception as DispatchException;
use PDW\DebugWidget;

class Module {
    /**
     * Register a specific autoloader for the module
     */
	public function registerAutoloaders() {
		$loader = new Loader();
        $loader->registerNamespaces(
            array(
                'Books\Backend\Controllers' => '../app/backend/controllers/',
                'Books\Backend\Forms'       => '../app/backend/forms/',
                'Books\Backend\Models'      => '../app/backend/models/',
                'Books\Backend\Models\Base' => '../app/backend/models/base/',
                'Books\Backend\Libraries'   => '../app/backend/libraries/',
            )
        );
        $loader->registerDirs(
            array(
                '../app/backend/libraries/'
            )
        );

		$loader->register();
	}

	/**
	 * Register the services here to make them general or register in the ModuleDefinition to make them module-specific
	 */
	public function registerServices( $di)
	{
        $config = include __DIR__ . "/../config/config.php";

        //Registering a dispatcher
        $di->set('dispatcher', function() use ($di) {
            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace('Books\Backend\Controllers');

            $evManager = $di->getShared('eventsManager');

            $evManager->attach(
                "dispatch:beforeException",
                function($event, $dispatcher, $exception) {
                    // Handle 404 exceptions
                    if ($exception instanceof DispatchException) {
                        $dispatcher->forward(array(
                            'module' => 'backend',
                            'controller' => 'error',
                            'action'     => 'show404'
                        ));
                        return false;
                    }
                    switch ($exception->getCode()) {
                        case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                        case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                            $dispatcher->forward(
                                array(
                                    'module' => 'backend',
                                    'controller' => 'error',
                                    'action'     => 'show404',
                                )
                            );
                            return false;
                    }
                }
            );
            $dispatcher->setEventsManager($evManager);

            return $dispatcher;
        });
        /**
         * Setting up the view component
         */
        // Registering the view component
        $di->set('view', function() use ($config) {
            $view = new View();
            $view->setViewsDir($config->application->viewsBack);
            return $view;
        });
        $di->set('adminAuth', function () {
            return new AdminAuth();
        });
        /**
         * Start the session the first time some component request the session service
         */
        $di->setShared('session', function () {
            $session = new SessionAdapter();
            $session->start();

            return $session;
        });

        if (PHALCONDEBUG == true) {
            $debugWidget = new DebugWidget($di);
        }
	}


}
