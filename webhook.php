<?php

require_once __DIR__ . '/vendor/autoload.php';

use Twilio\Rest\Client;
use OpenAI;
use Twilio\TwiML\MessagingResponse;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Twilio Credentials
$sid = $_ENV['TWILIO_SID'];
$token = $_ENV['TWILIO_TOKEN'];
$twilio = new Client($sid, $token);
$twilio_number = "whatsapp:+19152866718"; // Your Twilio WhatsApp Number

// OpenAI API Key
$openai = OpenAI::client($_ENV['OPENAI_API_KEY']);

// Get incoming message
$from = $_POST['From'] ?? '';
$body = trim($_POST['Body'] ?? '');
$id = $_POST['MessageSid'] ?? '';
$status = $_POST['SmsStatus'] ?? '';

// **Ignore messages from Twilio's own number**
if ($from === $twilio_number) {
    exit;
}

// Log incoming message (for debugging)
$logFile = "twilio_log.json";

// Read existing log file if it exists
$existingData = [];
if (file_exists($logFile)) {
    $jsonContent = file_get_contents($logFile);
    if (!empty($jsonContent)) {
        $existingData = json_decode($jsonContent, true);
        if (!is_array($existingData)) {
            $existingData = []; // Ensure it's an array
        }
    }
}

// Generate AI response using OpenAI
$openaiResponse = $openai->chat()->create([
    'model' => 'gpt-3.5-turbo',
    'messages' => [
        ['role' => 'system', 'content' => 'You are a helpful assistant.'],
        ['role' => 'user', 'content' => $body]
    ],
    'max_tokens' => 100
]);

$aiMessage = $openaiResponse['choices'][0]['message']['content'] ?? 'Sorry, I am unable to process that request.';

// Append new message to the existing data
$existingData[] = [
    "id" => $id,
    "from" => $from,
    "message" => $body,
    "status" => $status,
    "date" => date('Y-m-d H:i:s'),
    "ai_response" => $aiMessage
];

// Save the updated JSON array back to the file
file_put_contents($logFile, json_encode($existingData, JSON_PRETTY_PRINT));

// Send the AI response back via Twilio
$twilio->messages->create(
    $from,
    [
        'from' => $twilio_number,
        'body' => $aiMessage
    ]
);

// Twilio needs XML response
header("Content-Type: text/xml");
$response = new MessagingResponse();
echo $response;

?>