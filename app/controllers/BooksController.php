<?php

use Books\App\Models\Books;
use Books\App\Models\Category;
use Phalcon\Paginator\Pager;
use Phalcon\Paginator\Adapter\NativeArray as Paginator;
use Phalcon\Mvc\View;
class BooksController extends ControllerBase {

    /**
     * Initializes the controller
     */
    public function initialize()
    {
        parent::initialize();

        /**
         * Breadcrumbs for this section
         */
        $this->bc->add('Danh sách sách', 'books');
        $this->title = 'Quản lý sách';
        $this->assets->addCss('js/plugins/select2/select2.min.css');
        $this->assets->addJs('js/plugins/select2/select2.min.js');
        $this->assets->addJs('js/books/bookTool.js');
        $this->assets->addJs('js/plugins/ui/jquery-ui.min.js');
        //$this->assets->addJs('js/MathJax/MathJax.js?config=TeX-AMS_HTML-full');
    }

    /**
     * Index action
     */
    public function indexAction() {
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
        $conditions = Books::buildConditions($search);
        $parameters["sort"] = array('updated_at' => -1);
        $parameters["conditions"] = $conditions;
        $books = Books::find($parameters);
        $pager = new Pager(
            new Paginator(array(
                'data'  => $books,
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
        $categories = Category::getDropdown();
        $book = new Books();
        if ($this->request->isPost()) {
            $book->name = $this->request->getPost("name");
            $book->image = $this->request->getPost("image");
            $book->description = $this->request->getPost("description");
            $book->status = (int)$this->request->getPost("status");
            $book->author = $this->request->getPost("author");
            $book->price = (float)$this->request->getPost("price");
            $book->free = (int)$this->request->getPost("free");
            $book->test = (int)$this->request->getPost("test");
            $book->rate = (float)$this->request->getPost("rate");
            $book->viewer = (int)$this->request->getPost("viewer");
            $book->category_ids = $this->request->getPost("category_ids");
            $book->created_by = new MongoId($this->identity['id']);
            $book->modified_by = new MongoId($this->identity['id']);
            $book->chapters = array();

            if($book->category_ids == null) {
                $book->category_ids = array();
            }

            if (!$book->save()) {
                foreach ($book->getMessages() as $message) {
                    $this->flash->error($message);
                }
            } else {
                // Update to categories
                Category::updateBook($book->category_ids, $book);

                $this->flash->success("Sách đã được lưu thành công.");
                $this->response->redirect('books/edit/' . $book->getId()->{'$id'});
            }
        }
        $this->tag->setDefaults((array)$book);
        $this->view->setVar('categories', $categories);
        $this->view->setVar('book', $book);
    }

    public function editAction($id) {
        $request =$this->request;
        $showchap = $request->get('showchap');
        $this->addViewVar('showchap', $showchap);
        $book = Books::findByid($id);
        $categories = Category::getDropdown();
        if (!$book) {
            $this->flash->error("Sách có mã ({$id}) không tồn tại trong hệ thông.");
            return $this->dispatcher->forward(array(
                "controller" => "books",
                "action" => "index"
            ));
        }
        if ($this->request->isPost()) {
            $book->name = $this->request->getPost("name");
            $book->image = $this->request->getPost("image");
            $book->description = $this->request->getPost("description");
            $book->status = (int)$this->request->getPost("status");
            $book->author = $this->request->getPost("author");
            $book->price = (float)$this->request->getPost("price");
            $book->free = $this->request->getPost("free");
            $book->test = $this->request->getPost("test");
            $book->rate = (float)$this->request->getPost("rate");
            $book->viewer = (int)$this->request->getPost("viewer");
            $book->category_ids = $this->request->getPost("category_ids");
            $book->modified_by = new MongoId($this->identity['id']);

            if($book->category_ids == null) {
                $book->category_ids = array();
            }

            if (!$book->save()) {
                foreach ($book->getMessages() as $message) {
                    $this->flash->error($message);
                }
            } else {
                // Update to categories
                Category::updateBook($book->category_ids, $book);

                $this->flash->success("Sách ({$book->name}) đã cập nhật thành công.");
                return $this->dispatcher->forward(array(
                    "controller" => "books",
                    "action" => "index"
                ));
            }
        }
        $this->tag->setDefaults((array)$book);
        $this->view->setVar('categories', $categories);
        $this->view->setVar('book', $book);

    }
    public function saveAction() {
        return $this->response->redirect('books/edit/1?showchap=1');
    }
}
