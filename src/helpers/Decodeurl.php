<?php
namespace App\Helpers;
class Decodeurl{
    public function base64UrlDecode($data) {
        $remainder = strlen($data) % 4;
        if($remainder){
            $padlen = 4 - $remainder;
            $data .= str_repeat('=', $padlen);
        }

        return base64_decode(strtr($data, '-_', '+/'));
    }
}
?>