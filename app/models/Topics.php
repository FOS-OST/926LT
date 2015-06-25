<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 6/23/15
 * Time: 09:48
 */

namespace Books\App\Models;


use MongoId;

class Topics extends ModelBase{
    /**
     * @var MongoId
     */
    private $id;
    /**
     * @var String
     */
    private $name;
    /**
     * @var int
     */
    private $index=-1;
    /**
     * @var bool
     */
    private $status=true; // show/hide status
    /**
     * @var int
     */
    private $number_book_display=3;// number ebooks display by default on client

    private $ebooks=array();// list ebooks in this topic , format array('book index'=>'book id')

    public function getSource()
    {
        return "topics";
    }


}