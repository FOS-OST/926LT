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
use Books\Backend\Models\Sections;
use Helper;
use Phalcon\Paginator\Pager;
use Phalcon\Paginator\Adapter\NativeArray as Paginator;
use Phalcon\Mvc\View;

class SectionsController extends ControllerBase
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
        $this->bc->add('Sections', 'admin/sections/index');
        $this->title = 'Sections Management';
    }

    public function indexAction()
    {
        $chapter_id = $this->request->getQuery("chapter_id");
        if ($this->request->isAjax() == true) {
            if ($chapter_id) {
                $chapter = Chapters::findById($chapter_id);
                $sections = Sections::find(array(
                    'conditions' => array(
                        'chapter_id' => $chapter_id,
                        'status' => array('$gt' => -1),
                    ),
                    'sort' => array('order' => 1),
                ));
            } else {
                echo "Chapter is required";
                exit;
            }
            echo $this->view->partial('sections/_index', array('sections' => $sections, 'chapter' => $chapter));
            exit;
        }
    }

    /**
     * Displays the creation form
     */
    public function editAction()
    {
        $chapterId = $this->request->getQuery("chapter_id");
        $id = $this->request->getQuery("id");
        $chapter = Chapters::findById($chapterId);
        if ($this->request->isAjax() == true) {
            if (!$chapter) {
                echo "Chapter is required";
                exit;
            }
            if ($id) {
                $section = Sections::findById($id);
                $this->tag->setDefaults((array)$section);
                $this->tag->setDefault('id', $id);
            } else {
                $section = new Sections();
                $this->tag->setDefaults((array)$section);
                $this->tag->setDefault('order', count($chapter->sections) + 1);
            }
            $this->tag->setDefault('chapter_id', $chapterId);
            echo $this->view->partial('sections/_edit', array('chapter' => $chapter));
            exit;
        }
    }

    public function saveAction()
    {
        if ($this->request->isAjax() == true) {
            if ($this->request->isPost() == true) {
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
                $section->breadcrumb = array(
                    'book_name' => $chapter->book_name,
                    'chapter_name' => $chapter->name,
                );

                $section->name = $this->request->getPost("name");
                $section->content = $this->request->getPost("content");
                $section->order = (int)$this->request->getPost("order");
                $section->type = $this->request->getPost("type");
                $section->check_question = filter_var($this->request->getPost("check_question"), FILTER_VALIDATE_BOOLEAN);
                $section->time = $this->request->getPost("time");
                $section->random = filter_var($this->request->getPost("random"), FILTER_VALIDATE_BOOLEAN);
                $section->free = filter_var($this->request->getPost("free"), FILTER_VALIDATE_BOOLEAN);
                $section->status = (int)$this->request->getPost("status", 'int', 1);
                $section->chapter_id = $chapterId;
                /*$section->chapter = array(
                'id' => $chapterId,
                'name' => $chapter->name
                );*/
                if (!$section->save()) {

                }
// Update to categories
                Chapters::updateSection($chapter, $section);
            }
            $sections = Sections::find(array(
                'conditions' => array(
                    'chapter_id' => $chapter->getId()->{'$id'}
                )
            ));
            echo $this->view->partial('sections/_index', array('sections' => $sections, 'chapter' => $chapter));
            exit;
        }
    }

    public function findAction()
    {
        $sections = Sections::find(array(
            'sort' => array('order' => 1)
        ));
        echo $this->view->partial('sections/_sections', array('sections' => $sections));
        exit;
    }

    /*
    * Save section by order
    */
    public function saveSectionOrderAction()
    {
        $request = $this->request;
        if ($request->isPost() == true) {
            if ($request->isAjax() == true) {
                $sectionIds = $request->getPost('sectionIds');
                $ids = array();
// Find sections by ids
                foreach ($sectionIds as $sec) {
                    $ids[] = new MongoId($sec['id']);
                }
                $sections = Sections::find(array(
                    'conditions' => array(
                        '_id' => array('$in' => $ids)
                    )
                ));
                foreach ($sections as $section) {
                    foreach ($sectionIds as $index => $sec) {
                        if ($section->getId()->{'$id'} == $sec['id']) {
                            $section->order = (int)$sec['order'];
                            $section->updated_at = '';
                            if (!$section->save()) {
                                echo json_encode(array('error' => true));
                                exit;
                            }
                            unset($sectionIds[$index]);
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


    /*
    * delete
    */
    public function deleteAction()
    {
        $request = $this->request;
        if ($request->isPost() == true) {
            if ($request->isAjax() == true) {
                $id = $request->getPost('id');
                $section = Sections::findById($id);
                $section->status = Helper::STATUS_DELETE;
                if ($section->save()) {
                    echo json_encode(array('error' => false, 'msg' => "Đã xóa thành công {$section->name}."));
                } else {
                    echo json_encode(array('error' => true, 'msg' => "Đã xóa thất bại {$section->name}."));
                }
                exit;
            }
        } else {
            echo json_encode(array('error' => true, 'msg' => "Truy cập không hợp lệ."));
        }
        exit;
    }
}
