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
     * @var MongoId
     */
    protected $id;
    /**
     * @var String
     */
    protected $name;
    /**
     * @var int
     */
    protected $index=-1;
    /**
     * @var bool
     */
    protected $status=true;// show/hine status

    /**
     * @var bool
     */
    protected $allow_translate=false;

    /**
     * @var bool
     */
    protected $is_free=true;// section is free or not

    /**
     * @var string
     */
    protected $type=Sections::TYPE_CONTENT;

    public function getSource()
    {
        return "sections";
    }


}