<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AISAP AI - Learn AI Simply</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        mark {
            background-color: #fef08a;
            padding: 2px 0;
        }
        h1 {
            transition: 0.2s;
        }
        h1:hover {
            text-shadow: 0 0 5px darkgrey;
            transition: 0.2s;
        }
    </style>
</head>
<body>
    <div x-data="chatApp()" x-cloak class="flex flex-col h-screen bg-white">
        <!-- Header -->
        <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <h1 @click="window.location.reload()" class="text-xl font-semibold text-gray-900 cursor-pointer">AISAP</h1>
                <span class="text-sm text-gray-500 ml-2">AI as Simple as Possible</span>
            </div>
            <div class="flex items-center gap-2">
                @auth
                    <span class="text-sm text-gray-700">Welcome, {{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                            Sign Out
                        </button>
                    </form>
                @else
                    <!-- Sign In -->
                    <a href="{{ route('login') }}" class="px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        Sign In
                    </a>

                    <!-- Sign Up -->
                    <a href="{{ route('register') }}" class="px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        Sign Up
                    </a>
                @endauth
            </div>
        </div>

        <!-- Chat Area -->
        <div class="flex-1 overflow-y-auto px-6 py-8" x-ref="chatContainer">
            <div class="max-w-3xl mx-auto">
                <!-- Welcome Screen -->
                <div x-show="messages.length === 0" class="text-center mb-12">
                    <h2 class="text-3xl font-semibold text-gray-900 mb-3">
                        Learn AI in Simple Terms
                    </h2>
                    <p class="text-gray-600 mb-8">
                        Ask me anything about AI, and I'll explain it using everyday analogies.
                    </p>
                    
                    <!-- Suggested Terms -->
                    <div class="flex flex-wrap gap-3 justify-center">
                        <template x-for="term in suggestedTerms" :key="term">
                            <button
                                @click="sendSuggestedTerm(term)"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors text-sm"
                                x-text="term"
                            ></button>
                        </template>
                    </div>
                </div>

                <!-- Messages -->
                <template x-for="message in messages" :key="message.id">
                    <div class="mb-6" :class="message.type === 'user' ? 'flex justify-end' : ''">
                        <!-- User Message -->
                        <template x-if="message.type === 'user'">
                            <div class="bg-gray-100 rounded-2xl px-5 py-3 max-w-2xl">
                                <p class="text-gray-900" x-text="message.content"></p>
                            </div>
                        </template>

                        <!-- AI Message -->
                        <template x-if="message.type === 'ai'">
                            <div class="max-w-full">
                                <!-- Main Content -->
                                <div 
                                    class="text-gray-800 leading-relaxed mb-3"
                                    @mouseup="handleTextSelection(message.id, $event)"
                                    x-html="renderMessageContent(message)"
                                ></div>

                                <!-- Action Buttons -->
                                <div class="flex gap-2 flex-wrap">
                                    <!-- One-line Summary Button -->
                                    <template x-if="message.oneLine">
                                        <div class="mb-3">
                                            <!-- One-line summary (toggle visibility) -->
                                            <div x-show="message.showOneLine" class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded mt-2" x-transition>
                                                <p class="text-sm text-gray-700">
                                                    <strong>Summary:</strong> <span x-text="message.oneLine"></span>
                                                </p>
                                            </div>
                                        </div>
                                    </template>
                                    
                                    <!-- Copy Button -->
                                    <button
                                        @click="copyToClipboard(message.content)"
                                        class="flex items-center gap-1 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                                    >
                                        <div class="inline-flex items-center cursor-pointer text-gray-600 hover:text-gray-900 gap-1 rounded transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                            <span>Copy</span>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- Loading Indicator -->
                <template x-if="isLoading">
                    <div class="flex items-center gap-2 text-gray-500">
                        <div class="animate-pulse">Thinking...</div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Highlight Button Popup -->
        <template x-if="selectedText">
            <button
                @click="highlightText()"
                class="fixed bg-gray-900 text-white px-3 py-2 rounded-lg text-sm shadow-lg hover:bg-gray-800 transition-colors z-50"
                :style="`left: ${selectedText.position.x}px; top: ${selectedText.position.y}px; transform: translateX(-50%);`"
            >
                Highlight
            </button>
        </template>

        <!-- Input Area -->
        <div class="border-t border-gray-200 px-6 py-4 bg-white">
            <div class="max-w-3xl mx-auto">
                @auth
                    <div class="flex items-center gap-3 mb-2">
                        <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                            <input
                                type="checkbox"
                                x-model="isOneLineMode"
                                class="w-4 h-4 rounded border-gray-300"
                            />
                            Summarize
                        </label>
                    </div>
                    
                    <div class="flex gap-3 items-start">
                        <div class="flex-1 relative">
                            <textarea
                                x-model="input"
                                @keydown.enter.prevent="if (!$event.shiftKey) sendMessage()"
                                placeholder="Ask me about any AI concept..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl resize-none focus:outline-none focus:border-gray-400 text-gray-900"
                                rows="1"
                                style="min-height: 52px; max-height: 200px;"
                            ></textarea>
                        </div>
                        
                        <button
                            @click="sendMessage()"
                            :disabled="!input.trim()"
                            class="p-4 bg-gray-900 text-white rounded-xl hover:bg-gray-800 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </button>
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-gray-600 mb-4">Please sign in to start chatting</p>
                        <a href="{{ route('login') }}" class="px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors">
                            Sign In
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <!-- Alpine.js Chat Logic -->
    <script>
        function chatApp() {
            return {
                messages: [],
                input: '',
                isOneLineMode: false,
                isLoading: false,
                highlights: {},
                selectedText: null,
                suggestedTerms: [
                    'Neural Networks',
                    'Machine Learning',
                    'Deep Learning',
                    'Natural Language Processing'
                ],

                sendMessage() {
                    if (!this.input.trim()) return;

                    const userMessage = {
                        id: Date.now(),
                        type: 'user',
                        content: this.input
                    };
                    this.messages.push(userMessage);

                    const userInput = this.input;
                    this.input = '';
                    this.isLoading = true;

                    this.$nextTick(() => {
                        this.$refs.chatContainer.scrollTop = this.$refs.chatContainer.scrollHeight;
                    });

                    fetch('/chat/send', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            message: userInput,
                            oneLineMode: this.isOneLineMode
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.full_reply) {
                            const aiMessage = {
                                id: Date.now() + 1,
                                type: 'ai',
                                content: data.full_reply,
                                showOneLine: this.isOneLineMode,
                                oneLine: data.one_line_reply || null
                            };
                            this.messages.push(aiMessage);
                        } else {
                            alert('Error: AI did not return a valid response.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to contact server. Make sure you are logged in.');
                    })
                    .finally(() => {
                        this.isLoading = false;
                        this.$nextTick(() => {
                            this.$refs.chatContainer.scrollTop = this.$refs.chatContainer.scrollHeight;
                        });
                    });
                },

                sendSuggestedTerm(term) {
                    this.input = term;
                    this.sendMessage();
                },

                copyToClipboard(content) {
                    navigator.clipboard.writeText(content).then(() => {
                        alert('Copied to clipboard!');
                    });
                },

                handleTextSelection(messageId, event) {
                    setTimeout(() => {
                        const selection = window.getSelection();
                        const text = selection.toString();
                        
                        if (text.length > 0) {
                            const range = selection.getRangeAt(0);
                            const rect = range.getBoundingClientRect();
                            
                            this.selectedText = {
                                messageId: messageId,
                                text: text,
                                position: { 
                                    x: rect.left + rect.width / 2, 
                                    y: rect.top - 40 + window.scrollY 
                                }
                            };
                        } else {
                            this.selectedText = null;
                        }
                    }, 100);
                },

                highlightText() {
                    if (this.selectedText) {
                        const highlightId = `${this.selectedText.messageId}-${Date.now()}`;
                        this.highlights[highlightId] = {
                            messageId: this.selectedText.messageId,
                            text: this.selectedText.text
                        };
                        this.selectedText = null;
                        window.getSelection().removeAllRanges();
                    }
                },

                renderMessageContent(message) {
                    let content = message.content;
                    Object.entries(this.highlights).forEach(([id, highlight]) => {
                        if (highlight.messageId === message.id) {
                            content = content.replace(
                                highlight.text,
                                `<mark>${highlight.text}</mark>`
                            );
                        }
                    });
                    return content.replace(/\n/g, '<br/>');
                }
            }
        }
    </script>
</body>
</html>