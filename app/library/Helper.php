<?php
/**
 * Created by PhpStorm.
 * User: HIEUTRIEU
 * Date: 6/23/2015
 * Time: 4:29 PM
 */
class Helper {
    const DATE_FORMAT_FULL = 'd-m-Y h:i:s';
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

    public static function formatDate(MongoDate $date, $formatType = 'FULL') {
        return date(self::DATE_FORMAT_FULL, $date->sec);
    }

    public static function getOptionsStatus(){
        return array(
            1 => 'Show',
            0 => 'Hide',
        );
    }

}