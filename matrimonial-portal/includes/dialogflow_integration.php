<?php
// dialogflow_integration.php

// Include necessary files
require_once 'db_connection.php';

// Function to send a query to Dialogflow
function sendToDialogflow($query, $sessionId) {
    // Dialogflow API credentials
    $projectId = 'your-dialogflow-project-id'; // Replace with your Dialogflow project ID
    $accessToken = 'your-dialogflow-access-token'; // Replace with your Dialogflow access token

    // API endpoint
    $url = "https://dialogflow.googleapis.com/v2/projects/$projectId/agent/sessions/$sessionId:detectIntent";

    // Request payload
    $data = [
        "queryInput" => [
            "text" => [
                "text" => $query,
                "languageCode" => "en-US"
            ]
        ]
    ];

    // Initialize cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the request
    $response = curl_exec($ch);
    curl_close($ch);

    // Decode the response
    return json_decode($response, true);
}

// Handle incoming chatbot requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $query = $input['query'];
    $sessionId = $input['sessionId']; // Unique session ID for each user

    // Send the query to Dialogflow
    $dialogflowResponse = sendToDialogflow($query, $sessionId);

    // Extract the response from Dialogflow
    $responseText = $dialogflowResponse['queryResult']['fulfillmentText'];

    // Return the response to the frontend
    echo json_encode(['response' => $responseText]);
}
?>