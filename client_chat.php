<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['type_user'] != 0) {
    header("Location: login.php");
    exit;
}
$client_id = $_SESSION['user_id'];
$therapists = $pdo->query("SELECT id, fullname_user, specialty, last_activity, TIMESTAMPDIFF(MINUTE, last_activity, NOW()) < 2 AS online FROM tb_users WHERE type_user=2 ORDER BY fullname_user")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>چت با مشاور</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Vazirmatn';background:linear-gradient(135deg,#e0eafc,#cfdef3);padding:20px}
        .flex{display:flex;flex-wrap:wrap;gap:20px;max-width:1400px;margin:0 auto}
        .sidebar{flex:1.2;min-width:260px;background:rgba(255,255,255,0.3);backdrop-filter:blur(12px);border-radius:40px;padding:20px}
        .sidebar h3{color:#1e1b4b;margin-bottom:20px}
        .therapist-card{display:flex;align-items:center;gap:15px;padding:12px;margin-bottom:12px;background:rgba(255,255,255,0.6);border-radius:30px;cursor:pointer;border:1px solid transparent}
        .therapist-card.active{background:white;border-color:#4f46e5}
        .avatar{width:50px;height:50px;background:linear-gradient(145deg,#818cf8,#c084fc);border-radius:30px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:white;position:relative}
        .online-dot{position:absolute;bottom:0;right:0;width:12px;height:12px;background:#22c55e;border-radius:50%;border:2px solid white}
        .offline{background:#94a3b8}
        .chat-area{flex:3;min-width:300px;background:rgba(255,255,255,0.3);backdrop-filter:blur(12px);border-radius:40px;display:flex;flex-direction:column;overflow:hidden}
        .chat-header{background:rgba(79,70,229,0.2);padding:15px 20px;display:flex;align-items:center;gap:12px;border-bottom:1px solid rgba(255,255,255,0.3)}
        .messages{flex:1;overflow-y:auto;padding:15px;display:flex;flex-direction:column;gap:10px;background:rgba(0,0,0,0.02)}
        .message{max-width:75%;padding:8px 15px;border-radius:25px;word-wrap:break-word}
        .client{background:#4f46e5;color:white;align-self:flex-end;border-bottom-right-radius:5px}
        .therapist{background:white;color:#1e293b;align-self:flex-start;border-bottom-left-radius:5px}
        .message img{max-width:150px;border-radius:12px;margin-top:5px;cursor:pointer}
        .time{font-size:0.6rem;margin-top:5px;opacity:0.7}
        .input-area{padding:15px;background:rgba(255,255,255,0.2);display:flex;gap:10px;border-top:1px solid rgba(255,255,255,0.3)}
        .input-area input{flex:1;padding:12px 18px;border:none;border-radius:40px;background:white;font-family:inherit}
        .input-area button, .file-label{background:#4f46e5;border:none;width:45px;border-radius:30px;color:white;cursor:pointer;display:inline-flex;align-items:center;justify-content:center}
        .refresh-btn{background:#4f46e5;border:none;border-radius:30px;padding:6px 12px;color:white;cursor:pointer;margin-right:auto}
        @media (max-width:700px){.flex{flex-direction:column}.sidebar{flex:auto}}
    </style>
</head>
<body>
<div class="flex">
    <div class="sidebar">
        <h3><i class="fas fa-user-md"></i> مشاوران</h3>
        <div id="therapistsList">
            <?php foreach($therapists as $t): ?>
                <div class="therapist-card" data-id="<?=$t['id']?>" data-name="<?=htmlspecialchars($t['fullname_user'])?>" data-specialty="<?=htmlspecialchars($t['specialty']??'')?>">
                    <div class="avatar"><i class="fas fa-user-circle"></i><div class="online-dot <?=$t['online']?'':'offline'?>"></div></div>
                    <div><h4><?=htmlspecialchars($t['fullname_user'])?></h4><p><?=htmlspecialchars($t['specialty']??'مشاور')?></p></div>
                </div>
            <?php endforeach; ?>
        </div>
        <button id="testBtn" style="margin-top:15px; width:100%; background:#4f46e5; border:none; padding:10px; border-radius:30px; color:white;">انتخاب اولین مشاور</button>
        <button id="refreshGlobalBtn" style="margin-top:10px; width:100%; background:#f59e0b; border:none; padding:10px; border-radius:30px; color:white;"><i class="fas fa-sync-alt"></i> بروزرسانی دستی</button>
    </div>
    <div class="chat-area" id="chatArea" style="display:none">
        <div class="chat-header" id="chatHeader"></div>
        <div class="messages" id="messages"></div>
        <div class="input-area">
            <input type="text" id="msg" placeholder="پیام خود را بنویسید...">
            <label for="img" class="file-label"><i class="fas fa-image"></i></label>
            <input type="file" id="img" accept="image/*" style="display:none">
            <button id="send"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
</div>
<script>
const clientId = <?=json_encode($client_id)?>;
let current = null, lastId = 0, pollInterval = null;
const chatArea = document.getElementById('chatArea'), header = document.getElementById('chatHeader'), msgsDiv = document.getElementById('messages'), msgInp = document.getElementById('msg'), sendBtn = document.getElementById('send'), fileInp = document.getElementById('img');

function escapeHtml(s){ return s ? s.replace(/[&<>]/g,m=>m==='&'?'&amp;':m==='<'?'&lt;':'&gt;') : ''; }
function formatTime(t){ return new Date(t).toLocaleTimeString('fa-IR', {hour:'2-digit', minute:'2-digit'}); }
function scrollDown(){ msgsDiv.scrollTop = msgsDiv.scrollHeight; }

function loadMessages(){
    if(!current) return;
    fetch(`get_messages.php?with=${current.id}&last_id=${lastId}&_=${Date.now()}`)
        .then(res=>res.json())
        .then(data=>{
            if(data.length===0) return;
            data.forEach(m=>{
                const isMe = (parseInt(m.from_user) === parseInt(clientId));
                const div = document.createElement('div');
                div.className = `message ${isMe ? 'client' : 'therapist'}`;
                div.innerHTML = `<div>${escapeHtml(m.message)}</div>${m.image_path ? `<img src="${m.image_path}" onclick="window.open(this.src)">` : ''}<div class="time">${formatTime(m.created_at)}</div>`;
                msgsDiv.appendChild(div);
                if(parseInt(m.id) > lastId) lastId = parseInt(m.id);
            });
            scrollDown();
        }).catch(console.error);
}

function startPolling(){ if(pollInterval) clearInterval(pollInterval); pollInterval = setInterval(()=>{ if(current) loadMessages(); }, 3000); }

function sendMessage(){
    if(!current) return;
    const text = msgInp.value.trim();
    const file = fileInp.files[0];
    if(!text && !file) return;
    const fd = new FormData();
    fd.append('to_user', current.id);
    if(text) fd.append('message', text);
    if(file) fd.append('image', file);
    fetch('send_message.php', { method:'POST', body:fd })
        .then(()=>{ msgInp.value = ''; fileInp.value = ''; loadMessages(); })
        .catch(console.error);
}

function selectTherapist(t){
    if(!t.id) return;
    if(pollInterval) clearInterval(pollInterval);
    current = t; lastId = 0;
    msgsDiv.innerHTML = '';
    header.innerHTML = `<i class="fas fa-user-circle fa-2x"></i><div><h3>${escapeHtml(t.name)}</h3><p style="font-size:0.7rem;">${escapeHtml(t.specialty)}</p></div><button id="refreshInside" class="refresh-btn"><i class="fas fa-sync-alt"></i> بروز</button>`;
    document.getElementById('refreshInside')?.addEventListener('click', ()=>loadMessages());
    chatArea.style.display = 'flex';
    loadMessages(); startPolling();
    document.querySelectorAll('.therapist-card').forEach(c=>c.classList.remove('active'));
    document.querySelector(`.therapist-card[data-id='${t.id}']`)?.classList.add('active');
}

document.querySelectorAll('.therapist-card').forEach(card=>{
    card.addEventListener('click',()=>{
        selectTherapist({id:parseInt(card.dataset.id), name:card.dataset.name, specialty:card.dataset.specialty});
    });
});
document.getElementById('testBtn').addEventListener('click',()=>{
    let first = document.querySelector('.therapist-card');
    if(first) selectTherapist({id:parseInt(first.dataset.id), name:first.dataset.name, specialty:first.dataset.specialty});
    else alert('مشاوری نیست');
});
document.getElementById('refreshGlobalBtn').addEventListener('click',()=>{
    if(current) loadMessages(); else alert('لطفاً ابتدا یک مشاور انتخاب کنید');
});
sendBtn.addEventListener('click', sendMessage);
msgInp.addEventListener('keypress', e=>{ if(e.key==='Enter') sendMessage(); });
fileInp.addEventListener('change', sendMessage);
</script>
</body>
</html>