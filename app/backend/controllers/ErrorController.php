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

        $this->identity = $this->auth->getIdentity();
        $this->bc->add('Errors', 'error');

        $this->viewVars['bc'] = $this->bc->generate();
        $this->viewVars['identity'] = $this->identity;
    }

    /**
     * Index action
     */
    public function show404Action() {
        $this->viewVars['title'] = 'Errors';
        $this->view->setVars($this->viewVars);
    }

    public function accessAction() {
        $this->bc->add('Errors', 'error');
        $this->title = 'Access';
    }
}
