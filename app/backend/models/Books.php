<?php
/**
 * Created by PhpStorm.
 * User: hieutrieu
 * Date: 6/23/15
 * Time: 11:42
 */

namespace Books\Backend\Models;

use Books\Backend\Models\Base\ModelBase;
use MongoRegex;
use Phalcon\Mvc\Model\Validator\PresenceOf;
use Phalcon\Mvc\Collection;

/**
 * Created by PhpStorm.
 * User: michael
 * Date: 6/23/15
 * Time: 09:54
 */

class Books extends ModelBase {
    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var array
     */
    public $category_ids = array();

    /**
     *
     * @var string
     */
    public $author;

    /**
     *
     * @var string
     */
    public $image = '/files/images/book_bg.jpg';

    /**
     *
     * @var string
     */
    public $description;

    /**
     *
     * @var float
     */
    public $price = 0;

    /**
     *
     * @var integer
     */
    public $free = 0;

    /**
     *
     * @var string
     */
    public $created_by;

    /**
     *
     * @var string
     */
    public $modified_by;

    /**
     *
     * @var integer
     * @var MongoId
     */
    public $rate = 0;

    /**
     *
     * @var integer
     * @var String
     */
    public $number_buyer = 0;

    /**
     *
     * @var integer
     * @var int
     */
    public $action=0;

    /**
     *
     * @var integer
     * @var int
     */
    public $status = 0;

    /**
     *
     * @var array
     */
    public $chapters=array();// the book's chapters, format array('chapter_index'=>'chapter_id')

    /**
     * Validations and business logic
     *
     * @return boolean
     * @var array
     */
    public function validation()
    {
        $this->validate(
            new PresenceOf(
                array(
                    "field" => "name",
                    "message" => "Tên sách là bắt buộc"
                )
            )
        );

        if ($this->validationHasFailed() == true) {
            return false;
        }
        return true;
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource() {
        return 'books';
    }

    static function buildConditions($search, $bookIds = array(),$role) {
        $searchRegex = new MongoRegex("/$search/i");
        $conditions = array(
            'status' => array('$gt' => -1),
            '$or' => array(
                array('name' => $searchRegex),
            )
        );
        if($role && ($role->allowPublish || $role->allowBook)) {
        } else {
            if(count($bookIds)) {
                $conditions['_id'] = array('$in' => $bookIds);
            } else {
                $conditions['_id'] = array('$in' =>array());
            }
        }
        return $conditions;
    }

    /**
     * Update chapter to chapters of books
     * @param $book
     * @param $chapterId
     * @param $chapterName
     */
    static function updateChapter($book, $chapter){
        $chapterId = $chapter->getId()->{'$id'};
        $chapter = array('id' => $chapterId, 'order' => $chapter->order, 'name' => $chapter->name);
        $chapterIds = array();
        $chapters = $book->chapters;
        foreach ($book->chapters as $index => $chap) {
            $chapterIds[] = $chap['id'];
            if ($chapterId == $chap['id']) {
                $chapters[$index] = $chapter;
            }
        }
        if (!in_array($chapterId, $chapterIds)) {
            $chapters[] = $chapter;
        }
        // sort chapters by ASC
        usort($chapters, function($a, $b) {
            //return strcmp($a['order'], $b['order']);
            if($a['order'] > $b['order']) {
                return 1;
            } else {
                return -1;
            }
        });
        $book->updated_at = '';
        $book->chapters = $chapters;
        $book->save();
    }
}
