<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 6/23/15
 * Time: 15:42
 */

namespace Books\App\Models;


class NormalTest extends Sections {
    /**
     * @var int
     */
    private $number_questions=0;
    /**
     * @var int
     */
    private $time=5;// time to test , 0 is not limited
    /**
     * @var int
     */
    private $get_question=0;// 0 - order, 1- random
    /**
     * @var int
     */
    private $answer_check=0; // 0 - precheck, 1 - after check

    private $questions=array();// array of questions on this test , format array('index'=>'question_id')

}