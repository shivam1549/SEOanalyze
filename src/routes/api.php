<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE');
header('Access-Control-Allow-Headers: Content-Type, x-requested-with, Authorization');
require(__DIR__.'/../config/database.php');
require(__DIR__ . '/../helpers/Response.php');
require(__DIR__ . '/../controllers/UserController.php');
require(__DIR__ . '/../controllers/CsvController.php');
require(__DIR__ . '/../controllers/CampaignController.php');
require(__DIR__ . '/../controllers/SegmentController.php');
require(__DIR__.'/../middleware/AuthMiddleware.php');
require(__DIR__ . '/../controllers/ProjectController.php');


use App\Controllers\UserController;
use App\Controllers\CsvController;
use App\Controllers\SegmentController;
use App\Helpers\Response;
use App\Controllers\CampaignController;
use App\Controllers\ProjectController;
use App\Middleware\Jwtmiddleware;


$requestURI = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Admin side Routes

// Remove the script name from the URI
$basePath = '/analyze'; // This should match your base path
$requestURI = str_replace($basePath, '', $requestURI); // Remove the base path
$requestMethod = $_SERVER['REQUEST_METHOD'];

if($requestURI === '/api/register' && $requestMethod === 'POST'){
    // echo "got it";
    (new UserController())->registerUser();
}
if($requestURI === '/api/login' && $requestMethod === 'POST'){
    // echo "got it";
    (new UserController())->loginUser();
}

if($requestURI === '/api/changepassword' && $requestMethod === 'POST'){
    // echo "got it";
    $jwt = new Jwtmiddleware();
    if($jwt->handle()){
    (new UserController())->changepassword();
    }
    else{
        return Response::json(['error'=> 'Token verification falied'], 401); 
    }
}

// Create projects Api
if($requestURI === '/api/create-project' && $requestMethod === 'POST'){
    //  echo "got it";
   
    $jwt = new Jwtmiddleware();
    if($jwt->handle()){
   
    (new ProjectController())->store();
    }
    else{
        return Response::json(['error'=> 'Token verification falied'], 401);
    }
}


if($requestURI === '/api/getcsvuploadprogress' && $requestMethod === 'GET'){
    //  echo "got it";
   
    $jwt = new Jwtmiddleware();
    if($jwt->handle()){
   
    (new CsvController())->getprogress();
    }
    else{
        return Response::json(['error'=> 'Token verification falied'], 401);
    }
}

if($requestURI === '/api/uploadcsv' && $requestMethod === 'POST'){
    //  echo "got it";
   
    $jwt = new Jwtmiddleware();
    if($jwt->handle()){
   
    (new CsvController())->store();
    }
    else{
        return Response::json(['error'=> 'Token verification falied'], 401);
    }
}



if($requestURI === '/api/getpatientdata' && $requestMethod === 'GET'){
    //  echo "got it";
   
    $jwt = new Jwtmiddleware();
    if($jwt->handle()){
   
    (new CsvController())->getpatientdata();
    }
    else{
        return Response::json(['error'=> 'Token verification falied'], 401);
    }
}


if($requestURI === '/api/createsegments' && $requestMethod === 'POST'){
    //  echo "got it";
   
    $jwt = new Jwtmiddleware();
    if($jwt->handle()){
   
    (new SegmentController())->createsegments();
    }
    else{
        return Response::json(['error'=> 'Token verification falied'], 401);
    }
}


if($requestURI === '/api/createcampaign' && $requestMethod === 'POST'){
    //  echo "got it";
   
    $jwt = new Jwtmiddleware();
    if($jwt->handle()){
   
    (new CampaignController())->createcampaign();
    }
    else{
        return Response::json(['error'=> 'Token verification falied'], 401);
    }
}

if($requestURI === '/api/getsegments' && $requestMethod === 'GET'){
    //  echo "got it";
   
    $jwt = new Jwtmiddleware();
    if($jwt->handle()){
   
    (new SegmentController())->getallsegments();
    }
    else{
        return Response::json(['error'=> 'Token verification falied'], 401);
    }
}


if($requestURI === '/api/getsegmentsdata' && $requestMethod === 'GET'){
    //  echo "got it";
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    $jwt = new Jwtmiddleware();
    if($jwt->handle()){
   
    (new SegmentController())->getallsegmentsdata($id);
    }
    else{
        return Response::json(['error'=> 'Token verification falied'], 401);
    }
}

// if($requestURI === '/api/deletecategory' && $requestMethod === 'GET'){
//     //  echo "got it";
//     $id = isset($_GET['id']) ? $_GET['id'] : null;
//     $jwt = new Jwtmiddleware();
//     if($jwt->handle()){
   
//     (new CategoryController())->deleteCategory($id);
//     }
//     else{
//         return Response::json(['error'=> 'Token verification falied'], 401);
//     }
// }

  


?>