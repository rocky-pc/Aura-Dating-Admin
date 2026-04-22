@extends('admin.layout')

@section('title', 'Swipes Management')

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

.stat-strip{display:grid;grid-template-columns:repeat(5,1fr);gap:10px;margin-bottom:12px;animation:fu .4s .05s ease both}
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
.ss-ca{--sc1:#ffbe0b;--sc2:#ffd55a;--sg:rgba(255,190,11,.1)}
.ss-cb{--sc1:#8b5cf6;--sc2:#a78bfa;--sg:rgba(139,92,246,.1)}

.filter-bar{background:var(--sur);border:1px solid var(--bdr);border-radius:var(--r);padding:11px 14px;margin-bottom:10px;animation:fu .4s .1s ease both}
.filter-form{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.fi{background:var(--sur2);border:1px solid var(--bdr);border-radius:var(--rsm);padding:6px 10px;color:var(--txt);font-size:.74rem;outline:none;transition:border-color .18s,background .18s;-webkit-appearance:none;appearance:none}
.fi:focus{border-color:rgba(255,61,127,.4);background:var(--sur3)}
.fi::placeholder{color:var(--txt3)}
.fi-search{flex:1;min-width:170px;padding-left:30px;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='13' height='13' fill='rgba(237,233,255,0.28)' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1 0 0 0-.115-.099zm-5.44 1.398a5.5 6.5 0 0 1 0-11 5.5 5.5 0 0 1 0 11z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:10px center}
.fi-sel{padding-right:28px;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='9' height='9' fill='rgba(237,233,255,0.32)' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 0 0 1 .753 1.659l-4.796 5.48a1 0 0 1-1.506 0z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:calc(100% - 9px) center;cursor:pointer}
.fi-sel option{background:var(--sur2)}
.filter-actions{display:flex;gap:6px;margin-left:auto}
.btn-filter{display:inline-flex;align-items:center;gap:5px;background:linear-gradient(135deg,var(--rose),var(--violet));border-radius:var(--rsm);padding:6px 13px;font-size:.73rem;font-weight:600;color:#fff;box-shadow:0 3px 12px rgba(255,61,127,.28);transition:opacity .18s,transform .18s}
.btn-filter:hover{opacity:.88;transform:translateY(-1px)}
.btn-clear{display:inline-flex;align-items:center;gap:5px;background:var(--sur2);border:1px solid var(--bdr);border-radius:var(--rsm);padding:6px 11px;font-size:.73rem;font-weight:500;color:var(--txt2);transition:background .18s,color .18s}
.btn-clear:hover{background:var(--sur3);color:var(--txt)}

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
.u-av{width:32px;height:32px;border-radius:9px;display:grid;place-items:center;font-family:'Syne',sans-serif;font-size:.62rem;font-weight:700;color:#fff;flex-shrink:0;background:linear-gradient(135deg,var(--c1),var(--c2))}
.u-info{flex:1;min-width:0}
.u-name{font-size:.74rem;color:var(--txt);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.u-meta{font-size:.62rem;color:var(--txt3);margin-top:1px}

.bd{display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:100px;font-size:.62rem;font-weight:600;white-space:nowrap}
.bd-like{background:rgba(255,61,127,.1);color:#ff3d7f;border:1px solid rgba(255,61,127,.22)}
.bd-dislike{background:rgba(156,163,175,.1);color:#9ca3af;border:1px solid rgba(156,163,175,.22)}
.bd-superlike{background:rgba(139,92,246,.1);color:#8b5cf6;border:1px solid rgba(139,92,246,.22)}
.bd-favorite{background:rgba(255,190,11,.1);color:var(--amber);border:1px solid rgba(255,190,11,.22)}
.bd-match{background:rgba(6,214,160,.08);color:var(--teal);border:1px solid rgba(6,214,160,.22)}

.act-btns{display:flex;align-items:center;gap:5px}
.ab{width:28px;height:28px;border-radius:7px;display:grid;place-items:center;font-size:.78rem;border:1px solid var(--bdr);background:var(--sur2);color:var(--txt2);transition:background .18s,color .18s,border-color .18s;flex-shrink:0}
.ab-delete{color:var(--red);border-color:rgba(255,71,87,.22);background:rgba(255,71,87,.08)}
.ab-delete:hover{background:rgba(255,71,87,.2);border-color:rgba(255,71,87,.4)}
.ab-view{color:var(--blue);border-color:rgba(59,158,255,.22);background:rgba(59,158,255,.08)}
.ab-view:hover{background:rgba(59,158,255,.2);border-color:rgba(59,158,255,.4)}

.pag-wrap{display:flex;justify-content:space-between;align-items:center;padding:11px 14px;border-top:1px solid var(--bdr);background:rgba(255,255,255,.01)}
.pag-info{font-size:.68rem;color:var(--txt3)}
.pagination{display:flex;gap:4px;list-style:none}
.page-item .page-link{display:flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:7px;background:var(--sur2);border:1px solid var(--bdr);color:var(--txt2);font-size:.7rem;font-family:'Figtree',sans-serif;transition:background .15s,color .15s}
.page-item .page-link:hover{background:rgba(255,255,255,.08);color:var(--txt)}
.page-item.active .page-link{background:linear-gradient(135deg,var(--rose),var(--violet));border-color:transparent;color:#fff;box-shadow:0 2px 10px rgba(255,61,127,.3)}
.page-item.disabled .page-link{opacity:.3;pointer-events:none}

.empty-state{text-align:center;padding:50px 20px}
.empty-ico{font-size:2.4rem;margin-bottom:10px;opacity:.38}
.empty-txt{font-size:.78rem;color:var(--txt3)}

@keyframes fu{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}
*::-webkit-scrollbar{width:3px;height:3px}
*::-webkit-scrollbar-thumb{background:rgba(255,255,255,.08);border-radius:3px}

@media(max-width:1400px){.stat-strip{grid-template-columns:repeat(3,1fr)}}
@media(max-width:1024px){.stat-strip{grid-template-columns:repeat(2,1fr)}.fi-search{min-width:140px}}
@media(max-width:860px){.ph{flex-direction:column;align-items:flex-start;gap:10px}.dt thead th:nth-child(4),.dt td:nth-child(4){display:none}}
@media(max-width:600px){.up{padding:10px 10px 20px}.stat-strip{grid-template-columns:1fr 1fr;gap:8px}.dt thead th:nth-child(5),.dt td:nth-child(5){display:none}}
</style>
@endsection

@section('content')
<div class="up">

  {{-- PAGE HEADER --}}
  <div class="ph">
    <div>
      <div class="ph-title">Swipes Management</div>
      <div class="ph-sub">View all user swipe interactions with detailed profile info</div>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="back-btn">
      <i class="bi bi-arrow-left"></i> Dashboard
    </a>
  </div>

  {{-- STAT STRIP --}}
  <div class="stat-strip">
    <div class="ss ss-ct">
      <div class="ss-blob"></div>
      <div class="ss-ico">👆</div>
      <div>
        <div class="ss-val">{{ number_format($stats['total'] ?? 0) }}</div>
        <div class="ss-lbl">Total Swipes</div>
      </div>
    </div>
    <div class="ss ss-cr">
      <div class="ss-blob"></div>
      <div class="ss-ico">❤️</div>
      <div>
        <div class="ss-val">{{ number_format($stats['likes'] ?? 0) }}</div>
        <div class="ss-lbl">Likes</div>
      </div>
    </div>
    <div class="ss ss-ca">
      <div class="ss-blob"></div>
      <div class="ss-ico">💫</div>
      <div>
        <div class="ss-val">{{ number_format($stats['super_likes'] ?? 0) }}</div>
        <div class="ss-lbl">Super Likes</div>
      </div>
    </div>
    <div class="ss ss-cg">
      <div class="ss-blob"></div>
      <div class="ss-ico">🤝</div>
      <div>
        <div class="ss-val">{{ number_format($stats['matches'] ?? 0) }}</div>
        <div class="ss-lbl">Matches</div>
      </div>
    </div>
    <div class="ss ss-cb">
      <div class="ss-blob"></div>
      <div class="ss-ico">❌</div>
      <div>
        <div class="ss-val">{{ number_format($stats['dislikes'] ?? 0) }}</div>
        <div class="ss-lbl">Dislikes</div>
      </div>
    </div>
  </div>

  {{-- FILTER BAR --}}
  <div class="filter-bar">
    <form method="GET" action="{{ route('admin.swipes.index') }}" class="filter-form">
      <input type="text" name="search" class="fi fi-search"
        placeholder="Search by name or email…"
        value="{{ request('search') }}">
      <select name="action" class="fi fi-sel">
        <option value="">All Actions</option>
        <option value="like"      @selected(request('action')==='like')>❤️ Likes</option>
        <option value="dislike"   @selected(request('action')==='dislike')>❌ Dislikes</option>
        <option value="super_like" @selected(request('action')==='super_like')>💫 Super Likes</option>
        <option value="match"     @selected(request('action')==='match')>🤝 Matches</option>
      </select>
      <div class="filter-actions">
        <button type="submit" class="btn-filter">
          <i class="bi bi-funnel-fill"></i> Filter
        </button>
        <a href="{{ route('admin.swipes.index') }}" class="btn-clear">
          <i class="bi bi-x"></i> Clear
        </a>
      </div>
    </form>
  </div>

  {{-- RESULTS META --}}
  <div class="results-meta">
    <span class="results-count">
      Showing <strong>{{ $swipes->firstItem() ?? 0 }}–{{ $swipes->lastItem() ?? 0 }}</strong>
      of <strong>{{ $swipes->total() ?? 0 }}</strong> swipes
    </span>
  </div>

  {{-- TABLE --}}
  <div class="tcard">
    <div style="overflow-x:auto">
      <table class="dt">
        <thead>
          <tr>
            <th>#</th>
            <th>Swiper (From)</th>
            <th>Swiped (To)</th>
            <th>Action</th>
            <th>Match</th>
            <th>Date</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse($swipes as $swipe)
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
              $fromUser = $swipe->swiper;
              $toUser = $swipe->swiped;
              $fromProfile = $fromUser?->profile;
              $toProfile = $toUser?->profile;
              $fromColor = $colors[($fromUser?->id ?? 0) % 7];
              $toColor = $colors[($toUser?->id ?? 1) % 7];
              $fromInitials = strtoupper(substr($fromProfile?->first_name ?? $fromUser?->email ?? '?', 0, 2));
              $toInitials = strtoupper(substr($toProfile?->first_name ?? $toUser?->email ?? '?', 0, 2));
              $fromName = $fromProfile?->first_name ? $fromProfile->first_name . ' ' . ($fromProfile->last_name ?? '') : ($fromUser?->email ?? 'Deleted User');
              $toName = $toProfile?->first_name ? $toProfile->first_name . ' ' . ($toProfile->last_name ?? '') : ($toUser?->email ?? 'Deleted User');
            @endphp
            <tr>
              <td class="m">{{ $swipe->id }}</td>
              <td>
                <div class="uc">
                  <div class="u-av" style="--c1:{{ $fromColor[0] }};--c2:{{ $fromColor[1] }}">{{ $fromInitials }}</div>
                  <div class="u-info">
                    <div class="u-name">{{ $fromName }}</div>
                    <div class="u-meta">{{ $fromProfile?->gender ?? '—' }} • {{ $fromProfile?->date_of_birth ? now()->diffInYears($fromProfile->date_of_birth) . ' yrs' : '—' }}</div>
                  </div>
                </div>
              </td>
              <td>
                <div class="uc">
                  <div class="u-av" style="--c1:{{ $toColor[0] }};--c2:{{ $toColor[1] }}">{{ $toInitials }}</div>
                  <div class="u-info">
                    <div class="u-name">{{ $toName }}</div>
                    <div class="u-meta">{{ $toProfile?->gender ?? '—' }} • {{ $toProfile?->date_of_birth ? now()->diffInYears($toProfile->date_of_birth) . ' yrs' : '—' }}</div>
                  </div>
                </div>
              </td>
              <td>
                @if($swipe->action === 'like')
                  <span class="bd bd-like">❤️ Like</span>
                @elseif($swipe->action === 'dislike')
                  <span class="bd bd-dislike">❌ Dislike</span>
                @elseif($swipe->action === 'super_like')
                  <span class="bd bd-superlike">💫 Super Like</span>
                @elseif($swipe->action === 'favorite')
                  <span class="bd bd-favorite">⭐ Favorite</span>
                @endif
              </td>
              <td>
                @if($swipe->is_match)
                  <span class="bd bd-match">🤝 Yes</span>
                @else
                  <span class="bd bd-dislike">No</span>
                @endif
              </td>
              <td class="m">{{ $swipe->created_at->format('M d, Y H:i') }}</td>
              <td>
                <div class="act-btns">
                  <a href="{{ route('admin.swipes.show', $swipe->id) }}" class="ab ab-view" title="View Details">
                    <i class="bi bi-eye-fill"></i>
                  </a>
                  <form method="POST" action="{{ route('admin.swipes.destroy', $swipe->id) }}" style="display:contents" onsubmit="return confirm('Delete this swipe?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="ab ab-delete" title="Delete">
                      <i class="bi bi-trash-fill"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7">
                <div class="empty-state">
                  <div class="empty-ico">📭</div>
                  <div class="empty-txt">No swipes found</div>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="pag-wrap">
      <span class="pag-info">Page {{ $swipes->currentPage() }} of {{ $swipes->lastPage() }}</span>
      {{ $swipes->withQueryString()->links() }}
    </div>
  </div>

</div>{{-- /up --}}
@endsection