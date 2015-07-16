<?php

use Books\App\Models\Books;
use Books\App\Models\Chapters;
use Books\App\Models\Questions;
use Books\App\Models\Sections;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Mvc\View;
class QuestionsController extends ControllerBase
{

    /**
     * Initializes the controller
     */
    public function initialize() {
        parent::initialize();

        /**
         * Breadcrumbs for this section
         */
        $this->bc->add('Books', 'books');
        $this->bc->add('Questions', 'questions');
        $this->title = 'Questions Management';
    }

    /**
     * Index action
     */
    public function indexAction() {
        $type = '';
        $request = $this->request;
        $sectionId = $request->getQuery('section_id');
        $section = Sections::findById($sectionId);
        $questions = array();
        if($section->type == Sections::TYPE_NORMAL_PRACTICE) {
            $questions = Questions::find(array(
                'conditions' => array(
                    'section.id' => $sectionId
                ),
                'sort' => array('order' => 1),
            ));
        } elseif($section->type == Sections::TYPE_SUMMARY_PRACTICE) {
            foreach($section->questions as $question) {
                $questionIds[] = new MongoId($question['id']);
            }
            if(count($questionIds)) {
                $questions = Questions::find(array(
                    'conditions' => array(
                        '_id' => array('$in' => $questionIds)
                    )
                ));
            }
            $type = '_summary';
        }
        if ($request->isAjax() == true) {
            $this->view->partial("questions/_index{$type}", array('questions' => $questions, 'section' => $section));
        }
        exit;
    }

    public function editAction() {
        $request =$this->request;
        $id = $request->getQuery("id");
        $sectionId = $request->getQuery("section_id");
        $type = $request->getQuery("type");
        $section = Sections::findById($sectionId);
        if ($request->isAjax() == true) {
            if(!$section) {
                echo "Section is required";
                exit;
            }
            if($id) {
                $question = Questions::findById($id);
                $question->answers = Questions::getViewAnswers($question->answers, $question->type);
                $this->tag->setDefaults((array)$question);
                $this->view->type = $question->type;
            } else {
                $question = new Questions();
                $question->answers = Questions::getViewAnswers($question->answers, $type);
                $this->tag->setDefaults((array)$question);
                $this->tag->setDefault('order', count($section->questions)+1);
                $this->tag->setDefault('type', $type);
                $this->view->type = $type;
            }
            $this->tag->setDefault('section_id', $sectionId);
            $this->tag->setDefault('chapter_id', $section->chapter_id);
            if($section->type == Sections::TYPE_NORMAL_PRACTICE) {
                echo $this->view->partial('questions/_edit');
            }

        }
        exit;
    }

    public function saveAction() {
        //debug($_POST, true);
        if ($this->request->isAjax() == true) {
            if ($this->request->isPost()==true) {
                $id = $this->request->getPost("_id");
                $sectionId = $this->request->getPost("section_id");
                $section = Sections::findById($sectionId);
                if (!$section) {
                    echo "Section is required";
                    exit;
                }
                if ($id != '') {
                    $question = Questions::findById($id);
                    if (!$question) {
                        echo "Question does not exist " + $id;
                        exit;
                    }
                } else {
                    $question = new Questions();
                }
                $allow_translate = $this->request->getPost("allow_translate");
                $question->name = $this->request->getPost("name");
                $question->question = $this->request->getPost("question");
                $question->allow_translate = isset($allow_translate)?1:0;
                $question->order = (int) $this->request->getPost("order");
                $question->correct_msg = $this->request->getPost("correct_msg");
                $question->incorrect_msg = $this->request->getPost("incorrect_msg");
                $question->type = $this->request->getPost("type");
                $question->group_id = $this->request->getPost("group_id");
                $answers = Questions::renderAnswers($this->request->getPost("answers"), $question->type);
                $question->status = filter_var($this->request->getPost("status"), FILTER_VALIDATE_BOOLEAN);
                $question->section = array('id' => $sectionId, 'name' => $section->name);
                $question->answers = $answers;
                $is_group = $this->request->getPost("is_group");
                if(isset($is_group)) {
                    if($question->group_id) {
                        $question->group_id = new MongoId($question->group_id);
                    } else {
                        $question->group_id = new MongoId();
                    }
                } else {
                    $question->group_id = null;
                }
                if($question->allow_translate) {
                    $question->translates = $this->request->getPost("translates");
                }
                if (!$question->save()) {
                    echo "Save failed";
                    exit;
                }
                // Update to questions
                Sections::updateQuestion($section, $question);
            }
            $questions = Questions::find(array(
                'conditions' => array(
                    'section.id' => $sectionId
                ),
                'sort' => array('order' => 1),
            ));
            echo $this->view->partial('questions/_index', array('questions' => $questions,'section' => $section));
            exit;
        }
    }

    public function editSummaryAction() {
        $request =$this->request;
        $id = $request->getQuery("id");
        $sectionId = $request->getQuery("section_id");
        $bookId = $request->getQuery("book_id");
        $section = Sections::findById($sectionId);
        $book = Books::findById($bookId);
        if ($request->isAjax() == true) {
            if(!$book) {
                echo "Book is required";
                exit;
            }
            if(!$section) {
                echo "Section is required";
                exit;
            }

            // Get Section old or new section and set default value
            if($id) {
                $question = Questions::findById($id);
                $this->tag->setDefaults((array)$question);
                $this->tag->setDefault('id', $id);
            } else {
                $question = new Questions();
                $this->tag->setDefaults((array)$question);
                $this->tag->setDefault('order', count($section->questions)+1);
            }
            $this->tag->setDefault('section_id', $sectionId);
            $this->tag->setDefault('book_id', $bookId);
            $questions = $this->getAllQuestionByBook($book);
            echo $this->view->partial('questions/_edit_summary', array('section' => $section, 'questions' => $questions));


        }
        exit;
    }

    public function saveSummaryAction() {
        if ($this->request->isAjax() == true) {
            if ($this->request->isPost() == true) {
                $numbers = $this->request->getPost("number");
                $bookId = $this->request->getPost("book_id");
                $book = Books::findById($bookId);
                //$questions = $this->getAllQuestionByBook($book);
                $sectionId = $this->request->getPost("section_id");
                $questionIds = json_decode($this->request->getPost("questionIds"));

                $section = Sections::findById($sectionId);
                $questionIdRs = array();
                foreach($questionIds as $chapId => $chap) {
                    foreach ($chap->sections as $secId => $sec) {
                        foreach ($sec->questions as $type => $quesIds) {
                            $indexs = (array)array_rand($quesIds, $numbers[$chapId][$secId][$type]);
                            foreach($indexs as $index) {
                                $questionIdRs[] = new MongoId($quesIds[$index]);
                            }
                        }
                    }
                }
                $questions = Questions::find(array(
                    'conditions' => array(
                        '_id' => array('$in' => $questionIdRs)
                    )
                ));
                $secQuestions = array();
                foreach($questions as $index => $question) {
                    $secQuestions[] = array(
                        'id' => $question->getId()->{'$id'},
                        'name' => $question->name,
                        'order' => $index,
                        'type' => $question->type,
                        'status' => $question->status,
                    );
                }
                $section->questions = $secQuestions;
                $section->save();
                echo json_encode(array('error' => false, 'msg' => 'Create Successfully.'));
            }
        }
        exit;
    }

    public function getAllQuestionByBook($book){
        $chapterIds = array();
        $questions = array();
        // Get All Chapter in books
        $chapters = Chapters::find(array(
            'conditions' => array('book_id' => $book->getId()->{'$id'}),
            'sort' => array('order' => 1),
        ));
        foreach($chapters as $chapter){
            $chapterIds[] = $chapter->getId()->{'$id'};
        }
        // Get all questions of Chapters type NORMAL_PRACTICE
        $sections = Sections::find(array(
            'conditions' => array(
                '$and' => array(
                    array('chapter_id'=> array('$in' => $chapterIds)),
                    array('type' => Sections::TYPE_NORMAL_PRACTICE),
                )
            )
        ));
        foreach($chapters as $chap) {
            $questions[$chap->getId()->{'$id'}]['name'] = $chap->name;
            $questions[$chap->getId()->{'$id'}]['sections'] = array();
            foreach ($sections as $sec) {
                if($chap->getId()->{'$id'} == $sec->chapter_id) {
                    $questions[$chap->getId()->{'$id'}]['sections'][$sec->getId()->{'$id'}]['name'] = $sec->name;
                    $questions[$chap->getId()->{'$id'}]['sections'][$sec->getId()->{'$id'}]['questions'] = array();
                    foreach ($sec->questions as $quest) {
                        $questions[$chap->getId()->{'$id'}]['sections'][$sec->getId()->{'$id'}]['questions'][$quest['type']][] = $quest['id'];
                    }
                }
            }
        }
        return $questions;
    }

    /*
     * Save question by order
     */
    public function saveQuestionOrderAction() {
        $request =$this->request;
        if ($request->isPost()==true) {
            if ($request->isAjax() == true) {
                $questionIds = $request->getPost('questionIds');
                $ids = array();
                // Find sections by ids
                foreach($questionIds as $index => $quest) {
                    $ids[$index] = new MongoId($quest['id']);
                }
                $questions = Questions::find(array(
                    'conditions' => array(
                        '_id' => array('$in' => $ids)
                    )
                ));
                foreach($questions as $question){
                    foreach($questionIds as $index => $quest) {
                        if($question->getId()->{'$id'} == $quest['id']) {
                            $question->order = (int)$quest['order'];
                            $question->updated_at = '';
                            if(!$question->save()) {
                                echo json_encode(array('error' => true));
                                exit;
                            }
                            unset($questionIds[$index]);
                        }
                    }
                }
                echo json_encode(array('error' => $ids));
                exit;
            }
        }
        echo json_encode(array('error' => true));
        exit;
    }

    /**
     * Index action
     */
    public function searchAction() {
        $type = '';
        $request = $this->request;
        $bookId = $request->getQuery('book_id');
        $chapterIds = array();
        $questionIds = array();
        $chapters = Chapters::find(array(
            'conditions' => array('book_id' => $bookId)
        ));
        foreach($chapters as $chapter) {
            $chapterIds[] = $chapter->getId()->{'$id'};
        }
        $sections = Sections::find(array(
            'conditions' => array('chapter_id' => array('$in' => $chapterIds))
        ));
        foreach($sections as $section) {
            foreach($section->questions as $question) {
                $questionIds[] = new MongoId($question['id']);
            }
        }
        if(count($questionIds)) {
            $questions = Questions::find(array(
                'conditions' => array(
                    '_id' => array('$in' => $questionIds)
                )
            ));
        }
        if ($request->isAjax() == true) {
            $this->view->partial("questions/_search", array('questions' => $questions));
        }
        exit;
    }
}
