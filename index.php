<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Style Chat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 h-screen flex items-center justify-center font-sans">
    <div class="container mx-auto h-5/6 max-w-6xl rounded-lg overflow-hidden shadow-xl flex">
        <!-- Sidebar / Contacts List -->
        <div class="w-1/3 bg-white border-r border-gray-200">
            <!-- Header -->
            <div class="bg-emerald-600 text-white p-4 flex justify-between items-center">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                        <i class="fas fa-user text-gray-500 text-xl"></i>
                    </div>
                    <h1 class="ml-3 font-semibold">Chats</h1>
                </div>
                <!-- <div class="flex space-x-4">
                    <i class="fas fa-circle-notch cursor-pointer"></i>
                    <i class="fas fa-comment-alt cursor-pointer"></i>
                    <i class="fas fa-ellipsis-v cursor-pointer"></i>
                </div> -->
            </div>

            <!-- Search Bar -->
            <div class="p-3 bg-gray-50">
                <div class="bg-white rounded-full flex items-center px-3 py-2 shadow-sm border border-gray-200">
                    <i class="fas fa-search text-gray-400 mr-2"></i>
                    <input type="text" placeholder="Search or start new chat" class="w-full outline-none text-sm">
                </div>
            </div>

            <!-- Contact List -->
            <div class="overflow-y-auto h-[calc(100%-120px)]">
                <ul id="contact-list" class="divide-y divide-gray-200">
                    <!-- Contacts will be populated here by JavaScript -->
                </ul>
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="w-2/3 flex flex-col">
            <!-- Chat Header -->
            <div class="bg-gray-100 p-4 flex justify-between items-center border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center overflow-hidden">
                        <i class="fas fa-user text-gray-500 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h2 id="current-contact" class="font-semibold text-gray-800">Select a contact</h2>
                        <p class="text-xs text-gray-500">Click on a contact to start chatting</p>
                    </div>
                </div>
                <!-- <div class="flex space-x-4 text-gray-500">
                    <i class="fas fa-search cursor-pointer"></i>
                    <i class="fas fa-paperclip cursor-pointer"></i>
                    <i class="fas fa-ellipsis-v cursor-pointer"></i>
                </div> -->
            </div>

            <!-- Messages Area -->
            <div id="conversation"
                class="flex-1 overflow-y-auto p-4 bg-[#e5ded8] bg-opacity-30 bg-[url('https://web.whatsapp.com/img/bg-chat-tile-dark_a4be4c74ff5df6e4c6fd38ea92315556.png')]">
                <!-- Messages will be populated here by JavaScript -->
                <div class="flex justify-center items-center h-full text-gray-500">
                    <p>Select a contact to view messages</p>
                </div>
            </div>

            <!-- Message Input -->
            <!-- <div class="flex items-center space-x-2 text-gray-500 mr-2">
                    <i class="far fa-smile text-xl cursor-pointer"></i>
                    <i class="fas fa-paperclip text-xl cursor-pointer"></i>
                </div> -->
            <!-- <div class="ml-2 w-10 h-10 bg-emerald-500 rounded-full flex items-center justify-center text-white cursor-pointer">
                    <i class="fas fa-microphone"></i>
                </div> -->
            <div class="bg-gray-100 p-4 flex items-center">

                <input id="send-massage" type="text" placeholder="Type a message"
                    class="flex-1 py-2 px-4 rounded-full border border-gray-300 focus:outline-none focus:border-emerald-500">
                <div
                    class="ml-2 w-10 h-10 bg-emerald-500 rounded-full flex items-center justify-center text-white cursor-pointer">
                    <i class="fas fa-paper-plane"></i>
                </div>


            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const contactList = document.getElementById('contact-list');
            const conversationDiv = document.getElementById('conversation');
            const currentContact = document.getElementById('current-contact');
            const ownerNumber = "whatsapp:+19152866718";

            const inputField = document.getElementById("send-massage");
            const sendButton = document.querySelector(".bg-emerald-500");
            let userNumber; // Declare userNumber outside the event listener
           
            sendButton.addEventListener("click", function() {
                const message = inputField.value.trim();
                
                if (message !== "" && userNumber) {
                    userNumber = userNumber.replace("whatsapp:", "");
                    fetch('send_message.php', { // Ensure correct PHP file name
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                UserMessage: message,
                                userNumber: userNumber
                            })
                        })
                        .then(response => response.text())
                        .then(data => {
                            console.log(data); // Logs "Message sent successfully"
                            inputField.value = "";
                        })
                        .catch(error => console.error("Error:", error));
                } else {
                    console.error("User number or message is missing");
                }
            });


            // Fetch conversation data from the server
            fetch('fetch_conversations.php')
                .then(response => response.json())
                .then(data => {
                    // Populate the contact list
                    for (const number in data) {
                        if (number === ownerNumber) {
                            continue;
                        }

                        // Extract the last message for preview
                        const messages = [...data[number], ...(data[ownerNumber] || []).filter(msg => msg.to ===
                            number)];
                        messages.sort((a, b) => new Date(a.timestamp) - new Date(b.timestamp));
                        const lastMessage = messages.length > 0 ? messages[messages.length - 1] : null;

                        const li = document.createElement('li');
                        li.className = 'hover:bg-gray-100 cursor-pointer';
                        li.innerHTML = `
                            <div class="flex items-center p-3">
                                <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-500 mr-3">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between">
                                        <h3 class="font-semibold text-gray-800 truncate">${formatPhoneNumber(number.replace("whatsapp:", ""))}</h3>
                                        <span class="text-xs text-gray-500">${lastMessage ? formatTimestamp(lastMessage.timestamp) : ''}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 truncate">${lastMessage ? (lastMessage.to === ownerNumber ? lastMessage.message : 'You: ' + lastMessage.message) : 'No messages yet'}</p>
                                </div>
                            </div>
                        `;

                        li.addEventListener('click', () => showConversation(number, data[number], data[
                            ownerNumber] || []));
                        contactList.appendChild(li);
                    }
                });


            function showConversation(number, userMessages, ownerMessages) {
                // Set userNumber dynamically
                userNumber = number;

                // Update the header with the current contact
                currentContact.textContent = formatPhoneNumber(number.replace("whatsapp:", ""));

                // Clear previous conversation
                conversationDiv.innerHTML = '';

                // Filter owner messages for this specific contact
                const relevantOwnerMessages = ownerMessages.filter(msg => msg.to === number);

                // Combine both user and owner messages into one array
                const allMessages = [...userMessages, ...relevantOwnerMessages];

                // Sort messages by timestamp
                allMessages.sort((a, b) => new Date(a.timestamp) - new Date(b.timestamp));

                // Create a message group for rendering
                let currentDate = '';

                // Display messages in sorted order
                allMessages.forEach(message => {
                    // Check if this is a new date and add a date separator if needed
                    const messageDate = new Date(message.timestamp).toLocaleDateString();
                    if (messageDate !== currentDate) {
                        currentDate = messageDate;
                        const dateSeparator = document.createElement('div');
                        dateSeparator.className = 'flex justify-center my-4';
                        dateSeparator.innerHTML = `
                <div class="bg-white px-3 py-1 rounded-lg text-xs text-gray-500 shadow-sm">
                    ${formatDateHeader(message.timestamp)}
                </div>
            `;
                        conversationDiv.appendChild(dateSeparator);
                    }

                    const messageDiv = document.createElement('div');

                    // Determine if the message is from the owner or user
                    if (message.to === ownerNumber) {
                        // Message from user to owner
                        messageDiv.className = 'flex justify-start mb-3';
                        messageDiv.innerHTML = `
                <div class="bg-white rounded-lg px-4 py-2 max-w-[70%] shadow-sm relative message-left">
                    <p class="text-gray-800">${message.message}</p>
                    <div class="flex justify-end items-center mt-1">
                        <span class="text-xs text-gray-500">${formatTimestamp(message.timestamp)}</span>
                        ${getStatusIcon(message.status)}
                    </div>
                </div>
            `;
                    } else {
                        // Message from owner to user
                        messageDiv.className = 'flex justify-end mb-3';
                        messageDiv.innerHTML = `
                <div class="bg-emerald-100 rounded-lg px-4 py-2 max-w-[70%] shadow-sm relative message-right">
                    <p class="text-gray-800">${message.message}</p>
                    <div class="flex justify-end items-center mt-1">
                        <span class="text-xs text-gray-500">${formatTimestamp(message.timestamp)}</span>
                        ${getStatusIcon(message.status)}
                    </div>
                </div>
            `;
                    }

                    conversationDiv.appendChild(messageDiv);
                });

                // Auto-scroll to the latest message
                conversationDiv.scrollTop = conversationDiv.scrollHeight;
            }


            // Function to format phone number to a more readable format
            function formatPhoneNumber(phoneStr) {
                // Extract just the number part
                const match = phoneStr.match(/\+(\d+)/);
                if (!match) return phoneStr;

                const number = match[1];
                // Format based on length - this is a simple example
                if (number.length === 10) {
                    return `(${number.substring(0, 3)}) ${number.substring(3, 6)}-${number.substring(6)}`;
                } else if (number.length === 11) {
                    return `+${number.charAt(0)} (${number.substring(1, 4)}) ${number.substring(4, 7)}-${number.substring(7)}`;
                }
                return phoneStr;
            }

            // Function to get appropriate status icon
            function getStatusIcon(status) {
                switch (status.toLowerCase()) {
                    case 'sent':
                        return '<i class="fas fa-check text-gray-400 ml-1 text-xs"></i>';
                    case 'delivered':
                        return '<i class="fas fa-check-double text-gray-400 ml-1 text-xs"></i>';
                    case 'read':
                        return '<i class="fas fa-check-double text-blue-500 ml-1 text-xs"></i>';
                    default:
                        return '<i class="fas fa-clock text-gray-400 ml-1 text-xs"></i>';
                }
            }

            // Function to format timestamp into readable HH:MM AM/PM format
            function formatTimestamp(timestamp) {
                const date = new Date(timestamp);
                return date.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                });
            }

            // Function to format date header
            function formatDateHeader(timestamp) {
                const date = new Date(timestamp);
                const today = new Date();
                const yesterday = new Date(today);
                yesterday.setDate(yesterday.getDate() - 1);

                if (date.toDateString() === today.toDateString()) {
                    return 'Today';
                } else if (date.toDateString() === yesterday.toDateString()) {
                    return 'Yesterday';
                } else {
                    return date.toLocaleDateString('en-US', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                }
            }
        });
    </script>
</body>

</html>
