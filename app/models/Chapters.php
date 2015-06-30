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
     * @var String
     */
    public $name;
    /**
     * @var String
     */
    public $description;
    /**
     * @var int
     */
    public $order=0;
    /**
     * @var bool
     */
    public $status=true;// show/hide status
    /**
     * @var MongoId
     */
    public $book_id;// the parent book id

    public $sections=array();

    public function getSource()
    {
        return "chapters";
    }

    /**
     * Update chapter to chapters of books
     * @param $book
     * @param $chapterId
     * @param $chapterName
     */
    static function updateSection($chapter, $section){
        $sectionId = $section->getId()->{'$id'};
        $section = array(
            'id' => $sectionId,
            'order' => $section->order,
            'name' => $section->name,
            'type' => $section->type,
            'status' => $section->status
        );
        $sectionIds = array();
        $sections = $chapter->sections;
        foreach ($sections as $index => $sect) {
            $sectionIds[] = $sect['id'];
            if ($sectionId == $sect['id']) {
                $sections[$index] = $section;
            }
        }
        if (!in_array($sectionId, $sectionIds)) {
            $sections[] = $section;
        }
        // sort chapters by ASC
        usort($sections, function($a, $b) {
            //return strcmp($a['order'], $b['order']);
            if($a['order'] > $b['order']) {
                return 1;
            } else {
                return -1;
            }
        });
        $chapter->updated_at = '';
        $chapter->sections = $sections;
        $chapter->save();
    }

}