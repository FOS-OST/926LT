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
     * @var MongoId
     */
    private $id;
    /**
     * @var String
     */
    private $content; // formated content
    /**
     * @var string
     */
    private $type=Questions::TYPE_SINGLE_CHOICE;
    /**
     * @var int
     */
    private $group_id;// if question is group ==> create new group id =time() and put to all child questions
    /**
     * @var array
     */
    private $answers=array();// list of answers, format array('id'=>'content')
    /**
     * @var array
     */
    private $correct_answers=array();// list of ordered correct answers, fromat array('index'=>'answer_id')



    public function getSource()
    {
        return "questions";
    }


}