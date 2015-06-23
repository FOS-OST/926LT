<?php

class ErrorController extends ControllerBase {

    /**
     * Initializes the controller
     */
    public function initialize() {
        $this->view->setTemplateBefore('private');
        $this->bc = new Breadcrumbs();

        /**
         * Breadcrumbs for this section
         */
        $this->bc->add('Errors', 'error');
        $this->title = 'Errors';
    }

    /**
     * Index action
     */
    public function show404Action() {

    }

    public function accessAction() {
        $this->bc->add('Errors', 'error');
        $this->title = 'Access';
    }
}
