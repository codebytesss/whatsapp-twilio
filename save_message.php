<?php
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['message'])) {
    $message = $data['message'];
    file_put_contents("prompt.txt", $message . PHP_EOL, FILE_APPEND);
    echo "Message saved successfully.";
} else {
    echo "No message received.";
}
?>
