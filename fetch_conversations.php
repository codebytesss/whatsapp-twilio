<?php

require __DIR__ . '/vendor/autoload.php'; // Load Twilio SDK

use Twilio\Rest\Client;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Twilio Credentials
$account_sid =  $_ENV['TWILIO_SID'];
$auth_token =  $_ENV['TWILIO_TOKEN'];
$twilio_number = $_ENV['TWILIO_WHATSAPP_FROM']; // Twilio WhatsApp Number

// Initialize Twilio Client
$client = new Client($account_sid, $auth_token);

// Fetch messages
$messages = $client->messages->read([], 50); // Fetch last 50 messages

$conversationData = [];

foreach ($messages as $message) {
    $from = $message->from;
    $to = $message->to;
    $body = $message->body;
    $status = $message->status;
    $timestamp = $message->dateSent->format('Y-m-d H:i:s');

    // Group by sender
    if (!isset($conversationData[$from])) {
        $conversationData[$from] = [];
    }

    $conversationData[$from][] = [
        "to" => $to,
        "message" => $body,
        "status" => $status,
        "timestamp" => $timestamp
    ];
}

// Convert to JSON format
header('Content-Type: application/json');
echo json_encode($conversationData, JSON_PRETTY_PRINT);
?>