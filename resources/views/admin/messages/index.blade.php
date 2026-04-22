@extends('admin.layout')

@section('title', 'Messages Management')

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

.stat-strip{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:12px;animation:fu .4s .05s ease both}
.ss{background:var(--sur);border:1px solid var(--bdr);border-radius:var(--r);padding:12px 14px;display:flex;align-items:center;gap:12px;position:relative;overflow:hidden;transition:transform .2s,box-shadow .2s}
.ss:hover{transform:translateY(-2px);box-shadow:0 6px 24px rgba(0,0,0,.35),0 0 22px var(--sg,rgba(255,61,127,.1))}
.ss::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,var(--sc1,transparent),transparent 60%);opacity:.07;pointer-events:none}
.ss-blob{position:absolute;width:60px;height:60px;border-radius:50%;background:var(--sc1);filter:blur(24px);opacity:.18;right:-10px;top:-10px;pointer-events:none;transition:opacity .3s}
.ss:hover .ss-blob{opacity:.28}
.ss-ico{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--sc1),var(--sc2));display:grid;place-items:center;font-size:1rem;box-shadow:0 3px 10px rgba(0,0,0,.3);flex-shrink:0}
.ss-val{font-family:'Syne',sans-serif;font-size:1.3rem;font-weight:800;color:#fff;line-height:1;letter-spacing:-.03em}
.ss-lbl{font-size:.68rem;color:var(--txt2);margin-top:2px}
.ss-ct{--sc1:#3b9eff;--sc2:#80c0ff;--sg:rgba(59,158,255,.1)}
.ss-cr{--sc1:#ff3d7f;--sc2:#ff8ab8;--sg:rgba(255,61,127,.12)}
.ss-cg{--sc1:#06d6a0;--sc2:#00ffcc;--sg:rgba(6,214,160,.1)}

.filter-bar{background:var(--sur);border:1px solid var(--bdr);border-radius:var(--r);padding:11px 14px;margin-bottom:10px;animation:fu .4s .1s ease both}
.filter-form{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.fi{background:var(--sur2);border:1px solid var(--bdr);border-radius:var(--rsm);padding:6px 10px;color:var(--txt);font-size:.74rem;outline:none;transition:border-color .18s,background .18s;-webkit-appearance:none;appearance:none}
.fi:focus{border-color:rgba(255,61,127,.4);background:var(--sur3)}
.fi::placeholder{color:var(--txt3)}
.fi-search{flex:1;min-width:170px;padding-left:30px;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='13' height='13' fill='rgba(237,233,255,0.28)' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1 0 0 0-.115-.099zm-5.44 1.398a5.5 6.5 0 0 1 0-11 5.5 6.5 0 0 1 0 11z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:10px center}
.btn-filter{display:inline-flex;align-items:center;gap:5px;background:linear-gradient(135deg,var(--rose),var(--violet));border-radius:var(--rsm);padding:6px 13px;font-size:.73rem;font-weight:600;color:#fff;box-shadow:0 3px 12px rgba(255,61,127,.28);transition:opacity .18s,transform .18s}
.btn-filter:hover{opacity:.88;transform:translateY(-1px)}

.results-meta{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;animation:fu .4s .14s ease both}
.results-count{font-size:.7rem;color:var(--txt2)}
.results-count strong{color:var(--txt);font-weight:600}

.tcard{background:var(--sur);border:1px solid var(--bdr);border-radius:var(--r);overflow:hidden;animation:fu .45s .18s ease both}
.dt{width:100%;border-collapse:collapse}
.dt thead th{padding:9px 13px;text-align:left;font-family:'Syne',sans-serif;font-size:.59rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--txt3);background:rgba(255,255,255,.02);border-bottom:1px solid var(--bdr);white-space:nowrap}
.dt tbody tr{border-bottom:1px solid rgba(255,255,255,.033);transition:background .12s}
.dt tbody tr:last-child{border-bottom:none}
.dt tbody tr:hover{background:rgba(255,255,255,.032)}
.dt td{padding:9px 13px;color:var(--txt);vertical-align:middle;font-size:.74rem}
.dt td.m{color:var(--txt2);font-size:.69rem}

.uc{display:flex;align-items:center;gap:9px}
.u-av{width:40px;height:40px;border-radius:10px;display:grid;place-items:center;font-family:'Syne',sans-serif;font-size:.72rem;font-weight:700;color:#fff;flex-shrink:0;background:linear-gradient(135deg,var(--c1),var(--c2))}
.u-info{flex:1;min-width:0}
.u-name{font-size:.8rem;color:var(--txt);font-weight:600}
.u-meta{font-size:.64rem;color:var(--txt3);margin-top:2px}
.u-msg{font-size:.68rem;color:var(--txt2);margin-top:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:200px}

.bd{display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:100px;font-size:.62rem;font-weight:600;white-space:nowrap}
.bd-active{background:rgba(6,214,160,.08);color:var(--teal);border:1px solid rgba(6,214,160,.22)}
.bd-inactive{background:rgba(156,163,175,.1);color:#9ca3af;border:1px solid rgba(156,163,175,.22)}

.act-btns{display:flex;align-items:center;gap:5px}
.ab{width:32px;height:32px;border-radius:8px;display:grid;place-items:center;font-size:.85rem;border:1px solid var(--bdr);background:var(--sur2);color:var(--txt2);transition:background .18s,color .18s,border-color .18s;flex-shrink:0}
.ab-delete{color:var(--red);border-color:rgba(255,71,87,.22);background:rgba(255,71,87,.08)}
.ab-delete:hover{background:rgba(255,71,87,.2);border-color:rgba(255,71,87,.4)}
.ab-view{color:var(--blue);border-color:rgba(59,158,255,.22);background:rgba(59,158,255,.08)}
.ab-view:hover{background:rgba(59,158,255,.2);border-color:rgba(59,158,255,.4)}
.ab-chat{color:var(--rose);border-color:rgba(255,61,127,.22);background:rgba(255,61,127,.08)}
.ab-chat:hover{background:rgba(255,61,127,.2);border-color:rgba(255,61,127,.4)}

.pag-wrap{display:flex;justify-content:space-between;align-items:center;padding:11px 14px;border-top:1px solid var(--bdr);background:rgba(255,255,255,.01)}
.pag-info{font-size:.68rem;color:var(--txt3)}
.pagination{display:flex;gap:4px;list-style:none}
.page-item .page-link{display:flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:7px;background:var(--sur2);border:1px solid var(--bdr);color:var(--txt2);font-size:.7rem;font-family:'Figtree',sans-serif;transition:background .15s,color .15s}
.page-item .page-link:hover{background:rgba(255,255,255,.08);color:var(--txt)}
.page-item.active .page-link{background:linear-gradient(135deg,var(--rose),var(--violet));border-color:transparent;color:#fff;box-shadow:0 2px 10px rgba(255,61,127,.3)}

.empty-state{text-align:center;padding:50px 20px}
.empty-ico{font-size:2.4rem;margin-bottom:10px;opacity:.38}
.empty-txt{font-size:.78rem;color:var(--txt3)}

@keyframes fu{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}
*::-webkit-scrollbar{width:3px;height:3px}
*::-webkit-scrollbar-thumb{background:rgba(255,255,255,.08);border-radius:3px}

@media(max-width:1200px){.stat-strip{grid-template-columns:repeat(2,1fr)}}
@media(max-width:900px){.ph{flex-direction:column;align-items:flex-start;gap:10px}.dt thead th:nth-child(3),.dt td:nth-child(3){display:none}}
@media(max-width:600px){.up{padding:10px 10px 20px}.stat-strip{grid-template-columns:1fr;gap:8px}}
</style>
@endsection

@section('content')
<div class="up">

  {{-- PAGE HEADER --}}
  <div class="ph">
    <div>
      <div class="ph-title">Messages Management</div>
      <div class="ph-sub">View all user conversations and messages</div>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="back-btn">
      <i class="bi bi-arrow-left"></i> Dashboard
    </a>
  </div>

  {{-- STAT STRIP --}}
  <div class="stat-strip">
    <div class="ss ss-ct">
      <div class="ss-blob"></div>
      <div class="ss-ico">💬</div>
      <div>
        <div class="ss-val">{{ number_format($stats['total_matches'] ?? 0) }}</div>
        <div class="ss-lbl">Active Matches</div>
      </div>
    </div>
    <div class="ss ss-cr">
      <div class="ss-blob"></div>
      <div class="ss-ico">💌</div>
      <div>
        <div class="ss-val">{{ number_format($stats['total_messages'] ?? 0) }}</div>
        <div class="ss-lbl">Total Messages</div>
      </div>
    </div>
    <div class="ss ss-cg">
      <div class="ss-blob"></div>
      <div class="ss-ico">🔴</div>
      <div>
        <div class="ss-val">{{ number_format($stats['unread_messages'] ?? 0) }}</div>
        <div class="ss-lbl">Unread</div>
      </div>
    </div>
  </div>

  {{-- FILTER BAR --}}
  <div class="filter-bar">
    <form method="GET" action="{{ route('admin.messages.index') }}" class="filter-form">
      <input type="text" name="search" class="fi fi-search"
        placeholder="Search by name or email…"
        value="{{ request('search') }}">
      <button type="submit" class="btn-filter">
        <i class="bi bi-funnel-fill"></i> Filter
      </button>
    </form>
  </div>

  {{-- RESULTS META --}}
  <div class="results-meta">
    <span class="results-count">
      Showing <strong>{{ $conversations->firstItem() ?? 0 }}–{{ $conversations->lastItem() ?? 0 }}</strong>
      of <strong>{{ $conversations->total() ?? 0 }}</strong> conversations
    </span>
  </div>

  {{-- TABLE --}}
  <div class="tcard">
    <div style="overflow-x:auto">
      <table class="dt">
        <thead>
          <tr>
            <th>#</th>
            <th>User 1</th>
            <th>User 2</th>
            <th>Matched</th>
            <th>Messages</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse($conversations as $match)
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
              $msgCount = $match->conversation?->messages?->count() ?? 0;
              $color1 = $colors[($user1?->id ?? 0) % 7];
              $color2 = $colors[($user2?->id ?? 1) % 7];
              $initials1 = strtoupper(substr($profile1?->first_name ?? $user1?->email ?? '?', 0, 2));
              $initials2 = strtoupper(substr($profile2?->first_name ?? $user2?->email ?? '?', 0, 2));
              $name1 = $profile1?->first_name ? $profile1->first_name . ' ' . ($profile1->last_name ?? '') : ($user1?->email ?? 'Deleted');
              $name2 = $profile2?->first_name ? $profile2->first_name . ' ' . ($profile2->last_name ?? '') : ($user2?->email ?? 'Deleted');
            @endphp
            <tr>
              <td class="m">{{ $match->id }}</td>
              <td>
                <div class="uc">
                  <div class="u-av" style="--c1:{{ $color1[0] }};--c2:{{ $color1[1] }}">{{ $initials1 }}</div>
                  <div class="u-info">
                    <div class="u-name">{{ $name1 }}</div>
                    <div class="u-meta">{{ $profile1?->gender ?? '—' }} • {{ $user1?->email ?? '—' }}</div>
                  </div>
                </div>
              </td>
              <td>
                <div class="uc">
                  <div class="u-av" style="--c1:{{ $color2[0] }};--c2:{{ $color2[1] }}">{{ $initials2 }}</div>
                  <div class="u-info">
                    <div class="u-name">{{ $name2 }}</div>
                    <div class="u-meta">{{ $profile2?->gender ?? '—' }} • {{ $user2?->email ?? '—' }}</div>
                  </div>
                </div>
              </td>
              <td class="m">{{ $match->created_at->format('M d, Y') }}</td>
              <td class="m">{{ $msgCount }}</td>
              <td>
                <span class="bd {{ $match->is_active ? 'bd-active' : 'bd-inactive' }}">
                  {{ $match->is_active ? '🟢 Active' : '🔴 Inactive' }}
                </span>
              </td>
              <td>
                <div class="act-btns">
                  <a href="{{ route('admin.messages.show', $match->id) }}" class="ab ab-chat" title="View Chat">
                    <i class="bi bi-chat-fill"></i>
                  </a>
                  <form method="POST" action="{{ route('admin.messages.destroy', $match->id) }}" style="display:contents" onsubmit="return confirm('Unmatch these users?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="ab ab-delete" title="Unmatch">
                      <i class="bi bi-heart-break-fill"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7">
                <div class="empty-state">
                  <div class="empty-ico">💭</div>
                  <div class="empty-txt">No conversations yet</div>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="pag-wrap">
      <span class="pag-info">Page {{ $conversations->currentPage() }} of {{ $conversations->lastPage() }}</span>
      {{ $conversations->withQueryString()->links() }}
    </div>
  </div>

</div>{{-- /up --}}
@endsection