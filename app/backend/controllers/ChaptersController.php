<?php
/**
 * Created by PhpStorm.
 * User: HIEUTRIEU
 * Date: 7/25/2015
 * Time: 2:33 PM
 */
namespace Books\Backend\Controllers;

use Books\Backend\Models\Books;
use Books\Backend\Models\Chapters;
use Phalcon\Paginator\Pager;
use Phalcon\Paginator\Adapter\NativeArray as Paginator;
use Phalcon\Mvc\View;

class ChaptersController extends ControllerBase {
    /**
     * Initializes the controller
     */
    public function initialize() {
        parent::initialize();

        /**
         * Breadcrumbs for this section
         */
        $this->bc->add('Chapters', 'admin/chapters/index');
        $this->title = 'Chapters Management';
    }

    public function indexAction() {
        $book_id = $this->request->getQuery("book_id");
        if ($this->request->isAjax() == true) {
            if($book_id) {
                $chapters = Chapters::find(array(
                    'conditions' => array(
                        'book_id' => $book_id,
                        'status' => array('$gt' => -1),
                    ),
                    'sort' => array('order' => 1)
                ));
                echo $this->view->partial('chapters/_index', array('chapters' => $chapters));
            } else {
                echo "{$book_id} is required";
                exit;
            }
            exit;
        }
    }

    /**
     * Displays the creation form
     */
    public function editAction() {

        $bookId = $this->request->getQuery("book_id");
        $id = $this->request->getQuery("id");
        $book = Books::findById($bookId);
        if ($this->request->isAjax() == true) {
            if (!$book) {
                echo "Book is required";
                exit;
            }
            if($id) {
                $chapter = Chapters::findById($id);
                $this->tag->setDefaults((array)$chapter);
                $this->tag->setDefault('id', $id);
            } else {
                $chapter = new Chapters();
                $this->tag->setDefaults((array)$chapter);
                $this->tag->setDefault('book_id', $bookId);
                $this->tag->setDefault('order', count($book->chapters)+1);
            }
            if (!$chapter) {
                echo "Chapter is required";
                exit;
            }
            echo $this->view->partial('chapters/_edit', array('bookId' => $bookId));
            exit;
        }
    }

    public function saveAction() {
        if ($this->request->isAjax() == true) {
            if ($this->request->isPost()==true) {
                $id = $this->request->getPost("id");
                $bookId = $this->request->getPost("book_id");
                $book = Books::findById($bookId);
                if (!$book) {
                    echo "Book is required";
                    exit;
                }
                if ($id != '') {
                    $chapter = Chapters::findById($id);
                    if (!$id) {
                        echo "Chapter does not exist " + $id;
                        exit;
                    }
                } else {
                    $chapter = new Chapters();
                }
                $chapter->book_name = $book->name;
                $chapter->name = $this->request->getPost("name");
                $chapter->description = $this->request->getPost("description");
                $chapter->order = (int)$this->request->getPost("order");
                $chapter->number_display = $this->request->getPost("number_display");
                $chapter->book_id = $bookId;

                if (!$chapter->save()) {
                    foreach ($chapter->getMessages() as $message) {
                        $this->flash->error($message);
                    }
                }
                // Update to categories
                Books::updateChapter($book, $chapter);
            }
            echo $this->view->partial('books/_chapters', array('book' => $book));
            exit;
        }
    }

    /*
     * delete
     */
    public function deleteAction() {
        $request =$this->request;
        if ($request->isPost()==true) {
            if ($request->isAjax() == true) {
                $id = $request->getPost('id');
                $chapter = Chapters::findById($id);
                $chapter->status = Helper::STATUS_DELETE;
                $chapter->save();
                echo json_encode(array('error' => false));
                exit;
            }
        }
        echo json_encode(array('error' => true));
        exit;
    }
}
