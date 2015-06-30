<?php
use Books\App\Models\Books;
use Books\App\Models\Chapters;
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
        $this->bc->add('Chapters', 'chapters');
        $this->title = 'Chapters Management';
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
                $chapter->name = $this->request->getPost("name");
                $chapter->description = $this->request->getPost("description");
                $chapter->order = $this->request->getPost("order");
                $chapter->book_id = $bookId;

                if (!$chapter->save()) {
                    foreach ($chapter->getMessages() as $message) {
                        $this->flash->error($message);
                    }
                }
                // Update to categories
                Books::updateChapter($book, $chapter);
            }
            /*$this->response->setJsonContent(array(
                'status' => 'error',
                'html' => $this->view->getPartial('books/_chapter', array('book' => $book)),
            ));
            $this->response->send();*/
            echo $this->view->partial('books/_chapters', array('book' => $book));
            exit;
        }
    }

    public function sectionsAction() {
        $id = $this->request->getQuery("id");
        if ($this->request->isAjax() == true) {
            if($id) {
                $chapter = Chapters::findById($id);
                $this->tag->setDefaults((array)$chapter);
                $this->tag->setDefault('id', $id);
            } else {
                echo "Chapter is required";
                exit;
            }
            if (!$chapter) {
                echo "Chapter is required";
                exit;
            }
            echo $this->view->partial('chapters/_sections', array('chapter' => $chapter));
            exit;
        }
    }
}
