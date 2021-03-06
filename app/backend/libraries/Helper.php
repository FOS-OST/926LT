<?php
use Phalcon\Translate\Adapter\NativeArray;

/**
 * Created by PhpStorm.
 * User: HIEUTRIEU
 * Date: 6/23/2015
 * Time: 4:29 PM
 */
class Helper {
    const DATE_FORMAT_FULL = 'd-m-Y h:i:s';
    const DATE_FORMAT_SHORT = 'd-m-Y';
    const DATE_FORMAT_TIME = 'h:i';
    const CURRENCY_VND = 'VND';

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const STATUS_DELETE = -1;
    const ACTION_IMPORT = 1;
    const ACTION_TEST = 0;
    const ACTION_PRINT = 2;
    
    public static function formatCurrency($price, $show=true) {
        $price = is_numeric($price)?$price:0;
        $price = number_format($price, 0, ',', ',');
        if($show) {
            return $price . ' ' . Helper::CURRENCY_VND;
        } else {
            return $price;
        }
    }

    public static function limitString($input, $length, $ellipses = true, $strip_html = true) {
        //strip tags, if desired
        if ($strip_html) {
            $input = strip_tags($input);
        }

        //no need to trim, already shorter than trim length
        if (strlen($input) <= $length) {
            return $input;
        }

        //find last space within length
        $last_space = strrpos(substr($input, 0, $length), ' ');
        $trimmed_text = substr($input, 0, $last_space);

        //add ellipses (...)
        if ($ellipses) {
            $trimmed_text .= '...';
        }

        return $trimmed_text;
    }

    public static function formatDate(MongoDate $date, $formatType = self::DATE_FORMAT_FULL) {
        return date($formatType, $date->sec);
    }

    public static function getOptionsStatus(){
        return array(
            1 => 'Hiện',
            0 => 'Ẩn',
        );
    }

    public static function renderBookInUser($books, $number=300) {
        //$userBooks = array();
        $userBooks = '';
        //$label = array('label-info', 'label-warning','label-primary', 'label-success');
        foreach($books as $index => $book) {
            //$lb = array_rand($label, 1);
            //$userBooks[] = "<i class='label label-info'>".self::limitString($book['name'],15)."</i>";
            $userBooks .= "<i class='label label-warning'>".self::limitString($book['name'],20)."</i> ";
            if($index > 10) {
                $userBooks .= '...';
                break;
            }
        }
        return $userBooks;
        //return self::limitString(implode(' ', $userBooks), $number, true, true);
    }

    public static function getOptionsCheckAnswer(){
        return array(
            1 => 'Check trước',
            0 => 'Check sau',
        );
    }

    /**
     * @param $list
     * @return array
     * Ramdom array
     */
    public static function shuffleAssoc($list) {
        if (!is_array($list)) return $list;

        $keys = array_keys($list);
        shuffle($keys);
        $random = array();
        foreach ($keys as $key) {
            $random[$key] = $list[$key];
        }
        return $random;
    }

    public static function br2nl($string) {
        return preg_replace('/\<br(\s*)?\/?\>/i', "", $string);
    }

    public static function getFullAnswer($type, $answer) {
        $stickerIcon = '';
        switch($type) {
            case 'SINGLE':
                $stickerIcon = $answer['correct'] ? '<i class="fa fa-dot-circle-o"></i> ':'<i class="fa fa-circle-o"></i> ';
                $stickerIcon = "<div class='col-xs-1'>{$stickerIcon}</div><div class='col-xs-11'>{$answer['answer']}</div>";
                break;
            case 'MULTI':
                $stickerIcon = $answer['correct'] ? '<i class="fa fa-check-square-o"></i> ':'<i class="fa fa-square-o"></i> ';
                $stickerIcon = "<div class='col-xs-1'>{$stickerIcon}</div><div class='col-xs-11'>{$answer['answer']}</div>";
                break;
            case 'FREE_TEXT':
                $stickerIcon = "<div style='margin-bottom:5px;'>{$answer['answer']} = <span style='border:1px solid #808080;padding:1px 10px;'>{$answer['correct']}</span></div>";
                break;
            case 'PLACE_ANSWER_TEXT':
                $stickerIcon = "<div style='margin-bottom:5px;'><span style='border:1px solid #808080;padding:1px 10px;'>...</span> ".$answer['answer']."</div>";
                break;
            case 'PLACE_ANSWER_IMAGE':
                $stickerIcon = "<div style='margin-bottom:5px;'><img src='{$answer['correct']}' width='50'/> {$answer['answer']} </div>";
                break;
            case 'SORT':
                $stickerIcon = "<div style='margin-bottom:5px;'>{$answer['order']} <span style='border:1px solid #808080;padding:1px 10px;'>{$answer['answer']}</span></div>";
                break;

            default:
                debug($answer);
                $stickerIcon = $answer['answer'];
                break;
        }
        return $stickerIcon;
    }

    public static function getBookStatus($value) {
        $status = 'Không rõ tình trạng';
        switch($value) {
            case self::STATUS_ACTIVE:
                $status = "<span class='label label-success' style='font-size: small'><i class='fa fa-check-square-o'></i> Đã xuất bản</span>";
                break;
            case self::STATUS_INACTIVE:
                $status = "<span class='label label-warning' style='font-size: small'><i class='fa fa-square-o'></i> Chưa xuất bản</span>";
                break;
            case self::STATUS_DELETE:
                $status = "<span class='label label-danger' style='font-size: small'><i class='fa fa-trash-o'></i> Xóa tạm vào thùng rác</span>";
                break;
        }
        return $status;
    }
    public static function getIconStatus($value, $label = 1) {
        $status = '';
        switch($value) {
            case self::STATUS_ACTIVE:
                if($label) {
                    $status = "<span class='label label-success' title='Đã kích hoạt / Đã xuất bản' data-toggle='tooltip' data-placement='top'><i class='fa fa-check-square-o'></i></span>";
                } else {
                    $status = "<i class='fa fa-check-square-o'></i>";
                }
                break;
            case self::STATUS_INACTIVE:
                if($label) {
                    $status = "<span class='label label-warning' title='Chưa kích hoạt / Chưa xuất bản' data-toggle='tooltip' data-placement='top'><i class='fa fa-square-o'></i></span>";
                } else {
                    $status = "<i class='fa fa-square-o'></i>";
                }
                break;
            case self::STATUS_DELETE:
                if($label) {
                    $status = "<span class='label label-danger' title='Đã xóa tạm vào thùng rác' data-toggle='tooltip' data-placement='top'><i class='fa fa-trash-o'></i></span>";
                } else {
                    $status = "<i class='fa fa-trash-o'></i>";
                }
                break;
        }
        return $status;
    }

    public static function menuType(){
        return array("0" => 'Ngang', "1" => 'Dọc');
    }
    public static function getMenuType($value){
        $menuType = self::menuType();
        return isset($menuType[$value])?$menuType[$value]:'';

    }

    public static function menuClassification(){
        return array('HOME'=>'HOME','TOP'=>'TOP','HOT'=>'HOT','MYBOOK'=>'MYBOOK','LATEST'=>'LATEST');
    }

    public static function getTranslateQuestionType($type) {
        require APP_PATH."/app/messages/vi.php";
        //Return a translation object
        $t = new NativeArray(array(
            "content" => $messages
        ));
        switch($type) {
            case 'SINGLE':
                return $t->_('Single Choice');
                break;
            case 'MULTI':
                return $t->_('Multi Choice');
                break;
            case 'FREE_TEXT':
                return $t->_('Free Text');
                break;
            case 'PLACE_ANSWER_TEXT':
                return $t->_('Place Answer Text');
                break;
            case 'PLACE_ANSWER_IMAGE':
                return $t->_('Place Answer Image');
                break;
            case 'SORT':
                return $t->_('Sort');
                break;
            case 'GROUP':
                return $t->_('Question group');
                break;
        }
    }
}