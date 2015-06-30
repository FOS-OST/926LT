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
    
    /**
     * @var String
     */
    public $question; // formated content
    /**
     * @var string
     */
    public $type=Questions::TYPE_SINGLE_CHOICE;
    /**
     * @var int
     */
    public $group_id;// if question is group ==> create new group id =time() and put to all child questions
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

    public $allow_translate = false; // boolean
    public $section = array();
    public $correct_msg = '';
    public $incorrect_msg = '';


    public function getSource()
    {
        return "questions";
    }

    public static function renderAnswers($answers, $type){
        $answerData = array();
        if($type == self::TYPE_FREE_TEXT) {
            $answerData = array(
                'text' => $answers['text'],
                'correct' => true
            );
        } else {
            foreach ($answers['text'] as $index => $answer) {
                if ($type == self::TYPE_SINGLE_CHOICE || $type == self::TYPE_PLACE_ANSWER_IMAGE) {
                    $answerData[] = array(
                        'text' => $answer,
                        'correct' => $index == $answers['correct'] ? 'on' : 'off'
                    );
                } elseif ($type == self::TYPE_MULTI_CHOICE) {
                    $answerData[] = array(
                        'text' => $answer,
                        'correct' => (isset($answers['correct'][$index]) && $index == $answers['correct'][$index]) ? 'on' : 'off'
                    );
                }
            }
        }
        return $answerData;
    }
}