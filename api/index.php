<!DOCTYPE html>

<html class="light" lang="en"><head>
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
                "inverse-on-surface": "#f3eeff",
                "surface-container": "#efebff",
                "on-secondary-fixed-variant": "#474363",
                "surface-container-highest": "#e3dfff",
                "error": "#ba1a1a",
                "on-error": "#ffffff",
                "inverse-surface": "#2d2a5b",
                "surface": "#fcf8ff",
                "outline": "#7b7486",
                "surface-bright": "#fcf8ff",
                "on-secondary": "#ffffff",
                "outline-variant": "#cbc3d7",
                "surface-tint": "#6d3bd7",
                "on-primary": "#ffffff",
                "on-tertiary-fixed": "#1a1b23",
                "secondary-fixed-dim": "#c8c2e9",
                "primary-fixed-dim": "#d0bcff",
                "inverse-primary": "#d0bcff",
                "secondary": "#5f5a7c",
                "on-error-container": "#93000a",
                "error-container": "#ffdad6",
                "dream-glow": "#FAF5FF",
                "on-secondary-fixed": "#1b1735",
                "background": "#fcf8ff",
                "on-primary-fixed-variant": "#5516be",
                "on-tertiary-fixed-variant": "#46464f",
                "primary-container": "#8455ef",
                "on-tertiary": "#ffffff",
                "tertiary-fixed": "#e3e1ed",
                "surface-container-lowest": "#ffffff",
                "on-tertiary-container": "#fffbff",
                "lavender-mist": "#E0E7FF",
                "on-background": "#181445",
                "on-surface": "#181445",
                "secondary-fixed": "#e5deff",
                "surface-container-low": "#f6f2ff",
                "tertiary-container": "#74747e",
                "surface-dim": "#dad6ff",
                "tertiary-fixed-dim": "#c7c5d1",
                "muted-violet": "#7C3AED",
                "on-secondary-container": "#605b7d",
                "surface-variant": "#e3dfff",
                "on-surface-variant": "#494454",
                "secondary-container": "#dcd5fd",
                "on-primary-container": "#fffbff",
                "surface-container-high": "#e9e5ff",
                "primary-fixed": "#e9ddff",
                "tertiary": "#5b5b65",
                "primary": "#6b38d4",
                "on-primary-fixed": "#23005c"
        },
        "borderRadius": {
                "DEFAULT": "0.25rem",
                "lg": "0.5rem",
                "xl": "0.75rem",
                "full": "9999px"
        },
        "spacing": {
                "base": "8px",
                "margin-mobile": "16px",
                "container-max": "1200px",
                "margin-desktop": "48px",
                "gutter": "24px"
        },
        "fontFamily": {
                "headline-sm": [
                        "Plus Jakarta Sans"
                ],
                "display-lg": [
                        "Plus Jakarta Sans"
                ],
                "display-lg-mobile": [
                        "Plus Jakarta Sans"
                ],
                "label-md": [
                        "Manrope"
                ],
                "headline-md": [
                        "Plus Jakarta Sans"
                ],
                "body-md": [
                        "Manrope"
                ],
                "body-lg": [
                        "Manrope"
                ]
        }
},
    },
  }
</script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .material-symbols-outlined[data-weight="fill"] {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
<style>
    body {
      min-height: max(884px, 100dvh);
    }
  </style>
</head>
<body class="bg-background dark:bg-inverse-surface text-on-background dark:text-inverse-on-surface font-body-md text-base h-screen overflow-hidden flex selection:bg-primary-container selection:text-on-primary-container">
<!-- NavigationDrawer -->
<nav class="fixed inset-y-0 left-0 z-50 w-[280px] bg-surface-container-low dark:bg-inverse-surface border-r border-outline-variant/30 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col hidden md:flex shadow-lg" id="drawer">
<div class="p-6 flex items-center justify-between border-b border-outline-variant/30">
<div class="flex items-center gap-3">
<!-- Brand Logo -->
<div class="w-8 h-8 rounded-xl bg-gradient-to-br from-primary to-muted-violet flex items-center justify-center relative overflow-hidden shadow-md">
<span class="material-symbols-outlined text-white text-[18px]">auto_awesome</span>
</div>
<span class="font-headline-sm text-xl text-primary dark:text-primary-fixed-dim truncate">Luntian AI</span>
</div>
<button class="md:hidden p-2 rounded-xl border border-transparent hover:bg-surface-container-highest dark:hover:bg-surface-variant transition-all text-on-surface-variant dark:text-inverse-on-surface" id="close-drawer">
<span class="material-symbols-outlined">close</span>
</button>
</div>
<div class="p-6 flex-shrink-0">
<button class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-primary to-muted-violet dark:from-primary-container dark:to-primary text-white py-3 px-6 rounded-xl shadow-md hover:shadow-lg transition-all active:scale-95 duration-200">
<span class="material-symbols-outlined" data-weight="fill">add</span>
<span class="font-label-md tracking-wide uppercase">Add new chat</span>
</button>
</div>
<div class="flex-1 overflow-y-auto px-2 py-4 space-y-2">
<div class="px-6 py-1 text-xs font-label-md text-on-surface-variant dark:text-outline uppercase tracking-wider mb-2">Recent Threads</div>
<!-- Tabs -->
<a class="flex items-center gap-3 px-6 py-3 bg-surface-container-highest dark:bg-surface-variant/50 text-primary dark:text-primary-fixed-dim rounded-xl mx-2 transition-all font-label-md group shadow-sm" href="#">
<span class="material-symbols-outlined text-[20px]">add</span>
<span class="truncate flex-1">New Chat</span>
</a>
<a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant dark:text-inverse-on-surface hover:bg-surface-container-highest dark:hover:bg-surface-variant/30 rounded-xl mx-2 transition-all font-label-md group" href="#">
<span class="material-symbols-outlined text-[20px]">history</span>
<span class="truncate flex-1">Recent Threads</span>
</a>
<a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant dark:text-inverse-on-surface hover:bg-surface-container-highest dark:hover:bg-surface-variant/30 rounded-xl mx-2 transition-all font-label-md group" href="#">
<span class="material-symbols-outlined text-[20px]">bookmark</span>
<span class="truncate flex-1">Saved Prompts</span>
</a>
<a class="flex items-center gap-3 px-6 py-3 text-on-surface-variant dark:text-inverse-on-surface hover:bg-surface-container-highest dark:hover:bg-surface-variant/30 rounded-xl mx-2 transition-all font-label-md group" href="#">
<span class="material-symbols-outlined text-[20px]">archive</span>
<span class="truncate flex-1">Archive</span>
</a>
</div>
<div class="p-6 border-t border-outline-variant/30 flex items-center gap-3 mt-auto bg-surface-container-low dark:bg-inverse-surface">
<img alt="User profile" class="w-10 h-10 rounded-full border border-outline-variant/30 object-cover shadow-sm" data-alt="A small circular avatar portrait of a professional user in a minimalist botanical setting, soft warm lighting, earthy tones." src="https://lh3.googleusercontent.com/aida-public/AB6AXuA6Gp3JSzxDQZ3_Ra8rFLPiRUr6PzzODqJJ64q6IuoJC9N-a677LDR_Yvj8peSLuBPosM8HFFpaxvlR7o3WH6DZN6yXSghVZ7I51uA_12njjzRYBXpkfUbcyU6jOoKK0-WaXUi70hQgwpXYGmjJ0B8usPOmrSjN-ckN1cqBqJegNqRClRb7uRt5OU0ag_6lFo4rvhqTwQkFMonls11sgz9_FlBrXQ_cYYW-fc6rQ8GH9PdFsI2cz_4D"/>
<div class="flex flex-col overflow-hidden">
<span class="font-label-md text-on-background dark:text-inverse-on-surface truncate">Luntian User</span>
<span class="text-xs font-body-md text-on-surface-variant dark:text-outline truncate">Pro Member • Botanical Precision</span>
</div>
</div>
<div class="px-6 pb-6 text-xs font-body-md text-on-surface-variant/70 dark:text-outline/70 text-center">
            © 2024 Luntian. Credits to Percy Mic P. Nono
        </div>
</nav>
<!-- Overlay for mobile drawer -->
<div class="fixed inset-0 bg-on-background/20 dark:bg-black/50 z-40 hidden md:hidden backdrop-blur-sm transition-opacity duration-300 opacity-0" id="drawer-overlay"></div>
<!-- Main Content Area -->
<main class="flex-1 flex flex-col h-full relative md:ml-[280px] bg-surface-bright dark:bg-inverse-surface">
<!-- TopAppBar -->
<header class="fixed top-0 left-0 md:left-[280px] right-0 z-30 h-16 bg-surface-bright/80 dark:bg-inverse-surface/80 backdrop-blur-md border-b border-outline-variant/30 flex items-center justify-between px-4 md:px-6 transition-all">
<div class="flex items-center gap-3">
<button class="md:hidden p-2 -ml-2 rounded-xl hover:bg-surface-container dark:hover:bg-surface-variant/30 transition-colors text-primary dark:text-primary-fixed-dim" id="open-drawer">
<span class="material-symbols-outlined">menu</span>
</button>
<div class="flex flex-col">
<h1 class="font-headline-sm text-xl md:text-2xl text-primary dark:text-primary-fixed-dim leading-tight">Luntian Assistant</h1>
<span class="text-xs font-body-md text-on-surface-variant dark:text-outline hidden sm:block">Intelligent — precise, helpful, and expressive.</span>
</div>
</div>
<div class="flex items-center gap-2">
<button class="p-2 rounded-xl hover:bg-surface-container dark:hover:bg-surface-variant/30 transition-all text-on-surface-variant dark:text-inverse-on-surface active:scale-95" id="theme-toggle">
<span class="material-symbols-outlined dark:hidden">dark_mode</span>
<span class="material-symbols-outlined hidden dark:block">light_mode</span>
</button>
<button class="p-2 -mr-2 rounded-xl hover:bg-surface-container dark:hover:bg-surface-variant/30 transition-all text-on-surface-variant dark:text-inverse-on-surface active:scale-95">
<span class="material-symbols-outlined">settings</span>
</button>
</div>
</header>
<!-- Chat Canvas -->
<div class="flex-1 overflow-y-auto pt-20 pb-32 px-4 md:px-8 scroll-smooth relative" id="chat-container">
<div class="max-w-[800px] mx-auto space-y-8 flex flex-col pb-8">
<!-- Initial State / Empty State -->
<div class="flex flex-col items-center justify-center text-center py-16 mt-8 mb-4">
<div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary-container to-muted-violet flex items-center justify-center mb-6 shadow-lg shadow-primary-container/20">
<span class="material-symbols-outlined text-white text-3xl" data-weight="fill">auto_awesome</span>
</div>
<h2 class="font-display-lg-mobile md:font-display-lg mb-4 text-on-background dark:text-inverse-on-surface">How can I help you grow today?</h2>
<p class="text-on-surface-variant dark:text-outline max-w-md font-body-md">I'm your botanical-precision assistant. Ask me anything about plant care, sustainable practices, or environmental data.</p>
<div class="flex flex-wrap justify-center gap-3 mt-8">
<button class="px-5 py-2.5 bg-surface-container text-on-surface dark:bg-surface-variant dark:text-inverse-on-surface hover:bg-primary-container hover:text-white transition-all rounded-xl font-label-md shadow-sm active:scale-95">Identify this leaf</button>
<button class="px-5 py-2.5 bg-surface-container text-on-surface dark:bg-surface-variant dark:text-inverse-on-surface hover:bg-primary-container hover:text-white transition-all rounded-xl font-label-md shadow-sm active:scale-95">Indoor watering schedule</button>
<button class="px-5 py-2.5 bg-surface-container text-on-surface dark:bg-surface-variant dark:text-inverse-on-surface hover:bg-primary-container hover:text-white transition-all rounded-xl font-label-md shadow-sm active:scale-95">Soil pH for Monstera</button>
</div>
</div>
<!-- Example Message: User -->
<div class="flex items-end justify-end gap-3 w-full">
<div class="max-w-[85%] md:max-w-[75%] bg-primary-container text-white dark:bg-primary dark:text-on-primary rounded-2xl rounded-br-sm px-6 py-4 shadow-md shadow-primary-container/20 font-body-md relative group">
<p class="">Can you give me a quick summary of how to care for a Fiddle Leaf Fig? It's looking a bit droopy.</p>
<div class="absolute -bottom-8 right-0 opacity-0 group-hover:opacity-100 transition-opacity flex gap-2">
<button class="text-xs text-on-surface-variant dark:text-outline hover:text-primary bg-surface-container rounded-xl p-1.5 shadow-sm"><span class="material-symbols-outlined text-[16px]">edit</span></button>
</div>
</div>
</div>
<!-- Example Message: Assistant -->
<div class="flex items-start justify-start gap-3 w-full">
<div class="w-8 h-8 rounded-xl bg-gradient-to-br from-primary-container to-muted-violet flex-shrink-0 flex items-center justify-center shadow-md mt-1">
<span class="material-symbols-outlined text-white text-[18px]">auto_awesome</span>
</div>
<div class="max-w-[85%] md:max-w-[75%] bg-surface-container dark:bg-surface-variant/40 text-on-background dark:text-inverse-on-surface rounded-2xl rounded-bl-sm px-6 py-5 shadow-sm font-body-md">
<p class="mb-4">A drooping Fiddle Leaf Fig (Ficus lyrata) is usually a sign of a watering issue or environmental stress. Here is a precise care summary to help it recover:</p>
<ul class="space-y-3 list-none mb-5">
<li class="flex items-start gap-3">
<span class="material-symbols-outlined text-primary dark:text-primary-fixed-dim text-[20px] shrink-0 mt-0.5">water_drop</span>
<span><strong>Watering:</strong> They prefer to dry out slightly between waterings. Check the top 2-3 inches of soil; if dry, water thoroughly until it drains out the bottom. Drooping can mean either under-watering (crispy edges) or over-watering (yellowing leaves).</span>
</li>
<li class="flex items-start gap-3">
<span class="material-symbols-outlined text-primary dark:text-primary-fixed-dim text-[20px] shrink-0 mt-0.5">light_mode</span>
<span><strong>Light:</strong> Bright, indirect light is crucial. A spot near an east-facing window is ideal. Too little light will cause stunted growth and dropping leaves.</span>
</li>
<li class="flex items-start gap-3">
<span class="material-symbols-outlined text-primary dark:text-primary-fixed-dim text-[20px] shrink-0 mt-0.5">thermostat</span>
<span><strong>Environment:</strong> Keep it away from drafts (AC vents or heaters). They thrive in consistent temperatures (60-85°F).</span>
</li>
</ul>
<div class="bg-surface-container-highest dark:bg-black/40 rounded-xl p-4 text-on-surface dark:text-inverse-on-surface font-mono text-sm mb-4 relative group shadow-inner">
<div class="absolute top-2 right-2 flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
<span class="text-xs text-on-surface-variant/70 font-label-md uppercase">JSON</span>
<button class="p-1.5 rounded-lg hover:bg-surface-variant/50"><span class="material-symbols-outlined text-[14px]">content_copy</span></button>
</div>
<pre><code>{
  "plant": "Ficus lyrata",
  "status": "drooping",
  "action_items": ["check_soil_moisture", "assess_light", "verify_temp"]
}</code></pre>
</div>
<p class="text-sm font-medium">I'd recommend checking the soil moisture first. Is the soil currently bone dry or soggy?</p>
<div class="flex gap-2 mt-5 pt-4 border-t border-outline-variant/30">
<button class="p-2 rounded-xl hover:bg-primary-container/20 hover:text-primary text-on-surface-variant dark:text-outline transition-colors"><span class="material-symbols-outlined text-[18px]">thumb_up</span></button>
<button class="p-2 rounded-xl hover:bg-error-container/50 hover:text-error text-on-surface-variant dark:text-outline transition-colors"><span class="material-symbols-outlined text-[18px]">thumb_down</span></button>
<button class="p-2 rounded-xl hover:bg-primary-container/20 hover:text-primary text-on-surface-variant dark:text-outline transition-colors"><span class="material-symbols-outlined text-[18px]">refresh</span></button>
</div>
</div>
</div>
</div>
</div>
<!-- Sticky Input Area -->
<div class="absolute bottom-0 left-0 right-0 p-4 md:p-6 bg-gradient-to-t from-surface-bright dark:from-inverse-surface via-surface-bright/95 dark:via-inverse-surface/95 to-transparent pt-12">
<div class="max-w-[800px] mx-auto relative">
<div class="bg-surface-container-lowest dark:bg-surface-variant shadow-lg shadow-primary-container/10 border border-outline-variant/30 rounded-2xl p-2 flex items-end gap-2 focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 transition-all duration-300">
<button class="p-3 rounded-xl hover:bg-surface-container dark:hover:bg-surface-container-highest text-on-surface-variant dark:text-outline transition-colors flex-shrink-0 self-end">
<span class="material-symbols-outlined">attach_file</span>
</button>
<textarea class="flex-1 max-h-32 bg-transparent border-none focus:ring-0 resize-none py-3.5 px-2 font-body-md text-base text-on-background dark:text-inverse-on-surface placeholder:text-on-surface-variant/60 dark:placeholder:text-outline/60" placeholder="Ask anything..." rows="1" style="min-height: 52px;"></textarea>
<div class="flex items-center gap-1 self-end mb-1 mr-1">
<button class="p-2.5 rounded-xl hover:bg-surface-container dark:hover:bg-surface-container-highest text-on-surface-variant dark:text-outline transition-colors flex-shrink-0">
<span class="material-symbols-outlined">mic</span>
</button>
<button class="p-2.5 rounded-xl bg-gradient-to-r from-primary to-muted-violet text-white hover:shadow-md hover:scale-105 transition-all active:scale-95 flex-shrink-0">
<span class="material-symbols-outlined" data-weight="fill">send</span>
</button>
</div>
</div>
<div class="text-center mt-4 text-xs font-label-md uppercase tracking-widest text-on-surface-variant/60 dark:text-outline/60">
                    Luntian Assistant can make mistakes. Verify important botanical info.
                </div>
</div>
</div>
</main>
<script>
        // Drawer toggle logic
        const drawer = document.getElementById('drawer');
        const overlay = document.getElementById('drawer-overlay');
        const openBtn = document.getElementById('open-drawer');
        const closeBtn = document.getElementById('close-drawer');

        function toggleDrawer() {
            const isClosed = drawer.classList.contains('-translate-x-full');
            if (isClosed) {
                drawer.classList.remove('hidden');
                // Small delay to allow display:block to apply before transition
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
                    drawer.classList.add('hidden');
                }, 300);
            }
        }

        openBtn.addEventListener('click', toggleDrawer);
        closeBtn.addEventListener('click', toggleDrawer);
        overlay.addEventListener('click', toggleDrawer);

        // Auto-resize textarea
        const tx = document.getElementsByTagName("textarea");
        for (let i = 0; i < tx.length; i++) {
            tx[i].setAttribute("style", "height:" + (tx[i].scrollHeight) + "px;overflow-y:hidden; min-height: 52px;");
            tx[i].addEventListener("input", OnInput, false);
        }

        function OnInput() {
            this.style.height = "auto";
            this.style.height = (this.scrollHeight) + "px";
            if(this.scrollHeight > 128) {
                this.style.overflowY = 'auto';
            } else {
                 this.style.overflowY = 'hidden';
            }
        }

        // Theme Toggle Logic
        const themeToggle = document.getElementById('theme-toggle');
        const htmlElement = document.documentElement;

        themeToggle.addEventListener('click', () => {
            if (htmlElement.classList.contains('dark')) {
                htmlElement.classList.remove('dark');
                htmlElement.classList.add('light');
                localStorage.setItem('theme', 'light');
            } else {
                htmlElement.classList.remove('light');
                htmlElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        });

        // Initialize theme based on preference or system settings
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
          document.documentElement.classList.add('dark');
          document.documentElement.classList.remove('light');
        } else {
          document.documentElement.classList.remove('dark');
          document.documentElement.classList.add('light');
        }
    </script>
</body></html>
