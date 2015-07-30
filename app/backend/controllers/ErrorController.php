<?php
namespace Books\Backend\Controllers;

use Books\Backend\Libraries\Breadcrumbs;

class ErrorController extends ControllerBase {

    /**
     * Initializes the controller
     */
    public function initialize() {
        $this->view->setTemplateBefore('private');
        $this->bc = new Breadcrumbs();

        $this->bc->add('Errors', 'admin/error/show404');

        $this->viewVars['bc'] = $this->bc->generate();
    }

    /**
     * Index action
     */
    public function show404Action() {
        $this->viewVars['title'] = 'Errors';
        $this->view->setVars($this->viewVars);
    }

    public function accessAction() {
        $this->bc->add('Access');
        $this->title = 'Access';
    }
}
