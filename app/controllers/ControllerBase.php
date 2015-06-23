<?php

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller {
    protected $identity = null;
    protected $title    = '';
    protected $bc       = null;
    protected $viewVars = [];
    /**
     * Initializes the controller
     */
    public function initialize()
    {
        $this->bc = new Breadcrumbs();
        $this->title = $this->tag->getTitle('title');
        $this->view->setTemplateBefore('private');
        $this->identity = $this->auth->getIdentity();
    }

    /**
     * This sets all the view variables before rendering
     */
    public function afterExecuteRoute() {
        /**
         * This effectively will set the breadcrumbs array in the view
         * and will allow us to render it
         */
        $this->addViewVar('bc', $this->bc->generate());
        $this->addViewVar('title', $this->title);
        $this->addViewVar('identity', $this->identity);
        $this->view->setVars($this->viewVars);
        if (!is_array($this->identity)) {
            return $this->response->redirect('auth/login');
        }
    }

    protected function addViewVar($variable, $value)
    {
        $this->viewVars[$variable] = $value;
    }

    protected function resetViewVars()
    {
        $this->viewVars = [];
    }
}
