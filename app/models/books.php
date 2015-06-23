<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 6/23/15
 * Time: 09:54
 */

namespace Books\App\Models;

use MongoId;

class Books extends ModelBase{
    /**
     * @var MongoId
     */
    private $id;
    /**
     * @var String
     */
    private $name;
    /**
     * @var String
     */
    private $avatar; // image file path
    /**
     * @var int
     */
    private $price=0;// book's price
    /**
     * @var int
     */
    private $init_buy_number=500;

    /**
     * @var String
     */
    private $author;

    /**
     * @var array
     */
    private $chapters=array();// the book's chapters, format array('chapter_index'=>'chapter_id')

    public function getSource()
    {
        return "books";
    }


}