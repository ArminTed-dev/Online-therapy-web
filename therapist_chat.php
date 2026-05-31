<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['type_user'] != 2) {
    header("Location: login.php");
    exit;
}
$tid = $_SESSION['user_id'];
$tname = $_SESSION['fullname'];

$clients = $pdo->prepare("SELECT DISTINCT 
    CASE WHEN from_user=? THEN to_user ELSE from_user END as cid,
    u.fullname_user, u.last_activity,
    TIMESTAMPDIFF(MINUTE, u.last_activity, NOW())<2 as online,
    (SELECT COUNT(*) FROM chat_messages WHERE to_user=? AND from_user=cid AND is_read=0) as unread
    FROM chat_messages 
    JOIN tb_users u ON u.id = (CASE WHEN from_user=? THEN to_user ELSE from_user END)
    WHERE from_user=? OR to_user=?
    GROUP BY cid ORDER BY MAX(created_at) DESC");
$clients->execute([$tid,$tid,$tid,$tid,$tid]);
$clients = $clients->fetchAll();
$online=0; $unread=0; foreach($clients as $c){ if($c['online']) $online++; $unread+=$c['unread']; }
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پنل مشاور</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Vazirmatn';background:linear-gradient(135deg,#1e1b4b,#2e1a6b);padding:20px}
        .panel{max-width:1400px;margin:0 auto;background:rgba(255,255,255,0.1);backdrop-filter:blur(12px);border-radius:48px;overflow:hidden;display:flex;flex-direction:column;height:90vh}
        .top{background:rgba(79,70,229,0.3);padding:15px 25px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap}
        .stats{display:flex;gap:20px}
        .stat{background:rgba(255,255,255,0.15);border-radius:30px;padding:5px 18px;text-align:center;color:white}
        .stat-num{font-size:1.5rem;font-weight:bold}
        .workspace{display:flex;flex:1;overflow:hidden}
        .clients{width:300px;background:rgba(0,0,0,0.2);overflow-y:auto;border-left:1px solid rgba(255,255,255,0.1)}
        .clients h4{padding:15px;color:white;border-bottom:1px solid rgba(255,255,255,0.1)}
        .client-item{display:flex;align-items:center;gap:12px;padding:12px;margin:8px;background:rgba(255,255,255,0.05);border-radius:30px;cursor:pointer}
        .client-item.active{background:rgba(255,255,255,0.2)}
        .client-avatar{width:45px;height:45px;background:#818cf8;border-radius:30px;display:flex;align-items:center;justify-content:center;position:relative}
        .online-dot{width:10px;height:10px;background:#22c55e;border-radius:50%;position:absolute;bottom:0;right:0;border:2px solid #1e1b4b}
        .offline{background:#94a3b8}
        .chat{flex:1;display:flex;flex-direction:column}
        .chat-top{padding:12px 20px;background:rgba(0,0,0,0.2);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap}
        .quick{display:flex;gap:8px}
        .quick button{background:rgba(255,255,255,0.2);border:none;padding:5px 12px;border-radius:30px;color:white;cursor:pointer}
        .messages{flex:1;overflow-y:auto;padding:20px;display:flex;flex-direction:column;gap:10px}
        .message{max-width:70%;padding:8px 15px;border-radius:25px}
        .client-msg{background:linear-gradient(135deg,#4f46e5,#7c3aed);color:white;align-self:flex-start;border-bottom-left-radius:5px}
        .therapist-msg{background:white;color:#1e293b;align-self:flex-end;border-bottom-right-radius:5px}
        .message img{max-width:150px;border-radius:12px;margin-top:5px;cursor:pointer}
        .input-area{padding:15px;background:rgba(0,0,0,0.2);display:flex;gap:10px;border-top:1px solid rgba(255,255,255,0.1)}
        .input-area input{flex:1;padding:12px;border-radius:40px;border:none;background:white}
        .input-area button{background:#4f46e5;border:none;padding:0 20px;border-radius:40px;color:white;cursor:pointer}
        .refresh-btn{background:#4f46e5;border:none;border-radius:30px;padding:5px 12px;color:white;cursor:pointer}
        @media (max-width:700px){ .clients{width:90px} .client-name{display:none} }
    </style>
</head>
<body>
<div class="panel">
    <div class="top">
        <div style="display:flex; align-items:center; gap:12px; color:white"><i class="fas fa-user-md fa-2x"></i><div><h2><?=htmlspecialchars($tname)?></h2><p>روانشناس بالینی</p></div></div>
        <div class="stats"><div class="stat"><div class="stat-num" id="onlineCount"><?=$online?></div><div>آنلاین</div></div><div class="stat"><div class="stat-num" id="unreadCount"><?=$unread?></div><div>جدید</div></div></div>
    </div>
    <div class="workspace">
        <div class="clients"><h4><i class="fas fa-users"></i> مراجعین</h4><div id="clientsList"></div></div>
        <div class="chat">
            <div class="chat-top">
                <div><h4 id="selectedName">انتخاب کنید</h4><span id="onlineStatus" style="font-size:0.7rem"></span></div>
                <div style="display:flex; gap:10px; align-items:center">
                    <button id="refreshChatBtn" class="refresh-btn"><i class="fas fa-sync-alt"></i> بروز</button>
                    <div class="quick">
                        <button data-reply="در حال بررسی پیام شما هستم، لطفاً صبور باشید.">⏳ بررسی</button>
                        <button data-reply="یک تمرین تنفس عمیق انجام دهید و بعد به من بگویید چه حسی دارید.">🧘 تنفس</button>
                        <button data-reply="برای شما وقت جلسه ویدیویی تعیین کنم؟">🎥 جلسه</button>
                    </div>
                </div>
            </div>
            <div class="messages" id="chatMessages"></div>
            <div class="input-area">
                <input type="text" id="msgInput" placeholder="پاسخ خود را بنویسید...">
                <button id="sendMsgBtn"><i class="fas fa-paper-plane"></i> ارسال</button>
            </div>
        </div>
    </div>
</div>
<script>
const tid = <?=json_encode($tid)?>;
let current = null, lastId = 0, poll = null;
const clients = <?=json_encode($clients)?>;
const clientsDiv = document.getElementById('clientsList'), msgsDiv = document.getElementById('chatMessages'), selectedName = document.getElementById('selectedName'), onlineStatus = document.getElementById('onlineStatus'), msgInput = document.getElementById('msgInput'), sendBtn = document.getElementById('sendMsgBtn'), onlineSpan = document.getElementById('onlineCount'), unreadSpan = document.getElementById('unreadCount');

function escapeHtml(s){ return s ? s.replace(/[&<>]/g,m=>m==='&'?'&amp;':m==='<'?'&lt;':'&gt;') : ''; }
function formatTime(t){ return new Date(t).toLocaleTimeString('fa-IR', {hour:'2-digit', minute:'2-digit'}); }
function scrollDown(){ msgsDiv.scrollTop = msgsDiv.scrollHeight; }

function loadMessages(){
    if(!current) return;
    fetch(`get_messages.php?with=${current.id}&last_id=${lastId}&_=${Date.now()}`)
        .then(r=>r.json())
        .then(data=>{
            if(data.length===0) return;
            data.forEach(m=>{
                const isMe = (parseInt(m.from_user) === parseInt(tid));
                const div = document.createElement('div');
                div.className = `message ${isMe ? 'therapist-msg' : 'client-msg'}`;
                div.innerHTML = `<div>${escapeHtml(m.message)}</div>${m.image_path ? `<img src="${m.image_path}" onclick="window.open(this.src)">` : ''}<div class="time" style="font-size:0.6rem; margin-top:5px; opacity:0.7">${formatTime(m.created_at)}</div>`;
                msgsDiv.appendChild(div);
                if(parseInt(m.id) > lastId) lastId = parseInt(m.id);
            });
            scrollDown();
        }).catch(console.error);
}

function startPolling(){ if(poll) clearInterval(poll); poll = setInterval(()=>{ if(current) loadMessages(); }, 3000); }

function sendMessage(){
    if(!current) return;
    const text = msgInput.value.trim();
    if(!text) return;
    const fd = new FormData();
    fd.append('to_user', current.id);
    fd.append('message', text);
    fetch('send_message.php', { method:'POST', body:fd }).then(()=>{ msgInput.value=''; loadMessages(); updateStats(); }).catch(console.error);
}

function selectClient(c){
    if(poll) clearInterval(poll);
    current = { id: c.cid, name: c.fullname_user };
    lastId = 0;
    msgsDiv.innerHTML = '';
    selectedName.innerText = escapeHtml(c.fullname_user);
    onlineStatus.innerText = c.online ? '🟢 آنلاین' : '⚫ آفلاین';
    onlineStatus.style.color = c.online ? '#22c55e' : '#aaa';
    loadMessages(); startPolling();
    document.querySelectorAll('.client-item').forEach(el=>el.classList.remove('active'));
    const activeEl = document.querySelector(`.client-item[data-id='${c.cid}']`);
    if(activeEl) activeEl.classList.add('active');
}

function renderClients(){
    clientsDiv.innerHTML = '';
    if(clients.length===0){ clientsDiv.innerHTML = '<div style="padding:15px; color:#aaa">هیچ مراجعه‌کننده‌ای نیست</div>'; return; }
    clients.forEach(c=>{
        const div = document.createElement('div');
        div.className = 'client-item';
        div.setAttribute('data-id', c.cid);
        div.innerHTML = `<div class="client-avatar"><i class="fas fa-user"></i><div class="online-dot ${c.online?'':'offline'}"></div></div><div class="client-name" style="color:white">${escapeHtml(c.fullname_user)}</div>${c.unread?`<span style="background:#ef4444; border-radius:20px; padding:2px 8px; font-size:0.7rem; color:white; margin-left:8px;">${c.unread}</span>`:''}`;
        div.onclick = () => selectClient(c);
        clientsDiv.appendChild(div);
    });
}

function updateStats(){
    fetch('get_unread_stats.php').then(r=>r.json()).then(d=>{ if(d.online!==undefined) onlineSpan.innerText=d.online; if(d.unread!==undefined) unreadSpan.innerText=d.unread; }).catch(()=>{});
}

sendBtn.addEventListener('click', sendMessage);
msgInput.addEventListener('keypress', e=>{ if(e.key==='Enter') sendMessage(); });
document.getElementById('refreshChatBtn').addEventListener('click', ()=>{ if(current) loadMessages(); else alert('ابتدا یک مراجعه‌کننده انتخاب کنید'); });
document.querySelectorAll('.quick button').forEach(btn=>{
    btn.addEventListener('click', ()=>{
        const reply = btn.getAttribute('data-reply');
        if(reply && current){ msgInput.value = reply; sendMessage(); }
    });
});
renderClients(); updateStats(); setInterval(()=>{ if(current) loadMessages(); updateStats(); }, 4000);
</script>
</body>
</html>