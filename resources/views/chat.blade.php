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
    </style>
</head>
<body>
    <div x-data="chatApp()" x-cloak class="flex flex-col h-screen bg-white">
        <!-- Header -->
        <div class="border-b border-gray-200 px-6 py-4">
            <div class="flex items-center gap-2">
                <!-- add icon here 
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                </svg> -->
                <h1 class="text-xl font-semibold text-gray-900">AISAP</h1>
                <span class="text-sm text-gray-500 ml-2">AI as Soon as Possible</span>
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
                        Ask me anything about AI, and I'll explain it using everyday analogies
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
                                
                                <!-- One-line Summary -->
                                <template x-if="message.oneLine">
                                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-3 rounded">
                                        <p class="text-sm text-gray-700">
                                            <strong>One-line summary:</strong> <span x-text="message.oneLine"></span>
                                        </p>
                                    </div>
                                </template>
                                
                                <!-- Action Buttons -->
                                <div class="flex gap-2 flex-wrap">
                                    <!-- Different Explanation Button -->
                                    <template x-if="message.showAlternative">
                                        <button
                                            @click="getAlternativeExplanation(message.id)"
                                            class="flex items-center gap-1 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                            Different explanation?
                                        </button>
                                    </template>
                                    
                                    <!-- One-line Summary Button -->
                                    <template x-if="message.showOneLine">
                                        <button
                                            @click="generateOneLine(message.id)"
                                            class="px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                                        >
                                            One-line summary
                                        </button>
                                    </template>
                                    
                                    <!-- Copy Button -->
                                    <button
                                        @click="copyToClipboard(message.content)"
                                        class="flex items-center gap-1 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                        Copy
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
                <div class="flex items-center gap-3 mb-2">
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input
                            type="checkbox"
                            x-model="isOneLineMode"
                            class="w-4 h-4 rounded border-gray-300"
                        />
                        One-line only
                    </label>
                </div>
                
                <div class="flex gap-3 items-end">
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
                        class="p-3 bg-gray-900 text-white rounded-xl hover:bg-gray-800 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

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

                init() {
                    // Any initialization code
                },

                // sendMessage() {
                //     if (!this.input.trim()) return;

                //     const userMessage = {
                //         id: Date.now(),
                //         type: 'user',
                //         content: this.input
                //     };

                //     this.messages.push(userMessage);
                //     const userInput = this.input;
                //     this.input = '';
                //     this.isLoading = true;

                //     // Scroll to bottom
                //     this.$nextTick(() => {
                //         this.$refs.chatContainer.scrollTop = this.$refs.chatContainer.scrollHeight;
                //     });

                //     // Simulate AI response (replace with the model later)
                //     setTimeout(() => {
                //         const aiMessage = {
                //             id: Date.now() + 1,
                //             type: 'ai',
                //             content: this.isOneLineMode 
                //                 ? `Here's a quick explanation: ${userInput} is like a concept that helps computers think smarter!`
                //                 : `Great question about ${userInput}! Let me explain this in simple terms.\n\nThink of it like this: Imagine you're teaching a child to recognize different fruits. You show them many examples of apples - red ones, green ones, big and small. Eventually, they learn what makes an apple an apple.\n\nThat's similar to how AI learns! It looks at lots of examples and finds patterns, just like how you learned to recognize things when you were young.\n\nThe main difference is that AI uses mathematics and computers to do this learning process much faster than humans, but the basic idea of "learning from examples" is the same!`,
                //             showAlternative: true,
                //             showOneLine: !this.isOneLineMode,
                //             oneLine: null
                //         };
                        
                //         this.messages.push(aiMessage);
                //         this.isLoading = false;

                //         this.$nextTick(() => {
                //             this.$refs.chatContainer.scrollTop = this.$refs.chatContainer.scrollHeight;
                //         });
                //     }, 1000);
                // },

                sendMessage() {
                    // 1. Cek input kosong
                    if (!this.input.trim()) return;

                    // 2. Tampilkan pesan User di layar
                    const userMessage = {
                        id: Date.now(),
                        type: 'user',
                        content: this.input
                    };
                    this.messages.push(userMessage);

                    // Simpan input ke variabel sementara
                    const userInput = this.input;
                    this.input = ''; // Kosongkan kotak ketik
                    this.isLoading = true; // Munculkan "Thinking..."

                    // Scroll ke bawah
                    this.$nextTick(() => {
                        this.$refs.chatContainer.scrollTop = this.$refs.chatContainer.scrollHeight;
                    });

                    // 3. KIRIM KE SERVER (Fetch API)
                    fetch('/chat/send', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            message: userInput,
                            // Kirim mode one-line jika perlu (opsional, tergantung controller)
                            oneLineMode: this.isOneLineMode 
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        // 4. TERIMA JAWABAN DARI PYTHON
                        if (data.success) {
                            const aiMessage = {
                                id: Date.now() + 1,
                                type: 'ai',
                                content: data.response, // <--- INI JAWABAN ASLI DARI API.PY
                                showAlternative: false, // Fitur ini sementara dimatikan dulu karena butuh logika tambahan
                                showOneLine: false,
                                oneLine: null
                            };
                            this.messages.push(aiMessage);
                        } else {
                            // Jika Error
                            alert('Error: ' + data.response);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Gagal menghubungi server. Pastikan terminal "php artisan serve" dan "python api.py" menyala.');
                    })
                    .finally(() => {
                        this.isLoading = false; // Hilangkan "Thinking..."
                        this.$nextTick(() => {
                            this.$refs.chatContainer.scrollTop = this.$refs.chatContainer.scrollHeight;
                        });
                    });
                },

                sendSuggestedTerm(term) {
                    this.input = term;
                    this.sendMessage();
                },

                getAlternativeExplanation(messageId) {
                    this.messages = this.messages.map(msg => {
                        if (msg.id === messageId && msg.type === 'ai') {
                            return {
                                ...msg,
                                content: `Here's another way to think about it!\n\nImagine this as a recipe book. When you follow recipes multiple times, you start understanding cooking patterns and can even create your own dishes.\n\nAI works similarly - it follows patterns from data it has seen before and can then make predictions or decisions about new situations it encounters.\n\nThe key is practice and experience, just like becoming a better cook over time!`,
                                showAlternative: true
                            };
                        }
                        return msg;
                    });
                },

                generateOneLine(messageId) {
                    this.messages = this.messages.map(msg => {
                        if (msg.id === messageId && msg.type === 'ai') {
                            return {
                                ...msg,
                                oneLine: `In simple terms: It's a way for computers to learn from examples, just like humans do!`,
                                showOneLine: false
                            };
                        }
                        return msg;
                    });
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
                    
                    // Apply highlights
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