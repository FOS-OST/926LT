<?php

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
        $request = $this->request;
        $sectionId = $request->getQuery('section_id');
        $section = Sections::findById($sectionId);
        $questions = Questions::find(array(
            'conditions' => array(
                'section.id' => $sectionId
            )
        ));
        //debug($section);
        if ($request->isAjax() == true) {
            if($section->type == Sections::TYPE_NORMAL_PRACTICE) {
                $this->view->partial('questions/_index', array('questions' => $questions, 'section' => $section));
            } elseif($section->type == Sections::TYPE_SUMMARY_PRACTICE) {
                $this->view->partial('questions/_index_summary', array('questions' => $questions, 'section' => $section));
            }
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
                $this->tag->setDefaults((array)$question);
                $this->tag->setDefault('id', $id);
                $this->view->type = $question->type;
            } else {
                $question = new Questions();
                $this->tag->setDefaults((array)$question);
                $this->tag->setDefault('order', count($section->questions)+1);
                $this->tag->setDefault('type', $type);
                $this->view->type = $type;
            }
            $this->tag->setDefault('section_id', $sectionId);
            $this->tag->setDefault('chapter_id', $section->chapter_id);
            if($section->type == Sections::TYPE_NORMAL_PRACTICE) {
                echo $this->view->partial('questions/_edit');
            } elseif($section->type == Sections::TYPE_SUMMARY_PRACTICE) {
                // Get all questions of type NORMAL_PRACTICE
                $sections = Sections::find(array(
                    'conditions' => array(
                        '$and' => array(
                            array('chapter_id' => $section->chapter_id),
                            array('type' => Sections::TYPE_NORMAL_PRACTICE),
                        )
                    )
                ));
                $questions = array();
                foreach($sections as $sec) {
                    foreach($sec->questions as $quest) {
                        $questions[$sec->getId()->{'$id'}][$quest['type']][] = $quest['id'];
                    }
                }
                echo $this->view->partial('questions/_edit_summary', array('section' => $section, 'questions' => $questions, 'sections' => $sections));
            }

        }
        exit;
    }

    public function saveAction() {
        //debug($_POST, true);
        if ($this->request->isAjax() == true) {
            if ($this->request->isPost()==true) {
                $id = $this->request->getPost("id");
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
                $allowTranslate = $this->request->getPost("allow_translate");
                $question->name = $this->request->getPost("name");
                $question->question = $this->request->getPost("question");
                $question->allow_translate = isset($allowTranslate)?1:0;
                $question->order = $this->request->getPost("order");
                $question->correct_msg = $this->request->getPost("correct_msg");
                $question->incorrect_msg = $this->request->getPost("incorrect_msg");
                $question->type = $this->request->getPost("type");
                $answers = Questions::renderAnswers($this->request->getPost("answers"), $question->type);
                $question->status = filter_var($this->request->getPost("status"), FILTER_VALIDATE_BOOLEAN);
                $question->section = array('id' => $sectionId, 'name' => $section->name);
                $question->answers = $answers;
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
                )
            ));
            echo $this->view->partial('questions/_index', array('questions' => $questions,'section' => $section));
            exit;
        }
    }

    public function editSummaryAction() {
        $request =$this->request;
        $id = $request->getQuery("id");
        $sectionId = $request->getQuery("section_id");
        $section = Sections::findById($sectionId);
        if ($request->isAjax() == true) {
            if(!$section) {
                echo "Section is required";
                exit;
            }
            if($id) {
                $question = Questions::findById($id);
                $this->tag->setDefaults((array)$question);
                $this->tag->setDefault('id', $id);
            } else {
                $question = new Questions();
                $this->tag->setDefaults((array)$question);
                $this->tag->setDefault('order', count($section->questions)+1);
                $this->tag->setDefault('type', $type);

            }
            $this->tag->setDefault('section_id', $sectionId);
            $this->tag->setDefault('chapter_id', $section->chapter_id);
            if($section->type == Sections::TYPE_NORMAL_PRACTICE) {
                echo $this->view->partial('questions/_edit');
            } elseif($section->type == Sections::TYPE_SUMMARY_PRACTICE) {
                // Get all questions of type NORMAL_PRACTICE
                $sections = Sections::find(array(
                    'conditions' => array(
                        '$and' => array(
                            array('chapter_id' => $section->chapter_id),
                            array('type' => Sections::TYPE_NORMAL_PRACTICE),
                        )
                    )
                ));
                $questions = array();
                foreach($sections as $sec) {
                    foreach($sec->questions as $quest) {
                        $questions[$sec->getId()->{'$id'}][$quest['type']][] = $quest['id'];
                    }
                }
                echo $this->view->partial('questions/_edit_summary', array('section' => $section, 'questions' => $questions, 'sections' => $sections));
            }

        }
        exit;
    }

    public function saveSummaryAction() {
        //if ($this->request->isAjax() == true) {
            if ($this->request->isPost() == true) {
                $numbers = $this->request->getPost("number");
                $sectionId = $this->request->getPost("section_id");
                $questionIds = json_decode($this->request->getPost("questionIds"));
                debug($questionIds, true);
                $section = Sections::findById($sectionId);
                $questionIdRs = array();
                foreach($questionIds as $type => $qIds) {
                    $indexs = array_rand($qIds, $numbers[$type]);
                    foreach($indexs as $index) {
                        $questionIdRs[] = new MongoId($qIds[$index]);
                    }
                }
                $questions = Questions::find(array(
                    'conditions' => array(
                        '_id' => array('$in' => $questionIdRs)
                    )
                ));

                // Delete all question old
                //And then do the query
                $questionOlds = Questions::find(array(
                    'conditions' => array(
                        'section.id' => $section->getId()->{'$id'}
                    )
                ));

                //And then proceed to delete normally
                foreach($questionOlds as $qOld){
                    $qOld->delete();
                }

                $secQuestions = array();
                foreach($questions as $index => $question) {
                    $newQuestion = clone $question;
                    $newQuestion->_id = new MongoId();
                    $newQuestion->section = array(
                        'id' => $section->getId()->{'$id'},
                        'name' => $section->name
                    );
                    $newQuestion->save();
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
        //}
        exit;
    }
}
