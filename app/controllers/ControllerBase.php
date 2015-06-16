<?php

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    protected $bc       = null;
    protected $viewVars = [];
    /**
     * Initializes the controller
     */
    public function initialize()
    {
        $this->bc = new Breadcrumbs();
    }

    /**
     * This sets all the view variables before rendering
     */
    public function afterExecuteRoute()
    {
        /**
         * This effectively will set the breadcrumbs array in the view
         * and will allow us to render it
         */
        $this->addViewVar('bc', $this->bc->generate());

        $this->view->setVars($this->viewVars);
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
