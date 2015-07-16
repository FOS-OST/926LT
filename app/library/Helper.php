<?php
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
    public static function formatCurrency($price, $show=true) {
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
}