<?php

require_once __DIR__ . '/vendor/autoload.php';

use Twilio\Rest\Client;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Twilio Credentials
$sid = $_ENV['TWILIO_SID'];
$token = $_ENV['TWILIO_TOKEN'];
$twilio = new Client($sid, $token);
$twilio_number = "whatsapp:+19152866718"; // Your Twilio WhatsApp Number

// Read JSON from the POST request
$inputJSON = file_get_contents("php://input");
$input = json_decode($inputJSON, true);

$UserMessage = $input['UserMessage'] ?? null;
$UserNumber = $input['userNumber'] ?? null;

// Validate input
if (!$UserMessage || !$UserNumber) {
    die("Error: Missing required parameters.");
}

// Send message via Twilio
$twilio->messages->create(
    "whatsapp:$UserNumber",
    [
        'from' => $twilio_number,
        'body' => $UserMessage
    ]
);

echo "Message sent successfully";
exit;

?>
