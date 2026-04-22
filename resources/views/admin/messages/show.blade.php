@extends('admin.layout')

@section('title', 'Chat Details')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=Figtree:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root {
  --bg:#080612;--sur:#100e1e;--sur2:#161328;--sur3:#1d1a30;
  --bdr:rgba(255,255,255,0.07);--bdr2:rgba(255,255,255,0.13);
  --rose:#ff3d7f;--violet:#8b5cf6;--teal:#06d6a0;
  --amber:#ffbe0b;--blue:#3b9eff;--red:#ff4757;
  --txt:#ede9ff;--txt2:rgba(237,233,255,0.48);--txt3:rgba(237,233,255,0.2);
  --r:13px;--rsm:8px;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Figtree',sans-serif;font-size:13px;background:var(--bg);color:var(--txt)}
a{text-decoration:none;color:inherit}
button{cursor:pointer;border:none;background:none;font-family:inherit;color:inherit}
input,select{font-family:inherit}

.up{padding:16px 18px 28px;position:relative}
.up::before,.up::after{content:'';position:fixed;border-radius:50%;pointer-events:none;filter:blur(100px);opacity:.09;z-index:0}
.up::before{width:440px;height:440px;background:radial-gradient(circle,var(--rose),transparent 70%);top:-100px;right:-60px;animation:da 20s ease-in-out infinite alternate}
.up::after{width:360px;height:360px;background:radial-gradient(circle,var(--violet),transparent 70%);bottom:0;left:0;animation:db 25s ease-in-out infinite alternate}
@keyframes da{to{transform:translate(-40px,60px)scale(1.1)}}
@keyframes db{to{transform:translate(60px,-40px)scale(1.15)}}
.up>*{position:relative;z-index:1}

.ph{display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;animation:fu .4s ease both}
.ph-title{font-family:'Syne',sans-serif;font-size:1.15rem;font-weight:800;letter-spacing:-.03em;color:var(--txt)}
.ph-sub{font-size:.7rem;color:var(--txt2);margin-top:2px}
.back-btn{display:inline-flex;align-items:center;gap:6px;background:var(--sur);border:1px solid var(--bdr);border-radius:var(--rsm);padding:6px 13px;font-size:.73rem;font-weight:500;color:var(--txt2);transition:background .18s,color .18s,border-color .18s}
.back-btn:hover{background:var(--sur2);color:var(--txt);border-color:var(--bdr2)}
.back-btn i{font-size:.8rem;color:var(--rose)}

.match-info{background:var(--sur);border:1px solid var(--bdr);border-radius:var(--r);padding:14px 16px;margin-bottom:14px;animation:fu .4s .1s ease both}
.mi-users{display:flex;align-items:center;gap:20px}
.mi-vs{font-size:.8rem;color:var(--txt3);font-weight:700}
.mi-user{display:flex;align-items:center;gap:10px;flex:1;padding:10px 14px;background:var(--sur2);border-radius:var(--rsm)}
.mi-avatar{width:44px;height:44px;border-radius:12px;display:grid;place-items:center;font-family:'Syne',sans-serif;font-size:.9rem;font-weight:700;color:#fff;flex-shrink:0;background:linear-gradient(135deg,var(--c1),var(--c2))}
.mi-details{flex:1;min-width:0}
.mi-name{font-size:.9rem;font-weight:700;color:var(--txt)}
.mi-email{font-size:.68rem;color:var(--txt3);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.mi-meta{font-size:.64rem;color:var(--txt2);margin-top:4px}
.mi-match-date{font-size:.65rem;color:var(--txt3);margin-left:auto}

.chat-container{background:var(--sur);border:1px solid var(--bdr);border-radius:var(--r);overflow:hidden;animation:fu .4s .15s ease both}
.chat-header{padding:12px 16px;border-bottom:1px solid var(--bdr);background:rgba(255,255,255,.01)}
.chat-title{font-family:'Syne',sans-serif;font-size:.85rem;font-weight:700;color:var(--txt)}

.chat-messages{max-height:500px;overflow-y:auto;padding:16px;display:flex;flex-direction:column;gap:12px}
.chat-date-group{text-align:center;margin:8px 0}
.chat-date{font-size:.62rem;color:var(--txt3);background:var(--sur2);padding:4px 10px;border-radius:100px}

.msg{display:flex;flex-direction:column;max-width:70%}
.msg-sent{align-self:flex-end}
.msg-received{align-self:flex-start}
.msg-bubble{padding:10px 14px;border-radius:16px;font-size:.8rem;line-height:1.4;word-wrap:break-word}
.msg-sent .msg-bubble{background:linear-gradient(135deg,var(--rose),var(--violet));color:#fff;border-bottom-right-radius:4px}
.msg-received .msg-bubble{background:var(--sur2);color:var(--txt);border-bottom-left-radius:4px}
.msg-time{font-size:.58rem;color:var(--txt3);margin-top:4px;padding:0 4px}
.msg-sent .msg-time{text-align:right}
.msg-meta{display:flex;align-items:center;gap:6px;justify-content:flex-end}

.msg-image{display:none}
.msg-image img{max-width:200px;max-height:200px;border-radius:12px;margin-top:6px}

.msg.unread .msg-bubble{border:1px solid var(--teal)}

.empty-chat{text-align:center;padding:40px 20px;color:var(--txt3)}
.empty-chat-ico{font-size:2rem;margin-bottom:8px;opacity:.4}

@keyframes fu{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}
*::-webkit-scrollbar{width:4px}
*::-webkit-scrollbar-thumb{background:rgba(255,255,255,.1);border-radius:4px}

@media(max-width:900px){.mi-users{flex-direction:column;gap:10px}.mi-vs{display:none}}
@media(max-width:600px){.up{padding:10px 10px 20px}}
</style>
@endsection

@section('content')
<div class="up">

  {{-- PAGE HEADER --}}
  <div class="ph">
    <div>
      <div class="ph-title">Chat Details</div>
      <div class="ph-sub">Full conversation view</div>
    </div>
    <a href="{{ route('admin.messages.index') }}" class="back-btn">
      <i class="bi bi-arrow-left"></i> Back to Messages
    </a>
  </div>

  {{-- MATCH INFO --}}
  <div class="match-info">
    <div class="mi-users">
      @php
        $colors = [
          ['#ff3d7f','#8b5cf6'],
          ['#06d6a0','#3b9eff'],
          ['#3b9eff','#ec4899'],
          ['#ffbe0b','#ff3d7f'],
          ['#8b5cf6','#06d6a0'],
          ['#ec4899','#ffbe0b'],
          ['#14b8a6','#8b5cf6'],
        ];
        $user1 = $match->userOne;
        $user2 = $match->userTwo;
        $profile1 = $user1?->profile;
        $profile2 = $user2?->profile;
        $color1 = $colors[($user1?->id ?? 0) % 7];
        $color2 = $colors[($user2?->id ?? 1) % 7];
        $initials1 = strtoupper(substr($profile1?->first_name ?? $user1?->email ?? '?', 0, 2));
        $initials2 = strtoupper(substr($profile2?->first_name ?? $user2?->email ?? '?', 0, 2));
        $name1 = $profile1?->first_name ? $profile1->first_name . ' ' . ($profile1->last_name ?? '') : ($user1?->email ?? 'Deleted');
        $name2 = $profile2?->first_name ? $profile2->first_name . ' ' . ($profile2->last_name ?? '') : ($user2?->email ?? 'Deleted');
      @endphp
      <div class="mi-user">
        <div class="mi-avatar" style="--c1:{{ $color1[0] }};--c2:{{ $color1[1] }}">{{ $initials1 }}</div>
        <div class="mi-details">
          <div class="mi-name">{{ $name1 }}</div>
          <div class="mi-email">{{ $user1?->email ?? '—' }}</div>
          <div class="mi-meta">{{ $profile1?->gender ?? '—' }} • {{ $user1?->is_active ? 'Active' : 'Inactive' }}</div>
        </div>
      </div>
      <div class="mi-vs">❤️</div>
      <div class="mi-user">
        <div class="mi-avatar" style="--c1:{{ $color2[0] }};--c2:{{ $color2[1] }}">{{ $initials2 }}</div>
        <div class="mi-details">
          <div class="mi-name">{{ $name2 }}</div>
          <div class="mi-email">{{ $user2?->email ?? '—' }}</div>
          <div class="mi-meta">{{ $profile2?->gender ?? '—' }} • {{ $user2?->is_active ? 'Active' : 'Inactive' }}</div>
        </div>
      </div>
      <div class="mi-match-date">
        Matched: {{ $match->created_at->format('M d, Y') }}
      </div>
    </div>
  </div>

  {{-- CHAT MESSAGES --}}
  <div class="chat-container">
    <div class="chat-header">
      <div class="chat-title">💬 Conversation ({{ $messages->count() }} messages)</div>
    </div>
    <div class="chat-messages" id="chatMessages">
      @if($messages->isEmpty())
        <div class="empty-chat">
          <div class="empty-chat-ico">💭</div>
          <div>No messages yet</div>
        </div>
      @else
        @php $lastDate = null; @endphp
        @foreach($messages as $message)
          @php
            $msgDate = $message->created_at->format('Y-m-d');
            $isSent = $message->sender_id === $user1->id;
          @endphp
          @if($lastDate !== $msgDate)
            <div class="chat-date-group">
              <span class="chat-date">{{ \Carbon\Carbon::parse($msgDate)->format('F d, Y') }}</span>
            </div>
            @php $lastDate = $msgDate; @endphp
          @endif
          <div class="msg {{ $isSent ? 'msg-sent' : 'msg-received' }} {{ !$message->is_read && !$isSent ? 'unread' : '' }}">
            <div class="msg-bubble">{{ $message->message }}</div>
            <div class="msg-meta">
              <span class="msg-time">{{ $message->created_at->format('H:i') }}</span>
              @if($isSent)
                <span style="font-size:.6rem;color:var(--txt3)">
                  {{ $message->is_read ? '✓✓' : '✓' }}
                </span>
              @endif
            </div>
          </div>
        @endforeach
      @endif
    </div>
  </div>

</div>{{-- /up --}}
@endsection