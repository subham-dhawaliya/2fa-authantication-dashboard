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
        .message .time { font-size: 10px; color: #9ca3af; margin-top: 4px; display: flex; align-items: center; gap: 4px; }
        .message.sent .time { justify-content: flex-end; }
        .message .tick { font-size: 12px; }
        .message .tick.sent { color: #9ca3af; }
        .message .tick.read { color: #3b82f6; }
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
        .call-action-btn.mute { background: #374151; color: white; }
        .call-action-btn.mute.muted { background: #f59e0b; color: white; }
        .call-action-btn.switch-cam { background: #2563eb; color: white; }
        .call-action-btn:hover { transform: scale(1.1); }
        #video-container { display: none; width: 100%; max-width: 800px; position: relative; }
        #video-container.show { display: block; }
        #remote-video { width: 100%; border-radius: 8px; background: #000; min-height: 400px; }
        #local-video { position: absolute; bottom: 20px; right: 20px; width: 150px; border-radius: 8px; border: 2px solid white; }
        .video-call-controls { position: absolute; bottom: 80px; left: 50%; transform: translateX(-50%); display: flex; gap: 15px; z-index: 10; }
        .caller-info.hidden { display: none; }
        @media (max-width: 768px) { .users-sidebar { width: 80px; } .users-header, .user-info { display: none; } .user-item { justify-content: center; padding: 12px; } }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo"><i class="fas fa-comments"></i> Chat</div>
        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('user.dashboard') }}" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </header>
    <div class="chat-container">
        <div class="users-sidebar">
            <div class="users-header"><h3>Conversations</h3></div>
            <div class="users-list">
                @foreach($users as $user)
                <div class="user-item" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}" data-user-avatar="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : ($user->avatar ?? '') }}" onclick="selectUser({{ $user->id }})">
                    <div class="user-avatar">
                        @if($user->profile_picture)<img src="{{ asset('storage/' . $user->profile_picture) }}" alt="{{ $user->name }}">
                        @elseif($user->avatar)<img src="{{ $user->avatar }}" alt="{{ $user->name }}">
                        @else {{ strtoupper(substr($user->name, 0, 1)) }} @endif
                    </div>
                    <div class="user-info"><h4>{{ $user->name }}</h4><p>Click to chat</p></div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="chat-area" id="chat-area">
            <div class="no-chat"><i class="fas fa-comments "></i><h3>Select a conversation</h3><p>Choose a user from the left to start chatting</p></div>
        </div>
    </div>
    <div class="call-modal" id="call-modal">
        <div class="caller-info" id="caller-info">
            <div class="caller-avatar" id="caller-avatar"></div>
            <div class="caller-name" id="caller-name"></div>
            <div class="call-status" id="call-status">Calling...</div>
        </div>
        <div id="video-container">
            <video id="remote-video" autoplay playsinline></video>
            <video id="local-video" autoplay playsinline muted></video>
            <div class="video-call-controls" id="video-call-controls" style="display:none;">
                <button class="call-action-btn mute" id="mute-btn" onclick="toggleMute()"><i class="fas fa-microphone"></i></button>
                <button class="call-action-btn switch-cam" id="switch-cam-btn" onclick="switchCamera()"><i class="fas fa-sync-alt"></i></button>
                <button class="call-action-btn end" onclick="endCall()"><i class="fas fa-phone-slash"></i></button>
            </div>
        </div>
        <audio id="remote-audio" autoplay></audio>
        <div class="call-actions" id="call-actions">
            <button class="call-action-btn accept" onclick="acceptCall()"><i class="fas fa-phone"></i></button>
            <button class="call-action-btn reject" onclick="rejectCall()"><i class="fas fa-phone-slash"></i></button>
        </div>
        <div class="call-actions" id="ongoing-call-actions" style="display:none;">
            <button class="call-action-btn mute" id="audio-mute-btn" onclick="toggleMute()"><i class="fas fa-microphone"></i></button>
            <button class="call-action-btn end" onclick="endCall()"><i class="fas fa-phone-slash"></i></button>
        </div>
    </div>
    <input type="file" id="file-input" accept="image/*" style="display:none" onchange="handleFileSelect(event)">

<script>
const currentUserId = {{ auth()->id() }};
let selectedUserId = null, selectedUserName = '', selectedUserAvatar = '', selectedFile = null;
let pusher, chatChannel, callChannel, peerConnection = null, localStream = null;
let currentCallType = 'audio', isCallIncoming = false, callActive = false, isMuted = false;
let currentFacingMode = 'user'; // 'user' for front camera, 'environment' for back camera
const emojis = ['😀','😂','😍','🥰','😎','🤔','😢','😡','👍','👎','❤️','🔥','🎉','👏','🙏','💪','✨','🌟','💯','🤝','👋','🎁','🌈','☀️','🌙','⭐','💬','📷','🎵','🎮'];

Pusher.logToConsole = true;
pusher = new Pusher('0e82200317882bf6c2c8', {
    cluster: 'ap2', forceTLS: true, authEndpoint: '/broadcasting/auth',
    auth: { headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } }
});

chatChannel = pusher.subscribe('private-chat.' + currentUserId);
chatChannel.bind('App\\Events\\MessageSent', function(data) {
    if (selectedUserId === data.sender_id) { appendMessage(data, false); scrollToBottom(); }
});
chatChannel.bind('App\\Events\\MessageRead', function(data) {
    // Update ticks for read messages
    updateMessageTicks(data.message_ids);
});

callChannel = pusher.subscribe('private-call.' + currentUserId);
callChannel.bind('App\\Events\\CallSignal', function(data) { handleCallSignal(data); });

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
            <div class="chat-header-left"><div class="user-avatar">${avatarHtml}</div><h3>${user.name}</h3></div>
            <div class="call-buttons">
                <button class="call-btn audio" onclick="startCall('audio')" title="Audio Call"><i class="fas fa-phone"></i></button>
                <button class="call-btn video" onclick="startCall('video')" title="Video Call"><i class="fas fa-video"></i></button>
            </div>
        </div>
        <div class="messages-area" id="messages-area">
            ${messages.length === 0 ? '<div class="empty-state"><i class="fas fa-comment-dots"></i><p>No messages yet. Say hello!</p></div>' : messages.map(msg => renderMessage(msg)).join('')}
        </div>
        <div class="message-input-area">
            <div class="preview-container" id="preview-container"><img id="preview-image" src=""><button class="remove-preview" onclick="removePreview()"><i class="fas fa-times"></i></button></div>
            <div class="input-actions" style="position:relative;">
                <button class="action-btn" onclick="toggleEmojiPicker()" title="Emoji"><i class="fas fa-smile"></i></button>
                <button class="action-btn" onclick="document.getElementById('file-input').click()" title="Send Photo"><i class="fas fa-image"></i></button>
                <div class="emoji-picker" id="emoji-picker"><div class="emoji-grid">${emojis.map(e => `<span class="emoji-item" onclick="insertEmoji('${e}')">${e}</span>`).join('')}</div></div>
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
    } else { content = escapeHtml(msg.message); }
    
    let tickHtml = '';
    if (msg.is_mine) {
        if (msg.is_read) {
            tickHtml = '<span class="tick read"><i class="fas fa-check-double"></i></span>';
        } else {
            tickHtml = '<span class="tick sent"><i class="fas fa-check"></i></span>';
        }
    }
    
    return `<div class="message ${msg.is_mine ? 'sent' : 'received'}" data-message-id="${msg.id}"><div class="bubble">${content}</div><span class="time">${msg.created_at} ${tickHtml}</span></div>`;
}

function sendMessage() {
    const input = document.getElementById('message-input');
    const message = input.value.trim();
    if (!selectedUserId || (!message && !selectedFile)) return;
    const formData = new FormData();
    formData.append('receiver_id', selectedUserId);
    if (message) formData.append('message', message);
    if (selectedFile) formData.append('attachment', selectedFile);
    input.value = ''; removePreview();
    fetch('/chat/send', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: formData })
    .then(res => res.json()).then(data => {
        if (data.success) { 
            appendMessage({ 
                id: data.message.id,
                message: data.message.message, 
                attachment: data.message.attachment, 
                attachment_type: data.message.attachment_type, 
                created_at: data.message.created_at, 
                is_mine: true,
                is_read: false 
            }, true); 
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
    } else { content = escapeHtml(data.message || ''); }
    
    let tickHtml = '';
    if (isMine) {
        if (data.is_read) {
            tickHtml = '<span class="tick read"><i class="fas fa-check-double"></i></span>';
        } else {
            tickHtml = '<span class="tick sent"><i class="fas fa-check"></i></span>';
        }
    }
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${isMine ? 'sent' : 'received'}`;
    messageDiv.dataset.messageId = data.id;
    messageDiv.innerHTML = `<div class="bubble">${content}</div><span class="time">${data.created_at} ${tickHtml}</span>`;
    messagesArea.appendChild(messageDiv);
    
    // If received message, mark as read
    if (!isMine && data.id) {
        markMessagesAsRead([data.id], data.sender_id);
    }
}

function markMessagesAsRead(messageIds, senderId) {
    fetch('/chat/mark-read', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
        },
        body: JSON.stringify({ message_ids: messageIds, sender_id: senderId })
    });
}

function updateMessageTicks(messageIds) {
    messageIds.forEach(id => {
        const msgEl = document.querySelector(`[data-message-id="${id}"]`);
        if (msgEl) {
            const tickEl = msgEl.querySelector('.tick');
            if (tickEl) {
                tickEl.className = 'tick read';
                tickEl.innerHTML = '<i class="fas fa-check-double"></i>';
            }
        }
    });
}

function toggleEmojiPicker() { document.getElementById('emoji-picker').classList.toggle('show'); }
function insertEmoji(emoji) { document.getElementById('message-input').value += emoji; document.getElementById('message-input').focus(); document.getElementById('emoji-picker').classList.remove('show'); }
function handleFileSelect(event) {
    const file = event.target.files[0];
    if (file && file.type.startsWith('image/')) {
        selectedFile = file;
        const reader = new FileReader();
        reader.onload = function(e) { document.getElementById('preview-image').src = e.target.result; document.getElementById('preview-container').classList.add('show'); };
        reader.readAsDataURL(file);
    }
    event.target.value = '';
}
function removePreview() { selectedFile = null; document.getElementById('preview-container').classList.remove('show'); document.getElementById('preview-image').src = ''; }
function scrollToBottom() { const m = document.getElementById('messages-area'); if (m) m.scrollTop = m.scrollHeight; }
function handleKeyPress(event) { if (event.key === 'Enter') sendMessage(); }
function escapeHtml(text) { if (!text) return ''; const div = document.createElement('div'); div.textContent = text; return div.innerHTML; }
document.addEventListener('click', function(e) { if (!e.target.closest('.input-actions')) document.getElementById('emoji-picker')?.classList.remove('show'); });
</script>

<script>
// WebRTC Call Functions
const iceServers = { 
    iceServers: [
        { urls: 'stun:stun.l.google.com:19302' },
        { urls: 'stun:stun1.l.google.com:19302' },
        { urls: 'stun:stun2.l.google.com:19302' },
        { urls: 'stun:stun3.l.google.com:19302' }
    ]
};

let pendingIceCandidates = [];
let remoteDescriptionSet = false;

// Base64 encode/decode for SDP to prevent corruption
function encodeSdp(sdp) {
    return btoa(unescape(encodeURIComponent(sdp)));
}

function decodeSdp(encoded) {
    return decodeURIComponent(escape(atob(encoded)));
}

function sendIceCandidate(candidate, receiverId) {
    const serializedCandidate = {
        candidate: candidate.candidate,
        sdpMid: candidate.sdpMid,
        sdpMLineIndex: candidate.sdpMLineIndex
    };
    sendSignalTo(receiverId, 'ice-candidate', serializedCandidate);
}

async function startCall(type) {
    if (!selectedUserId || callActive) return;
    currentCallType = type;
    isCallIncoming = false;
    callActive = true;
    remoteDescriptionSet = false;
    pendingIceCandidates = [];
    
    showCallModal(selectedUserName, selectedUserAvatar, 'Calling...');
    document.getElementById('call-actions').style.display = 'none';
    document.getElementById('ongoing-call-actions').style.display = 'flex';
    
    try {
        localStream = await navigator.mediaDevices.getUserMedia({ audio: true, video: type === 'video' });
        console.log('Got local stream for call');
        
        if (type === 'video') {
            document.getElementById('local-video').srcObject = localStream;
            document.getElementById('video-container').classList.add('show');
            document.getElementById('caller-info').classList.add('hidden');
            document.getElementById('video-call-controls').style.display = 'flex';
            document.getElementById('ongoing-call-actions').style.display = 'none';
        }
        
        createPeerConnection(selectedUserId);
        localStream.getTracks().forEach(track => {
            peerConnection.addTrack(track, localStream);
            console.log('Added local track:', track.kind);
        });
        
        const offer = await peerConnection.createOffer();
        await peerConnection.setLocalDescription(offer);
        console.log('Created and set local offer');
        
        // Encode SDP to prevent corruption during transmission
        sendSignal('offer', { type: offer.type, sdp: encodeSdp(offer.sdp) }, type);
    } catch (err) {
        console.error('Error starting call:', err);
        callActive = false;
        hideCallModal();
        alert('Could not access camera/microphone. Please allow permission.');
    }
}

function createPeerConnection(targetUserId) {
    peerConnection = new RTCPeerConnection(iceServers);
    console.log('Created new RTCPeerConnection');
    
    peerConnection.onicecandidate = (event) => {
        if (event.candidate && callActive) {
            console.log('Got ICE candidate, sending...');
            sendIceCandidate(event.candidate, targetUserId);
        }
    };
    
    peerConnection.ontrack = (event) => {
        console.log('Received remote track:', event.track.kind, 'streams:', event.streams.length);
        const stream = event.streams[0];
        if (currentCallType === 'video') {
            document.getElementById('remote-video').srcObject = stream;
            showVideoCallUI();
        } else {
            const audioEl = document.getElementById('remote-audio');
            audioEl.srcObject = stream;
            audioEl.play().then(() => console.log('Audio playing')).catch(e => console.log('Audio play error:', e));
        }
        document.getElementById('call-status').textContent = 'Connected';
    };

    peerConnection.oniceconnectionstatechange = () => {
        if (!peerConnection) return;
        console.log('ICE connection state:', peerConnection.iceConnectionState);
        const state = peerConnection.iceConnectionState;
        if (state === 'connected' || state === 'completed') {
            document.getElementById('call-status').textContent = 'Connected';
        } else if (state === 'failed') {
            document.getElementById('call-status').textContent = 'Connection failed';
            setTimeout(cleanupCall, 2000);
        } else if (state === 'disconnected') {
            document.getElementById('call-status').textContent = 'Disconnected';
        }
    };

    peerConnection.onconnectionstatechange = () => {
        if (!peerConnection) return;
        console.log('Connection state:', peerConnection.connectionState);
    };
    
    peerConnection.onsignalingstatechange = () => {
        if (!peerConnection) return;
        console.log('Signaling state:', peerConnection.signalingState);
    };
}

function handleCallSignal(data) {
    console.log('Received signal:', data.type);
    
    if (data.type === 'offer') {
        isCallIncoming = true;
        callActive = true;
        remoteDescriptionSet = false;
        pendingIceCandidates = [];
        currentCallType = data.call_type || 'audio';
        showCallModal(data.caller_name, data.caller_avatar, `Incoming ${currentCallType} call...`);
        document.getElementById('call-actions').style.display = 'flex';
        document.getElementById('ongoing-call-actions').style.display = 'none';
        // Store offer with encoded SDP
        window.incomingOffer = data.data;
        window.callerId = data.caller_id;
    } else if (data.type === 'answer' && peerConnection) {
        console.log('Received answer, setting remote description');
        try {
            // Decode the SDP
            const decodedSdp = decodeSdp(data.data.sdp);
            const sdp = new RTCSessionDescription({ type: data.data.type, sdp: decodedSdp });
            peerConnection.setRemoteDescription(sdp).then(() => {
                console.log('Remote description set (answer)');
                remoteDescriptionSet = true;
                processPendingIceCandidates();
            }).catch(e => console.error('Error setting remote description:', e));
        } catch (e) {
            console.error('Error decoding answer SDP:', e);
        }
    } else if (data.type === 'ice-candidate') {
        if (peerConnection && remoteDescriptionSet) {
            console.log('Adding ICE candidate');
            const iceCandidate = new RTCIceCandidate(data.data);
            peerConnection.addIceCandidate(iceCandidate).catch(e => console.error('Error adding ICE candidate:', e));
        } else {
            console.log('Buffering ICE candidate');
            pendingIceCandidates.push(data.data);
        }
    } else if (data.type === 'end-call') {
        console.log('Call ended by remote');
        cleanupCall();
    }
}

function processPendingIceCandidates() {
    console.log('Processing', pendingIceCandidates.length, 'pending ICE candidates');
    pendingIceCandidates.forEach(candidate => {
        if (peerConnection && candidate) {
            const iceCandidate = new RTCIceCandidate(candidate);
            peerConnection.addIceCandidate(iceCandidate).catch(e => console.error('Error adding buffered ICE:', e));
        }
    });
    pendingIceCandidates = [];
}

async function acceptCall() {
    console.log('Accepting call...');
    document.getElementById('call-actions').style.display = 'none';
    document.getElementById('ongoing-call-actions').style.display = 'flex';
    document.getElementById('call-status').textContent = 'Connecting...';
    
    try {
        if (!window.incomingOffer || !window.incomingOffer.sdp) {
            throw new Error('No offer received');
        }
        
        console.log('Getting user media...');
        localStream = await navigator.mediaDevices.getUserMedia({ audio: true, video: currentCallType === 'video' });
        console.log('Got local stream');
        
        if (currentCallType === 'video') {
            document.getElementById('local-video').srcObject = localStream;
            showVideoCallUI();
        }
        
        createPeerConnection(window.callerId);
        
        localStream.getTracks().forEach(track => {
            peerConnection.addTrack(track, localStream);
            console.log('Added local track:', track.kind);
        });
        
        console.log('Setting remote description (offer)...');
        // Decode the SDP from base64
        const decodedSdp = decodeSdp(window.incomingOffer.sdp);
        const offerSdp = new RTCSessionDescription({ 
            type: window.incomingOffer.type || 'offer', 
            sdp: decodedSdp 
        });
        await peerConnection.setRemoteDescription(offerSdp);
        console.log('Remote description set');
        remoteDescriptionSet = true;
        
        processPendingIceCandidates();
        
        console.log('Creating answer...');
        const answer = await peerConnection.createAnswer();
        await peerConnection.setLocalDescription(answer);
        console.log('Local description set, sending answer...');
        
        // Encode answer SDP
        sendSignalTo(window.callerId, 'answer', { type: answer.type, sdp: encodeSdp(answer.sdp) });
        document.getElementById('call-status').textContent = 'Connecting...';
        
    } catch (err) {
        console.error('Error accepting call:', err);
        document.getElementById('call-status').textContent = 'Error: ' + err.message;
        setTimeout(cleanupCall, 3000);
    }
}

function rejectCall() {
    sendSignalTo(window.callerId, 'end-call', null);
    cleanupCall();
}

function endCall() {
    const receiverId = isCallIncoming ? window.callerId : selectedUserId;
    if (receiverId) sendSignalTo(receiverId, 'end-call', null);
    cleanupCall();
}

function cleanupCall() {
    console.log('Cleaning up call...');
    callActive = false;
    remoteDescriptionSet = false;
    pendingIceCandidates = [];
    if (peerConnection) { 
        peerConnection.close(); 
        peerConnection = null; 
    }
    if (localStream) { 
        localStream.getTracks().forEach(track => track.stop()); 
        localStream = null; 
    }
    hideCallModal();
}

function showCallModal(name, avatar, status) {
    document.getElementById('caller-name').textContent = name || 'Unknown';
    document.getElementById('call-status').textContent = status;
    document.getElementById('caller-avatar').innerHTML = avatar ? `<img src="${avatar}">` : (name ? name.charAt(0).toUpperCase() : '?');
    document.getElementById('call-modal').classList.add('show');
    document.getElementById('caller-info').classList.remove('hidden');
    document.getElementById('video-call-controls').style.display = 'none';
    isMuted = false;
    updateMuteButtons();
}

function showVideoCallUI() {
    document.getElementById('caller-info').classList.add('hidden');
    document.getElementById('video-container').classList.add('show');
    document.getElementById('video-call-controls').style.display = 'flex';
    document.getElementById('ongoing-call-actions').style.display = 'none';
}

function hideCallModal() {
    document.getElementById('call-modal').classList.remove('show');
    document.getElementById('video-container').classList.remove('show');
    document.getElementById('caller-info').classList.remove('hidden');
    document.getElementById('video-call-controls').style.display = 'none';
    document.getElementById('remote-video').srcObject = null;
    document.getElementById('local-video').srcObject = null;
    document.getElementById('remote-audio').srcObject = null;
    isMuted = false;
}

function toggleMute() {
    if (!localStream) return;
    isMuted = !isMuted;
    localStream.getAudioTracks().forEach(track => track.enabled = !isMuted);
    updateMuteButtons();
}

function updateMuteButtons() {
    const muteBtn = document.getElementById('mute-btn');
    const audioMuteBtn = document.getElementById('audio-mute-btn');
    const icon = isMuted ? '<i class="fas fa-microphone-slash"></i>' : '<i class="fas fa-microphone"></i>';
    if (muteBtn) {
        muteBtn.innerHTML = icon;
        muteBtn.classList.toggle('muted', isMuted);
    }
    if (audioMuteBtn) {
        audioMuteBtn.innerHTML = icon;
        audioMuteBtn.classList.toggle('muted', isMuted);
    }
}

async function switchCamera() {
    if (!localStream || currentCallType !== 'video') return;
    
    try {
        // Toggle facing mode
        currentFacingMode = currentFacingMode === 'user' ? 'environment' : 'user';
        
        // Get new video stream with different camera
        const newStream = await navigator.mediaDevices.getUserMedia({
            audio: true,
            video: { facingMode: currentFacingMode }
        });
        
        // Get the new video track
        const newVideoTrack = newStream.getVideoTracks()[0];
        const newAudioTrack = newStream.getAudioTracks()[0];
        
        // Replace video track in peer connection
        if (peerConnection) {
            const senders = peerConnection.getSenders();
            const videoSender = senders.find(s => s.track && s.track.kind === 'video');
            if (videoSender) {
                await videoSender.replaceTrack(newVideoTrack);
            }
        }
        
        // Stop old video track
        localStream.getVideoTracks().forEach(track => track.stop());
        
        // Update local stream
        localStream = newStream;
        
        // Apply mute state to new audio track
        newAudioTrack.enabled = !isMuted;
        
        // Update local video preview
        document.getElementById('local-video').srcObject = localStream;
        
        console.log('Camera switched to:', currentFacingMode);
    } catch (err) {
        console.error('Error switching camera:', err);
        // Revert facing mode if failed
        currentFacingMode = currentFacingMode === 'user' ? 'environment' : 'user';
    }
}

function sendSignal(type, data, callType) { sendSignalTo(selectedUserId, type, data, callType); }

function sendSignalTo(receiverId, type, data, callType) {
    console.log('Sending signal:', type, 'to:', receiverId);
    const payload = JSON.stringify({ 
        receiver_id: receiverId, 
        type: type, 
        data: data, 
        call_type: callType || currentCallType 
    });
    
    // Use sendBeacon for end-call signal (faster, works even on page close)
    if (type === 'end-call') {
        const blob = new Blob([payload], { type: 'application/json' });
        navigator.sendBeacon('/call/signal?_token=' + document.querySelector('meta[name="csrf-token"]').content, blob);
        return;
    }
    
    fetch('/call/signal', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: payload
    }).then(res => res.json()).then(data => console.log('Signal sent:', data)).catch(e => console.error('Signal error:', e));
}

// Handle page close/refresh - end call properly
window.addEventListener('beforeunload', function() {
    if (callActive) {
        const receiverId = isCallIncoming ? window.callerId : selectedUserId;
        if (receiverId) {
            const payload = JSON.stringify({ 
                receiver_id: receiverId, 
                type: 'end-call', 
                data: null, 
                call_type: currentCallType 
            });
            const blob = new Blob([payload], { type: 'application/json' });
            navigator.sendBeacon('/call/signal?_token=' + document.querySelector('meta[name="csrf-token"]').content, blob);
        }
    }
});
</script>
</body>
</html>