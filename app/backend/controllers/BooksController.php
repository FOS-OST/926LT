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
use Books\Backend\Models\Permissions;
use Books\Backend\Models\Questions;
use Books\Backend\Models\Roles;
use Books\Backend\Models\Sections;
use Helper;
use MongoId;
use Phalcon\Paginator\Pager;
use Phalcon\Paginator\Adapter\NativeArray as Paginator;
use Phalcon\Mvc\View;
use stdClass;

class BooksController extends ControllerBase {

    /**
     * Initializes the controller
     */
    public function initialize(){
        parent::initialize();

        $this->bc->add('Danh sách sách', 'books');
        $this->title = 'Quản lý sách';
        $this->assets->addCss('js/plugins/select2/select2.min.css');
        $this->assets->addJs('js/plugins/select2/select2.min.js');
        $this->assets->addJs('js/books/bookTool.js');
        $this->assets->addJs('js/books/admin_book_answer.js');
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
        $permissionArr = $this->adminAcl->getPermissions($this->admin['id']);
        $bookIds = $permissionArr['index'];
        $conditions = Books::buildConditions($search, $bookIds,$this->role);
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
        $this->view->setVar('permissionArr', $permissionArr);
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
            $book->status = 0;
            $book->author = $this->request->getPost("author");
            $book->price = (float)$this->request->getPost("price");
            $book->free = filter_var($this->request->getPost("free"), FILTER_VALIDATE_BOOLEAN);
            $book->test = filter_var($this->request->getPost("test"), FILTER_VALIDATE_BOOLEAN);
            $book->rate = (float)$this->request->getPost("rate");
            $book->viewer = (int)$this->request->getPost("viewer");
            $book->category_ids = $this->request->getPost("category_ids");
            $book->created_by = new MongoId($this->admin['id']);
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
                // Allow permission for this user
                $permission = new Permissions();
                $permission->user_id = new MongoId($this->admin['id']);
                $permission->book_id = $book->getId();
                $permission->allowView = 1;
                $permission->allowEdit = 1;
                $permission->allowPublish = 0;
                $permission->allowDelete = 1;
                $permission->allowTest = 1;
                if($permission->save()) {
                    foreach ($permission->getMessages() as $message) {
                        $this->flash->error($message);
                    }
                }
                $this->flash->success("Sách đã được lưu thành công.");
                $this->response->redirect('admin/books/edit/' . $book->getId()->{'$id'});
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
                "module" => "backend",
                "controller" => "books",
                "action" => "index"
            ));
        }
        if ($this->request->isPost()) {
            $book->name = $this->request->getPost("name");
            $book->image = $this->request->getPost("image");
            $book->description = $this->request->getPost("description");
            $book->number_buyer = (int)$this->request->getPost("number_buyer");
            $book->author = $this->request->getPost("author");
            $book->price = (float)$this->request->getPost("price");
            $book->free = (int)filter_var($request->getPost("free", 'int', 0), FILTER_VALIDATE_BOOLEAN);
            $book->action = (int)$request->getPost("action");
            $book->rate = (float)$this->request->getPost("rate");
            $book->viewer = (int)$this->request->getPost("viewer");
            $book->category_ids = $this->request->getPost("category_ids");
            $book->modified_by = new MongoId($this->admin['id']);

            $categoryOdd = $this->request->getPost("category_odd");
            if($book->category_ids == null) {
                $book->category_ids = array();
            }

            if (!$book->save()) {
                foreach ($book->getMessages() as $message) {
                    $this->flash->error($message);
                }
            } else {
                if($categoryOdd != '') {
                    $categoryOdds = explode(',', $categoryOdd);
                } else {
                    $categoryOdds = array();
                }

                // Filter topic odd to remove this book
                $categoryOddIds = array();
                foreach($categoryOdds as $categoryOdd) {
                    if(!in_array($categoryOdd, $book->category_ids)) {
                        $categoryOddIds[] = $categoryOdd;;
                    }
                }

                // Update to categories
                Category::updateBook($book->category_ids, $book, $categoryOddIds);

                $this->flash->success("Sách ({$book->name}) đã cập nhật thành công.");
                return $this->dispatcher->forward(array(
                    "module" => "backend",
                    "controller" => "books",
                    "action" => "index"
                ));
            }
        }
        $permissionArr = $this->adminAcl->getPermissions($this->admin['id']);
        $this->tag->setDefaults((array)$book);
        $this->tag->setDefault('category_odd', implode(',',$book->category_ids));
        $this->view->setVar('categories', $categories);
        $this->view->setVar('book', $book);
        $this->view->setVar('permissionArr', $permissionArr);

    }
    public function deleteAction($id) {
        $book = Books::findByid($id);
        $book->status = Helper::STATUS_DELETE;
        if($book->save()) {
            // Update to categories
            Category::updateBook($book->category_ids, $book);
        }
        return $this->response->redirect('admin/books/index');
    }

    public function previewAction($id, $type = 'chapter') {
        $sectionIds = array();
        $questionArr = array();
        $sectionQuestions = array();
        $groupQuestions = array();
        $sections = Sections::find(array(
            'conditions' => array(
                'chapter_id' => $id,
                'status' => array('$gt' => -1),
                'type' => array('$in' => array(Sections::TYPE_NORMAL_PRACTICE))
            ),
            'sort' => array('order' => 1)
        ));
        foreach($sections as $section) {
            $sectionIds[] = $section->getId()->{'$id'};
        }
        $questions = array();
        if(count($sectionIds)) {
            $questions = Questions::find(array(
                'conditions' => array(
                    'section.id' => array('$in' => $sectionIds),
                    'status' => array('$gt' => -1),
                )
            ));
        }
        $groupId = null;
        foreach($questions as &$question) {
            $question = $question->composerInfo();
            if($question->type == Questions::TYPE_GROUP) {
                $groupId = $question->id;
            }
            if($question->group_id && $question->group_id == $groupId) {
                $groupQuestions[$groupId][] = $question;
            } else {
                $questionArr[$question->section_id][] = $question;
            }

        }
        foreach($sections as $section) {
            $sectionQuestion = new stdClass();
            $sectionQuestion->section = $section->composerInfo();
            $sectionQuestion->questions = array();
            if(isset($questionArr[$section->getId()->{'$id'}])) {
                $sectionQuestion->questions = $questionArr[$section->getId()->{'$id'}];
            }
            $sectionQuestions[] = $sectionQuestion;
        }
        $this->addViewVar('sectionQuestions', $sectionQuestions);
        $this->addViewVar('groupQuestions',$groupQuestions);
    }


    public function publishAction($id,$status) {
        $book = Books::findByid($id);
        $book->status = intval($status);
        if($status) {
            $book->action = 0;
        }
        $book->modified_by = new MongoId($this->admin['id']);
        if($book->save()) {
            // Update to categories
            Category::updateBook($book->category_ids, $book);

            /**
             * Get all permission of this book and deny all
             */
            if($status) {
                $permissions = Permissions::find(array(
                    'conditions' => array(
                        'book_id' => $book->getId()
                    )
                ));
                foreach ($permissions as $permission) {
                    $permission->allowEdit = 0;
                    $permission->allowPublish = 0;
                    $permission->allowDelete = 0;
                    $permission->save();
                }
            }
        }
        if($status) {
            $this->flash->success("Sách ({$book->name}) đã xuất bản thành công.");
        } else {
            $this->flash->success("Sách ({$book->name}) đã hủy xuất bản thành công.");
        }
        return $this->dispatcher->forward(array(
            "module" => "backend",
            "controller" => "books",
            "action" => "index"
        ));
    }

    public function makeVirtualUserAction() {
        if ($this->request->isAjax() == true) {
            if ($this->request->isPost() == true) {
                $virtualUser = rand(1, 1000);

                echo json_encode(array('error'=>0,'number_buyer' => $virtualUser,'msg' => 'Đã tạo số người mua ảo thành công.'));
                exit;
            }
        }
    }
}
