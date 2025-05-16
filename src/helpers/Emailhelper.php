<?php
namespace App\Helpers;
require(__DIR__.'/../../vendor/autoload.php');
use PHPMailer\PHPMailer\PHPMailer;

class Sendemail{
    public $email;
    public $subject;
    public $body;
    public function __construct($email, $subject, $body)
    {
       $this->body = $body;
       $this->email = $email; 
       $this->subject = $subject;
    }
public function mailsent(){

  

    $userMail = new PHPMailer;
    $userMail->isSMTP();
    $userMail->Host = 'smtp.gmail.com';
    $userMail->SMTPAuth = true;
    $userMail->Username = 'bornwithcode@gmail.com'; // Your Gmail email address
    $userMail->Password = 'tniq baau fkfo wfna'; // Your Gmail password or app-specific password
    $userMail->SMTPSecure = 'tls';
    $userMail->Port = 587;
    $userMail->isHTML(true);

    $userMail->setFrom('bornwithcode@gmail.com', 'Web Status Admin');
    $userMail->addAddress($this->email);

    $userMail->Subject = $this->subject;
    $userMail->Body = $this->body;
    $userMail->send();
}
}
?>