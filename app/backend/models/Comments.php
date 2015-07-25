<?php
/**
 * Created by PhpStorm.
 * User: hieutrieu
 * Date: 6/23/15
 * Time: 11:42
 */

namespace Books\Backend\Models;

use Books\Backend\Models\Base\ModelBase;

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