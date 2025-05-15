<?php
namespace App\Controllers;
require(__DIR__ . '/../models/Segments.php');
use App\Helpers\Response;
use App\Models\Segments;
class SegmentController{

    public $segments;

    public function __construct()
    {
      $this->segments = new Segments();
    }

    public function createsegments(){
        if (!isset($_POST)) {
            return Response::json(['error' => 'Invalid request. JSON data missing'], 400);
        }

        if (empty($_POST['fields']) || empty($_POST['values']) || empty($_POST['segment_name']) || empty($_POST['segment_logic'])) {
            return Response::json(['error' => 'All fields are required'], 400);
        }

        

        $input = $_POST;
        if (count($input['fields']) !== count($input['values'])) {
            return Response::json(['error' => 'Fields and values count mismatch'], 400);
        }
    
        $result = $this->segments->storesegments($input);
        if(isset($result['success'])){
            return Response::json(['message'=>$result['success']],201);
        }
        else{
            return Response::json(['error'=>$result['error']],500);
        }


    }

    public function getallsegments(){
        $result = $this->segments->getsegments();
        if(isset($result['success'])){
            return Response::json(['message'=>$result],201);
        }
        else{
            return Response::json(['error'=>$result['error']],500);
        }
    }

    public function getallsegmentsdata($id){
        $result = $this->segments->getSegmentAudience($id);
        if(isset($result['success'])){
            return Response::json(['message'=>$result],201);
        }
        else{
            return Response::json(['error'=>$result['error']],500);
        }
    }
}

?>