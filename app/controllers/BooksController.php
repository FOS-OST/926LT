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
        $this->assets->addCss('js/plugins/jquery.alert/alertify.core.css');
        $this->assets->addJs('js/plugins/jquery.alert/alertify.min.js');
        $this->assets->addJs('js/books/bookTool.js');
    }

    /**
     * Index action
     */
    public function indexAction() {
    }

    public function createAction() {

    }
    public function editAction($id) {
        $request =$this->request;
        $showchap = $request->get('showchap');
        $this->addViewVar('showchap', $showchap);
    }
    public function saveAction() {
        return $this->response->redirect('books/edit/1?showchap=1');
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
