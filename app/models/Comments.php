<?php
/**
 * Created by PhpStorm.
 * User: Hieutrieu
 * Date: 4/7/15
 * Time: 14:25
 */

namespace Books\App\Models;

use MongoId;

class Comments extends ModelBase {
    /**
     * @var object MongoId
     */
    public $book_id;
    /**
     * @var array
     */
    public $user = array();
    /**
     * @var string
     */
    public $comment;
    /**
     * @var object
     */
    public $answer;
    /**
     * @var int
     */
    public $status = 1;

    public function getSource(){
        return "comments";
    }
}