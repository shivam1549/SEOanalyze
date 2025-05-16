<?php

namespace App\Models;
require(__DIR__.'/../helpers/Decodeurl.php');

use App\Config\Database;
use App\Helpers\Decodeurl;

class User
{
    protected $db;
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    public function register($firstname, $lastname, $email, $password)
    {

        
        $firstname = $this->db->real_escape_string($firstname);
        $email = $this->db->real_escape_string($email);
        $lastname = $this->db->real_escape_string($lastname);

        $hashpassword = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO users (firstname,lastname,email,password) VALUES ('$firstname','$lastname','$email','$hashpassword')";
        $result = $this->db->query($query);
        return $result;
    }

    // Check if user exist in database
    public function checkIfUseralreadyexist($email) {
        $query = "SELECT email FROM users WHERE email = '$email'";
        $result = $this->db->query($query);
        if($result->num_rows > 0){
            return true;
        }
        else{
            return false;
        }
    }

    public function login($email, $password)
    {
        $email = $this->db->real_escape_string($email);
        $password = $this->db->real_escape_string($password);
    
        $query = "SELECT email,firstname, lastname, id, password FROM users WHERE email = '$email' LIMIT 1";
        $result = $this->db->query($query);
        if($result->num_rows > 0){
          $data = $result->fetch_assoc();
          $dbpssword = $data['password'];
            if(password_verify($password, $dbpssword)){
                $getjwt = $this->createJsontoken($email, $password);
                $response = [
                    "success"=>true,
                    'id' =>$data['id'],
                    'username' => $data['firstname'],
                    'email' => $data['email'],
                    'jwt' => $getjwt
                ];
                // return $response;
            }
            else{
                $response = ["error"=>"password not correct"];
               
            }
        }
        else{
            $response = ["error"=>"Email not found"];
       
        }
        return $response;
    }

    public function changepass($password, $currentpassword, $confirmpassword){
        $password = $this->db->real_escape_string($password);
        $currentpassword = $this->db->real_escape_string($currentpassword);
        $confirmpassword = $this->db->real_escape_string($confirmpassword);

        $authheder = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : null;

        $token = str_replace('Bearer ', '', $authheder);
        list($header, $payload, $signature) = explode('.', $token);
        $decodeurl = new Decodeurl();
        $decodedpayload = json_decode($decodeurl->base64UrlDecode($payload), true);
        $email = $this->db->real_escape_string($decodedpayload['user_id']);

        $sql = "SELECT email from users WHERE email = '$email' LIMIT 1";
        $result = $this->db->query($sql);
        if($result->num_rows != 1){
            $result = ["error"=>"User Not Found"];
            return $result;
            exit;
        }
        $hashpassword = password_hash($currentpassword, PASSWORD_BCRYPT);
        $sql = "UPDATE users SET password = '$hashpassword' WHERE email = '$email'";
        $result = $this->db->query($sql);
        if($this->db->affected_rows > 0){
            $result = ["success"=>"Password Updated"];   
        }
        else{
            $result = ["error" => "Something went wrong please try again"];
        }
        return $result;
        // echo $token;

    }

    public function createJsontoken($email){
        $header = json_encode(['alg'=>'HS256', 'typ'=>'JWT']);
        $base64URLheader = str_replace(['+','/','='], ['-','_',''], base64_encode($header));

        $payload = json_encode(['user_id'=>$email, 'exp'=> time() + 3600]);
        $base64URLpayload = str_replace(['+','/','='], ['-','_',''], base64_encode($payload));

        $secretkey = getenv('JWT_SECRET');
        $signature = hash_hmac('sha256', $base64URLheader . "." . $base64URLpayload, $secretkey, true);
        $base64URLsignature = str_replace(['+','/','='], ['-','_',''], base64_encode($signature));

        $jwt = $base64URLheader . "." . $base64URLpayload . "." . $base64URLsignature;
        return $jwt;
    }

    public function __destruct()
    {
        $this->db->close();
    }
}
