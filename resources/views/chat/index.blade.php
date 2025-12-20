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
        .header { background: #fff; padding: 15px 25px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e0e0e0; }
        .header .logo { font-size: 20px; font-weight: 700; color: #5046e5; display: flex; align-items: center; gap: 10px; }
        .header .back-btn { background: #f3f4f6; color: #374151; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 6px; }
        .header .back-btn:hover { background: #e5e7eb; }
        .chat-container { display: flex; flex: 1; overflow: hidden; }
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
        .user-info h4 { font-size: 14px; font-weight: 600; color: #1f2937; margin-bottom: 2px; }
        .user-info p { font-size: 12px; color: #6b7280; }
        .chat-area { flex: 1; display: flex; flex-direction: column; background: #f8f9fa; }
        .chat-header { background: #fff; padding: 12px 20px; border-bottom: 1px solid #e0e0e0; display: flex; align-items: center; justify-content: space-between; }
        .chat-header-left { display: flex; align-items: center; gap: 12px; }
        .chat-header .user-avatar { width: 40px; height: 40px; }
        .chat-header h3 { font-size: 15px; font-weight: 600; color: #1f2937; }
        .call-buttons { display: flex; gap: 8px; }
        .call-btn { width: 38px; height: 38px; border-radius: 50%; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 16px; transition: all 0.2s; }
        .call-btn.audio { background: #dcfce7; color: #16a34a; }
        .call-btn.video { background: #dbeafe; color: #2563eb; }
        .call-btn:hover { transform: scale(1.1); }
        .messages-area { flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 10px; }
        .empty-state { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #9ca3af; }
        .empty-state i { font-size: 60px; margin-bottom: 15px; opacity: 0.5; }
        .message { max-width: 70%; display: flex; flex-direction: column; }
        .message.sent { align-self: flex-end; }
        .message.received { align-self: flex-start; }
        .message .bubble { padding: 10px 14px; border-radius: 8px; font-size: 14px; line-height: 1.4; }
        .message.sent .bubble { background: linear-gradient(135deg, #5046e5, #7c3aed); color: white; border-bottom-right-radius: 4px; }
        .message.received .bubble { background: white; color: #1f2937; border: 1px solid #e0e0e0; border-bottom-left-radius: 4px; }
        .message .bubble img { max-width: 250px; border-radius: 6px; cursor: pointer; }
        .message .time { font-size: 10px; color: #9ca3af; margin-top: 4px; }
        .message.sent .time { text-align: right; }
        .message-input-area { background: #fff; padding: 15px 20px; border-top: 1px solid #e0e0e0; }
        .input-actions { display: flex; gap: 8px; margin-bottom: 10px; }
        .action-btn { width: 36px; height: 36px; border-radius: 50%; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 16px; background: #f3f4f6; color: #6b7280; transition: all 0.2s; }
        .action-btn:hover { background: #e5e7eb; color: #5046e5; }
        .message-input { display: flex; gap: 12px; align-items: center; }
        .message-input input { flex: 1; padding: 12px 16px; border: 1px solid #e0e0e0; border-radius: 6px; font-size: 14px; outline: none; }
        .message-input input:focus { border-color: #5046e5; }
        .message-input button { background: linear-gradient(135deg, #5046e5, #7c3aed); color: white; border: none; padding: 12px 20px; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 6px; }
        .no-chat { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #f8f9fa; }
        .no-chat i { font-size: 80px; color: #d1d5db; margin-bottom: 20px; }
        .no-chat h3 { font-size: 18px; color: #6b7280; margin-bottom: 8px; }
        .emoji-picker { position: absolute; bottom: 100%; left: 0; background: white; border: 1px solid #e0e0e0; border-radius: 8px; padding: 10px; display: none; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 280px; z-index: 100; }
        .emoji-picker.show { display: block; }
        .emoji-grid { display: grid; grid-template-columns: repeat(8, 1fr); gap: 5px; max-height: 200px; overflow-y: auto; }
        .emoji-item { font-size: 20px; cursor: pointer; padding: 5px; text-align: center; border-radius: 4px; transition: background 0.2s; }
        .emoji-item:hover { background: #f3f4f6; }
        .preview-container { display: none; padding: 10px; background: #f8f9fa; border-radius: 6px; margin-bottom: 10px; position: relative; }
        .preview-container.show { display: block; }
        .preview-container img { max-height: 100px; border-radius: 4px; }
        .preview-container .remove-preview { position: absolute; top: 5px; right: 5px; background: #ef4444; color: white; border: none; width: 24px; height: 24px; border-radius: 50%; cursor: pointer; }
        /* Call Modal */
        .call-modal { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.9); display: none; flex-direction: column; align-items: center; justify-content: center; z-index: 1000; }
        .call-modal.show { display: flex; }
        .call-modal .caller-info { text-align: center; color: white; margin-bottom: 30px; }
        .call-modal .caller-avatar { width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #5046e5, #7c3aed); display: flex; align-items: center; justify-content: center; font-size: 48px; color: white; margin: 0 auto 20px; overflow: hidden; }
        .call-modal .caller-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .call-modal .caller-name { font-size: 24px; font-weight: 600; margin-bottom: 8px; }
        .call-modal .call-status { font-size: 14px; color: #9ca3af; }
        .call-actions { display: flex; gap: 20px; }
        .call-action-btn { width: 60px; height: 60px; border-radius: 50%; border: none; cursor: pointer; font-size: 24px; transition: all 0.2s; }
        .call-action-btn.accept { background: #22c55e; color: white; }
        .call-action-btn.reject { background: #ef4444; color: white; }
        .call-action-btn.end { background: #ef4444; color: white; }
        .call-action-btn:hover { transform: scale(1.1); }
        #video-container { display: none; width: 100%; max-width: 800px; position: relative; }
        #video-container.show { display: block; }
        #remote-video { width: 100%; border-radius: 8px; background: #000; }
        #local-video { position: absolute; bottom: 20px; right: 20px; width: 150px; border-radius: 8px; border: 2px solid white; }
        @media (max-width: 768px) { .users-sidebar { width: 80px; } .users-header, .user-info { display: none; } .user-item { justify-content: center; padding: 12px; } }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo"><i class="fas fa-comments"></i> Chat</div>
        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('user.dashboard') }}" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </header>

    <div class="chat-container">
        <div class="users-sidebar">
            <div class="users-header"><h3>Conversations</h3></div>
            <div class="users-list">
                @foreach($users as $user)
                <div class="user-item" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}" data-user-avatar="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : ($user->avatar ?? '') }}" onclick="selectUser({{ $user->id }})">
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
                
        <div class="chat-area" id="chat-area">
            <div class="no-chat">
                <i class="fas fa-comments"></i>
                <h3>Select a conversation</h3>
                <p>Choose a user from the left to start chatting</p>
            </div>
        </div>
    </div>

    <!-- Call Modal -->
    <div class="call-modal" id="call-modal">
        <div class="caller-info">
            <div class="caller-avatar" id="caller-avatar"></div>
            <div class="caller-name" id="caller-name"></div>
            <div class="call-status" id="call-status">Calling...</div>
        </div>
        <div id="video-container">
            <video id="remote-video" autoplay playsinline></video>
            <video id="local-video" autoplay playsinline muted></video>
        </div>
        <div class="call-actions" id="call-actions">
            <button class="call-action-btn accept" id="accept-call" onclick="acceptCall()"><i class="fas fa-phone"></i></button>
            <button class="call-action-btn reject" id="reject-call" onclick="rejectCall()"><i class="fas fa-phone-slash"></i></button>
        </div>
        <div class="call-actions" id="ongoing-call-actions" style="display:none;">
            <button class="call-action-btn end" onclick="endCall()"><i class="fas fa-phone-slash"></i></button>
        </div>
    </div>

    <input type="file" id="file-input" accept="image/*" style="display:none" onchange="handleFileSelect(event)">
    <script>
        const currentUserId = {{ auth()->id() }};
        const currentUserName = "{{ auth()->user()->name }}";
        let selectedUserId = null;
        let selectedUserName = '';
        let selectedUserAvatar = '';
        let selectedFile = null;
        let pusher, chatChannel, callChannel;
        let peerConnection = null;
        let localStream = null;
        let currentCallType = 'audio';
        let isCallIncoming = false;

        const emojis = ['😀','😂','😍','🥰','😎','🤔','😢','😡','👍','👎','❤️','🔥','🎉','👏','🙏','💪','✨','🌟','💯','🤝','👋','🎁','🌈','☀️','🌙','⭐','💬','📷','🎵','🎮'];

        // Initialize Pusher
        Pusher.logToConsole = true;
        pusher = new Pusher('0e82200317882bf6c2c8', {
            cluster: 'ap2',
            forceTLS: true,
            authEndpoint: '/broadcasting/auth',
            auth: { headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } }
        });

        // Chat channel
        chatChannel = pusher.subscribe('private-chat.' + currentUserId);
        chatChannel.bind('App\\Events\\MessageSent', function(data) {
            console.log('Message received:', data);
            if (selectedUserId === data.sender_id) {
                appendMessage(data, false);
                scrollToBottom();
            }
        });

        // Call channel
        callChannel = pusher.subscribe('private-call.' + currentUserId);
        callChannel.bind('App\\Events\\CallSignal', function(data) {
            console.log('Call signal received:', data);
            handleCallSignal(data);
        });

        function selectUser(userId) {
            selectedUserId = userId;
            const userItem = document.querySelector(`[data-user-id="${userId}"]`);
            selectedUserName = userItem.dataset.userName;
            selectedUserAvatar = userItem.dataset.userAvatar;
            
            document.querySelectorAll('.user-item').forEach(item => item.classList.remove('active'));
            userItem.classList.add('active');
            loadMessages(userId);
        }

        function loadMessages(userId) {
            fetch(`/chat/messages/${userId}`).then(res => res.json()).then(data => renderChatArea(data.user, data.messages));
        }

        function renderChatArea(user, messages) {
            const chatArea = document.getElementById('chat-area');
            let avatarHtml = user.avatar ? `<img src="${user.avatar}" alt="${user.name}">` : user.name.charAt(0).toUpperCase();
            
            chatArea.innerHTML = `
                <div class="chat-header">
                    <div class="chat-header-left">
                        <div class="user-avatar">${avatarHtml}</div>
                        <h3>${user.name}</h3>
                    </div>
                    <div class="call-buttons">
                        <button class="call-btn audio" onclick="startCall('audio')" title="Audio Call"><i class="fas fa-phone"></i></button>
                        <button class="call-btn video" onclick="startCall('video')" title="Video Call"><i class="fas fa-video"></i></button>
                    </div>
                </div>
                <div class="messages-area" id="messages-area">
                    ${messages.length === 0 ? `<div class="empty-state"><i class="fas fa-comment-dots"></i><p>No messages yet. Say hello!</p></div>` : 
                    messages.map(msg => renderMessage(msg)).join('')}
                </div>
                <div class="message-input-area">
                    <div class="preview-container" id="preview-container">
                        <img id="preview-image" src="">
                        <button class="remove-preview" onclick="removePreview()"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="input-actions" style="position:relative;">
                        <button class="action-btn" onclick="toggleEmojiPicker()" title="Emoji"><i class="fas fa-smile"></i></button>
                        <button class="action-btn" onclick="document.getElementById('file-input').click()" title="Send Photo"><i class="fas fa-image"></i></button>
                        <div class="emoji-picker" id="emoji-picker">
                            <div class="emoji-grid">${emojis.map(e => `<span class="emoji-item" onclick="insertEmoji('${e}')">${e}</span>`).join('')}</div>
                        </div>
                    </div>
                    <div class="message-input">
                        <input type="text" id="message-input" placeholder="Type a message..." onkeypress="handleKeyPress(event)">
                        <button onclick="sendMessage()"><i class="fas fa-paper-plane"></i> Send</button>
                    </div>
                </div>`;
            scrollToBottom();
        }

        function renderMessage(msg) {
            let content = '';
            if (msg.attachment && msg.attachment_type === 'image') {
                content = `<img src="${msg.attachment}" onclick="window.open('${msg.attachment}', '_blank')">`;
                if (msg.message) content += `<p style="margin-top:8px">${escapeHtml(msg.message)}</p>`;
            } else {
                content = escapeHtml(msg.message);
            }
            return `<div class="message ${msg.is_mine ? 'sent' : 'received'}"><div class="bubble">${content}</div><span class="time">${msg.created_at}</span></div>`;
        }

        function sendMessage() {
            const input = document.getElementById('message-input');
            const message = input.value.trim();
            if (!selectedUserId || (!message && !selectedFile)) return;
            
            const formData = new FormData();
            formData.append('receiver_id', selectedUserId);
            if (message) formData.append('message', message);
            if (selectedFile) formData.append('attachment', selectedFile);
            
            input.value = '';
            removePreview();
            
            fetch('/chat/send', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: formData
            }).then(res => res.json()).then(data => {
                if (data.success) {
                    appendMessage({ message: data.message.message, attachment: data.message.attachment, attachment_type: data.message.attachment_type, created_at: data.message.created_at, is_mine: true }, true);
                    scrollToBottom();
                }
            });
        }

        function appendMessage(data, isMine) {
            const messagesArea = document.getElementById('messages-area');
            if (!messagesArea) return;
            const emptyState = messagesArea.querySelector('.empty-state');
            if (emptyState) emptyState.remove();
            
            let content = '';
            if (data.attachment && data.attachment_type === 'image') {
                content = `<img src="${data.attachment}" onclick="window.open('${data.attachment}', '_blank')">`;
                if (data.message) content += `<p style="margin-top:8px">${escapeHtml(data.message)}</p>`;
            } else {
                content = escapeHtml(data.message || '');
            }
            
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${isMine ? 'sent' : 'received'}`;
            messageDiv.innerHTML = `<div class="bubble">${content}</div><span class="time">${data.created_at}</span>`;
            messagesArea.appendChild(messageDiv);
        }

        function toggleEmojiPicker() {
            document.getElementById('emoji-picker').classList.toggle('show');
        }

        function insertEmoji(emoji) {
            const input = document.getElementById('message-input');
            input.value += emoji;
            input.focus();
            document.getElementById('emoji-picker').classList.remove('show');
        }

        function handleFileSelect(event) {
            const file = event.target.files[0];
            if (file && file.type.startsWith('image/')) {
                selectedFile = file;
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-image').src = e.target.result;
                    document.getElementById('preview-container').classList.add('show');
                };
                reader.readAsDataURL(file);
            }
            event.target.value = '';
        }

        function removePreview() {
            selectedFile = null;
            document.getElementById('preview-container').classList.remove('show');
            document.getElementById('preview-image').src = '';
        }

        // WebRTC Call Functions
        const iceServers = { iceServers: [{ urls: 'stun:stun.l.google.com:19302' }, { urls: 'stun:stun1.l.google.com:19302' }] };

        async function startCall(type) {
            if (!selectedUserId) return;
            currentCallType = type;
            isCallIncoming = false;
            
            showCallModal(selectedUserName, selectedUserAvatar, 'Calling...');
            document.getElementById('call-actions').style.display = 'none';
            document.getElementById('ongoing-call-actions').style.display = 'flex';
            
            try {
                localStream = await navigator.mediaDevices.getUserMedia({ audio: true, video: type === 'video' });
                if (type === 'video') {
                    document.getElementById('local-video').srcObject = localStream;
                    document.getElementById('video-container').classList.add('show');
                }
                
                peerConnection = new RTCPeerConnection(iceServers);
                localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));
                
                peerConnection.onicecandidate = (event) => {
                    if (event.candidate) {
                        sendSignal('ice-candidate', event.candidate);
                    }
                };
                
                peerConnection.ontrack = (event) => {
                    document.getElementById('remote-video').srcObject = event.streams[0];
                    if (currentCallType === 'video') document.getElementById('video-container').classList.add('show');
                };
                
                const offer = await peerConnection.createOffer();
                await peerConnection.setLocalDescription(offer);
                sendSignal('offer', offer, type);
            } catch (err) {
                console.error('Error starting call:', err);
                endCall();
                alert('Could not access camera/microphone');
            }
        }

        function handleCallSignal(data) {
            console.log('Handling signal:', data.type);
            
            if (data.type === 'offer') {
                isCallIncoming = true;
                currentCallType = data.call_type;
                showCallModal(data.caller_name, data.caller_avatar, `Incoming ${data.call_type} call...`);
                document.getElementById('call-actions').style.display = 'flex';
                document.getElementById('ongoing-call-actions').style.display = 'none';
                window.incomingOffer = data.data;
                window.callerId = data.caller_id;
            } else if (data.type === 'answer') {
                peerConnection.setRemoteDescription(new RTCSessionDescription(data.data));
                document.getElementById('call-status').textContent = 'Connected';
            } else if (data.type === 'ice-candidate') {
                if (peerConnection) peerConnection.addIceCandidate(new RTCIceCandidate(data.data));
            } else if (data.type === 'end-call') {
                endCall();
            }
        }

        async function acceptCall() {
            document.getElementById('call-actions').style.display = 'none';
            document.getElementById('ongoing-call-actions').style.display = 'flex';
            document.getElementById('call-status').textContent = 'Connecting...';
            
            try {
                localStream = await navigator.mediaDevices.getUserMedia({ audio: true, video: currentCallType === 'video' });
                if (currentCallType === 'video') {
                    document.getElementById('local-video').srcObject = localStream;
                    document.getElementById('video-container').classList.add('show');
                }
                
                peerConnection = new RTCPeerConnection(iceServers);
                localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));
                
                peerConnection.onicecandidate = (event) => {
                    if (event.candidate) sendSignalTo(window.callerId, 'ice-candidate', event.candidate);
                };
                
                peerConnection.ontrack = (event) => {
                    document.getElementById('remote-video').srcObject = event.streams[0];
                    if (currentCallType === 'video') document.getElementById('video-container').classList.add('show');
                };
                
                await peerConnection.setRemoteDescription(new RTCSessionDescription(window.incomingOffer));
                const answer = await peerConnection.createAnswer();
                await peerConnection.setLocalDescription(answer);
                sendSignalTo(window.callerId, 'answer', answer);
                document.getElementById('call-status').textContent = 'Connected';
            } catch (err) {
                console.error('Error accepting call:', err);
                endCall();
            }
        }

        function rejectCall() {
            sendSignalTo(window.callerId, 'end-call', null);
            hideCallModal();
        }

        function endCall() {
            if (peerConnection) { peerConnection.close(); peerConnection = null; }
            if (localStream) { localStream.getTracks().forEach(track => track.stop()); localStream = null; }
            
            const receiverId = isCallIncoming ? window.callerId : selectedUserId;
            if (receiverId) sendSignalTo(receiverId, 'end-call', null);
            
            hideCallModal();
        }

        function showCallModal(name, avatar, status) {
            const modal = document.getElementById('call-modal');
            document.getElementById('caller-name').textContent = name;
            document.getElementById('call-status').textContent = status;
            const avatarEl = document.getElementById('caller-avatar');
            avatarEl.innerHTML = avatar ? `<img src="${avatar}">` : name.charAt(0).toUpperCase();
            modal.classList.add('show');
        }

        function hideCallModal() {
            document.getElementById('call-modal').classList.remove('show');
            document.getElementById('video-container').classList.remove('show');
            document.getElementById('remote-video').srcObject = null;
            document.getElementById('local-video').srcObject = null;
        }

        function sendSignal(type, data, callType = null) {
            sendSignalTo(selectedUserId, type, data, callType);
        }

        function sendSignalTo(receiverId, type, data, callType = null) {
            fetch('/call/signal', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ receiver_id: receiverId, type: type, data: data, call_type: callType || currentCallType })
            });
        }

        function scrollToBottom() {
            const messagesArea = document.getElementById('messages-area');
            if (messagesArea) messagesArea.scrollTop = messagesArea.scrollHeight;
        }

        function handleKeyPress(event) { if (event.key === 'Enter') sendMessage(); }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Close emoji picker when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.input-actions')) {
                document.getElementById('emoji-picker')?.classList.remove('show');
            }
        });
    </script>
</body>
</html>