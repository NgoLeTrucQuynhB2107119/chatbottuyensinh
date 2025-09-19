<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Chatbot - Rasa + Laravel (API)</title>

    <style>
        :root {
            --bg: #f4f7fb;
            --card: #ffffff;
            --accent: #2b6ef6;
            --muted: #8a8f98;
            --bot-bg: #eef4ff;
            --user-bg: #2b6ef6;
        }

        body {
            margin: 0;
            font-family: Inter, system-ui, Arial, sans-serif;
        }

        /* Nút toggle mở chat */
        #chatToggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            font-size: 28px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: 0.2s;
        }

        #chatToggle:hover {
            transform: scale(1.05);
            background: #174ed4;
        }

        /* Chat wrapper */
        .chat-wrapper {
            position: fixed;
            bottom: 90px;
            right: 20px;
            width: 340px;
            height: 480px;
            background: var(--card);
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            display: none;
            flex-direction: column;
            overflow: hidden;
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Header */
        .chat-header {
            background: var(--accent);
            color: #fff;
            padding: 12px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-header h3 {
            margin: 0;
            font-size: 16px;
        }

        .chat-header button {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 6px;
            padding: 4px 8px;
            color: #fff;
            font-size: 12px;
            cursor: pointer;
            transition: 0.2s;
        }

        .chat-header button:hover {
            background: rgba(255, 255, 255, 0.35);
        }

        /* Body chat */
        .chat-body {
            flex: 1;
            padding: 12px;
            overflow-y: auto;
            background: var(--bg);
        }

        /* Tin nhắn */
        .msg {
            display: flex;
            align-items: flex-end;
            margin-bottom: 10px;
        }

        .msg.user {
            justify-content: flex-end;
        }

        .msg.bot {
            justify-content: flex-start;
        }

        /* Avatar bot */
        .msg.bot .avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--accent);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            margin-right: 6px;
        }

        /* Bubble */
        .bubble {
            padding: 10px 14px;
            border-radius: 16px;
            max-width: 70%;
            line-height: 1.4;
            font-size: 14px;
            word-wrap: break-word;
        }

        .bubble.user {
            background: var(--accent);
            color: #fff;
            border-bottom-right-radius: 4px;
        }

        .bubble.bot {
            background: var(--bot-bg);
            color: #000;
            border-bottom-left-radius: 4px;
        }

        /* Input area */
        .input-area {
            display: flex;
            align-items: center;
            padding: 8px;
            border-top: 1px solid #ddd;
            background: #fff;
        }

        .input-area input {
            flex: 1;
            border: 1px solid #ddd;
            border-radius: 20px;
            padding: 8px 12px;
            font-size: 14px;
            outline: none;
        }

        .input-area input:focus {
            border-color: var(--accent);
        }

        .icon-btn {
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            margin-left: 6px;
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
        }

        .icon-btn:hover {
            background: #174ed4;
        }

        .icon-small {
            width: 32px;
            height: 32px;
            font-size: 14px;
        }

        /* Typing indicator */
        .typing {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .typing .bubble {
            background: var(--bot-bg);
            padding: 8px 12px;
            border-radius: 16px;
            border-bottom-left-radius: 4px;
            display: flex;
            gap: 4px;
        }

        .dot {
            width: 6px;
            height: 6px;
            background: #666;
            border-radius: 50%;
            animation: blink 1.4s infinite;
        }

        .dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes blink {

            0%,
            80%,
            100% {
                opacity: 0.2;
            }

            40% {
                opacity: 1;
            }
        }
    </style>
</head>

<body>

    <!-- Toggle button -->
    <button id="chatToggle">💬</button>

    <!-- Chat window -->
    <div class="chat-wrapper" id="chatWrapper">
        <div class="chat-header">
            <h3>Chatbot Tuyển sinh</h3>
            <button id="newChatBtn">Cuộc trò chuyện mới</button>
        </div>

        <div class="chat-body" id="chatBody"></div>

        <div class="input-area">
            <input id="inputMessage" type="text" placeholder="Nhập tin nhắn..." />
            <button id="voiceBtn" class="icon-btn icon-small" title="Voice">🎤</button>
            <button id="sendBtn" class="icon-btn">➤</button>
            <button id="clearBtn" class="icon-btn icon-small" title="Xóa">🗑️</button>
        </div>
    </div>

    <script>
        const chatToggle = document.getElementById('chatToggle');
        const chatWrapper = document.getElementById('chatWrapper');
        const chatBody = document.getElementById('chatBody');
        const input = document.getElementById('inputMessage');
        const sendBtn = document.getElementById('sendBtn');
        const clearBtn = document.getElementById('clearBtn');
        const newChatBtn = document.getElementById('newChatBtn');
        const voiceBtn = document.getElementById('voiceBtn');

        let sender = localStorage.getItem('senderId') || 'web_' + Date.now();
        localStorage.setItem('senderId', sender);

        // Toggle widget
        chatToggle.addEventListener('click', () => {
            chatWrapper.style.display = chatWrapper.style.display === 'flex' ? 'none' : 'flex';
        });

        sendBtn.addEventListener('click', sendMessage);
        input.addEventListener('keydown', e => {
            if (e.key === 'Enter') sendMessage();
        });
        clearBtn.addEventListener('click', () => {
            chatBody.innerHTML = '';
        });
        newChatBtn.addEventListener('click', () => {
            chatBody.innerHTML = '';
            renderBot("Xin chào, tôi có thể tư vấn tuyển sinh. Bạn muốn hỏi gì?");
        });

        // Voice input
        let recognition;
        if ('webkitSpeechRecognition' in window) {
            recognition = new webkitSpeechRecognition();
            recognition.lang = 'vi-VN';
            recognition.onresult = function(e) {
                input.value = e.results[0][0].transcript;
                sendMessage();
            };
        }

        voiceBtn.addEventListener('click', () => {
            if (recognition) recognition.start();
            else alert("Trình duyệt không hỗ trợ Voice Recognition");
        });

        function renderUser(text) {
            const row = document.createElement('div');
            row.className = 'msg user';
            row.innerHTML = `<div class="bubble user">${text}</div>`;
            chatBody.appendChild(row);
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        function renderBot(text) {
            const row = document.createElement('div');
            row.className = 'msg bot';
            row.innerHTML = `<div class="bubble bot">${text}</div>`;
            chatBody.appendChild(row);
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        async function sendMessage() {
            const text = input.value.trim();
            if (!text) return;
            renderUser(text);
            input.value = "";

            // Hiển thị typing indicator
            const typing = document.createElement('div');
            typing.className = 'typing';
            typing.innerHTML = `
              <div class="bubble bot">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
              </div>
            `;
            chatBody.appendChild(typing);
            chatBody.scrollTop = chatBody.scrollHeight;

            try {
                const res = await fetch("/api/chatbot", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        sender: sender,
                        message: text
                    })
                });
                const data = await res.json();

                // Xóa typing indicator
                typing.remove();

                if (Array.isArray(data)) {
                    let delay = 500; // ms
                    data.forEach((item, i) => {
                        if (item.text) {
                            setTimeout(() => {
                                renderBot(item.text);
                            }, delay * (i + 1));
                        }
                    });
                }
            } catch (err) {
                typing.remove();
                renderBot("❌ Lỗi kết nối server");
            }
        }

        // Greeting
        renderBot("Xin chào! Tôi có thể giúp bạn về học phí, khóa học, lịch khai giảng.");
    </script>
</body>

</html>