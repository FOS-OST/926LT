<?php

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Mvc\View;
class BooksController extends ControllerBase
{

    /**
     * Initializes the controller
     */
    public function initialize()
    {
        parent::initialize();

        /**
         * Breadcrumbs for this section
         */
        $this->bc->add('Books', 'books');
        $this->title = 'Books Management';
    }

    /**
     * Index action
     */
    public function indexAction() {
    }

    public function createAction() {

    }

    public function booktypeAction() {
        $request =$this->request;
        if ($request->isPost()==true) {
            if ($request->isAjax() == true) {
                $type = $request->getPost('type');
                $classView = BookSingle::getInstance();
                echo $classView->render();
            }
        }
        exit;
    }
}
