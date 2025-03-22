<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div id="navigation-bar">
        <div id="navigation-header">Navigation Header</div>
        <ul>
            <li><a href="">Chat</a></li>
            <li><a href="">Account</a></li>
        </ul>
        <form method="post" action="backend/clear_session.php">
            <button type="submit" name="clear_session">Clear Session</button>
        </form>
    </div>
    <div id="chat-container">
        <button id="toggle-nav-btn"><<</button>
        <div id="chat-header">Chat Header</div>
        <div id="chat-box">
        </div>
        <div id="input-area">
            <input type="text" id="userInput" placeholder="Gõ gì đó...">
            <button id="sendBtn">Gửi</button>
        </div> 
    </div>
    
    <script src="assets/js/nav.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>