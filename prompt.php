<?php
$status = false;
if (isset($_GET['secret'])) {
    $secret = $_GET['secret'];
    if ($secret === "Admin123456@") {
        $status = true;
    } else {
        echo "Invalid User";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Validate User</h1>
    <?php if ($status) : ?>
        <h2>User Validated</h2>
    <?php else : ?>
        <form id="validate" action="" method="GET">
            <label for="user">User Number</label>
            <input type="text" id="secret" name="secret">
            <button type="submit">Send</button>
        </form>
    <?php endif; ?>


    <?php if (isset($_GET['secret']) && $_GET['secret'] === "Admin123456@") : ?>
        <!-- This form is only accessible if the user is validated -->
        <form id="messageForm" action="save_message.php" method="POST">
            <label for="message">AI Content</label>
            <input type="text" id="message" name="message">
            <button type="submit">Send</button>
        </form>
    <?php endif; ?>

    <script>
        
        document.querySelector('#messageForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const message = document.querySelector('#message').value;

            // Send data to PHP file to save the message
            const response = await fetch('save_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ message })
            });

            const result = await response.text();
            console.log(result);
        });
    </script>
</body>
</html>
