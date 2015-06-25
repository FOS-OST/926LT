<?php

use Books\Models\Category;
use Phalcon\Paginator\Pager;
use Phalcon\Paginator\Adapter\NativeArray as Paginator;

class CategoryController extends ControllerBase {
    /**
     * Initializes the controller
     */
    public function initialize()
    {
        parent::initialize();

        /**
         * Breadcrumbs for this section
         */
        $this->bc->add('Category', 'Category');
        $this->title = 'Category Management';
    }

    /**
     * Index action
     */
    public function indexAction(){
        $currentPage = abs($this->request->getQuery('page', 'int', 1));
        $search = $this->request->getQuery('search', 'string', '');
        if ($currentPage == 0) {
            $currentPage = 1;
        }
        $this->persistent->parameters = null;
        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = array();
        }
        $conditions = Category::buildConditions($search);
        $parameters["sort"] = array('updated_at' => -1);
        $parameters["conditions"] = $conditions;
        $categorys = Category::find($parameters);
        $pager = new Pager(
            new Paginator(array(
                'data'  => $categorys,
                'limit' => 20,
                'page'  => $currentPage,
            ))
        );
        $this->view->setVar('pager', $pager);
        $this->view->setVar('search', $search);
    }

    /**
     * Displays the creation form
     */
    public function newAction() {
        if ($this->request->isPost()) {
            $category = new Category();

            $category->name = $this->request->getPost("name");
            $category->image = $this->request->getPost("image");
            $category->description = $this->request->getPost("description");
            $category->status = (int)$this->request->getPost("status");

            if (!$category->save()) {
                foreach ($category->getMessages() as $message) {
                    $this->flash->error($message);
                }

                return $this->dispatcher->forward(array(
                    "controller" => "category",
                    "action" => "new"
                ));
            }

            $this->flash->success("Category was saved successfully");
            return $this->dispatcher->forward(array(
                "controller" => "category",
                "action" => "index"
            ));
        }
    }

    /**
     * Saves a user edited
     *
     */
    public function editAction($id) {
        $category = Category::findByid($id);
        if (!$category) {
            $this->flash->error("Category does not exist " . $id);

            return $this->dispatcher->forward(array(
                "controller" => "category",
                "action" => "index"
            ));
        }
        if ($this->request->isPost()) {
            $category->name = $this->request->getPost("name");
            $category->image = $this->request->getPost("image");
            $category->description = $this->request->getPost("description");
            $category->status = (int)$this->request->getPost("status");

            if (!$category->save()) {
                foreach ($category->getMessages() as $message) {
                    $this->flash->error($message);
                }
            }

            $this->flash->success("Category was updated successfully");
            return $this->dispatcher->forward(array(
                "controller" => "category",
                "action" => "index"
            ));
        } else {
            $this->view->id = $category->_id->{'$id'};

            $this->tag->setDefault("id", $category->_id->{'$id'});
            $this->tag->setDefault("name", $category->name);
            $this->tag->setDefault("image", $category->image);
            $this->tag->setDefault("description", $category->description);
            $this->tag->setDefault("status", $category->status);
            $this->view->category = $category;
        }

    }

    public function activeAction() {
        $request =$this->request;
        if ($request->isPost()==true) {
            if ($request->isAjax() == true) {
                $id = $request->getPost('id');
                $value = $request->getPost('value');
                $category = Category::findByid($id);
                $category->status = (int)!$value;
                if ($category->save()) {
                    echo json_encode(array('error' => false));
                    exit;
                }
            }
        }
        echo json_encode(array('error' => true));
        exit;
    }
}
