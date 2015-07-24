<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 6/23/15
 * Time: 13:16
 */

namespace Books\App\Models;


use MongoId;

class Questions extends ModelBase {
    const TYPE_SINGLE_CHOICE='SINGLE';// SINGLE CHOICE
    const TYPE_MULTI_CHOICE='MULTI';// MULTIPLE CHOICES
    const TYPE_FREE_TEXT='FREE_TEXT'; // PUSH TEXT TO PLACE
    const TYPE_ORDER_ANSWER='ORDER_ANSWER'; // ORDER ANSWER 1-2-3-4
    const TYPE_PLACE_ANSWER_TEXT='PLACE_ANSWER_TEXT'; // SELECT PREDEFINED ANSWER TO PLACE
    const TYPE_PLACE_ANSWER_IMAGE='PLACE_ANSWER_IMAGE';// SELECT PREDEFINED ANSWERS TO IMAGES
    const TYPE_SORT='SORT';// SELECT PREDEFINED ANSWERS TO IMAGES
    const TYPE_GROUP='GROUP';// SELECT PREDEFINED ANSWERS TO IMAGES

    /**
     * @var String
     */
    public $question; // formated content
    /**
     * @var string
     */
    public $type=Questions::TYPE_SINGLE_CHOICE;
    /**
     * @var mongoId
     */
    public $group_id = null;// if question is group ==> create new group id =time() and put to all child questions
    /**
     * @var array
     */
    public $answers=array();// list of answers, format array('id'=>'content')
    /**
     * @var array
     */
    public $translates=array(
        'question' => '',
        'answers' => array()
    );// list of ordered correct answers, fromat array('index'=>'answer_id')

    /**
     * @var int
     */
    public $order = 0;

    public $allow_translate = 0; // boolean
    public $section = array();
    public $correct_msg = '';
    public $incorrect_msg = '';
    public $question_group_type = '';
    public $group_content = '';


    public function getSource()
    {
        return "questions";
    }

    public static function renderAnswers($answers, $type){
        $answerData = array();
        switch($type) {
            case self::TYPE_SINGLE_CHOICE:
                $answerData = self::getDataSingleAnswers($answers);
                break;
            case self::TYPE_MULTI_CHOICE:
                $answerData = self::getDataMultiAnswers($answers);
                break;
            case self::TYPE_FREE_TEXT:
                $answerData = self::getDataFreeTextAnswers($answers);
                break;
            case self::TYPE_SORT:
                $answerData = self::getDataSortAnswers($answers);
                break;
            case self::TYPE_PLACE_ANSWER_IMAGE:
                $answerData = self::getDataAnswerImageAnswers($answers);
                break;
            case self::TYPE_PLACE_ANSWER_TEXT:
                $answerData = self::getDataAnswerTextAnswers($answers);
                break;
        }
        return $answerData;
    }

    public static function getDataFreeTextAnswers($answers) {
        $answerData = array();
        foreach ($answers['sl'] as $index => $value) {
            $answerData[] = array(
                'variable' => $answers['variable'][$index],
                'answer' => $answers['answer'][$index],
                'html' => 0,
            );
        }
        /*$answers = explode("\n", $answers['text']);
        foreach($answers as $index => $answer) {
            $answerData = array(
                'order' => (int)($index+1),
                'answer' => strtolower($answer),
                'answer' => $answers['answer'],
                'html' => 0,
            );
        }*/
        return $answerData;
    }

    public static function getDataSingleAnswers($answers) {
        $answerData = array();
        foreach ($answers['sl'] as $index => $value) {
            $correct = $index == $answers['correct'] ? 1 : 0;
            $allowHtml = 0;
            if(isset($answers['html'])) {
                foreach ($answers['html'] as $htmlValue) {
                    if ($htmlValue == $index) {
                        $allowHtml = 1;
                    }
                }
            }
            $answerData[] = array(
                'order' => (int)($index+1),
                'answer' => isset($answers['answer'][$index]) ? $answers['answer'][$index] : '',
                'html' => $allowHtml,
                'correct' => (int)$correct,
            );
        }
        return $answerData;
    }

    public static function getDataMultiAnswers($answers) {
        $answerData = array();
        foreach ($answers['sl'] as $index => $value) {
            $correct = 0;
            if(isset($answers['correct'])) {
                foreach ($answers['correct'] as $correctValue) {
                    if ($correctValue == $index) {
                        $correct = 1;
                    }
                }
            }
            $allowHtml = 0;
            if(isset($answers['html'])) {
                foreach ($answers['html'] as $htmlValue) {
                    if ($htmlValue == $index) {
                        $allowHtml = 1;
                    }
                }
            }
            $answerData[] = array(
                'order' => (int)($index+1),
                'answer' => isset($answers['answer'][$index]) ? $answers['answer'][$index] : '',
                'html' => $allowHtml,
                'correct' => $correct,
            );
        }
        return $answerData;
    }

    public static function getDataSortAnswers($answers) {
        $answerData = array();
        foreach ($answers['sl'] as $index => $value) {
            $allowHtml = 0;
            if(isset($answers['html'])) {
                foreach ($answers['html'] as $htmlValue) {
                    if ($htmlValue == $index) {
                        $allowHtml = 1;
                    }
                }
            }
            $answerData[] = array(
                'order' => (int)($index+1),
                'answer' => isset($answers['answer'][$index]) ? $answers['answer'][$index] : '',
                'html' => $allowHtml,
            );
        }
        return $answerData;
    }

    public static function getDataAnswerImageAnswers($answers) {
        $answerData = array();
        foreach ($answers['sl'] as $index => $value) {
            $allowHtml = 0;
            if(isset($answers['html'])) {
                foreach ($answers['html'] as $htmlValue) {
                    if ($htmlValue == $index) {
                        $allowHtml = 1;
                    }
                }
            }
            $answerData[] = array(
                'order' => (int)($index+1),
                'answer' => isset($answers['answer'][$index]) ? $answers['answer'][$index] : '',
                'html' => $allowHtml,
                'correct' => isset($answers['correct'][$index]) ? $answers['correct'][$index] : '',
            );
        }
        return $answerData;
    }

    public static function getDataAnswerTextAnswers($answers) {
        $answerData = array();
        foreach ($answers['sl'] as $index => $value) {
            $allowHtml = 0;
            $answerData[] = array(
                'order' => (int)($index+1),
                'answer' => isset($answers['answer'][$index]) ? $answers['answer'][$index] : '',
                'html' => $allowHtml,
                'correct' => 1,
            );
        }
        return $answerData;
    }

    public static function getViewAnswers($answers, $type) {
        $answerData = array();
        switch($type) {
//            case self::TYPE_FREE_TEXT:
//                foreach($answers as $answer) {
//                    $answerData[] = $answer['answer'];
//                }
//                return implode("\n", $answerData);
//                break;
            default:
                return $answers;
        }

    }


    public static function saveQuestionGroup($request, $section) {
        $type = $request->getPost("type");
        $id = $request->getPost("_id");
        if($type == Questions::TYPE_GROUP) {
            if($id) {
                $question = Questions::findById($id);
            } else {
                $question = new Questions();
            }
            $question->group_id = null;
            $question->type = $type;
            $question->question = $request->getPost("question");
            $question->order = (int) $request->getPost("order");
            $question->status = $type;
            $question->status = filter_var($request->getPost("status"), FILTER_VALIDATE_BOOLEAN);
            $sectionArr = array('id' => $section->getId()->{'$id'}, 'name' => $section->name);
            $question->section = $sectionArr;
            $question->save();
            // Update to questions
            Sections::updateQuestion($section, $question);
        }
        return $question;
        /*
        $check = true;
        $type = $request->getPost("type");
        $groupAnswers = $request->getPost("answers");
        $allowTranslate = filter_var($request->getPost("allow_translate"), FILTER_VALIDATE_BOOLEAN);
        $translates = $request->getPost("translates");
        $content = $request->getPost("question");
        $order = (int) $request->getPost("order");
        $correctMsg = $request->getPost("correct_msg");
        $incorrectMsg = $request->getPost("incorrect_msg");
        $status = filter_var($request->getPost("status"), FILTER_VALIDATE_BOOLEAN);
        $sectionArr = array('id' => $section->getId()->{'$id'}, 'name' => $section->name);
        $groupId = time();

        foreach($groupAnswers as $answer) {
            $id = $answer['question_id'];
            if ($id != '') {
                $question = Questions::findById($id);
                if (!$question) {
                    $check = false;
                }
            } else {
                $question = new Questions();
            }
            $question->group_id = $groupId;
            $question->question = $content;
            $question->order = $order;
            $question->correct_msg = $correctMsg;
            $question->incorrect_msg = $incorrectMsg;
            $question->status = $status;
            $question->section = $sectionArr;
            $question->type = Self::TYPE_GROUP;
            $question->question_group_type = $answer['question_group_type'];
            $question->group_content = $answer['group_content'];

            $answers = Questions::renderAnswers($answer, $question->question_group_type);
            $question->answers = $answers;
            if ($allowTranslate) {
                $question->translates = $translates;
            }
            if (!$question->save()) {
                $check = false;
            }
            // Update to questions
            Sections::updateQuestion($section, $question);
        }
        return $check;*/
    }

    public static function saveQuestionSingle($request, $section) {
        $id = $request->getPost("_id");
        $answerPosts = $request->getPost("answers");
        $allowTranslate = (int)($request->getPost("allow_translate"));
        $translates = $request->getPost("translates");
        $content = $request->getPost("question");
        $order = (int) $request->getPost("order");
        $type = $request->getPost("type");
        $groupId = $request->getPost("group_id", 'string', null);
        $correctMsg = $request->getPost("correct_msg");
        $incorrectMsg = $request->getPost("incorrect_msg");
        $status = (int)($request->getPost("status"));
        $sectionArr = array('id' => $section->getId()->{'$id'}, 'name' => $section->name);

        if ($id != '') {
            $question = Questions::findById($id);
            if (!$question) {
                return false;
            }
        } else {
            $question = new Questions();
        }

        $question->question = $content;
        $question->order = $order;
        $question->correct_msg = $correctMsg;
        $question->incorrect_msg = $incorrectMsg;
        $question->status = $status;
        $question->section = $sectionArr;
        $question->type = $type;
        $question->group_id = $groupId;

        $answers = Questions::renderAnswers($answerPosts, $question->type);
        $question->answers = $answers;
        if ($allowTranslate) {
            $question->translates = $translates;
        }
        if (!$question->save()) {
            return false;
        }
        // Update to questions
        Sections::updateQuestion($section, $question);
        return true;
    }
}