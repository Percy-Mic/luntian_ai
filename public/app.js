document.addEventListener('DOMContentLoaded', () => {
    const chatBox = document.getElementById('chat-box');
    const userInput = document.getElementById('user-input');
    const sendBtn = document.getElementById('send-btn');
    const micBtn = document.getElementById('mic-btn');
    const fileUpload = document.getElementById('file-upload');
    const newChatBtn = document.getElementById('new-chat-btn');
    const historyList = document.getElementById('chat-history');
    const menuBtn = document.getElementById("menu");
    const sidebar = document.querySelector(".sidebar");
    const stopSpeechBtn = document.getElementById('stop-speech');

    // ===== CHAT STATE =====
    let chats = JSON.parse(localStorage.getItem('luntian_chats') || '[]');
    let currentChatId = localStorage.getItem('luntian_active_id') || null;
    let isListening = false;

    if (chats.length === 0) {
        createNewChat();
    } else {
        renderHistory();
        const activeIndex = chats.findIndex(c => c.id == currentChatId);
        loadChat(activeIndex !== -1 ? activeIndex : 0);
    }

    function saveToStorage() {
        localStorage.setItem('luntian_chats', JSON.stringify(chats));
        localStorage.setItem('luntian_active_id', currentChatId);
    }

    function renderHistory() {
        historyList.innerHTML = '';
        chats.forEach((chat, i) => {
            const li = document.createElement('li');
            li.className = `chat-item ${chat.id == currentChatId ? 'active' : ''}`;
            li.innerHTML = `
                <span class="chat-title" title="Double-click to rename">${chat.title}</span>
                <button class="delete-btn" data-index="${i}">
                    <img src="public/assets/icons/delete-svgrepo-com.svg" width="20px">
                </button>
            `;
            const titleSpan = li.querySelector('.chat-title');
            titleSpan.onclick = () => loadChat(i);
            titleSpan.ondblclick = () => {
                const newTitle = prompt('Rename your chat:', chat.title);
                if (newTitle && newTitle.trim() !== "") {
                    chats[i].title = newTitle.trim();
                    saveToStorage();
                    renderHistory();
                }
            };
            li.querySelector('.delete-btn').onclick = (e) => {
                e.stopPropagation();
                deleteChat(i);
            };
            historyList.appendChild(li);
        });
    }

    function loadChat(idx) {
        if (!chats[idx]) return;
        currentChatId = chats[idx].id;
        chatBox.innerHTML = '';
        window.speechSynthesis.cancel(); 
        if(stopSpeechBtn) stopSpeechBtn.style.display = 'none';
        chats[idx].messages.forEach(m => appendMessageDOM(m.role, m.text));
        saveToStorage();
        renderHistory();
    }

    function appendMessageDOM(role, text) {
        const msgDiv = document.createElement('div');
        msgDiv.className = `message ${role}`;
        const contentDiv = document.createElement('div');
        contentDiv.className = 'message-content';

        if (role === 'assistant' && typeof marked !== 'undefined') {
            contentDiv.innerHTML = marked.parse(text);
            contentDiv.querySelectorAll('pre').forEach((block) => {
                const container = document.createElement('div');
                container.className = 'code-container';
                const header = document.createElement('div');
                header.className = 'code-header';
                header.innerHTML = `<span class="code-lang">Code</span><button class="copy-btn">Copy</button>`;
                header.querySelector('.copy-btn').onclick = function() {
                    const codeText = block.querySelector('code').innerText;
                    navigator.clipboard.writeText(codeText);
                    this.innerHTML = 'Copied!';
                    setTimeout(() => { this.innerHTML = 'Copy'; }, 2000);
                };
                block.parentNode.insertBefore(container, block);
                container.appendChild(header);
                container.appendChild(block);
            });
        } else {
            contentDiv.textContent = text;
        }

        msgDiv.appendChild(contentDiv);
        chatBox.appendChild(msgDiv);
        chatBox.scrollTo({ top: chatBox.scrollHeight, behavior: 'smooth' });
    }

    function showTyping(show) {
        let loader = document.getElementById('luntian-loader');
        if (show && !loader) {
            loader = document.createElement('div');
            loader.id = 'luntian-loader';
            loader.className = 'message assistant typing';
            loader.innerHTML = '<span>.</span><span>.</span><span>.</span>';
            chatBox.appendChild(loader);
        } else if (!show && loader) {
            loader.remove();
        }
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    async function sendMessage() {
        const text = userInput.value.trim();
        if (!text) return;
        window.speechSynthesis.cancel(); 
        if(stopSpeechBtn) stopSpeechBtn.style.display = 'none';
        userInput.value = '';
        userInput.style.height = 'auto';
        const activeChat = chats.find(c => c.id == currentChatId);
        activeChat.messages.push({ role: 'user', text: text });
        appendMessageDOM('user', text);
        showTyping(true);

        try {
            const res = await fetch('/api/aiResponse.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: "include",
                body: JSON.stringify({ prompt: text, conversation_id: currentChatId })
            });
            const data = await res.json();
            showTyping(false);
            if (data.reply) {
                if (!currentChatId || currentChatId == 'temp') {
                    activeChat.id = data.conversation_id;
                    currentChatId = data.conversation_id;
                }
                activeChat.messages.push({ role: 'assistant', text: data.reply });
                appendMessageDOM('assistant', data.reply);
                const cleanText = data.reply.replace(/<[^>]*>?/gm, '').replace(/[`#*]/g, '');
                speakText(cleanText);
                saveToStorage();
                renderHistory();
            }
        } catch (err) {
            showTyping(false);
            appendMessageDOM('assistant', '⚠️ Connection lost.');
        }
    }

    // ===== SPEECH TO TEXT (STT) - FILIPINO VERSION =====
    const Recognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (Recognition) {
        const recognition = new Recognition();
        recognition.continuous = false; 
        recognition.interimResults = false;
        
        // SETTING TO FILIPINO
        recognition.lang = 'fil-PH'; 

        recognition.onstart = () => {
            isListening = true;
            micBtn.classList.add('pulse');
        };

        recognition.onresult = (event) => {
            const transcript = event.results[0][0].transcript;
            const currentVal = userInput.value;
            userInput.value = currentVal + (currentVal ? ' ' : '') + transcript;
            userInput.dispatchEvent(new Event('input'));
            userInput.focus();
        };

        recognition.onerror = (event) => {
            if (event.error === 'not-allowed') {
                alert("Hindi pinayagan ang mic. I-check ang browser settings.");
            }
            micBtn.classList.remove('pulse');
            isListening = false;
        };

        recognition.onend = () => {
            isListening = false;
            micBtn.classList.remove('pulse');
        };

        micBtn.onclick = (e) => {
            e.preventDefault();
            if (window.speechSynthesis.speaking) {
                window.speechSynthesis.cancel();
                if(stopSpeechBtn) stopSpeechBtn.style.display = 'none';
                return;
            }
            if (!isListening) {
                recognition.start();
            } else {
                recognition.stop();
            }
        };
    }

    function createNewChat() {
        window.speechSynthesis.cancel();
        if(stopSpeechBtn) stopSpeechBtn.style.display = 'none';
        const newId = 'temp-' + Date.now();
        chats.unshift({ id: newId, title: `New Chat ${chats.length + 1}`, messages: [] });
        currentChatId = newId;
        loadChat(0);
    }

    function deleteChat(idx) {
        if (confirm("Delete this conversation?")) {
            window.speechSynthesis.cancel();
            chats.splice(idx, 1);
            if (chats.length === 0) createNewChat();
            else loadChat(0);
        }
    }

    sendBtn.onclick = (e) => { e.preventDefault(); sendMessage(); };
    newChatBtn.onclick = createNewChat;
    
    if (stopSpeechBtn) {
        stopSpeechBtn.onclick = () => {
            window.speechSynthesis.cancel();
            stopSpeechBtn.style.display = 'none';
        };
    }

    userInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    menuBtn?.addEventListener("click", () => {
        sidebar.classList.toggle("open");
    });

    userInput.addEventListener("input", () => {
        userInput.style.height = "auto";
        userInput.style.height = (userInput.scrollHeight) + "px";
    });

    function speakText(text) {
        if (!text) return;
        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel(); 
            const utter = new SpeechSynthesisUtterance(text);
            
            utter.lang = 'en-US'; 
            
            utter.onstart = () => { if(stopSpeechBtn) stopSpeechBtn.style.display = 'flex'; };
            utter.onend = () => { if(stopSpeechBtn) stopSpeechBtn.style.display = 'none'; };
            utter.onerror = () => { if(stopSpeechBtn) stopSpeechBtn.style.display = 'none'; };
            window.speechSynthesis.speak(utter);
        }
    }
});
