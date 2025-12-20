<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; height: 100vh; display: flex; flex-direction: column; }
        
        /* Header */
        .header { background: #fff; padding: 15px 25px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e0e0e0; }
        .header .logo { font-size: 20px; font-weight: 700; color: #5046e5; display: flex; align-items: center; gap: 10px; }
        .header .back-btn { background: #f3f4f6; color: #374151; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 6px; }
        .header .back-btn:hover { background: #e5e7eb; }
        
        /* Chat Container */
        .chat-container { display: flex; flex: 1; overflow: hidden; }
        
        /* Users Sidebar */
        .users-sidebar { width: 300px; background: #fff; border-right: 1px solid #e0e0e0; display: flex; flex-direction: column; }
        .users-header { padding: 15px 20px; border-bottom: 1px solid #f0f0f0; }
        .users-header h3 { font-size: 14px; color: #374151; font-weight: 600; }
        .users-list { flex: 1; overflow-y: auto; }
        .user-item { display: flex; align-items: center; gap: 12px; padding: 12px 20px; cursor: pointer; transition: background 0.2s; border-bottom: 1px solid #f5f5f5; }
        .user-item:hover { background: #f8f9fa; }
        .user-item.active { background: #eff6ff; border-left: 3px solid #5046e5; }
        .user-avatar { width: 45px; height: 45px; border-radius: 50%; background: linear-gradient(135deg, #5046e5, #7c3aed); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 16px; overflow: hidden; flex-shrink: 0; }
        .user-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .user-info { flex: 1; min-width: 0; }
        .user-info h4 { font-size: 14px; font-weight: 600; color: #1f2937; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-info p { font-size: 12px; color: #6b7280; }
        
        /* Chat Area */
        .chat-area { flex: 1; display: flex; flex-direction: column; background: #f8f9fa; }
        
        /* Chat Header */
        .chat-header { background: #fff; padding: 15px 20px; border-bottom: 1px solid #e0e0e0; display: flex; align-items: center; gap: 12px; }
        .chat-header .user-avatar { width: 40px; height: 40px; }
        .chat-header h3 { font-size: 15px; font-weight: 600; color: #1f2937; }
        
        /* Messages Area */
        .messages-area { flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 10px; }
        
        /* Empty State */
        .empty-state { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #9ca3af; }
        .empty-state i { font-size: 60px; margin-bottom: 15px; opacity: 0.5; }
        .empty-state p { font-size: 14px; }
        
        /* Message Bubble */
        .message { max-width: 70%; display: flex; flex-direction: column; }
        .message.sent { align-self: flex-end; }
        .message.received { align-self: flex-start; }
        .message .bubble { padding: 10px 14px; border-radius: 8px; font-size: 14px; line-height: 1.4; }
        .message.sent .bubble { background: linear-gradient(135deg, #5046e5, #7c3aed); color: white; border-bottom-right-radius: 4px; }
        .message.received .bubble { background: white; color: #1f2937; border: 1px solid #e0e0e0; border-bottom-left-radius: 4px; }
        .message .time { font-size: 10px; color: #9ca3af; margin-top: 4px; }
        .message.sent .time { text-align: right; }
        
        /* Message Input */
        .message-input { background: #fff; padding: 15px 20px; border-top: 1px solid #e0e0e0; display: flex; gap: 12px; align-items: center; }
        .message-input input { flex: 1; padding: 12px 16px; border: 1px solid #e0e0e0; border-radius: 6px; font-size: 14px; outline: none; }
        .message-input input:focus { border-color: #5046e5; }
        .message-input button { background: linear-gradient(135deg, #5046e5, #7c3aed); color: white; border: none; padding: 12px 20px; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 6px; }
        .message-input button:hover { opacity: 0.9; }
        .message-input button:disabled { opacity: 0.5; cursor: not-allowed; }
        
        /* No Chat Selected */
        .no-chat { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #f8f9fa; }
        .no-chat i { font-size: 80px; color: #d1d5db; margin-bottom: 20px; }
        .no-chat h3 { font-size: 18px; color: #6b7280; margin-bottom: 8px; }
        .no-chat p { font-size: 14px; color: #9ca3af; }

        @media (max-width: 768px) {
            .users-sidebar { width: 80px; }
            .users-header, .user-info { display: none; }
            .user-item { justify-content: center; padding: 12px; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="logo">
            <i class="fas fa-comments"></i> Chat
        </div>
        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('user.dashboard') }}" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </header>

    <div class="chat-container">
        <!-- Users Sidebar -->
        <div class="users-sidebar">
            <div class="users-header">
                <h3>Conversations</h3>
            </div>
            <div class="users-list">
                @foreach($users as $user)
                <div class="user-item" data-user-id="{{ $user->id }}" onclick="selectUser({{ $user->id }})">
                    <div class="user-avatar">
                        @if($user->profile_picture)
                            <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="{{ $user->name }}">
                        @elseif($user->avatar)
                            <img src="{{ $user->avatar }}" alt="{{ $user->name }}">
                        @else
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="user-info">
                        <h4>{{ $user->name }}</h4>
                        <p>Click to chat</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Chat Area -->
        <div class="chat-area" id="chat-area">
            <div class="no-chat">
                <i class="fas fa-comments"></i>
                <h3>Select a conversation</h3>
                <p>Choose a user from the left to start chatting</p>
            </div>
        </div>
    </div>

    <script>
        const currentUserId = {{ auth()->id() }};
        let selectedUserId = null;
        let pusher = null;
        let channel = null;

        // Enable Pusher logging for debugging
        Pusher.logToConsole = true;

        // Initialize Pusher with direct values
        pusher = new Pusher('0e82200317882bf6c2c8', {
            cluster: 'ap2',
            forceTLS: true,
            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }
        });

        // Connection state logging
        pusher.connection.bind('connected', function() {
            console.log('Pusher connected successfully!');
        });

        pusher.connection.bind('error', function(err) {
            console.error('Pusher connection error:', err);
        });

        // Subscribe to private channel for receiving messages
        channel = pusher.subscribe('private-chat.' + currentUserId);
        
        channel.bind('pusher:subscription_succeeded', function() {
            console.log('Successfully subscribed to private-chat.' + currentUserId);
        });

        channel.bind('pusher:subscription_error', function(error) {
            console.error('Subscription error:', error);
        });

        // Listen for incoming messages
        channel.bind('App\\Events\\MessageSent', function(data) {
            console.log('Message received via Pusher:', data);
            // Show message if we're chatting with the sender
            if (selectedUserId === data.sender_id) {
                appendMessage(data, false);
                scrollToBottom();
            } else {
                // Optional: Show notification for messages from other users
                console.log('New message from user ' + data.sender_id);
            }
        });

        function selectUser(userId) {
            selectedUserId = userId;
            
            // Update active state
            document.querySelectorAll('.user-item').forEach(item => {
                item.classList.remove('active');
            });
            document.querySelector(`[data-user-id="${userId}"]`).classList.add('active');
            
            // Load messages
            loadMessages(userId);
        }

        function loadMessages(userId) {
            fetch(`/chat/messages/${userId}`)
                .then(res => res.json())
                .then(data => {
                    renderChatArea(data.user, data.messages);
                });
        }

        function renderChatArea(user, messages) {
            const chatArea = document.getElementById('chat-area');
            
            let avatarHtml = '';
            if (user.avatar) {
                avatarHtml = `<img src="${user.avatar}" alt="${user.name}">`;
            } else {
                avatarHtml = user.name.charAt(0).toUpperCase();
            }
            
            chatArea.innerHTML = `
                <div class="chat-header">
                    <div class="user-avatar">${avatarHtml}</div>
                    <h3>${user.name}</h3>
                </div>
                <div class="messages-area" id="messages-area">
                    ${messages.length === 0 ? `
                        <div class="empty-state">
                            <i class="fas fa-comment-dots"></i>
                            <p>No messages yet. Say hello!</p>
                        </div>
                    ` : messages.map(msg => `
                        <div class="message ${msg.is_mine ? 'sent' : 'received'}">
                            <div class="bubble">${escapeHtml(msg.message)}</div>
                            <span class="time">${msg.created_at}</span>
                        </div>
                    `).join('')}
                </div>
                <div class="message-input">
                    <input type="text" id="message-input" placeholder="Type a message..." onkeypress="handleKeyPress(event)">
                    <button onclick="sendMessage()">
                        <i class="fas fa-paper-plane"></i> Send
                    </button>
                </div>
            `;
            
            scrollToBottom();
        }

        function sendMessage() {
            const input = document.getElementById('message-input');
            const message = input.value.trim();
            
            if (!message || !selectedUserId) return;
            
            input.value = '';
            
            fetch('/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    receiver_id: selectedUserId,
                    message: message
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    appendMessage({
                        message: data.message.message,
                        created_at: data.message.created_at
                    }, true);
                    scrollToBottom();
                }
            });
        }

        function appendMessage(data, isMine) {
            const messagesArea = document.getElementById('messages-area');
            if (!messagesArea) return;
            
            // Remove empty state if exists
            const emptyState = messagesArea.querySelector('.empty-state');
            if (emptyState) emptyState.remove();
            
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${isMine ? 'sent' : 'received'}`;
            messageDiv.innerHTML = `
                <div class="bubble">${escapeHtml(data.message)}</div>
                <span class="time">${data.created_at}</span>
            `;
            messagesArea.appendChild(messageDiv);
        }

        function scrollToBottom() {
            const messagesArea = document.getElementById('messages-area');
            if (messagesArea) {
                messagesArea.scrollTop = messagesArea.scrollHeight;
            }
        }

        function handleKeyPress(event) {
            if (event.key === 'Enter') {
                sendMessage();
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>
