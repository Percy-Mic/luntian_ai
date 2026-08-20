<!DOCTYPE html>
<html class="light" lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>Luntian Assistant</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          "colors": {
            "on-secondary-container": "#605b7d",
            "on-secondary": "#ffffff",
            "background": "#fcf8ff",
            "on-surface-variant": "#494454",
            "outline": "#7b7486",
            "surface-container-highest": "#e3dfff",
            "on-primary": "#ffffff",
            "surface-container": "#efebff",
            "primary-container": "#8455ef",
            "surface-variant": "#e3dfff",
            "primary": "#6b38d4",
            "on-surface": "#181445",
            "surface-container-low": "#f6f2ff",
            "surface-container-lowest": "#ffffff",
            "on-background": "#181445",
            "surface-bright": "#fcf8ff",
            "inverse-surface": "#2d2a5b",
            "inverse-on-surface": "#f3eeff",
            "outline-variant": "#cbc3d7"
          },
          "borderRadius": {
            "DEFAULT": "0.25rem",
            "lg": "0.5rem",
            "xl": "0.75rem",
            "2xl": "1rem",
            "full": "9999px"
          },
          "fontFamily": {
            "body-md": ["Manrope"],
            "headline-sm": ["Plus Jakarta Sans"],
            "label-md": ["Manrope"]
          }
        }
      }
    }
  </script>
  <style>
    .material-symbols-outlined {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }

    /* Markdown Table Styling for Mobile & Desktop */
    .prose-table-wrapper {
      width: 100%;
      overflow-x: auto;
      margin-top: 0.75rem;
      margin-bottom: 0.75rem;
      border-radius: 0.5rem;
      border: 1px solid rgba(123, 116, 134, 0.2);
    }

    .message-ai table {
      width: 100%;
      border-collapse: collapse;
      text-align: left;
      font-size: 0.875rem;
      line-height: 1.25rem;
    }

    .message-ai th {
      background-color: rgba(107, 56, 212, 0.1);
      font-weight: 600;
      padding: 0.5rem 0.75rem;
      border-bottom: 1px solid rgba(123, 116, 134, 0.2);
    }

    .message-ai td {
      padding: 0.5rem 0.75rem;
      border-bottom: 1px solid rgba(123, 116, 134, 0.1);
      word-break: normal;
    }

    /* Code Block Formatting */
    .message-ai pre {
      background-color: #1e1b4b;
      color: #e0e7ff;
      padding: 0.75rem 1rem;
      border-radius: 0.5rem;
      overflow-x: auto;
      font-family: monospace;
      font-size: 0.85rem;
      margin-top: 0.5rem;
      margin-bottom: 0.5rem;
    }

    .message-ai code {
      background-color: rgba(107, 56, 212, 0.15);
      padding: 0.125rem 0.25rem;
      border-radius: 0.25rem;
      font-family: monospace;
      font-size: 0.875em;
    }

    .message-ai pre code {
      background-color: transparent;
      padding: 0;
    }
  </style>
  <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
</head>
<body class="bg-background text-on-background font-body-md h-screen overflow-hidden flex dark:bg-inverse-surface dark:text-inverse-on-surface">
  <div class="app-container flex w-full h-full relative">
    
    <!-- Mobile Drawer Overlay -->
    <div class="fixed inset-0 bg-on-background/30 z-40 hidden backdrop-blur-sm transition-opacity duration-300 opacity-0" id="drawer-overlay"></div>

    <!-- Sidebar Drawer -->
    <aside class="sidebar fixed inset-y-0 left-0 z-50 w-[280px] bg-surface-container-low dark:bg-inverse-surface border-r border-outline-variant/30 dark:border-outline/30 -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col font-headline-sm shadow-md md:shadow-none" id="drawer">
      <div class="sidebar-header flex items-center justify-between border-b border-outline-variant/30 dark:border-outline/30 px-6 py-4">
        <div class="brand flex items-center gap-3">
          <svg class="flex-shrink-0" height="24" viewBox="0 0 24 24" width="24">
            <circle cx="12" cy="12" fill="#6b38d4" r="10"></circle>
            <circle cx="12" cy="12" fill="#8455ef" r="5"></circle>
          </svg>
          <span class="font-headline-sm text-xl text-primary dark:text-primary-container truncate font-bold">Luntian AI</span>
        </div>
        <button class="md:hidden p-2 rounded-xl hover:bg-surface-container text-on-surface-variant dark:text-inverse-on-surface" id="close-drawer">
          <span class="material-symbols-outlined">close</span>
        </button>
      </div>

      <div class="p-4 flex-shrink-0">
        <button class="w-full flex items-center justify-center gap-2 bg-primary text-white py-3 px-4 rounded-xl shadow hover:bg-primary/90 transition-all active:scale-95 font-label-md" id="new-chat-btn">
          <span class="material-symbols-outlined text-[18px]">add</span>
          New Chat
        </button>
      </div>

      <ul class="history-list flex-1 overflow-y-auto px-3 py-2 space-y-1" id="chat-history">
        <!-- JS populates conversations here -->
      </ul>

      <div class="sidebar-footer p-4 border-t border-outline-variant/30 dark:border-outline/30 flex flex-col text-xs text-on-surface-variant/70 dark:text-inverse-on-surface/70 text-center gap-1 mt-auto">
        <p>© 2026 Luntian AI</p>
        <p class="text-[10px] uppercase tracking-widest font-semibold">Created by Percy Mic P. Nono</p>
      </div>
    </aside>

    <!-- Main Chat Container -->
    <main class="chat-area flex-1 flex flex-col h-full relative bg-surface-bright dark:bg-[#201d40]">
      
      <!-- Top Navigation Header -->
      <div class="flex items-center gap-3 p-4 border-b border-outline-variant/20 dark:border-outline/20 justify-between flex-shrink-0">
        <div class="flex items-center gap-3">
          <button class="md:hidden p-2 rounded-xl hover:bg-surface-container text-primary dark:text-primary-container" id="menu">
            <span class="material-symbols-outlined text-[20px]">menu</span>
          </button>
          <header class="ai-header flex flex-col">
            <h1 class="font-headline-sm text-lg md:text-xl font-semibold text-primary dark:text-primary-container leading-tight">Luntian Assistant</h1>
          </header>
        </div>
        <button class="p-2 rounded-xl hover:bg-surface-container text-primary dark:text-primary-container" id="theme-toggle" title="Toggle Theme">
          <span class="material-symbols-outlined dark:hidden text-[20px]">dark_mode</span>
          <span class="material-symbols-outlined hidden dark:block text-[20px]">light_mode</span>
        </button>
      </div>

      <!-- Messages Feed -->
      <section class="chat-box flex-1 overflow-y-auto p-4 md:p-6 pb-36 scroll-smooth flex flex-col gap-4 overflow-x-hidden" id="chat-box">
        <!-- Messages rendered via JS -->
      </section>

      <!-- Speech Control Floating Action Button -->
      <div class="flex justify-center w-full absolute bottom-28 z-10 pointer-events-none">
        <button class="stop-speech-btn bg-surface-variant text-on-surface-variant dark:bg-inverse-surface dark:text-inverse-on-surface px-4 py-2 rounded-full font-label-md shadow-md border border-outline-variant/30 pointer-events-auto hover:bg-surface-container-highest transition-colors" id="stop-speech" style="display: none;">
          Stop Speaking
        </button>
      </div>

      <!-- Input Bar Container -->
      <div class="input-wrapper absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-surface-bright dark:from-[#201d40] via-surface-bright/90 dark:via-[#201d40]/90 to-transparent pt-8">
        <div class="max-w-[800px] mx-auto relative">
          <form id="chat-form" class="bg-surface-container-lowest dark:bg-inverse-surface shadow-md border border-outline-variant/30 dark:border-outline/30 rounded-2xl p-2 flex items-end gap-2 focus-within:border-primary transition-all">
            <textarea class="flex-1 max-h-32 bg-transparent border-none focus:ring-0 resize-none py-2.5 px-3 font-body-md text-base text-on-background dark:text-inverse-on-surface placeholder:text-on-surface-variant/60 min-h-[48px]" id="user-input" placeholder="Ask anything..."></textarea>
            <div class="flex items-center gap-1 self-end mb-1 mr-1">
              <button class="p-2 rounded-xl hover:bg-surface-container text-on-surface-variant dark:text-inverse-on-surface transition-colors" id="mic-btn" type="button">
                <span class="material-symbols-outlined text-[20px]">mic</span>
              </button>
              <button class="p-2 rounded-xl bg-primary text-white hover:bg-primary/90 transition-all flex-shrink-0" id="send-btn" type="submit">
                <span class="material-symbols-outlined text-[20px]">send</span>
              </button>
            </div>
          </form>
        </div>
      </div>

    </main>
  </div>

  <script>
    // Theme Toggle Execution
    const themeToggleBtn = document.getElementById('theme-toggle');
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }

    if (themeToggleBtn) {
      themeToggleBtn.addEventListener('click', () => {
        const isDark = document.documentElement.classList.toggle('dark');
        localStorage.theme = isDark ? 'dark' : 'light';
      });
    }

    // Drawer Toggle Execution
    const drawer = document.getElementById('drawer');
    const overlay = document.getElementById('drawer-overlay');
    const openBtn = document.getElementById('menu');
    const closeBtn = document.getElementById('close-drawer');

    function openDrawer() {
      overlay.classList.remove('hidden');
      setTimeout(() => {
        drawer.classList.remove('-translate-x-full');
        overlay.classList.remove('opacity-0');
      }, 10);
    }

    function closeDrawer() {
      drawer.classList.add('-translate-x-full');
      overlay.classList.add('opacity-0');
      setTimeout(() => overlay.classList.add('hidden'), 300);
    }

    if (openBtn) openBtn.addEventListener('click', openDrawer);
    if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
    if (overlay) overlay.addEventListener('click', closeDrawer);

    // Auto-Resizing Textarea
    const tx = document.getElementById("user-input");
    if (tx) {
      tx.addEventListener("input", function() {
        this.style.height = "auto";
        this.style.height = (this.scrollHeight) + "px";
        this.style.overflowY = this.scrollHeight > 128 ? 'auto' : 'hidden';
      });
    }
  </script>
  <script src="app.js"></script>
</body>
</html>
