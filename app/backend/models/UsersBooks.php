<?php
/**
 * Created by PhpStorm.
 * User: hieutrieu
 * Date: 6/23/15
 * Time: 11:42
 */

namespace Books\Backend\Models;

use Books\Backend\Models\Base\ModelBase;
use MongoDate;
use MongoId;
use MongoRegex;
use Phalcon\Mvc\Collection;

/**
 * Created by PhpStorm.
 * User: michael
 * Date: 6/23/15
 * Time: 09:54
 */

class UsersBooks extends ModelBase {
    /**
     * @var mongoId
     */
    public $user_id;
    /**
     * @var mongoId
     */
    public $book_id;

    public $book_name;
    public $book_status;
    public $book_price = 0;
    public $book_author;
    public $created_by;
    public $created_by_name;


    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource() {
        return 'users_books';
    }

    static function buildConditions($daterange, $uid){
        $daterange = explode(' - ', $daterange);
        $startDate = new MongoDate(strtotime($daterange[0]));
        $endDate = new MongoDate(strtotime($daterange[1]));
        $conditions = array(
            '$and' => array(
                array('user_id' => new MongoId($uid)),
                array('created_at' =>
                    array(
                        '$gte' => $startDate,
                        '$lt' => $endDate
                    ),
                )
            )
        );
        return $conditions;
    }
}
