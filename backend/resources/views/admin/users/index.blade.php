@extends('admin.layout')

@section('title', 'Users Management')

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

.stat-strip{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:12px;animation:fu .4s .05s ease both}
.ss{background:var(--sur);border:1px solid var(--bdr);border-radius:var(--r);padding:12px 14px;display:flex;align-items:center;gap:12px;position:relative;overflow:hidden;transition:transform .2s,box-shadow .2s}
.ss:hover{transform:translateY(-2px);box-shadow:0 6px 24px rgba(0,0,0,.35),0 0 22px var(--sg,rgba(255,61,127,.1))}
.ss::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,var(--sc1,transparent),transparent 60%);opacity:.07;pointer-events:none}
.ss-blob{position:absolute;width:60px;height:60px;border-radius:50%;background:var(--sc1);filter:blur(24px);opacity:.18;right:-10px;top:-10px;pointer-events:none;transition:opacity .3s}
.ss:hover .ss-blob{opacity:.28}
.ss-ico{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--sc1),var(--sc2));display:grid;place-items:center;font-size:1rem;box-shadow:0 3px 10px rgba(0,0,0,.3);flex-shrink:0}
.ss-val{font-family:'Syne',sans-serif;font-size:1.3rem;font-weight:800;color:#fff;line-height:1;letter-spacing:-.03em}
.ss-lbl{font-size:.68rem;color:var(--txt2);margin-top:2px}
.ss-cr{--sc1:#ff3d7f;--sc2:#ff8ab8;--sg:rgba(255,61,127,.12)}
.ss-cg{--sc1:#06d6a0;--sc2:#00ffcc;--sg:rgba(6,214,160,.1)}
.ss-ca{--sc1:#ffbe0b;--sc2:#ffd55a;--sg:rgba(255,190,11,.1)}
.ss-cb{--sc1:#3b9eff;--sc2:#80c0ff;--sg:rgba(59,158,255,.1)}

.filter-bar{background:var(--sur);border:1px solid var(--bdr);border-radius:var(--r);padding:11px 14px;margin-bottom:10px;animation:fu .4s .1s ease both}
.filter-form{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.fi{background:var(--sur2);border:1px solid var(--bdr);border-radius:var(--rsm);padding:6px 10px;color:var(--txt);font-size:.74rem;outline:none;transition:border-color .18s,background .18s;-webkit-appearance:none;appearance:none}
.fi:focus{border-color:rgba(255,61,127,.4);background:var(--sur3)}
.fi::placeholder{color:var(--txt3)}
.fi-search{flex:1;min-width:170px;padding-left:30px;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='13' height='13' fill='rgba(237,233,255,0.28)' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.099zm-5.44 1.398a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:10px center}
.fi-sel{padding-right:28px;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='9' height='9' fill='rgba(237,233,255,0.32)' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:calc(100% - 9px) center;cursor:pointer}
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
.dt td.mn{font-family:monospace;font-size:.64rem;color:var(--txt3)}

.uc{display:flex;align-items:center;gap:9px}
.u-av{width:32px;height:32px;border-radius:9px;display:grid;place-items:center;font-family:'Syne',sans-serif;font-size:.62rem;font-weight:700;color:#fff;flex-shrink:0}
.u-email{font-size:.74rem;color:var(--txt);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:150px}

.bd{display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:100px;font-size:.62rem;font-weight:600;white-space:nowrap}
.bd-admin{background:rgba(255,71,87,.1);color:#ff5f6d;border:1px solid rgba(255,71,87,.22)}
.bd-mod{background:rgba(255,190,11,.1);color:var(--amber);border:1px solid rgba(255,190,11,.22)}
.bd-user{background:rgba(59,158,255,.1);color:var(--blue);border:1px solid rgba(59,158,255,.22)}
.bd-prem{background:rgba(255,190,11,.1);color:var(--amber);border:1px solid rgba(255,190,11,.22)}
.bd-free{background:rgba(255,255,255,.04);color:var(--txt3);border:1px solid var(--bdr)}
.bd-on{background:rgba(6,214,160,.08);color:var(--teal);border:1px solid rgba(6,214,160,.22)}
.bd-off{background:rgba(255,71,87,.08);color:var(--red);border:1px solid rgba(255,71,87,.18)}
.bd-vfy{background:rgba(6,214,160,.08);color:var(--teal);border:1px solid rgba(6,214,160,.22)}
.bd-uvfy{background:rgba(255,255,255,.04);color:var(--txt3);border:1px solid var(--bdr)}
.bd-dot{width:4px;height:4px;border-radius:50%;background:currentColor;animation:bdp 2s ease infinite}
@keyframes bdp{0%,100%{opacity:1}50%{opacity:.3}}

.act-btns{display:flex;align-items:center;gap:5px}
.ab{width:28px;height:28px;border-radius:7px;display:grid;place-items:center;font-size:.78rem;border:1px solid var(--bdr);background:var(--sur2);color:var(--txt2);transition:background .18s,color .18s,border-color .18s;flex-shrink:0}
.ab-view{color:var(--blue);border-color:rgba(59,158,255,.22);background:rgba(59,158,255,.08)}
.ab-view:hover{background:rgba(59,158,255,.2);border-color:rgba(59,158,255,.4)}
.ab-pause{color:var(--amber);border-color:rgba(255,190,11,.22);background:rgba(255,190,11,.08)}
.ab-pause:hover{background:rgba(255,190,11,.2);border-color:rgba(255,190,11,.4)}
.ab-play{color:var(--teal);border-color:rgba(6,214,160,.22);background:rgba(6,214,160,.08)}
.ab-play:hover{background:rgba(6,214,160,.2);border-color:rgba(6,214,160,.4)}
.ab-vfy{color:var(--teal);border-color:rgba(6,214,160,.22);background:rgba(6,214,160,.08)}
.ab-vfy:hover{background:rgba(6,214,160,.2);border-color:rgba(6,214,160,.4)}

.pag-wrap{display:flex;justify-content:space-between;align-items:center;padding:11px 14px;border-top:1px solid var(--bdr);background:rgba(255,255,255,.01)}
.pag-info{font-size:.68rem;color:var(--txt3)}
.pagination{display:flex;gap:4px;list-style:none}
.page-item .page-link{display:flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:7px;background:var(--sur2);border:1px solid var(--bdr);color:var(--txt2);font-size:.7rem;font-family:'Figtree',sans-serif;transition:background .15s,color .15s}
.page-item .page-link:hover{background:rgba(255,255,255,.08);color:var(--txt)}
.page-item.active .page-link{background:linear-gradient(135deg,var(--rose),var(--violet));border-color:transparent;color:#fff;box-shadow:0 2px 10px rgba(255,61,127,.3)}
.page-item.disabled .page-link{opacity:.3;pointer-events:none}

.drawer-overlay{position:fixed;inset:0;background:rgba(4,3,10,.72);backdrop-filter:blur(7px);-webkit-backdrop-filter:blur(7px);z-index:900;opacity:0;pointer-events:none;transition:opacity .28s ease}
.drawer-overlay.open{opacity:1;pointer-events:all}
.drawer{position:fixed;top:0;right:0;bottom:0;width:390px;background:var(--sur);border-left:1px solid var(--bdr);z-index:910;display:flex;flex-direction:column;transform:translateX(100%);transition:transform .3s cubic-bezier(.4,0,.2,1)}
.drawer.open{transform:translateX(0)}
.drawer-head{display:flex;justify-content:space-between;align-items:center;padding:15px 17px;border-bottom:1px solid var(--bdr);flex-shrink:0}
.drawer-tag{font-size:.58rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--rose);background:rgba(255,61,127,.08);border:1px solid rgba(255,61,127,.18);border-radius:5px;padding:2px 7px}
.drawer-title{font-family:'Syne',sans-serif;font-size:.88rem;font-weight:700;color:var(--txt);margin-left:8px}
.drawer-close{width:28px;height:28px;border-radius:7px;display:grid;place-items:center;background:var(--sur2);border:1px solid var(--bdr);color:var(--txt2);font-size:.85rem;transition:background .18s,color .18s}
.drawer-close:hover{background:rgba(255,71,87,.14);color:var(--red);border-color:rgba(255,71,87,.28)}
.drawer-body{flex:1;overflow-y:auto;padding:16px}
.drawer-body::-webkit-scrollbar{width:3px}
.drawer-body::-webkit-scrollbar-thumb{background:var(--bdr2);border-radius:3px}

.d-hero{background:var(--sur2);border:1px solid var(--bdr);border-radius:var(--r);padding:14px;margin-bottom:14px;display:flex;gap:13px;align-items:flex-start;position:relative;overflow:hidden}
.d-hero::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,61,127,.06),transparent 60%);pointer-events:none}
.d-av{width:50px;height:50px;border-radius:13px;display:grid;place-items:center;font-family:'Syne',sans-serif;font-size:1.1rem;font-weight:800;color:#fff;flex-shrink:0;box-shadow:0 4px 16px rgba(0,0,0,.35)}
.d-hero-name{font-family:'Syne',sans-serif;font-size:.86rem;font-weight:700;color:var(--txt);word-break:break-all;line-height:1.3}
.d-hero-sub{font-size:.68rem;color:var(--txt2);margin-top:3px}
.d-badges{display:flex;gap:5px;flex-wrap:wrap;margin-top:8px}

.ds{margin-bottom:14px}
.ds-title{font-family:'Syne',sans-serif;font-size:.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--txt3);margin-bottom:8px;display:flex;align-items:center;gap:7px}
.ds-title::after{content:'';flex:1;height:1px;background:var(--bdr)}
.d-grid{display:grid;grid-template-columns:1fr 1fr;gap:7px}
.d-field{background:var(--sur2);border:1px solid var(--bdr);border-radius:var(--rsm);padding:9px 11px;transition:border-color .18s}
.d-field:hover{border-color:var(--bdr2)}
.d-field.full{grid-column:1/-1}
.d-lbl{font-size:.59rem;color:var(--txt3);text-transform:uppercase;letter-spacing:.08em;margin-bottom:4px}
.d-val{font-size:.77rem;color:var(--txt);font-weight:500;word-break:break-all;line-height:1.3}
.d-val code{font-family:monospace;font-size:.68rem;background:var(--sur3);padding:2px 5px;border-radius:4px;color:var(--txt2)}

.drawer-foot{padding:12px 16px;border-top:1px solid var(--bdr);display:flex;gap:7px;flex-shrink:0;flex-wrap:wrap}
.df-close{flex:1;display:flex;align-items:center;justify-content:center;gap:6px;background:var(--sur2);border:1px solid var(--bdr);border-radius:var(--rsm);padding:8px 13px;font-size:.73rem;font-weight:600;color:var(--txt2);transition:background .18s,color .18s}
.df-close:hover{background:var(--sur3);color:var(--txt)}
.df-action{display:inline-flex;align-items:center;gap:6px;border-radius:var(--rsm);padding:8px 13px;font-size:.73rem;font-weight:600;border:1px solid;transition:background .18s;cursor:pointer}
.df-pause{color:var(--amber);border-color:rgba(255,190,11,.3);background:rgba(255,190,11,.08)}
.df-pause:hover{background:rgba(255,190,11,.18)}
.df-play{color:var(--teal);border-color:rgba(6,214,160,.3);background:rgba(6,214,160,.08)}
.df-play:hover{background:rgba(6,214,160,.18)}
.df-vfy{color:var(--teal);border-color:rgba(6,214,160,.3);background:rgba(6,214,160,.08)}
.df-vfy:hover{background:rgba(6,214,160,.18)}

.empty-state{text-align:center;padding:50px 20px}
.empty-ico{font-size:2.4rem;margin-bottom:10px;opacity:.38}
.empty-txt{font-size:.78rem;color:var(--txt3)}

@keyframes fu{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}
*::-webkit-scrollbar{width:3px;height:3px}
*::-webkit-scrollbar-thumb{background:rgba(255,255,255,.08);border-radius:3px}

@media(max-width:1280px){.stat-strip{gap:8px}.drawer{width:360px}}
@media(max-width:1024px){.stat-strip{grid-template-columns:repeat(2,1fr)}.fi-search{min-width:140px}}
@media(max-width:860px){.drawer{width:100%;border-left:none}.ph{flex-direction:column;align-items:flex-start;gap:10px}.dt thead th:nth-child(3),.dt td:nth-child(3){display:none}}
@media(max-width:600px){.up{padding:10px 10px 20px}.stat-strip{grid-template-columns:1fr 1fr;gap:8px}.dt thead th:nth-child(4),.dt td:nth-child(4){display:none}}
</style>
@endsection

@section('content')
<div class="up">

  {{-- PAGE HEADER --}}
  <div class="ph">
    <div>
      <div class="ph-title">Users Management</div>
      <div class="ph-sub">Manage, filter, and moderate all registered members</div>
    </div>
    <div style="display:flex; gap:10px; align-items:center">
      <button onclick="openCreateModal()" class="btn-filter">
        <i class="bi bi-person-plus-fill"></i> Add User
      </button>
      <a href="{{ route('admin.dashboard') }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Dashboard
      </a>
    </div>
  </div>

  {{-- STAT STRIP --}}
  <div class="stat-strip">
    <div class="ss ss-cr">
      <div class="ss-blob"></div>
      <div class="ss-ico">👥</div>
      <div>
        <div class="ss-val">{{ number_format($stats['total'] ?? 0) }}</div>
        <div class="ss-lbl">Total Users</div>
      </div>
    </div>
    <div class="ss ss-cg">
      <div class="ss-blob"></div>
      <div class="ss-ico">✅</div>
      <div>
        <div class="ss-val">{{ number_format($stats['active'] ?? 0) }}</div>
        <div class="ss-lbl">Active</div>
      </div>
    </div>
    <div class="ss ss-ca">
      <div class="ss-blob"></div>
      <div class="ss-ico">⭐</div>
      <div>
        <div class="ss-val">{{ number_format($stats['premium'] ?? 0) }}</div>
        <div class="ss-lbl">Premium</div>
      </div>
    </div>
    <div class="ss ss-cb">
      <div class="ss-blob"></div>
      <div class="ss-ico">🛡️</div>
      <div>
        <div class="ss-val">{{ number_format($stats['verified'] ?? 0) }}</div>
        <div class="ss-lbl">Verified</div>
      </div>
    </div>
  </div>

  {{-- FILTER BAR --}}
  <div class="filter-bar">
    <form method="GET" action="{{ route('admin.users.index') }}" class="filter-form">
      <input type="text" name="search" class="fi fi-search"
        placeholder="Search email or phone…"
        value="{{ request('search') }}">
      <select name="role" class="fi fi-sel">
        <option value="">All Roles</option>
        <option value="user"      @selected(request('role')==='user')>User</option>
        <option value="admin"     @selected(request('role')==='admin')>Admin</option>
        <option value="moderator" @selected(request('role')==='moderator')>Moderator</option>
      </select>
      <select name="status" class="fi fi-sel">
        <option value="">All Status</option>
        <option value="active"   @selected(request('status')==='active')>Active</option>
        <option value="inactive" @selected(request('status')==='inactive')>Inactive</option>
      </select>
      <select name="verified" class="fi fi-sel">
        <option value="">All Users</option>
        <option value="1" @selected(request('verified')==='1')>Verified</option>
        <option value="0" @selected(request('verified')==='0')>Unverified</option>
      </select>
      <div class="filter-actions">
        <button type="submit" class="btn-filter">
          <i class="bi bi-funnel-fill"></i> Filter
        </button>
        <a href="{{ route('admin.users.index') }}" class="btn-clear">
          <i class="bi bi-x"></i> Clear
        </a>
      </div>
    </form>
  </div>

  {{-- RESULTS META --}}
  <div class="results-meta">
    <span class="results-count">
      Showing <strong>{{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }}</strong>
      of <strong>{{ $users->total() ?? 0 }}</strong> users
    </span>
  </div>

  {{-- TABLE --}}
  <div class="tcard">
    <div style="overflow-x:auto">
      <table class="dt">
        <thead>
          <tr>
            <th>#</th>
            <th>User</th>
            <th>UUID</th>
            <th>Phone</th>
            <th>Role</th>
            <th>Plan</th>
            <th>Verified</th>
            <th>Status</th>
            <th>Joined</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $user)
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
              $c      = $colors[$user->id % 7];
              $initials = strtoupper(substr($user->email ?? $user->phone ?? '?', 0, 2));
            @endphp
            <tr>
              <td class="m">{{ $user->id }}</td>
              <td>
                <div class="uc">
                  <div class="u-av" style="background:linear-gradient(135deg,{{ $c[0] }},{{ $c[1] }})">{{ $initials }}</div>
                  <div class="u-email">{{ $user->email ?? '—' }}</div>
                </div>
              </td>
              <td class="mn">{{ substr($user->uuid, 0, 8) }}…</td>
              <td class="m">{{ $user->phone ?? '—' }}</td>
              <td>
                @if($user->role === 'admin')
                  <span class="bd bd-admin">● Admin</span>
                @elseif($user->role === 'moderator')
                  <span class="bd bd-mod">● Mod</span>
                @else
                  <span class="bd bd-user">● User</span>
                @endif
              </td>
              <td>
                @if($user->is_premium)
                  <span class="bd bd-prem">⭐ Premium</span>
                @else
                  <span class="bd bd-free">Free</span>
                @endif
              </td>
              <td>
                @if($user->is_verified)
                  <span class="bd bd-vfy"><i class="bi bi-patch-check-fill"></i> Yes</span>
                @else
                  <span class="bd bd-uvfy"><i class="bi bi-patch-check"></i> No</span>
                @endif
              </td>
              <td>
                @if($user->is_active)
                  <span class="bd bd-on"><span class="bd-dot"></span>Active</span>
                @else
                  <span class="bd bd-off">Inactive</span>
                @endif
              </td>
              <td class="m">{{ $user->created_at->format('M d, Y') }}</td>
              <td>
                <div class="act-btns">
                  <button class="ab ab-view" onclick="openDrawer({{ $user->id }})" title="View">
                    <i class="bi bi-eye-fill"></i>
                  </button>
                  <form method="POST" action="{{ route('admin.users.toggle-active', $user->id) }}" style="display:contents">
                    @csrf
                    <button type="submit" class="ab {{ $user->is_active ? 'ab-pause' : 'ab-play' }}"
                      title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                      <i class="bi bi-{{ $user->is_active ? 'pause-fill' : 'play-fill' }}"></i>
                    </button>
                  </form>
                  @unless($user->is_verified)
                    <form method="POST" action="{{ route('admin.users.verify', $user->id) }}" style="display:contents">
                      @csrf
                      <button type="submit" class="ab ab-vfy" title="Verify">
                        <i class="bi bi-patch-check-fill"></i>
                      </button>
                    </form>
                  @endunless
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="10">
                <div class="empty-state">
                  <div class="empty-ico">🔍</div>
                  <div class="empty-txt">No users match your filters</div>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="pag-wrap">
      <span class="pag-info">Page {{ $users->currentPage() }} of {{ $users->lastPage() }}</span>
      {{ $users->withQueryString()->links() }}
    </div>
  </div>

</div>{{-- /up --}}

{{-- CREATE USER MODAL --}}
<div id="createUserModal" class="drawer-overlay" onclick="closeCreateModal(event)" style="z-index:1000; display:none">
  <div class="drawer open" onclick="event.stopPropagation()" style="width:480px">
    <div class="drawer-head">
      <div style="display:flex;align-items:center">
        <span class="drawer-tag">New</span>
        <span class="drawer-title">Create User</span>
      </div>
      <button class="drawer-close" onclick="closeCreateModal()"><i class="bi bi-x-lg"></i></button>
    </div>
    <form method="POST" action="{{ route('admin.users.store') }}" class="drawer-body">
      @csrf
      
      <div style="display:grid; gap:12px; padding-bottom:20px">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px">
          <div>
            <div class="d-lbl">First Name *</div>
            <input type="text" name="first_name" class="fi" required style="width:100%; margin-top:6px" placeholder="John">
          </div>
          <div>
            <div class="d-lbl">Last Name</div>
            <input type="text" name="last_name" class="fi" style="width:100%; margin-top:6px" placeholder="Doe">
          </div>
        </div>

        <div>
          <div class="d-lbl">Email *</div>
          <input type="email" name="email" class="fi" required style="width:100%; margin-top:6px" placeholder="user@example.com">
        </div>

        <div>
          <div class="d-lbl">Phone Number</div>
          <input type="text" name="phone" class="fi" style="width:100%; margin-top:6px" placeholder="+1234567890">
        </div>

        <div>
          <div class="d-lbl">Password *</div>
          <input type="password" name="password" class="fi" required style="width:100%; margin-top:6px" minlength="6" placeholder="Min 6 characters">
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px">
          <div>
            <div class="d-lbl">Gender *</div>
            <select name="gender" class="fi" required style="width:100%; margin-top:6px">
              <option value="male">Male</option>
              <option value="female">Female</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div>
            <div class="d-lbl">Date of Birth *</div>
            <input type="date" name="date_of_birth" class="fi" required style="width:100%; margin-top:6px" max="{{ date('Y-m-d', strtotime('-18 years')) }}">
          </div>
        </div>

        <div>
          <div class="d-lbl">Role *</div>
          <select name="role" class="fi" required style="width:100%; margin-top:6px">
            <option value="user">User</option>
            <option value="moderator">Moderator</option>
            <option value="admin">Admin</option>
          </select>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px">
          <div style="display:flex; align-items:center; gap:8px; padding:10px; background:var(--sur2); border:1px solid var(--bdr); border-radius:var(--rsm)">
            <input type="checkbox" name="is_verified" id="is_verified" value="1">
            <label for="is_verified" style="font-size:.75rem; color:var(--txt)">Verified Account</label>
          </div>
          <div style="display:flex; align-items:center; gap:8px; padding:10px; background:var(--sur2); border:1px solid var(--bdr); border-radius:var(--rsm)">
            <input type="checkbox" name="is_premium" id="is_premium" value="1">
            <label for="is_premium" style="font-size:.75rem; color:var(--txt)">Premium Plan</label>
          </div>
        </div>

        <div>
          <div class="d-lbl">Bio</div>
          <textarea name="bio" class="fi" style="width:100%; margin-top:6px; min-height:80px; resize:vertical" placeholder="Tell something about this user..." maxlength="500"></textarea>
        </div>
      </div>

      <div class="drawer-foot" style="border-top:1px solid var(--bdr); padding:14px 16px; margin:0 -16px -16px">
        <button type="button" class="df-close" onclick="closeCreateModal()" style="flex:1">
          <i class="bi bi-x-circle"></i> Cancel
        </button>
        <button type="submit" class="btn-filter" style="flex:1; justify-content:center">
          <i class="bi bi-check-lg"></i> Create User
        </button>
      </div>
    </form>
  </div>
</div>

{{-- DRAWER OVERLAY --}}
<div class="drawer-overlay" id="drawerOverlay" onclick="closeDrawer()"></div>

{{-- DRAWER PANELS --}}
@foreach($users as $user)
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
    $c        = $colors[$user->id % 7];
    $initials = strtoupper(substr($user->email ?? $user->phone ?? '?', 0, 2));
    $lastSeen = $user->last_active_at
      ? \Carbon\Carbon::parse($user->last_active_at)->diffForHumans()
      : 'Never';
  @endphp
  <div class="drawer" id="drawer-{{ $user->id }}">
    <div class="drawer-head">
      <div style="display:flex;align-items:center">
        <span class="drawer-tag">Profile</span>
        <span class="drawer-title">User #{{ $user->id }}</span>
      </div>
      <button class="drawer-close" onclick="closeDrawer()"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="drawer-body">
      <div class="d-hero">
        <div class="d-av" style="background:linear-gradient(135deg,{{ $c[0] }},{{ $c[1] }})">{{ $initials }}</div>
        <div style="flex:1;min-width:0">
          <div class="d-hero-name">{{ $user->email ?? $user->phone ?? 'Unknown' }}</div>
          <div class="d-hero-sub">ID #{{ $user->id }} · Joined {{ $user->created_at->format('M d, Y') }}</div>
          <div class="d-badges">
            @if($user->role === 'admin')
              <span class="bd bd-admin">Admin</span>
            @elseif($user->role === 'moderator')
              <span class="bd bd-mod">Mod</span>
            @else
              <span class="bd bd-user">User</span>
            @endif
            @if($user->is_active)
              <span class="bd bd-on"><span class="bd-dot"></span>Active</span>
            @else
              <span class="bd bd-off">Inactive</span>
            @endif
            @if($user->is_premium)
              <span class="bd bd-prem">⭐ Premium</span>
            @endif
            @if($user->is_verified)
              <span class="bd bd-vfy"><i class="bi bi-patch-check-fill"></i> Verified</span>
            @endif
          </div>
        </div>
      </div>
      <div class="ds">
        <div class="ds-title">Identity</div>
        <div class="d-grid">
          <div class="d-field">
            <div class="d-lbl">User ID</div>
            <div class="d-val">#{{ $user->id }}</div>
          </div>
          <div class="d-field">
            <div class="d-lbl">Role</div>
            <div  >{{ ucfirst($user->role) }}</div>
          </div>
          <div class="d-field full">
            <div class="d-lbl">UUID</div>
            <div class="d-val"><code>{{ $user->uuid }}</code></div>
          </div>
        </div>
      </div>
      <div class="ds">
        <div class="ds-title">Contact</div>
        <div class="d-grid">
          <div class="d-field full">
            <div class="d-lbl">Email</div>
            <div class="d-val">{{ $user->email ?? '—' }}</div>
          </div>
          <div class="d-field full">
            <div class="d-lbl">Phone</div>
            <div class="d-val">{{ $user->phone ?? '—' }}</div>
          </div>
        </div>
      </div>
      <div class="ds">
        <div class="ds-title">Account</div>
        <div class="d-grid">
          <div class="d-field">
            <div class="d-lbl">Plan</div>
            <div class="d-val">{{ $user->is_premium ? '⭐ Premium' : 'Free' }}</div>
          </div>
          <div class="d-field">
            <div class="d-lbl">Verified</div>
            <div class="d-val">{{ $user->is_verified ? '✅ Yes' : '❌ No' }}</div>
          </div>
          <div class="d-field">
            <div class="d-lbl">Status</div>
            <div class="d-val">{{ $user->is_active ? '🟢 Active' : '🔴 Inactive' }}</div>
          </div>
          <div class="d-field">
            <div class="d-lbl">Last Active</div>
            <div class="d-val">{{ $lastSeen }}</div>
          </div>
          <div class="d-field full">
            <div class="d-lbl">Registered</div>
            <div class="d-val">{{ $user->created_at->format('d M Y, h:i A') }}</div>
          </div>
        </div>
      </div>
    </div>
    <div class="drawer-foot">
      <button class="df-close" onclick="closeDrawer()"><i class="bi bi-x-circle"></i> Close</button>
      <form method="POST" action="{{ route('admin.users.toggle-active', $user->id) }}" style="display:contents">
        @csrf
        <button type="submit" class="df-action {{ $user->is_active ? 'df-pause' : 'df-play' }}">
          <i class="bi bi-{{ $user->is_active ? 'pause-fill' : 'play-fill' }}"></i>
          {{ $user->is_active ? 'Deactivate' : 'Activate' }}
        </button>
      </form>
      @unless($user->is_verified)
        <form method="POST" action="{{ route('admin.users.verify', $user->id) }}" style="display:contents">
          @csrf
          <button type="submit" class="df-action df-vfy">
            <i class="bi bi-patch-check-fill"></i> Verify
          </button>
        </form>
      @endunless
    </div>
  </div>
@endforeach

@endsection

<script>
var activeDrawer = null;

function openCreateModal() {
  const modal = document.getElementById('createUserModal');
  modal.style.display = 'flex';
  modal.classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeCreateModal(e) {
  if (e && e.target !== e.currentTarget) return;
  const modal = document.getElementById('createUserModal');
  modal.style.display = 'none';
  modal.classList.remove('open');
  document.body.style.overflow = '';
}

function openDrawer(id) {
  closeDrawer(false);
  var ov = document.getElementById('drawerOverlay');
  var dr = document.getElementById('drawer-' + id);
  if (!dr) return;
  ov.classList.add('open');
  dr.classList.add('open');
  document.body.style.overflow = 'hidden';
  activeDrawer = dr;
}

function closeDrawer(restoreScroll) {
  restoreScroll = (restoreScroll !== false);
  document.getElementById('drawerOverlay').classList.remove('open');
  if (activeDrawer) {
    activeDrawer.classList.remove('open');
    activeDrawer = null;
  }
  if (restoreScroll) {
    document.body.style.overflow = '';
  }
}

document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeDrawer();
    closeCreateModal();
  }
});
</script>
@push('scripts')
@endpush