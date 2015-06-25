<?php

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Mvc\View;
class QuestionsController extends ControllerBase
{

    /**
     * Initializes the controller
     */
    public function initialize() {
        parent::initialize();

        /**
         * Breadcrumbs for this section
         */
        $this->bc->add('Books', 'books');
        $this->bc->add('Questions', 'questions');
        $this->title = 'Questions Management';
    }

    /**
     * Index action
     */
    public function indexAction() {
        $request =$this->request;
        if ($request->isAjax() == true) {
            $this->view->partial('questions/_index');
        }
        exit;
    }

    public function createAction() {

    }

    public function editAction($id) {
        $request =$this->request;
        if ($request->isAjax() == true) {
            $this->view->partial('questions/_edit');
        }
        exit;
    }
}
