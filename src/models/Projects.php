<?php

namespace App\Models;

use App\Config\Database;

class Projects
{
    protected $db;
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function storeproject($input)
    {
        $stmt = $this->db->prepare("INSERT INTO projects (user_id, project_name) VALUES (?,?)");
        if (!$stmt) {
            $response = ["error" => $this->db->error];
            return $response;
        }
        $stmt->bind_param("is", $input['user_id'], $input['project_name']);
        if ($stmt->execute()) {
            $stmt->close();
            $response = ["success" => true];
        } else {
            $response =  ["error" => "Porject Created"];
        }
        return $response;
    }
}
