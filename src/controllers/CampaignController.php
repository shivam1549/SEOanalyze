<?php
namespace App\Controllers;
require(__DIR__ . '/../models/Campaign.php');

use App\Helpers\Response;
use App\Models\Campaign;
class CampaignController
{
    public $campaign;

    public function __construct()
    {
        $this->campaign = new Campaign();
    }

    public function createcampaign(){
        if (!isset($_POST)) {
            return Response::json(['error' => 'Invalid request. JSON data missing'], 400);
        }
        // print_r($_REQUEST);
        if (empty($_POST['campaign_name']) || empty($_POST['segment_id']) || empty($_POST['template_message'])) {
            return Response::json(['error' => 'All camp fields are required'], 400);
        }

        $input = $_POST;

        $result = $this->campaign->storecampaign($input);
        if(isset($result['success'])){
            return Response::json(['message'=>$result['success']],201);
        }
        else{
            return Response::json(['error'=>$result['error']],500);
        }


    }
}

?>