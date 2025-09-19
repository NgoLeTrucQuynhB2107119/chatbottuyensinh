<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Chatbot - Rasa + Laravel (API)</title>
    <link rel="stylesheet" href="{{ asset("chatbot/chat.css") }}">

</head>

<body>

    <!-- Toggle button -->
    <button id="chatToggle">💬</button>
    <div class="stars"></div>

    <!-- Chat window -->
    <div class="chat-wrapper" id="chatWrapper">
        <div class="chat-header">
            <h3>Chatbot Tuyển sinh</h3>
            <button id="newChatBtn">Cuộc trò chuyện mới</button>
        </div>

        <!-- Form nhập thông tin -->
        <div class="user-info-form" id="userInfoForm">
            <h4>Vui lòng nhập thông tin</h4>
            <input id="name" type="text" placeholder="Họ tên (*)" />
            <input id="email" type="email" placeholder="Email (*)" />
            <input id="phone" type="text" placeholder="Số điện thoại (*)" />
            <input id="address" type="text" placeholder="Địa chỉ (*)" />
            <button id="submitInfo">Bắt đầu trò chuyện</button>
        </div>

        <div class="chat-body" id="chatBody"></div>

        <div class="input-area" id="inputArea">
            <input id="inputMessage" type="text" placeholder="Nhập tin nhắn..." />
            <button id="sendBtn" class="icon-btn">➤</button>
        </div>
    </div>

    <script src="{{ asset("chatbot/chat.js") }}"></script>
</body>

</html>