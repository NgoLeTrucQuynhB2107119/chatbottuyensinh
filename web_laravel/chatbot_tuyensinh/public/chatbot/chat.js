const chatToggle = document.getElementById("chatToggle");
const chatWrapper = document.getElementById("chatWrapper");
const chatBody = document.getElementById("chatBody");
const input = document.getElementById("inputMessage");
const sendBtn = document.getElementById("sendBtn");
const newChatBtn = document.getElementById("newChatBtn");
const userInfoForm = document.getElementById("userInfoForm");
const inputArea = document.getElementById("inputArea");
const submitInfo = document.getElementById("submitInfo");

let sender = "web_" + Date.now();
let userInfo = null;

chatToggle.addEventListener("click", () => {
    chatWrapper.style.display =
        chatWrapper.style.display === "flex" ? "none" : "flex";
});

newChatBtn.addEventListener("click", () => {
    chatBody.innerHTML = "";
    userInfoForm.style.display = "block";
    chatBody.style.display = "none";
    inputArea.style.display = "none";
});

submitInfo.addEventListener("click", () => {
    const name = document.getElementById("name").value.trim();
    const email = document.getElementById("email").value.trim();
    const phone = document.getElementById("phone").value.trim();
    const address = document.getElementById("address").value.trim();

    if (!name || !email || !phone || !address) {
        alert("Vui lòng nhập đầy đủ thông tin!");
        return;
    }

    userInfo = {
        name,
        email,
        phone,
        address,
    };

    userInfoForm.style.display = "none";
    chatBody.style.display = "block";
    inputArea.style.display = "flex";

    renderBot(
        `Xin chào <strong>${userInfo.name}</strong>, tôi có thể giúp gì cho bạn?`
    );
});

sendBtn.addEventListener("click", sendMessage);
input.addEventListener("keydown", (e) => {
    if (e.key === "Enter") sendMessage();
});

function renderUser(text) {
    const row = document.createElement("div");
    row.className = "msg user";
    row.innerHTML = `
        <div class="bubble user">${text}</div>
        <div class="avatar">👤</div>
      `;
    chatBody.appendChild(row);
    chatBody.scrollTop = chatBody.scrollHeight;
}

function renderBot(text) {
    const row = document.createElement("div");
    row.className = "msg bot";
    row.innerHTML = `
        <div class="avatar">🤖</div>
        <div class="bubble bot">${text}</div>
      `;
    chatBody.appendChild(row);
    chatBody.scrollTop = chatBody.scrollHeight;
}

async function sendMessage() {
    const text = input.value.trim();
    if (!text) return;
    renderUser(text);
    input.value = "";

    const typing = document.createElement("div");
    typing.className = "typing";
    typing.innerHTML = `
        <div class="bubble bot">
          <div class="dot"></div>
          <div class="dot"></div>
          <div class="dot"></div>
        </div>`;
    chatBody.appendChild(typing);
    chatBody.scrollTop = chatBody.scrollHeight;

    try {
        const res = await fetch("/api/chatbot", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                sender: sender,
                message: text,
            }),
        });
        const data = await res.json();

        typing.remove();

        if (Array.isArray(data)) {
            data.forEach((item, i) => {
                if (item.text) {
                    setTimeout(() => renderBot(item.text), 600 * (i + 1));
                }
            });
        }
    } catch (err) {
        typing.remove();
        renderBot("❌ Hệ thống đang bị lỗi, vui lòng thử lại sau!");
    }
}
