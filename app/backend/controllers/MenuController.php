<?php
/**
 * Created by PhpStorm.
 * User: HIEUTRIEU
 * Date: 7/25/2015
 * Time: 2:33 PM
 */
namespace Books\Backend\Controllers;

use Books\Backend\Models\Category;
use Books\Backend\Models\Menu;
use Helper;
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
        $this->bc->add($this->t->_('Menus'), 'admin/menu/index');
        $this->title = $this->t->_('Menu Management');
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
        $conditions = Menu::buildConditions($search, $this->role);
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
        $this->bc->add($this->t->_('Create menu'));
        $menu = new Menu();
        $categories = Category::find(array(
            'conditions' => array('status' => 1),
            'sort' => array('order' => 1)
        ));
        $this->view->categories = $categories;
        if ($this->request->isPost()) {
            $menu->name = $this->request->getPost("name");
            $menu->first_load = (int)$this->request->getPost("first_load");
            $menu->status = (int)$this->request->getPost("status");
            $menu->order = (int)$this->request->getPost("order");
            $menu->type = $this->request->getPost("type");
            $menu->banner = $this->request->getPost("banner");
            $menu->classification = $this->request->getPost("classification");
            $menu->icon = $this->request->getPost("icon");
            $menu->categories = (array)$this->request->getPost("categories");
            $menu->order = $menu->order == 0 ? Menu::count()+1 : $menu->order;

            if (!$menu->save()) {
                foreach ($menu->getMessages() as $message) {
                    $this->flash->error($message);
                }
            } else {
                $this->flash->success("Menu was saved successfully");
                return $this->dispatcher->forward(array(
                    "module" => "backend",
                    "controller" => "menu",
                    "action" => "index"
                ));
            }
        }
    }

    /**
     * Saves a menu edited
     *
     */
    public function editAction($id = '') {
        $this->bc->add($this->t->_('Edit menu'));

        $categories = Category::find(array(
            'conditions' => array('status' => 1),
            'sort' => array('order' => 1)
        ));
        if($id !='') {
            $menu = Menu::findByid($id);
        } else {
            $menu = new Menu();
        }
        if (!$menu) {
            $this->flash->error("Menu does not exist " . $id);

            return $this->dispatcher->forward(array(
                "module" => "backend",
                "controller" => "menu",
                "action" => "index"
            ));
        }
        if ($this->request->isPost()) {
            $menu->name = $this->request->getPost("name");
            $menu->first_load = (int)$this->request->getPost("first_load");
            $menu->status = (int)$this->request->getPost("status");
            $menu->order = (int)$this->request->getPost("order");
            $menu->type = $this->request->getPost("type");
            $menu->banner = $this->request->getPost("banner");
            $menu->classification = $this->request->getPost("classification");
            $menu->icon = $this->request->getPost("icon");
            $categoryIds = (array)$this->request->getPost("categories");
            $cats = Category::getCategoryByIds($categoryIds);
            $menu->order = $menu->order == 0 ? Menu::count() : $menu->order;
            $cates = array();
            foreach($cats as $index => $cat) {
                $cates[] = array(
                    'id' => $cat->getId(),
                    'order' => $index+1,
                    'name' => $cat->name,
                    'status' => $cat->status,
                );
            }
            $menu->categories = $cates;
            if (!$menu->save()) {
                foreach ($menu->getMessages() as $message) {
                    $this->flash->error($message);
                }
            } else {
                $this->flash->success($this->t->_('Data was saved successfully', array('name' => 'Menu')));
                return $this->dispatcher->forward(array(
                    "module" => "backend",
                    "controller" => "menu",
                    "action" => "index"
                ));
            }
        }
        $this->tag->setDefaults((array)$menu);
        $categorySelected = array();
        foreach($menu->categories as $menuCat) {
            if(isset($menuCat['id'])) $categorySelected[] = $menuCat['id'];
        }

        $this->view->categories = $categories;
        $this->view->categorySelected = $categorySelected;
        $this->view->menu = $menu;
    }

    public function saveorderAction() {
        $request =$this->request;
        if ($request->isPost()==true) {
            if ($request->isAjax() == true) {
                $categories = $request->getPost('categories');
                $id = $request->getPost('id');
                $category = Menu::findByid($id);
                $category->updated_at = '';
                $category->categories = $categories;
                if ($category->save()) {
                    echo json_encode(array('error' => false));
                    exit;
                }
            }
        }
        echo json_encode(array('error' => true));
        exit;
    }

    public function saveMenuOrderAction() {
        $request =$this->request;
        if ($request->isPost()==true) {
            if ($request->isAjax() == true) {
                $menuIds = $request->getPost('menuIds');
                $menus = Menu::find();
                foreach($menus as $menu){
                    foreach($menuIds as $index => $men) {
                        if($menu->getId()->{'$id'} == $men['id']) {
                            $menu->order = (int)$men['order'];
                            $menu->updated_at = '';
                            if(!$menu->save()) {
                                echo json_encode(array('error' => true));
                                exit;
                            }
                            unset($menuIds[$index]);
                        }
                    }
                }
                echo json_encode(array('error' => false));
                exit;
            }
        }
        echo json_encode(array('error' => true));
        exit;
    }

    public function deleteAction($id) {
        $menu = Menu::findByid($id);
        $menu->status = Helper::STATUS_DELETE;
        if(!$menu->save()) {
            foreach ($menu->getMessages() as $message) {
                $this->flash->error($message);
            }
        } else {
            return $this->response->redirect('admin/menu/index');
        }
    }
}
