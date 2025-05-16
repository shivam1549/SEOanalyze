<?php
include('src/config/database.php');
include('src/models/Website.php');
include('src/helpers/Emailhelper.php');
use App\Config\Database;
use App\Models\Website;
use App\Helpers\Sendemail;

class Checkrunningwebsite{
protected $db;

public function __construct()
{
    $connection = new Database();
    $this->db = $connection->getConnection();

    

}

public function checkWebsitestatus(){
    $sql = "SELECT * FROM `websites`";
    $result = $this->db->query($sql);
    $data = array();
    $totalwebsites = $result->num_rows;
    while($row = $result->fetch_assoc()){
        $data[] = $row;
    }
    $website = new Website();

    $batchsize = 5;
    $checkedcount = 0;
    while ($checkedcount < $totalwebsites) {
        echo $checkedcount;
    foreach(array_slice($data, $checkedcount, $batchsize) as $websitedata){
        
        // var_dump($websitedata['website']);
     
        //     var_dump($webdata);
            $status  = $website->getWebsitestatus($websitedata['website']);
            $this->updatewebsiteData($websitedata['userid'], $status, $websitedata['website']);
            if($status['http_status_code'] !== 200 || !$status['ssl_valid']){
                $this->processMail($websitedata['userid'], $status, $websitedata['website']);
            }
        
    }
    $checkedcount += $batchsize;
}
}

public function updatewebsiteData($userid, $status, $website){
    $jsonstatus = json_encode($status, true);
    $sql = "UPDATE websites SET status_code = '$jsonstatus', last_checked = NOW() WHERE userid = '$userid' AND website = '$website'";
    $result = $this->db->query($sql);
}

public function processMail($userid, $status, $website){
$sql = "SELECT * FROM users WHERE id = $userid LIMIT 1";
$result = $this->db->query($sql);
if($result->num_rows>0){
    $row = $result->fetch_assoc();
    $name = $row['username'];
    $mail = $row['email'];
    $subject = "Website Error Status";
    $body = '
    <p>Hello '.$name.'</p>
    <p>In Your website '.$website.' some issue detected please review it.</p>
    <p>Status : '.($status['http_status_code'] == NULL ? "No Status" : $status['http_status_code']).'</p>
    <p>SSL: '.($status['ssl_valid'] ? "working" : "Not working").'</p>
    <p>Web Status Team <br> Thanks,</p>
    ';

    // echo $body;
    $mailobject = new Sendemail($mail, $subject, $body);
    $mailobject->mailsent();
}
}

}

$obj = new Checkrunningwebsite();
$obj->checkWebsitestatus();
?>