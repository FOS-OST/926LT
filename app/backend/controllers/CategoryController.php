<?php
/**
 * Created by PhpStorm.
 * User: HIEUTRIEU
 * Date: 7/25/2015
 * Time: 2:33 PM
 */
namespace Books\Backend\Controllers;

use Books\Backend\Models\Books;
use Books\Backend\Models\Category;
use Books\Backend\Models\Menu;
use Helper;
use MongoId;
use Phalcon\Paginator\Pager;
use Phalcon\Paginator\Adapter\NativeArray as Paginator;

class CategoryController extends ControllerBase
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
        $this->bc->add('Danh sách chuyên đề', 'admin/category/index');
        $this->title = 'Quản lý chuyên đề';
        $this->assets->addJs('js/plugins/ui/jquery-ui.min.js');
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->bc->add('Danh sách chuyên đề', 'admin/category/index');
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
        $parameters["sort"] = array('order' => 1);
        $parameters["conditions"] = $conditions;
        $categorys = Category::find($parameters);
        $pager = new Pager(
            new Paginator(array(
                'data' => $categorys,
                'limit' => 30,
                'page' => $currentPage,
            ))
        );
        $this->view->setVar('pager', $pager);
        $this->view->setVar('search', $search);
    }

    /**
     * Displays the creation form
     */
    public function newAction()
    {
        $this->bc->add('Tạo chuyên đề', 'admin/category/new');
        $category = new Category();
        if ($this->request->isPost()) {
            $category->name = $this->request->getPost("name");
            $category->image = $this->request->getPost("image");
            $category->description = $this->request->getPost("description");
            $category->status = (int)$this->request->getPost("status");
            $category->order = (int)$this->request->getPost("order");
            $category->number_book_display = (int)$this->request->getPost("number_book_display");
            $category->order = $category->order == 0 ? (Category::count() + 1) : $category->order;

            if (!$category->save()) {
                foreach ($category->getMessages() as $message) {
                    $this->flash->error($message);
                }
            } else {
                $this->flash->success($this->t->_('Data was saved successfully', array('name' => 'Chuyên đề')));
                return $this->dispatcher->forward(array(
                    "module" => "backend",
                    "controller" => "category",
                    "action" => "index"
                ));
            }
        }
        $this->tag->setDefault("order", $category->order);
        $this->tag->setDefault("image", $category->image);
        $this->tag->setDefault("number_book_display", $category->number_book_display);
    }

    /**
     * Saves a Category edited
     *
     */
    public function editAction($id)
    {
        $this->bc->add('Sửa chuyên đề');
        $category = Category::findByid($id);
        if (!$category) {
            $this->flash->error("Category does not exist " . $id);

            return $this->dispatcher->forward(array(
                "module" => "backend",
                "controller" => "category",
                "action" => "index"
            ));
        }
        if ($this->request->isPost()) {
            $category->name = $this->request->getPost("name");
            $category->image = $this->request->getPost("image");
            $category->description = $this->request->getPost("description");
            $category->status = (int)$this->request->getPost("status");
            $category->order = (int)$this->request->getPost("order");
            $category->number_book_display = (int)$this->request->getPost("number_book_display");
            $category->order = $category->order == 0 ? Category::count() : $category->order;

            if (!$category->save()) {
                foreach ($category->getMessages() as $message) {
                    $this->flash->error($message);
                }
            } else {
                $this->flash->success($this->t->_('Data was saved successfully', array('name' => 'Chuyên đề')));
                return $this->dispatcher->forward(array(
                    "module" => "backend",
                    "controller" => "category",
                    "action" => "index"
                ));
            }
        } else {
            $this->view->id = $category->getId()->{'$id'};
            $this->tag->setDefault("id", $category->getId()->{'$id'});
            $this->tag->setDefault("name", $category->name);
            $this->tag->setDefault("image", $category->image);
            $this->tag->setDefault("description", $category->description);
            $this->tag->setDefault("status", $category->status);
            $this->tag->setDefault("order", $category->order);
            $this->tag->setDefault("number_book_display", $category->number_book_display);

            usort($category->ebooks, function ($a, $b) {
                //return strcmp($a['order'], $b['order']);
                if ($a['order'] > $b['order']) {
                    return 1;
                } else {
                    return -1;
                }
            });
            $this->view->category = $category;
        }
    }

    public function deleteAction($id) {
        $category = Category::findByid($id);
        $category->status = Helper::STATUS_DELETE;
        $books = Books::find(array(
            'conditions' => array(
                'category_ids' => $id
            )
        ));
        $menus = Menu::find(array(
            'conditions' => array(
                'categories.id' => new MongoId($id)
            )
        ));
        if(count($books)) {
            if ($this->request->isPost()) {
                if ($category->save()) {
                    foreach ($books as $book) {
                        $key = array_search($id, $book->category_ids);
                        unset($book->category_ids[$key]);
                        $book->save();
                    }
                    foreach ($menus as $menu) {
                        foreach ($menu->categories as $index => $cateMenu) {
                            if($category->getId() == $cateMenu['id']) {
                                $menu->categories[$index]['status'] = Helper::STATUS_DELETE;
                            }
                        }
                        $menu->save();
                    }
                    $this->flash->success("Chuyên đề đã được xóa thành công.");
                } else {
                    $this->flash->error("Có lỗi khi xóa chuyên đề này.");
                }
                return $this->dispatcher->forward(array(
                    "module" => "backend",
                    "controller" => "category",
                    "action" => "index"
                ));
            } else {
                $this->addViewVar('books', $books);
            }
        } else {
            if($category->save()) {
                foreach ($menus as $menu) {
                    foreach ($menu->categories as $index => $cateMenu) {
                        if($category->getId() == $cateMenu['id']) {
                            $menu->categories[$index]['status'] = Helper::STATUS_DELETE;
                        }
                    }
                    $menu->save();
                }
                $this->flash->success("Chuyên đề đã được xóa thành công.");
            } else {
                $this->flash->error("Có lỗi khi xóa chuyên đề này.");
            }
            return $this->dispatcher->forward(array(
                "module" => "backend",
                "controller" => "category",
                "action" => "index"
            ));
        }
    }

    public function saveorderAction() {
        $request = $this->request;
        if ($request->isPost() == true) {
            if ($request->isAjax() == true) {
                $ebooks = $request->getPost('ebooks');
                $id = $request->getPost('id');
                $category = Category::findByid($id);
                debug($category);
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

    public function saveCategoryOrderAction() {
        $request = $this->request;
        if ($request->isPost() == true) {
            if ($request->isAjax() == true) {
                $categoryIds = $request->getPost('categoryIds');
                $categories = Category::find();
                foreach ($categories as $category) {
                    foreach ($categoryIds as $index => $cat) {
                        if ($category->getId()->{'$id'} == $cat['id']) {
                            $category->order = (int)$cat['order'];
                            $category->updated_at = '';
                            if (!$category->save()) {
                                echo json_encode(array('error' => true));
                                exit;
                            }
                            unset($categoryIds[$index]);
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
}
