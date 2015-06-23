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
    private $content;
    /**
     * @var int
     */
    private $index=-1;
    /**
     * @var bool
     */
    private $status=true;// show/hine status

    public function getSource()
    {
        return "sections";
    }


}