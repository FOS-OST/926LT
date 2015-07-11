<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 6/23/15
 * Time: 12:05
 */

namespace Books\App\Models;


use MongoId;

class Sections extends ModelBase{
    const TYPE_CONTENT='SECTION_CONTENT';
    const TYPE_NORMAL_PRACTICE='NORMAL_PRACTICE';
    const TYPE_SUMMARY_PRACTICE='SUMMARY_PRACTICE';
    /**
     * @var String
     */
    public $name;
    /**
     * @var int
     */
    public $order=0;
    /**
     * @var bool
     */
    public $status=true;// show/hine status

    /**
     * @var bool
     */
    public $free=false;// free/nofree
    /**
     * @var bool
     */
    public $check_answer=false;// check before/check late

    /**
     * @var bool
     */
    public $random=false;// true: random / false: norandom:

    /**
     * @var int
     */
    public $time=5;
    /**
     * @var string
     */
    public $content;

    /**
     * @var int
     */
    public $chapter_id;

    /**
     * @var array
     */
    public $breadcrumb = array();

    /**
     * @var array
     */
    public $questions = array();

    /**
     * @var string
     */
    public $type=Sections::TYPE_CONTENT;

    public function getSource() {
        return "sections";
    }

    public static function getTypes() {
        return array(
            self::TYPE_CONTENT => 'Tài liệu',
            self::TYPE_NORMAL_PRACTICE => 'Trắc nghiệm',
            self::TYPE_SUMMARY_PRACTICE => 'Trắc nghiệm tổng hợp',
        );
    }

    public static function getType($value) {
        $types = self::getTypes();
        if(isset($types[$value])) {
            return $types[$value];
        } else {
            return null;
        }
    }

    /**
     * Update chapter to chapters of books
     * @param $book
     * @param $chapterId
     * @param $chapterName
     */
    static function updateQuestion($section, $question){
        $questionId = $question->getId()->{'$id'};
        $question = array(
            'id' => $questionId,
            'order' => $question->order,
            'name' => $question->name,
            'type' => $question->type,
            'status' => $question->status
        );
        $questionIds = array();
        $questions = $section->questions;
        foreach ($questions as $index => $ques) {
            $questionIds[] = $ques['id'];
            if ($questionId == $ques['id']) {
                $questions[$index] = $question;
            }
        }
        if (!in_array($questionId, $questionIds)) {
            $questions[] = $question;
        }
        // sort chapters by ASC
        usort($questions, function($a, $b) {
            //return strcmp($a['order'], $b['order']);
            if($a['order'] > $b['order']) {
                return 1;
            } else {
                return -1;
            }
        });
        $section->updated_at = '';
        $section->questions = $questions;
        $section->save();
    }

}