<!--?php require_once __DIR__ . '/_config.php'; ?--><!DOCTYPE html>

<html lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Luntian Assistant</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600&amp;family=Plus+Jakarta+Sans:wght@400;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          "colors": {
                  "secondary": "#5f5a7c",
                  "inverse-on-surface": "#f3eeff",
                  "outline": "#7b7486",
                  "primary-fixed": "#e9ddff",
                  "on-tertiary-container": "#fffbff",
                  "primary-container": "#8455ef",
                  "on-surface": "#181445",
                  "lavender-mist": "#E0E7FF",
                  "on-secondary-fixed": "#1b1735",
                  "tertiary-container": "#74747e",
                  "on-surface-variant": "#494454",
                  "primary-fixed-dim": "#d0bcff",
                  "surface-container-lowest": "#ffffff",
                  "on-secondary": "#ffffff",
                  "on-primary-fixed-variant": "#5516be",
                  "surface-bright": "#fcf8ff",
                  "dream-glow": "#FAF5FF",
                  "error-container": "#ffdad6",
                  "surface-tint": "#6d3bd7",
                  "on-tertiary": "#ffffff",
                  "secondary-fixed": "#e5deff",
                  "surface-container-high": "#e9e5ff",
                  "outline-variant": "#cbc3d7",
                  "tertiary": "#5b5b65",
                  "tertiary-fixed": "#e3e1ed",
                  "inverse-primary": "#d0bcff",
                  "on-secondary-container": "#605b7d",
                  "surface-container": "#efebff",
                  "on-tertiary-fixed": "#1a1b23",
                  "surface": "#fcf8ff",
                  "on-secondary-fixed-variant": "#474363",
                  "inverse-surface": "#2d2a5b",
                  "on-primary-container": "#fffbff",
                  "surface-variant": "#e3dfff",
                  "primary": "#6b38d4",
                  "muted-violet": "#7C3AED",
                  "background": "#fcf8ff",
                  "on-error": "#ffffff",
                  "surface-dim": "#dad6ff",
                  "on-error-container": "#93000a",
                  "secondary-fixed-dim": "#c8c2e9",
                  "on-primary-fixed": "#23005c",
                  "surface-container-low": "#f6f2ff",
                  "secondary-container": "#dcd5fd",
                  "on-primary": "#ffffff",
                  "tertiary-fixed-dim": "#c7c5d1",
                  "surface-container-highest": "#e3dfff",
                  "on-tertiary-fixed-variant": "#46464f",
                  "error": "#ba1a1a",
                  "on-background": "#181445"
          },
          "borderRadius": {
                  "DEFAULT": "0.25rem",
                  "lg": "0.5rem",
                  "xl": "0.75rem",
                  "full": "9999px"
          },
          "fontFamily": {
                  "body-lg": [
                          "Manrope"
                  ],
                  "label-md": [
                          "Manrope"
                  ],
                  "display-lg": [
                          "Plus Jakarta Sans"
                  ],
                  "headline-md": [
                          "Plus Jakarta Sans"
                  ],
                  "headline-sm": [
                          "Plus Jakarta Sans"
                  ],
                  "display-lg-mobile": [
                          "Plus Jakarta Sans"
                  ],
                  "body-md": [
                          "Manrope"
                  ]
          }
        },
      }
    }
  </script>
<style>
    .material-symbols-outlined {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
  </style>
<script src="marked.min.js"></script>
</head>
<body class="bg-background text-on-background font-body-md h-screen overflow-hidden flex selection:bg-primary-container selection:text-on-primary-container">
<div class="app-container flex w-full h-full relative">
<!-- Overlay for mobile drawer -->
<div class="fixed inset-0 bg-on-background/20 z-40 hidden md:hidden backdrop-blur-sm transition-opacity duration-300 opacity-0" id="drawer-overlay"></div>
<aside class="sidebar fixed inset-y-0 left-0 z-50 w-[280px] bg-surface-container-low border-r border-outline-variant/30 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col md:relative md:flex shadow-lg" id="drawer"><!--The sidebar-->
<div class="sidebar-header p-6 flex items-center justify-between border-b border-outline-variant/30"><!--The side bar header-->
<div class="brand flex items-center gap-3">
<svg class="flex-shrink-0" height="24" viewbox="0 0 24 24" width="24"><circle cx="12" cy="12" fill="yellowgreen" r="10"></circle><circle cx="12" cy="12" fill="sienna" r="5"></circle></svg>
<span class="font-headline-sm text-xl text-primary truncate">Luntian AI</span>
</div>
<button class="md:hidden p-2 rounded-xl hover:bg-surface-container-highest text-on-surface-variant" id="close-drawer">
<span class="material-symbols-outlined">close</span>
</button>
</div>
<div class="p-6 flex-shrink-0">
<button class="w-full flex items-center justify-center gap-2 bg-primary-container text-white py-3 px-6 rounded-xl shadow-md hover:shadow-lg transition-all active:scale-95 duration-200 font-label-md tracking-wide uppercase" id="new-chat-btn" title="New chat"><!--Add new conversation button-->
            Add new chat
        </button>
</div>
<ul class="history-list flex-1 overflow-y-auto px-2 py-4 space-y-2" id="chat-history"><!--Chat lists container--></ul>
<div class="sidebar-footer p-6 border-t border-outline-variant/30 flex flex-col text-xs font-body-md text-on-surface-variant/70 text-center gap-1 mt-auto bg-surface-container-low">
<i>© copyright Luntian AI 2025-2026.</i>
<i>credits to:<mark class="bg-primary-container/20 text-primary px-1 rounded">Percy Mic P. Nono</mark></i>
</div>
</aside>
<main class="chat-area flex-1 flex flex-col h-full relative bg-surface-bright">
<div class="flex items-center gap-3 p-4 md:p-6 pb-0 border-b border-outline-variant/30 md:border-none">
<button class="md:hidden p-2 -ml-2 rounded-xl hover:bg-surface-container transition-colors text-primary" id="menu">
<img alt="menu" class="w-6 h-6 object-contain" onerror="this.outerHTML='&lt;span class=\'material-symbols-outlined\'&gt;menu&lt;/span&gt;'" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDvH3quwSLmXRPnIFhfOQxpVF0aXKd_rnGk-93653zwh_NGpAPrCQN3XTF7cPxZMTx_gX3zp4GXmXEKgSwmiF-Ch790Tb0WBjNRdQhsboYqF_EwSvvesnLkBX1Emh90w8Iew1rvNw03TGOlueRpRbkPTlND7mGWRO5p76iJ4Nsr3OGijHSVxQPPH6sOe0VEYDZzqS501fgtOx04RCjvHZCH8U5oBAu5mWNRfW3PCSpwIT-oPp5f6Ihc" width="24px"/>
</button>
<header class="ai-header flex flex-col flex-1">
<h1 class="font-headline-sm text-xl md:text-2xl text-primary leading-tight">Luntian Assistant</h1>
<p class="text-xs font-body-md text-on-surface-variant hidden sm:block">Inteligent — precise, helpful, and expressive.</p>
</header>
</div>
<section class="chat-box flex-1 overflow-y-auto p-4 md:p-8 pb-32 scroll-smooth" id="chat-box"></section><!--Chat container-->
<div class="flex justify-center w-full absolute bottom-28 z-10 pointer-events-none">
<button class="stop-speech-btn bg-surface-variant text-on-surface-variant px-4 py-2 rounded-full font-label-md shadow-sm border border-outline-variant/30 pointer-events-auto hover:bg-surface-container-highest transition-colors" id="stop-speech" style="display: none;">
          Stop Speaking
        </button>
</div>
<div class="input-wrapper absolute bottom-0 left-0 right-0 p-4 md:p-6 bg-gradient-to-t from-surface-bright via-surface-bright/95 to-transparent pt-12">
<div class="max-w-[800px] mx-auto relative">
<form action="chat.php" class="bg-surface-container-lowest shadow-lg shadow-primary-container/10 border border-outline-variant/30 rounded-2xl p-2 flex items-end gap-2 focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 transition-all duration-300" method="POST">
<textarea active="" class="flex-1 max-h-32 bg-transparent border-none focus:ring-0 resize-none py-3.5 px-3 font-body-md text-base text-on-background placeholder:text-on-surface-variant/60 min-h-[52px]" id="user-input" placeholder="Ask anything..."></textarea><!-- Textarea -->
<div class="flex items-center gap-1 self-end mb-1 mr-1">
<button class="icon-button p-2.5 rounded-xl hover:bg-surface-container text-on-surface-variant transition-colors flex-shrink-0" id="mic-btn" type="button">
<img alt="open mic" class="w-6 h-6 object-contain" onerror="this.outerHTML='&lt;span class=\'material-symbols-outlined\'&gt;mic&lt;/span&gt;'" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDFo1101DP69XJpg3pJjeimwUYAJa3uAHck9rz5GyXAzNooWyx62XkcXAfN4hR-oEf_MGfZkbUeswcnlxoGjwgZtA6DG3Y9pZlKmE8FCCAsie1i4_5lRcQWt8xk9c8X4rOoaVwGD08-JiX-6Mlc6DnIFmH-qBjvax_JPXGxvi27rWeV6TM3R3HA5nUCRrLGKvoodA2K2Ul-UrCbcLmpkvrK3Lgg1GJT7fzWnRG0DhErOr8Cxc0LQf8X"/>
</button><!-- Mic -->
<button class="icon-button p-2.5 rounded-xl bg-primary-container text-white hover:shadow-md hover:scale-105 transition-all active:scale-95 flex-shrink-0" id="send-btn" type="submit">
<img alt="send" class="w-6 h-6 object-contain filter invert" onerror="this.outerHTML='&lt;span class=\'material-symbols-outlined\' data-weight=\'fill\'&gt;send&lt;/span&gt;'" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDsBmTgo1WPmFY1o2YXBbADC0bieCp0mxEn9mcPWuRfjAV1WN-M8yjwYw6hwoeio80cxeMfObPsLgBPGH_h0u5AXodT-WoJJ5AwfBHL4DDil2F9X-IO4nqufa3u2EZqU3vEDGyjrNmsLg-x6JMbgLnLDIza8Jok0_gKLePT0Cm9j-d2UUsigZMEYzIUaDjnIGJFiT59AdKiiGsJWSyh-V0Hq90m36TDsYldcHyfroVymA5whqyIYJ6_"/>
</button><!-- Send -->
</div>
</form>
</div>
</div>
</main>
</div>
<script>
    // Drawer toggle logic
    const drawer = document.getElementById('drawer');
    const overlay = document.getElementById('drawer-overlay');
    const openBtn = document.getElementById('menu');
    const closeBtn = document.getElementById('close-drawer');

    function toggleDrawer() {
        const isClosed = drawer.classList.contains('-translate-x-full');
        if (isClosed) {
            drawer.classList.remove('hidden');
            setTimeout(() => {
                drawer.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                setTimeout(() => overlay.classList.remove('opacity-0'), 10);
            }, 10);
        } else {
            drawer.classList.add('-translate-x-full');
            overlay.classList.add('opacity-0');
            setTimeout(() => {
                overlay.classList.add('hidden');
            }, 300);
        }
    }

    if(openBtn) openBtn.addEventListener('click', (e) => { e.preventDefault(); toggleDrawer(); });
    if(closeBtn) closeBtn.addEventListener('click', (e) => { e.preventDefault(); toggleDrawer(); });
    if(overlay) overlay.addEventListener('click', toggleDrawer);

    // Auto-resize textarea
    const tx = document.getElementById("user-input");
    if(tx) {
        tx.setAttribute("style", "height:" + (tx.scrollHeight) + "px;overflow-y:hidden; min-height: 52px;");
        tx.addEventListener("input", function() {
            this.style.height = "auto";
            this.style.height = (this.scrollHeight) + "px";
            if(this.scrollHeight > 128) {
                this.style.overflowY = 'auto';
            } else {
                 this.style.overflowY = 'hidden';
            }
        }, false);
    }
  </script>
<script src="app.js"></script>
</body></html>
