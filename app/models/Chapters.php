<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 6/23/15
 * Time: 11:42
 */

namespace Books\App\Models;


use MongoId;

class Chapters extends ModelBase{
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
    private $status=true;// show/hide status
    /**
     * @var MongoId
     */
    private $parent_book;// the parent book id

    private $sections=array();

    public function getSource()
    {
        return "chapters";
    }


}