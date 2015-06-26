<?php
namespace Books\App\Models;
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
     * @var string
     */
    public $category_id;

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
    public $price;

    /**
     *
     * @var integer
     */
    public $free;

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
    public $viewer = 0;
    /**
     *
     * @var integer
     * @var String
     */
    public $order = 0;

    /**
     *
     * @var integer
     * @var int
     */
    public $test;

    /**
     *
     * @var integer
     * @var int
     */
    public $status;

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
                    "message" => "The name is required"
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
    public function getSource()
    {
        return 'books';
    }

    static function buildConditions($search)
    {
        $searchRegex = new MongoRegex("/$search/i");
        $conditions = array(
            '$or' => array(
                array('name' => $searchRegex),
            )
        );
        return $conditions;
    }
}
