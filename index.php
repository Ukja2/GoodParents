<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>굿페런츠 AI 도우미</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Jua&display=swap');
        
        * {
            font-family: 'Jua', sans-serif;
        }

        .chat-container {
            height: calc(100vh - 100px);
            background: linear-gradient(135deg, #fff5f7 0%, #fff8f1 100%);
        }

        .message-container {
            height: calc(100vh - 230px);
            overflow-y: auto;
            scroll-behavior: smooth;
            padding-right: 10px;
        }

        .message-container::-webkit-scrollbar {
            width: 6px;
        }

        .message-container::-webkit-scrollbar-track {
            background: #feebc8;
            border-radius: 10px;
        }

        .message-container::-webkit-scrollbar-thumb {
            background: #fbd38d;
            border-radius: 10px;
        }

        .message-bubble {
            border-radius: 20px;
            box-shadow: 0 2px 8px rgba(251, 211, 141, 0.3);
            transition: all 0.3s ease;
        }

        .user-message {
            background: linear-gradient(135deg, #fbd38d 0%, #f6ad55 100%);
        }

        .bot-message {
            background: white;
            border: 2px solid #feebc8;
        }

        .category-button {
            transition: all 0.3s ease;
            transform: translateY(0);
        }

        .category-button:hover {
            transform: translateY(-2px);
        }

        .send-button {
            transition: all 0.2s ease;
        }

        .send-button:hover {
            transform: scale(1.05);
        }

        @keyframes dots {
            0%, 20% { content: '.'; }
            40% { content: '..'; }
            60% { content: '...'; }
            80% { content: '....'; }
            100% { content: '.....'; }
        }

        .loading-text::after {
            content: '.';
            animation: dots 1.5s steps(5, end) infinite;
        }

        .typing-indicator {
            background-color: #fff;
            border: 2px solid #feebc8;
            border-radius: 20px;
            padding: 15px 20px;
            display: none;
            align-items: center;
            margin-right: 48px;
            box-shadow: 0 2px 8px rgba(251, 211, 141, 0.3);
            position: fixed;
            bottom: 120px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            max-width: 90%;
            width: auto;
        }

        .typing-indicator.show {
            display: flex;
        }

        .typing-indicator span {
            height: 8px;
            width: 8px;
            background: #fbd38d;
            border-radius: 50%;
            margin: 0 2px;
            display: inline-block;
            animation: bounce 1.3s linear infinite;
        }

        .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
        .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }

        @keyframes bounce {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-4px); }
        }

        @media (max-width: 768px) {
            .typing-indicator {
                bottom: 100px;
                max-width: 85%;
            }
        }

        @media (max-width: 380px) {
            .typing-indicator {
                bottom: 90px;
                max-width: 80%;
                padding: 10px 15px;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-orange-50 via-white to-yellow-50 min-h-screen p-4">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white/90 backdrop-blur-lg rounded-3xl shadow-lg p-6 chat-container">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-orange-400 flex items-center justify-center">
                        <span class="text-2xl">🤗</span>
                    </div>
                    <h1 class="text-2xl font-bold bg-gradient-to-r from-orange-400 to-yellow-500 bg-clip-text text-transparent">
                        굿페런츠 AI 도우미 ✨
                    </h1>
                </div>
                <div class="flex gap-3">
                    <button onclick="showTopic('communication')" 
                        class="category-button px-4 py-2 bg-orange-100 hover:bg-orange-200 text-orange-700 rounded-full text-sm font-medium flex items-center gap-2">
                        💝 소통 방법
                    </button>
                    <button onclick="showTopic('discipline')" 
                        class="category-button px-4 py-2 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded-full text-sm font-medium flex items-center gap-2">
                        💫 훈육 가이드
                    </button>
                    <button onclick="showTopic('trends')" 
                        class="category-button px-4 py-2 bg-green-100 hover:bg-green-200 text-green-700 rounded-full text-sm font-medium flex items-center gap-2">
                        ⭐ 육아 트렌드
                    </button>
                    <button onclick="showTopic('emotion')" 
                        class="category-button px-4 py-2 bg-purple-100 hover:bg-purple-200 text-purple-700 rounded-full text-sm font-medium flex items-center gap-2">
                        💖 감정 조절
                    </button>
                </div>
            </div>
            
            <div class="message-container space-y-4 mb-6" id="messageContainer">
                <div class="bot-message message-bubble p-4 mr-12">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center">
                            <span class="text-xl">🌟</span>
                        </div>
                        <p class="font-medium text-orange-700">AI 도우미</p>
                    </div>
                    <p class="text-gray-600">안녕하세요! 저는 부모님들의 육아를 돕는 AI 도우미예요 💕</p>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                        <button onclick="showTopic('communication')" 
                            class="category-button p-4 bg-orange-50 hover:bg-orange-100 text-orange-700 rounded-xl text-left transition-all">
                            <span class="text-2xl mb-2 block">💝</span>
                            <h3 class="font-medium mb-1">아이와의 효과적인 소통방법</h3>
                            <p class="text-sm text-orange-600 opacity-75">감정 표현과 대화 기술 향상</p>
                        </button>
                        <button onclick="showTopic('discipline')" 
                            class="category-button p-4 bg-yellow-50 hover:bg-yellow-100 text-yellow-700 rounded-xl text-left transition-all">
                            <span class="text-2xl mb-2 block">💫</span>
                            <h3 class="font-medium mb-1">연령별 맞춤 훈육 가이드</h3>
                            <p class="text-sm text-yellow-600 opacity-75">발달 단계별 적절한 지도 방법</p>
                        </button>
                        <button onclick="showTopic('trends')" 
                            class="category-button p-4 bg-green-50 hover:bg-green-100 text-green-700 rounded-xl text-left transition-all">
                            <span class="text-2xl mb-2 block">⭐</span>
                            <h3 class="font-medium mb-1">최신 육아 트렌드 정보</h3>
                            <p class="text-sm text-green-600 opacity-75">현대적인 육아 방법과 도구</p>
                        </button>
                        <button onclick="showTopic('emotion')" 
                            class="category-button p-4 bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-xl text-left transition-all">
                            <span class="text-2xl mb-2 block">💖</span>
                            <h3 class="font-medium mb-1">감정 조절과 공감 대화법</h3>
                            <p class="text-sm text-purple-600 opacity-75">정서 지능 향상을 위한 팁</p>
                        </button>
                    </div>
                </div>
                <div id="typing-indicator" class="typing-indicator">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center">
                            <span class="text-lg">🤖</span>
                        </div>
                        <p class="text-gray-600 font-medium">답변을 준비중입니다</p>
                        <div class="flex">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 bg-white p-4 rounded-2xl shadow-sm border-2 border-orange-100">
                <textarea 
                    id="userInput" 
                    class="flex-1 p-3 bg-orange-50 rounded-xl border-2 border-orange-100 resize-none h-12 focus:outline-none focus:ring-2 focus:ring-orange-200 transition-all"
                    placeholder="질문을 입력해주세요... 💭"
                    onkeydown="if(event.keyCode === 13 && !event.shiftKey) { event.preventDefault(); sendMessage(); }"
                    rows="1"
                    oninput="this.style.height = ''; this.style.height = Math.min(this.scrollHeight, 120) + 'px'"></textarea>
                <button 
                    onclick="sendMessage()" 
                    class="send-button w-12 h-12 bg-orange-400 text-white rounded-xl hover:bg-orange-500 flex items-center justify-center">
                    <span class="text-xl">✉️</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        function addMessage(content, isUser = false) {
            const messageContainer = document.getElementById('messageContainer');
            const typingIndicator = document.getElementById('typing-indicator');
            const messageDiv = document.createElement('div');
            
            messageDiv.className = isUser ? 
                'user-message message-bubble p-4 ml-12' : 
                'bot-message message-bubble p-4 mr-12';
            
            const emoji = isUser ? '👤' : '🌟';
            const bgColor = isUser ? 'bg-orange-400' : 'bg-orange-100';
            const textColor = isUser ? 'text-white' : 'text-gray-600';
            
            messageDiv.innerHTML = `
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-10 h-10 rounded-full ${bgColor} flex items-center justify-center">
                        <span class="text-xl">${emoji}</span>
                    </div>
                    <p class="font-medium ${isUser ? 'text-white' : 'text-orange-700'}">${isUser ? '부모님' : 'AI 도우미'}</p>
                </div>
                <p class="${textColor} whitespace-pre-line">${content}</p>
            `;
            
            messageContainer.insertBefore(messageDiv, typingIndicator);
            messageContainer.scrollTop = messageContainer.scrollHeight;
        }

        function showTopic(topic) {
            let message = '';
            switch(topic) {
                case 'communication':
                    message = "아이와의 소통에 대해 궁금하신가요? 💝\n다음과 같은 질문들을 해보세요:\n\n" +
                        "🌸 아이의 감정을 이해하는 방법이 궁금해요\n" +
                        "🌸 아이가 말을 잘 안 들을 때는 어떻게 해야 하나요?\n" +
                        "🌸 효과적인 칭찬 방법을 알고 싶어요";
                    break;
                case 'discipline':
                    message = "훈육에 대해 고민이 있으신가요? 💫\n이런 질문들을 해보세요:\n\n" +
                        "✨ 떼쓰는 아이를 어떻게 다뤄야 할까요?\n" +
                        "✨ 스마트폰 사용을 제한하고 싶어요\n" +
                        "✨ 형제간 다툼을 조절하는 방법이 궁금해요";
                    break;
                case 'trends':
                    message = "최신 육아 트렌드가 궁금하신가요? ⭐\n이런 것들을 물어보세요:\n\n" +
                        "💫 요즘 인기있는 교육 방법은 무엇인가요?\n" +
                        "💫 디지털 시대에 맞는 육아 방법이 궁금해요\n" +
                        "💫 아이의 창의성을 키워주는 활동을 추천해주세요";
                    break;
            case 'emotion':
                    message = "감정 조절과 공감에 대해 알고 싶으신가요? 💖\n이런 질문들을 해보세요:\n\n" +
                        "💝 아이가 화를 낼 때 어떻게 대처해야 할까요?\n" +
                        "💝 아이의 불안감을 줄여주는 방법이 궁금해요\n" +
                        "💝 아이와 감정을 나누는 대화법을 알고 싶어요\n" +
                        "💝 아이의 자존감을 높이는 방법을 알려주세요\n" +
                        "💝 형제간의 질투심을 다루는 방법이 궁금해요";
                    break;
            }
            addMessage(message, false);
        }

        async function sendMessage() {
            const userInput = document.getElementById('userInput');
            const message = userInput.value.trim();
            const typingIndicator = document.getElementById('typing-indicator');
            
            if (!message) return;
            
            addMessage(message, true);
            userInput.value = '';
            userInput.style.height = '48px';
            
            typingIndicator.classList.add('show');
            
            try {
                const response = await fetch('process_chat.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ message: message })
                });
                
                const data = await response.json();
                
                typingIndicator.classList.remove('show');
                
                if (data.error) {
                    setTimeout(() => {
                        addMessage('죄송합니다. 오류가 발생했습니다: ' + data.error);
                    }, 1000);
                } else {
                    setTimeout(() => {
                        addMessage(data.response);
                    }, Math.random() * 1000 + 1000);
                }
            } catch (error) {
                typingIndicator.classList.remove('show');
                setTimeout(() => {
                    addMessage('죄송합니다. 서버와 통신 중 오류가 발생했습니다.');
                }, 1000);
                console.error('Error:', error);
            }
        }
    </script>
</body>
</html> 