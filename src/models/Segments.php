<?php

namespace App\Models;

use App\Config\Database;

class Segments
{
    protected $db;
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function storesegments($input)
    {


        $sql = "INSERT INTO segments (segment_name, segment_logic) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $input['segment_name'], $input['segment_logic']);

        if ($stmt->execute()) {
            $segment_id = $this->db->insert_id; // Get last inserted segment ID
            $stmt->close();

            $segment_fiters = "INSERT INTO segments_filters (segment_id, field, operator, value) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($segment_fiters);
            foreach ($input['fields'] as $index => $field) {
                if (empty($field)) {
                    continue;
                }
                $value = $input['values'][$index];

                $operator = (strpos($value, ',') !==  false) ? 'IN' : '=';
                $stmt->bind_param("isss", $segment_id, $field, $operator, $value);
                $stmt->execute();
            }
            $stmt->close();
            $response = ["success" => true];
        } else {
            $response =  ["error" => "Failed to save segment"];
        }
        return $response;
    }

    public function getsegments()
    {
        $sql = "SELECT 
                    s.id AS segment_id, 
                    s.segment_name, 
                    s.segment_logic, 
                    sf.field, 
                    sf.operator, 
                    sf.value
                FROM segments s
                LEFT JOIN segments_filters sf ON s.id = sf.segment_id
                ORDER BY s.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $segments = [];
            while ($row = $result->fetch_assoc()) {
                $segment_id = $row['segment_id'];

                // Group filters under their respective segments
                if (!isset($segments[$segment_id])) {
                    $segments[$segment_id] = [
                        'segment_id' => $row['segment_id'],
                        'segment_name' => $row['segment_name'],
                        'segment_logic' => $row['segment_logic'],
                        'filters' => []
                    ];
                }

                // If a filter exists, add it to the segment
                if ($row['field'] !== null) {
                    $segments[$segment_id]['filters'][] = [
                        'field' => $row['field'],
                        'operator' => $row['operator'],
                        'value' => $row['value']
                    ];
                }
            }

            $response = [
                "success" => true,
                "data" => array_values($segments) // Re-index the array
            ];
        } else {
            $response = [
                "success" => false,
                "error" => "No segments found"
            ];
        }

        return $response;
    }

    public function getSegmentAudience($id)
    {
        // Fetch filters for the given segment ID
        $sql = "SELECT sf.field, sf.operator, sf.value, s.segment_logic 
                FROM segments AS s 
                INNER JOIN segments_filters AS sf ON s.id = sf.segment_id 
                WHERE s.id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        $filters = [];
        $segment_logic = "AND"; // Default logic

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $filters[] = [
                    'field' => $row['field'],
                    'operator' => strtoupper($row['operator']), // IN, =, etc.
                    'value' => $row['value']
                ];
                $segment_logic = strtoupper($row['segment_logic']); // Ensure AND/OR is uppercase
            }
        }

        // No filters found, return empty response
        if (empty($filters)) {
            $response = ["error" => false, "message" => "No filters found for this segment"];
            return $response;
        }

        // Build WHERE condition dynamically
        $conditions = [];
        $params = [];
        $types = "";

        foreach ($filters as $filter) {
            $field = $filter['field'];
            $operator = $filter['operator'];
            $value = $filter['value'];

            if ($operator === "IN") {
                $valuesArray = explode(",", $value); // Convert "Kanpur,Lucknow" → ["Kanpur", "Lucknow"]
                $placeholders = implode(",", array_fill(0, count($valuesArray), "?"));
                $conditions[] = "$field IN ($placeholders)";
                $params = array_merge($params, $valuesArray);
                $types .= str_repeat("s", count($valuesArray)); // Strings for IN clause
            } else {
                $conditions[] = "$field $operator ?";
                $params[] = $value;
                $types .= "s";
            }
        }

        // Combine conditions using AND/OR logic
        $whereClause = implode(" $segment_logic ", $conditions);

        // 1. Get total count
        $countQuery = "SELECT COUNT(*) as totcount FROM audience_data WHERE $whereClause";
        $countStmt = $this->db->prepare($countQuery);

        if (!empty($params)) {
            $countStmt->bind_param($types, ...array_values($params));
        }

        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $totalCount = $countResult->fetch_assoc()['totcount']; // Get count


        // var_dump($whereClause);

        $query = "SELECT fullname, email, phone, district, state, gender FROM audience_data WHERE $whereClause";

        // Prepare statement
        $stmt = $this->db->prepare($query);

        if (!empty($params)) {
            $stmt->bind_param($types, ...array_values($params));
        }

        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch audience data
        $audience = [];
        while ($row = $result->fetch_assoc()) {
            $audience[] = $row;
        }

        $response = ["success" => true, "totcount" => $totalCount, "data" => $audience];
        return $response;
    }
}
