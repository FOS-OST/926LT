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
                    'section.id' => $sectionId,
                    'group_id' => null
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
        $groupQuestions = array();
        if ($request->isAjax() == true) {
            if(!$section) {
                echo "Section is required";
                exit;
            }
            if($id) {
                $question = Questions::findById($id);
                // private for Group
                if($question->type == Questions::TYPE_GROUP) {
                    // Get all question same group_id
                    $groupQuestions = Questions::find(array(
                        'conditions' => array(
                                'group_id' => $question->getId()->{'$id'},
                                'type' => array('$ne' => Questions::TYPE_GROUP)
                        )
                    ));
                } else {
                    $question->answers = Questions::getViewAnswers($question->answers, $question->type);
                }
                $this->tag->setDefaults((array)$question);
                $this->view->type = $question->type;
            } else {
                $question = new Questions();
                $question->type = $type;
                $question->answers = Questions::getViewAnswers($question->answers, $type);
                $this->tag->setDefaults((array)$question);
                $this->tag->setDefault('order', count($section->questions)+1);
                $this->view->type = $type;
            }
            $this->tag->setDefault('section_id', $sectionId);
            $this->tag->setDefault('chapter_id', $section->chapter_id);
            $this->view->groupQuestions = $groupQuestions;
            if($section->type == Sections::TYPE_NORMAL_PRACTICE) {
                if($question->type == Questions::TYPE_GROUP) {
                    echo $this->view->partial('questions/_edit_group');
                } else {
                    echo $this->view->partial('questions/_edit');
                }
            }

        }
        exit;
    }

    public function saveAction() {
        if ($this->request->isAjax() == true) {
            if ($this->request->isPost()==true) {
                $sectionId = $this->request->getPost("section_id");
                $section = Sections::findById($sectionId);
                if (!$section) {
                    echo "Section is required";
                    exit;
                }

                $type = $this->request->getPost("type");
                if($type == Questions::TYPE_GROUP) {
                    Questions::saveQuestionGroup($this->request, $section);
                } else {
                    Questions::saveQuestionSingle($this->request, $section);
                }
            }
            $questions = Questions::find(array(
                'conditions' => array(
                    'section.id' => $sectionId,
                    'group_id' => array('$ne' => null)
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

    public function grouptypeAction() {
        $request = $this->request;
        $type = $request->getQuery('type');
        $type = strtolower($type);
        if ($request->isAjax() == true) {
            $this->view->partial("questions/types/groups/{$type}", array('groupIndex' => time()));
        }
        exit;
    }


    public function saveGroupAction() {
        if ($this->request->isAjax() == true) {
            if ($this->request->isPost() == true) {
                $sectionId = $this->request->getPost("section_id");
                $section = Sections::findById($sectionId);
                if (!$section) {
                    echo "Section is required";
                    exit;
                }

                $question = Questions::saveQuestionGroup($this->request, $section);
                $this->tag->setDefaults($question->toArray());
                $this->tag->setDefault('section_id', $sectionId);
                $this->tag->setDefault('chapter_id', $section->chapter_id);

                $questions = Questions::find(array(
                    'conditions' => array(
                        'group_id' => $question->getId()->{'$id'},
                        'type' => array('$ne' => Questions::TYPE_GROUP)
                    ),
                    'sort' => array('order' => 1),
                ));
                echo $this->view->partial('questions/_edit_group', array('section' => $section, 'groupQuestions' => $questions));
                exit;
            }
        }
    }
    /**
     * Save child question in the group
     */
    public function editChildAction() {
        $request =$this->request;
        $id = $request->getQuery("id");
        $sectionId = $request->getQuery("section_id");
        $type = $request->getQuery("type");
        $groupId = $request->getQuery("group_id");
        $section = Sections::findById($sectionId);
        $groupQuestions = array();
        if ($request->isAjax() == true) {
            if(!$section) {
                echo "Section is required";
                exit;
            }
            if($id) {
                $question = Questions::findById($id);
                $question->answers = Questions::getViewAnswers($question->answers, $question->type);
                $this->tag->setDefaults($question->toArray());
                $this->view->type = $question->type;
            } else {
                $question = new Questions();
                $question->type = $type;
                $question->group_id = $groupId;
                $question->answers = Questions::getViewAnswers($question->answers, $type);
                $this->tag->setDefaults($question->toArray());
                $this->tag->setDefault('order', count($section->questions)+1);
                $this->view->type = $type;
            }
            $this->tag->setDefault('section_id', $sectionId);
            $this->tag->setDefault('chapter_id', $section->chapter_id);
            $this->view->groupQuestions = $groupQuestions;
            if($section->type == Sections::TYPE_NORMAL_PRACTICE) {
                echo $this->view->partial('questions/_edit_child');
            }
        }
        exit;
    }

    public function saveChildAction() {
        if ($this->request->isAjax() == true) {
            if ($this->request->isPost()==true) {
                $sectionId = $this->request->getPost("section_id");
                $section = Sections::findById($sectionId);
                if (!$section) {
                    echo "Section is required";
                    exit;
                }

                Questions::saveQuestionSingle($this->request, $section);
            }
            $groupId = $this->request->getPost("group_id");
            $questions = Questions::find(array(
                'conditions' => array(
                    'group_id' => $groupId,
                    'type' => array('$ne' => Questions::TYPE_GROUP)
                ),
                'sort' => array('order' => 1),
            ));
            echo $this->view->partial('questions/_index_child', array('groupQuestions' => $questions));
            exit;
        }
    }
}
