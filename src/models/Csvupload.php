<?php

namespace App\Models;

use App\Config\Database;

class Csvupload
{
    protected $db;
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function insertcsvData($data)
    {
        $stmt = $this->db->prepare("INSERT INTO audience_data (fullname, email, phone, district, state, gender) VALUES (?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            $response = ["error" => $this->db->error];
            return $response;
        }


        $batchSize = 500;
        $offset = 0;
        $file = "upload_progress.txt";
        
        while (!empty($batch = array_slice($data, $offset, $batchSize))) {
            $actualBatchSize = count($batch); // Get the exact batch size
        
            foreach ($batch as $row) {
                $fullname = $row['fullname'];
                $email = $row['email'];
                $phone = $row['phone'];
                $district = $row['district'];
                $state = $row['state'];
                $gender = $row['gender'];
        
                $stmt->bind_param('ssssss', $fullname, $email, $phone, $district, $state, $gender);
        
                if (!$stmt->execute()) {
                    return ["error" => $stmt->error . PHP_EOL];
                }
            }
        
            // ✅ Correctly update progress without using $totalRows
            file_put_contents($file, ($offset + $actualBatchSize) . " uploaded\n");
        
            // Move offset forward by the actual batch size
            $offset += $actualBatchSize;
        
            sleep(1); // Optional delay
        }
        

        $response = ["success" => true];

        $stmt->close();
        $this->db->close();

        return $response;
    }

    public function getexceluploadataprogress()
    {
        $file = "upload_progress.txt";

        if (file_exists($file)) {
            $response = [
                "success"=>true,
                "progress"=>file_get_contents($file)
            ];
        } else {
            $response = [
                "error"=>"Not uploading",   
            ];
        }

        return $response;
    }

    public function getpatientsdata() {
        $sql = "SELECT fullname, email, phone, district, state, gender, status, created_at FROM audience_data";
        $result = $this->db->query($sql);
    
        if ($result->num_rows > 0) {
            $data = []; // Initialize data array
    
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
    
            return [
                "success" => true,
                "data" => $data,
            ];
        } else {
            return [
                "error" => "No data found",
            ];
        }
    }
    
}
