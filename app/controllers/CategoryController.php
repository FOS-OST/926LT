<?php
use Books\App\Models\Category;
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
        $this->bc->add('Topics', 'category');
        $this->title = 'Topic Management';
        $this->assets->addJs('js/plugins/ui/jquery-ui.min.js');

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
        $category = new Category();
        if ($this->request->isPost()) {
            $category->name = $this->request->getPost("name");
            $category->image = $this->request->getPost("image");
            $category->description = $this->request->getPost("description");
            $category->status = (int)$this->request->getPost("status");
            $category->number_book_display = (int)$this->request->getPost("number_book_display");

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
        $this->tag->setDefault("image", $category->image);
        $this->tag->setDefault("number_book_display", $category->number_book_display);
    }

    /**
     * Saves a Category edited
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
            $category->number_book_display = (int)$this->request->getPost("number_book_display");

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
            $this->view->id = $category->getId()->{'$id'};
            $this->tag->setDefault("id", $category->getId()->{'$id'});
            $this->tag->setDefault("name", $category->name);
            $this->tag->setDefault("image", $category->image);
            $this->tag->setDefault("description", $category->description);
            $this->tag->setDefault("status", $category->status);
            $this->tag->setDefault("number_book_display", $category->number_book_display);

            usort($category->ebooks, function($a, $b) {
                //return strcmp($a['order'], $b['order']);
                if($a['order'] > $b['order']) {
                    return 1;
                } else {
                    return -1;
                }
            });
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

    public function saveorderAction() {
        $request =$this->request;
        if ($request->isPost()==true) {
            if ($request->isAjax() == true) {
                $ebooks = $request->getPost('ebooks');
                $id = $request->getPost('id');
                $category = Category::findByid($id);
                $category->updated_at = '';
                $category->ebooks = $ebooks;
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
