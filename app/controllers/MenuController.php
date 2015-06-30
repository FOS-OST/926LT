<?php
use Books\App\Models\Category;
use Books\App\Models\Menu;
use Phalcon\Paginator\Pager;
use Phalcon\Paginator\Adapter\NativeArray as Paginator;

class MenuController extends ControllerBase {
    /**
     * Initializes the controller
     */
    public function initialize()
    {
        parent::initialize();

        /**
         * Breadcrumbs for this section
         */
        $this->bc->add('Menu', 'menu');
        $this->title = 'Menu Management';
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
        $conditions = Menu::buildConditions($search);
        $parameters["sort"] = array('order' => 1);
        $parameters["conditions"] = $conditions;
        $menus = Menu::find($parameters);
        $pager = new Pager(
            new Paginator(array(
                'data'  => $menus,
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
        $menu = new menu();
        $categories = Category::find(array(
            'conditions' => array('status' => 1)
        ));
        $this->view->categories = $categories;
        if ($this->request->isPost()) {
            $menu->name = $this->request->getPost("name");
            $menu->first_load = $this->request->getPost("first_load");
            $menu->status = (int)$this->request->getPost("status");
            $menu->order = (int)$this->request->getPost("order");
            $menu->categories = (array)$this->request->getPost("categories");

            if (!$menu->save()) {
                foreach ($menu->getMessages() as $message) {
                    $this->flash->error($message);
                }

                return $this->dispatcher->forward(array(
                    "controller" => "menu",
                    "action" => "new"
                ));
            }

            $this->flash->success("Menu was saved successfully");
            return $this->dispatcher->forward(array(
                "controller" => "menu",
                "action" => "index"
            ));
        }
    }

    /**
     * Saves a menu edited
     *
     */
    public function editAction($id) {
        $menu = menu::findByid($id);
        $categories = Category::find(array(
            'conditions' => array('status' => 1)
        ));
        if (!$menu) {
            $this->flash->error("menu does not exist " . $id);

            return $this->dispatcher->forward(array(
                "controller" => "menu",
                "action" => "index"
            ));
        }
        if ($this->request->isPost()) {
            $menu->name = $this->request->getPost("name");
            $menu->first_load = $this->request->getPost("first_load");
            $menu->status = (int)$this->request->getPost("status");
            $menu->order = (int)$this->request->getPost("order");
            $menu->categories = (array)$this->request->getPost("categories");

            if (!$menu->save()) {
                foreach ($menu->getMessages() as $message) {
                    $this->flash->error($message);
                }
            }

            $this->flash->success("menu was updated successfully");
            return $this->dispatcher->forward(array(
                "controller" => "menu",
                "action" => "index"
            ));
        } else {
            $this->view->id = $menu->getId()->{'$id'};
            $this->tag->setDefaults((array)$menu);
            $this->tag->setDefault("id", $menu->getId()->{'$id'});

            /*usort($menu->categories, function($a, $b) {
                //return strcmp($a['order'], $b['order']);
                if($a['order'] > $b['order']) {
                    return 1;
                } else {
                    return -1;
                }
            });*/
            $this->view->categories = $categories;
            $this->view->categorySelected = $menu->categories;
        }
    }

    public function activeAction() {
        $request =$this->request;
        if ($request->isPost()==true) {
            if ($request->isAjax() == true) {
                $id = $request->getPost('id');
                $value = $request->getPost('value');
                $menu = Menu::findByid($id);
                $menu->status = (int)!$value;
                if ($menu->save()) {
                    echo json_encode(array('error' => false));
                    exit;
                }
            }
        }
        echo json_encode(array('error' => true));
        exit;
    }
}
