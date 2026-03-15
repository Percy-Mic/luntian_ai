<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Luntian Assistant</title>
  <link rel="stylesheet" href="style.css" />
  <script src="marked.min.js"></script>
</head>

<body>
  <div class="app-container">
    <aside class="sidebar"><!--The sidebar-->
      <div class="sidebar-header"><!--The side bar header-->
        <div class="brand">
          <svg width="24" height="24" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="#e9530eff"/><circle cx="12" cy="12" r="5" fill="#fff"/></svg>
          <span>Luntian AI</span>
        </div>
      </div>
      <button id="new-chat-btn" title="New chat"><!--Add new conversation button-->
          Add new chat
      </button>

      <ul id="chat-history" class="history-list"><!--Chat lists container--></ul>
      <div class="sidebar-footer">
        <i>&copy; copyright Luntian AI 2025-2026.</i>
        <i>credits to:<mark><br />Percy Mic P. Nono</mark></i>
      </div>
    </aside>

    <button id="menu">
      <img src="public/assets/icons/menu-alt-02-svgrepo-com.svg" alt="menu" width="40px">
    </button>

    <main class="chat-area">
      <header class="ai-header">
        <h1>Luntian Assistant</h1>
        <p>Inteligent — precise, helpful, and expressive.</p>
      </header>

      <section id="chat-box" class="chat-box"></section><!--Chat container-->

      <button id="stop-speech" class="stop-speech-btn" style="display: none;">
        Stop Speaking
      </button>

      <div class="input-wrapper">
        
        <form action="chat.php" method="POST">

          <textarea id="user-input" placeholder="Ask anything..."></textarea><!-- Textarea -->
        
          <button class="icon-button" id="mic-btn">
            <img src="public/assets/icons/mic-svgrepo-com.svg" alt="open mic"/>
          </button><!-- Mic -->
        
          <button class="icon-button" id="send-btn">
            <img src="public/assets/icons/send-svgrepo-com.svg" alt="send">
          </button><!-- Send -->
        </form>
      </div>
    </main>
    
  </div>

  <script src="app.js"></script>
</body>
</html>
