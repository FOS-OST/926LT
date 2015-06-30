<?php
use Books\App\Models\Books;
use Books\App\Models\Chapters;
use Books\App\Models\Sections;
use Phalcon\Paginator\Pager;
use Phalcon\Paginator\Adapter\NativeArray as Paginator;
use Phalcon\Mvc\View;

class SectionsController extends ControllerBase {
    /**
     * Initializes the controller
     */
    public function initialize() {
        parent::initialize();

        /**
         * Breadcrumbs for this section
         */
        $this->bc->add('Sections', 'sections');
        $this->title = 'Sections Management';
    }

    /**
     * Displays the creation form
     */
    public function editAction() {
        $chapterId = $this->request->getQuery("chapter_id");
        $id = $this->request->getQuery("id");
        $chapter = Chapters::findById($chapterId);
        if ($this->request->isAjax() == true) {
            if (!$chapter) {
                echo "Chapter is required";
                exit;
            }
            $this->tag->setDefault('chapter_id', $chapterId);
            if($id) {
                $section = Sections::findById($id);
                $this->tag->setDefaults((array)$section);
                $this->tag->setDefault('id', $id);
            } else {
                $section = new Sections();
                $this->tag->setDefault('order', count($chapter->sections)+1);
            }
            echo $this->view->partial('sections/_edit', array('chapter' => $chapter));
            exit;
        }
    }

    public function saveAction() {
        if ($this->request->isAjax() == true) {
            if ($this->request->isPost()==true) {
                $id = $this->request->getPost("id");
                $chapterId = $this->request->getPost("chapter_id");
                $chapter = Chapters::findById($chapterId);
                if (!$chapter) {
                    echo "Chapter is required";
                    exit;
                }
                if ($id != '') {
                    $section = Sections::findById($id);
                    if (!$section) {
                        echo "Section does not exist " + $id;
                        exit;
                    }
                } else {
                    $section = new Sections();
                }
                $section->name = $this->request->getPost("name");
                $section->content = $this->request->getPost("content");
                $section->order = $this->request->getPost("order");
                $section->type = $this->request->getPost("type");
                $section->status = filter_var($this->request->getPost("status"), FILTER_VALIDATE_BOOLEAN);
                $section->chapter = array(
                    'id' => $chapterId,
                    'name' => $chapter->name
                );

                if (!$section->save()) {
                    foreach ($section->getMessages() as $message) {
                        $this->flash->error($message);
                    }
                }
                // Update to categories
                Chapters::updateSection($chapter, $section);
            }
            echo $this->view->partial('chapters/_sections', array('chapter' => $chapter));
            exit;
        }
    }

    public function findAction() {
        $sections = Sections::find(array(
            'sort' => array('order' => 1)
        ));
        echo $this->view->partial('sections/_sections', array('sections' => $sections));
        exit;
    }
}
