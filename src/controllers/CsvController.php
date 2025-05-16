<?php
namespace App\Controllers;
require(__DIR__ . '/../models/Csvupload.php');
require(__DIR__.'../../../vendor/autoload.php');

use App\Helpers\Response;
use App\Models\Csvupload;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CsvController
{

    public $post;

    public function __construct()
    {
        $this->post = new Csvupload();
    }

    public function store()
    {
        if (!isset($_POST)) {
            return Response::json(['error' => 'Invalid request. JSON data missing'], 400);
        }

        if (!isset($_FILES['files'])) {
            return Response::json(['error' => 'Please Upload File'], 400);
        }

        $file = $_FILES['files']['tmp_name'];
        $fileName = $_FILES['files']['name'];
        $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
        $allowedTypes = ['csv', 'xls', 'xlsx'];

        if (!in_array(strtolower($fileType), $allowedTypes)) {
            return Response::json(['error' => 'Invalid file type. Please upload a CSV, XLS, or XLSX file.'], 400);
        }
        $data = [];
        $batchSize = 500;

        if ($fileType === 'csv') {
            if (($handle = fopen($file, 'r')) !== false) {
                $header = fgetcsv($handle);
                $expectedHeaders = ['fullname', 'email', 'phone', 'district', 'state', 'gender'];
                if (array_diff($expectedHeaders, $header)) {

                    return Response::json(['error' => 'CSV headers do not match the expected format.'], 400);
                }
                while (($row = fgetcsv($handle)) !== false) {
                    $data[] = array_combine($header, $row);

                }
                fclose($handle);
            }
        } else {
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);
            $header = array_map('strtolower', $rows[1]);
            unset($rows[1]); 
            $expectedHeaders = ['fullname', 'email', 'phone', 'district', 'state', 'gender'];
            if (array_diff($expectedHeaders, $header)) {
               
                return Response::json(['error' => 'Excel headers do not match the expected format.'], 400);
            }
            foreach ($rows as $row) {
                $data[] = array_combine($header, $row);

            }
        }

        if (!empty($data)) {
            $result = $this->post->insertcsvData($data);
        }

  

        if (isset($result['success'])) {
            return Response::json(['message' => $result['success']], 201);
        } else {
            return Response::json(['error' => $result['error']], 500);
        }
    }

    public function getprogress(){
        $result = $this->post->getexceluploadataprogress();
        if (isset($result['success'])) {
            return Response::json(['message' => $result], 201);
        } else {
            return Response::json(['error' => $result['error']], 500);
        }
    }

    public function getpatientdata(){
        $result = $this->post->getpatientsdata();
        if (isset($result['success'])) {
            return Response::json(['message' => $result], 201);
        } else {
            return Response::json(['error' => $result['error']], 500);
        }
    }
}
