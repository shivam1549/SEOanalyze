<?php

namespace App\Helpers;

class Validations
{

    // private static $titleregex = '/^[a-zA-Z\s]+$/';
    public static function titleValidate($title)
    {
        $title = preg_replace('/[^a-zA-Z0-9\s]/', '', $title);
        $title = trim($title);
        $title = preg_replace('/\s+/', ' ', $title);
        return $title;
    }

    public static function digitsValidations($digits)
    {
        $digits = preg_replace('/[^0-9]/', '', $digits);
        $digits = trim($digits);
        return $digits;
    }

    public static function textValidate($text)
    {
        $text = preg_replace('/[^a-zA-Z0-9-\s]/', '', $text);
        $text = trim($text);
        $text = preg_replace('/\s+/', ' ', $text);
        return $text;
    }

    public static function urlValidate($url)
    {
        $url = strtolower($url);
        $url = preg_replace('/[^a-z0-9-\s]/', '', $url);
        $url = preg_replace('/[\s]+/', '-', $url);
        $url = preg_replace('/-+/', '-', $url);
        $url = trim($url, '-');
        return $url;
    }

    public static function imageValidate($image){
        if ($image['error'] !== UPLOAD_ERR_OK) {
            return false;
        }
        $allowedTypes = ['image/jpeg','image/jpg','image/png', 'image/gif', 'image/webp'];
        $filetype = mime_content_type($image['tmp_name']);
        if(!in_array($filetype, $allowedTypes)){
            return false;
        }
        return true;
    }

    public static function imagesizeValidate($file, $maxsize){
        if ($file['size'] > $maxsize) {
            return false;
        }
        return true;
    }
}
