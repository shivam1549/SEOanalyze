<?php
namespace App\Models;
require(__DIR__.'/../models/Whatsapp.php');

use App\Models\Segments;
use App\Models\Whatsapp;



use App\Config\Database;

class Campaign
{
    protected $db;
    public $segments;
    public $whatsapp;
    public function __construct()
    {
        $this->segments = new Segments();
        $this->whatsapp = new Whatsapp();
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function sendMessages($segmentid,$messages){
      $audiencedata = $this->segments->getSegmentAudience($segmentid);
      if(isset($audiencedata['data'])){
        $audiences = $audiencedata['data'];
        $phone = '';
        $this->whatsapp->sendcampaignMessage($messages, $phone);
        // foreach($audiences as $data){
        //     $phone = $data['phone'];
        //     $this->whatsapp->sendcampaignMessage($messages, $phone);

        // }
      }
    }

    public function storecampaign($input)
    {

        $templatemessage = json_encode($input['template_message']);

        $sql = "INSERT INTO campaigns (campaign_name, segment_id, template_message) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sis", $input['campaign_name'], $input['segment_id'], $templatemessage);

   
        if ($stmt->execute()) {

            $stmt->close();
            $this->sendMessages($input['segment_id'],$input['template_message']);
            $response = ["success" => true];
        } else {
            $response =  ["error" => "Campaign Created"];
        }
        return $response;
    }
}
