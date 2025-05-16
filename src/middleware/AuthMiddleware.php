<?php
namespace App\Middleware;

use App\Helpers\Response;

class Jwtmiddleware{
    private $secretKey;
    public function __construct()
    {
        $this->secretKey = getenv('JWT_SECRET');
    }

    public function handle(){
        $authheader = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : null;
        if(!$authheader){
            return false;
        }
        $token = str_replace('Bearer ', '', $authheader);
        if(!$this->validateToken($token)){
            return false; 
        }

        return true;
      
    }

    private function validateToken($token){
        list($header, $payload, $signature) = explode('.', $token);

        $decodedHeader = json_decode($this->base64UrlDecode($header), true);
        $decodedpayLoad = json_decode($this->base64UrlDecode($payload), true);

        if($decodedHeader['alg'] !== 'HS256'){
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $header . '.' .$payload, $this->secretKey, true);
        $expectedSignature = $this->base64UrlEncode($expectedSignature);

        if($expectedSignature !== $signature){
            return false;
        }

        if(isset($decodedpayLoad['exp']) && time() > $decodedpayLoad['exp']){
            return false;
        }

        return true;

    }

    private function base64UrlDecode($data) {
        $remainder = strlen($data) % 4;
        if($remainder){
            $padlen = 4 - $remainder;
            $data .= str_repeat('=', $padlen);
        }

        return base64_decode(strtr($data, '-_', '+/'));
    }

    private function base64UrlEncode($data){
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }


}
?>