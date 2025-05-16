<?php

namespace App\Models;

class Whatsapp
{

    public $url = "https://graph.facebook.com/v21.0/517509864770500/messages";
    public $accessToken = "EAAlQXsgZAZCzUBO17QAzH4oK4Rn4YZCrjgQTAkjPtTYIQWIoohmhM7uV5426oWAGrB73Vtz2dXz3H6smBY2WjWomF0dVV8SgcEDAaLb4fBDXr4LMXLgrTpYe97ZAAj5DpRvjnvAR4gA2zWKkA4WMZAasvpc7f5SPfWtTzD3VZC8XX2y6ha0JKZBwQsuOjZBYuRDGgHayLIzW5LD2zKDo6Aocut7mR73ZAMKIZB47ZCv8UjscygZD";


    public function sendcampaignMessage($messageData, $number)
    {

        $template = [
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => '916395740776'
        ];

        if (!empty($messageData['message']) && !empty($messageData['image_url']) && !empty($messageData['button_text']) && !empty($messageData['button_link'])) {
            // Send Interactive Message (Text + Image + Button)
            $template["type"] = "template";
            $template["template"] = [
                "name" => "promotional_whatsapp", // Ensure this matches the exact template name in Meta
                "language" => [
                    "code" => "en_us"
                ],
                "components" => [
                    [
                        "type" => "body", 
                        "parameters" => [
                            [
                                "type" => "text",
                                "text" => "Default promotional message" // Ensure this is set dynamically
                            ]
                        ]
                    ]
                ]
            ];
            
            
            
            
            
        } elseif (!empty($messageData['image_url'])) {
            // Send Image-Only Message
            $template["type"] = "image";
            $template["image"] = [
                "link" => $messageData['image_url']
            ];
        } elseif (!empty($messageData['button_text']) && !empty($messageData['button_link'])) {
            // Send Button Message (Only if text is present)
            if (!empty($messageData['message'])) {
                $template["type"] = "interactive";
                $template["interactive"] = [
                    "type" => "button",
                    "body" => [
                        "text" => $messageData['message']
                    ],
                    "action" => [
                        "buttons" => [
                            [
                                "type" => "url",
                                "text" => $messageData['button_text'],
                                "url" => $messageData['button_link']
                            ]
                        ]
                    ]
                ];
            }
        } else {
            // Send Plain Text Message
            $template["type"] = "text";
            $template["text"] = [
                "preview_url" => true,
                "body" => $messageData['message']
            ];
        }



        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $this->accessToken,
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($template));

        $response = curl_exec($ch);
        curl_close($ch);
        file_put_contents("upload_progress.txt", $response);
        return json_decode($response, true);
    }
}
