<?php

namespace App\Controllers;

require(__DIR__ . '/../models/Projects.php');

use App\Helpers\Response;
use App\Models\Projects;


class ProjectController
{
    public $project;

    public function __construct()
    {
        $this->project = new Projects();
    }
    public function store()
    {
        if (!isset($_POST)) {
            return Response::json(['error' => 'Invalid request. JSON data missing'], 400);
        }
        print_r($_REQUEST);
        if (empty($_POST['user_id']) || empty($_POST['project_name'])) {
            return Response::json(['error' => 'All project fields are requiresd'], 400);
        }

        $input = $_POST;

        $result =  $this->project->storeproject($input);
        if (isset($result['success'])) {
            return Response::json(['message' => $result['success']], 201);
        } else {
            return Response::json(['error' => $result['error']], 500);
        }
    }
}
