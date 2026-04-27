@extends('admin.layout')

@section('title', 'Aura — Subscriptions Management')

@section('styles')
<style>
/* ───────────── PAGE HEADER ───────────── */
.ph{
  display:flex;justify-content:space-between;align-items:center;
  margin-bottom:14px;animation:fu .5s ease both;
}
.ph-title{
  font-family:'Syne',sans-serif;font-size:1.15rem;font-weight:800;
  letter-spacing:-.03em;color:var(--txt);
}
.ph-sub{font-size:.68rem;color:var(--txt-2);margin-top:1px}
.ph-left{display:flex;align-items:center;gap:12px}
.ph-icon{
  width:38px;height:38px;border-radius:10px;
  background:linear-gradient(135deg,#8b5cf6,#ec4899);
  display:grid;place-items:center;font-size:1rem;
  box-shadow:0 0 18px rgba(139,92,246,.3),0 4px 10px rgba(0,0,0,.35);
  flex-shrink:0;
}
.ph-actions{display:flex;gap:8px}

/* ───────────── BUTTONS ───────────── */
.btn{
  display:inline-flex;align-items:center;gap:6px;
  border-radius:7px;padding:7px 14px;
  font-size:.72rem;font-weight:600;font-family:'Syne',sans-serif;
  transition:all .2s;white-space:nowrap;cursor:pointer;border:none;
  text-decoration:none;
}
.btn-ghost{
  background:var(--surface);border:1px solid var(--border);
  color:var(--txt-2);
}
.btn-ghost:hover{background:var(--surface2);color:var(--txt);border-color:rgba(255,255,255,.12)}
.btn-primary-aura{
  background:var(--rose);color:#fff;
  box-shadow:0 0 16px rgba(255,61,127,.3),0 4px 10px rgba(0,0,0,.25);
}
.btn-primary-aura:hover{opacity:.88;transform:translateY(-1px)}

/* ───────────── STAT GRID ───────────── */
.sg{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:10px}

.sc{
  background:var(--surface);border:1px solid var(--border);
  border-radius:var(--r);padding:13px;
  position:relative;overflow:hidden;
  transition:transform .22s,box-shadow .22s,border-color .22s;
  animation:fu .5s ease both;cursor:default;
}
.sc:hover{
  transform:translateY(-3px);
  box-shadow:0 8px 28px rgba(0,0,0,.4),0 0 28px var(--glow,rgba(139,92,246,.1));
  border-color:rgba(255,255,255,.11);
}
.sc::after{
  content:'';position:absolute;inset:0;
  background:linear-gradient(135deg,var(--c1,transparent),transparent 60%);
  opacity:.07;pointer-events:none;
}
.scblob{
  position:absolute;width:65px;height:65px;border-radius:50%;
  background:var(--c1);filter:blur(26px);opacity:.16;
  top:-14px;right:-14px;pointer-events:none;transition:opacity .3s;
}
.sc:hover .scblob{opacity:.3}
.sct{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:9px}
.scico{
  width:32px;height:32px;border-radius:9px;
  background:linear-gradient(135deg,var(--c1),var(--c2));
  display:grid;place-items:center;font-size:.9rem;
  box-shadow:0 3px 10px rgba(0,0,0,.3);flex-shrink:0;
}
.trnd{font-size:.62rem;padding:2px 6px;border-radius:100px;font-weight:600}
.up  {background:rgba(6,214,160,.1);color:var(--teal)}
.dn  {background:rgba(255,71,87,.1);color:var(--red)}
.scv{
  font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:800;
  letter-spacing:-.04em;line-height:1;color:#fff;
}
.scl{font-size:.68rem;color:var(--txt-2);margin-top:2px}
.scbar{height:2px;background:rgba(255,255,255,.06);border-radius:2px;margin-top:10px;overflow:hidden}
.scbarf{
  height:100%;border-radius:2px;
  background:linear-gradient(90deg,var(--c1),var(--c2));
  width:var(--p,60%);animation:bg 1.2s cubic-bezier(.4,0,.2,1) both;
}
@keyframes bg{from{width:0}}
.sc:nth-child(1){animation-delay:.04s}
.sc:nth-child(2){animation-delay:.08s}
.sc:nth-child(3){animation-delay:.12s}
.sc:nth-child(4){animation-delay:.16s}

.cb{--c1:#3b9eff;--c2:#80c0ff;--glow:rgba(59,158,255,.1)}
.cg{--c1:#06d6a0;--c2:#00ffcc;--glow:rgba(6,214,160,.1)}
.ca{--c1:#ffbe0b;--c2:#ffd55a;--glow:rgba(255,190,11,.1)}
.cv{--c1:#8b5cf6;--c2:#c084fc;--glow:rgba(139,92,246,.1)}

/* ───────────── FILTER BAR ───────────── */
.filter-bar{
  background:var(--surface);border:1px solid var(--border);
  border-radius:var(--r);padding:11px 14px;
  margin-bottom:10px;animation:fu .5s .1s ease both;
}
.filter-form{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.fi{
  background:var(--surface2);border:1px solid var(--border);
  border-radius:6px;padding:6px 11px;
  color:var(--txt);font-size:.72rem;outline:none;
  transition:border-color .18s,box-shadow .18s;
  -webkit-appearance:none;appearance:none;
  font-family:'Syne',sans-serif;
}
.fi:focus{border-color:rgba(139,92,246,.35);box-shadow:0 0 0 3px rgba(139,92,246,.08)}
.fi::placeholder{color:var(--txt-3)}
.fi-sel{
  padding-right:28px;
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' fill='rgba(255,255,255,0.25)' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
  background-repeat:no-repeat;background-position:calc(100% - 10px) center;
  cursor:pointer;min-width:130px;
}
.fi-sel option{background:#0d0f14}
.filter-actions{display:flex;gap:6px;margin-left:auto}

/* ───────────── RESULTS META ───────────── */
.results-meta{
  display:flex;justify-content:space-between;align-items:center;
  margin-bottom:8px;animation:fu .5s .13s ease both;
}
.results-count{font-size:.68rem;color:var(--txt-3);font-weight:500}
.results-count strong{color:var(--txt-2);font-weight:600}

/* ───────────── TABLE PANEL ───────────── */
.panel{
  background:var(--surface);border:1px solid var(--border);
  border-radius:var(--r);overflow:hidden;
  animation:fu .55s .16s ease both;
}
.dt{width:100%;border-collapse:collapse}
.dt thead th{
  padding:8px 12px;text-align:left;
  font-family:'Syne',sans-serif;font-size:.58rem;font-weight:700;
  letter-spacing:.09em;text-transform:uppercase;
  color:var(--txt-3);background:rgba(255,255,255,.02);
  border-bottom:1px solid var(--border);white-space:nowrap;
}
.dt tbody tr{border-bottom:1px solid rgba(255,255,255,.03);transition:background .12s}
.dt tbody tr:last-child{border-bottom:none}
.dt tbody tr:hover{background:rgba(255,255,255,.03)}
.dt td{padding:9px 12px;color:var(--txt);vertical-align:middle;font-size:.73rem}
.td-muted{color:var(--txt-2);font-size:.68rem}
.td-mono{font-family:monospace;font-size:.6rem;color:var(--txt-3)}

/* User cell */
.u-cell{display:flex;align-items:center;gap:9px}
.u-av{
  width:30px;height:30px;border-radius:8px;
  display:grid;place-items:center;
  font-family:'Syne',sans-serif;font-size:.58rem;font-weight:700;
  color:#fff;flex-shrink:0;
}
.u-name{font-size:.72rem;font-weight:500;color:var(--txt);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:150px}
.u-sub{font-family:monospace;font-size:.58rem;color:var(--txt-3);margin-top:1px}

/* Stripe ID pill */
.stripe-id{
  display:inline-flex;align-items:center;gap:5px;
  background:var(--surface2);border:1px solid var(--border);
  border-radius:5px;padding:2px 7px;
  font-family:monospace;font-size:.6rem;color:var(--txt-3);
  max-width:130px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;
}

/* Expiry */
.expiry-wrap{display:flex;align-items:center;gap:5px;flex-wrap:wrap}
.expiry-date{font-size:.71rem;color:var(--txt-2)}

/* Badges */
.bd{
  display:inline-flex;align-items:center;gap:3px;
  padding:2px 7px;border-radius:100px;
  font-size:.62rem;font-weight:600;white-space:nowrap;
}
.bddot{width:4px;height:4px;border-radius:50%;background:currentColor;animation:pulse 2s ease infinite}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.3}}

.bd-active  {background:rgba(6,214,160,.08);color:var(--teal);border:1px solid rgba(6,214,160,.2)}
.bd-inactive{background:rgba(255,255,255,.04);color:var(--txt-3);border:1px solid var(--border)}
.bd-expired {background:rgba(255,71,87,.1);color:#ff5f6d;border:1px solid rgba(255,71,87,.2)}
.bd-today   {background:rgba(255,190,11,.1);color:var(--amber);border:1px solid rgba(255,190,11,.22)}
.bd-renew   {background:rgba(6,214,160,.08);color:var(--teal);border:1px solid rgba(6,214,160,.2)}
.bd-norenew {background:rgba(255,255,255,.04);color:var(--txt-3);border:1px solid var(--border)}

/* Plan badges */
.bd-free    {background:rgba(255,255,255,.04);color:var(--txt-3);border:1px solid var(--border)}
.bd-gold    {background:rgba(255,190,11,.1);color:var(--amber);border:1px solid rgba(255,190,11,.22)}
.bd-plat    {background:rgba(139,92,246,.1);color:var(--violet);border:1px solid rgba(139,92,246,.22)}

/* Action buttons */
.act-btns{display:flex;align-items:center;gap:4px}
.ab{
  width:28px;height:28px;border-radius:6px;
  display:grid;place-items:center;font-size:.78rem;
  border:1px solid;transition:all .18s;flex-shrink:0;cursor:pointer;
  background:none;
}
.ab-view{color:var(--blue);border-color:rgba(59,158,255,.22);background:rgba(59,158,255,.07)}
.ab-view:hover{background:rgba(59,158,255,.18);border-color:rgba(59,158,255,.4);transform:translateY(-1px)}
.ab-cancel{color:#ff5f6d;border-color:rgba(255,71,87,.22);background:rgba(255,71,87,.07)}
.ab-cancel:hover{background:rgba(255,71,87,.18);border-color:rgba(255,71,87,.4);transform:translateY(-1px)}

/* ───────────── PAGINATION ───────────── */
.pag-wrap{
  display:flex;align-items:center;justify-content:space-between;
  padding:11px 14px;border-top:1px solid var(--border);gap:10px;flex-wrap:wrap;
}
.pag-info{font-size:.68rem;color:var(--txt-3);font-weight:500}
.pag-wrap nav{display:flex;align-items:center;gap:4px}
.pag-wrap nav a,
.pag-wrap nav span{
  min-width:28px;height:28px;
  display:inline-flex;align-items:center;justify-content:center;
  border-radius:6px;font-size:.7rem;font-weight:600;
  border:1px solid var(--border);background:var(--surface2);
  color:var(--txt-2);transition:all .18s;padding:0 8px;
}
.pag-wrap nav a:hover{background:var(--surface);color:var(--txt);border-color:rgba(255,255,255,.12)}
.pag-wrap nav span[aria-current="page"]{background:var(--violet);color:#fff;border-color:var(--violet);font-weight:700}
.pag-wrap nav span.disabled{opacity:.3;pointer-events:none}

/* ───────────── EMPTY STATE ───────────── */
.empty-state{text-align:center;padding:52px 20px}
.empty-ico{font-size:2.4rem;margin-bottom:12px;opacity:.3}
.empty-title{font-size:.88rem;font-weight:600;color:var(--txt-2);margin-bottom:4px;font-family:'Syne',sans-serif}
.empty-txt{font-size:.7rem;color:var(--txt-3)}

/* ───────────── MODAL OVERLAY ───────────── */
.modal-overlay{
  position:fixed;inset:0;
  background:rgba(5,7,12,.75);
  backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);
  display:flex;align-items:center;justify-content:center;
  z-index:900;opacity:0;visibility:hidden;
  transition:opacity .22s,visibility .22s;padding:20px;
}
.modal-overlay.open{opacity:1;visibility:visible}
.modal-overlay.open .modal-box{transform:translateY(0) scale(1);opacity:1}

.modal-box{
  background:var(--surface);border:1px solid var(--border);
  border-radius:var(--r);width:100%;max-width:520px;
  transform:translateY(16px) scale(.98);opacity:0;
  transition:transform .25s cubic-bezier(.34,1.56,.64,1),opacity .22s;
  box-shadow:0 24px 60px rgba(0,0,0,.5),0 0 0 1px rgba(255,255,255,.04);
}
.modal-head{
  display:flex;align-items:center;justify-content:space-between;
  padding:14px 18px;border-bottom:1px solid var(--border);
}
.modal-tag{
  display:inline-flex;align-items:center;
  background:rgba(139,92,246,.1);color:var(--violet);
  border:1px solid rgba(139,92,246,.2);border-radius:4px;
  font-size:.6rem;font-weight:700;letter-spacing:.08em;
  text-transform:uppercase;padding:2px 7px;margin-right:10px;
}
.modal-title{font-family:'Syne',sans-serif;font-size:.86rem;font-weight:700;color:var(--txt)}
.modal-close{
  width:28px;height:28px;border-radius:6px;display:grid;place-items:center;
  background:var(--surface2);border:1px solid var(--border);
  color:var(--txt-3);font-size:.8rem;transition:all .18s;cursor:pointer;
}
.modal-close:hover{background:rgba(255,255,255,.07);color:var(--txt)}
.modal-body{padding:18px;max-height:70vh;overflow-y:auto}
.modal-foot{
  display:flex;gap:8px;padding:13px 18px;
  border-top:1px solid var(--border);
}
.modal-foot .btn{flex:1;justify-content:center}

/* Detail grid inside modal */
.d-section{margin-bottom:16px}
.ds-title{
  font-size:.6rem;font-weight:700;letter-spacing:.1em;
  text-transform:uppercase;color:var(--txt-3);margin-bottom:9px;
  display:flex;align-items:center;gap:8px;
}
.ds-title::after{content:'';flex:1;height:1px;background:var(--border)}
.d-grid{display:grid;grid-template-columns:1fr 1fr;gap:7px}
.d-field{background:var(--surface2);border:1px solid var(--border);border-radius:7px;padding:8px 11px}
.d-field.full{grid-column:1/-1}
.d-lbl{font-size:.6rem;font-weight:700;color:var(--txt-3);text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px}
.d-val{font-size:.74rem;color:var(--txt);font-weight:500;word-break:break-all}
.d-val code{font-family:monospace;font-size:.62rem;color:var(--violet)}

/* ───────────── ANIMATIONS ───────────── */
@keyframes fu{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}

/* ───────────── RESPONSIVE ───────────── */
@media(max-width:1200px){.sg{grid-template-columns:repeat(2,1fr)}}
@media(max-width:900px){
  .dt thead th:nth-child(4),.dt td:nth-child(4){display:none}
}
@media(max-width:700px){
  .ph{flex-direction:column;align-items:flex-start;gap:10px}
  .sg{grid-template-columns:1fr 1fr}
  .dt thead th:nth-child(5),.dt td:nth-child(5){display:none}
}
@media(max-width:480px){.sg{grid-template-columns:1fr}}
</style>
@endsection

@section('content')

{{-- ── PAGE HEADER ── --}}
<div class="ph">
    <div class="ph-left">
        <div class="ph-icon">💳</div>
        <div>
            <div class="ph-title">Subscriptions</div>
            <div class="ph-sub">Manage plans, renewals &amp; billing status</div>
        </div>
    </div>
    <div class="ph-actions">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost">← Dashboard</a>
    </div>
</div>

{{-- ── STAT STRIP ── --}}
<div class="sg">
    <div class="sc cb">
        <div class="scblob"></div>
        <div class="sct"><div class="scico">💳</div><span class="trnd up">All</span></div>
        <div class="scv">{{ number_format($stats['total']) }}</div>
        <div class="scl">Total Subscriptions</div>
        <div class="scbar"><div class="scbarf" style="--p:100%"></div></div>
    </div>
    <div class="sc cg">
        <div class="scblob"></div>
        <div class="sct"><div class="scico">✅</div><span class="trnd up">Live</span></div>
        <div class="scv">{{ number_format($stats['active']) }}</div>
        <div class="scl">Active</div>
        <div class="scbar"><div class="scbarf" style="--p:{{ $stats['total'] > 0 ? round($stats['active']/$stats['total']*100) : 0 }}%"></div></div>
    </div>
    <div class="sc ca">
        <div class="scblob"></div>
        <div class="sct"><div class="scico">🥇</div><span class="trnd up">Gold</span></div>
        <div class="scv">{{ number_format($stats['gold']) }}</div>
        <div class="scl">Gold Plan</div>
        <div class="scbar"><div class="scbarf" style="--p:{{ $stats['total'] > 0 ? round($stats['gold']/$stats['total']*100) : 0 }}%"></div></div>
    </div>
    <div class="sc cv">
        <div class="scblob"></div>
        <div class="sct"><div class="scico">💎</div><span class="trnd up">Platinum</span></div>
        <div class="scv">{{ number_format($stats['platinum']) }}</div>
        <div class="scl">Platinum Plan</div>
        <div class="scbar"><div class="scbarf" style="--p:{{ $stats['total'] > 0 ? round($stats['platinum']/$stats['total']*100) : 0 }}%"></div></div>
    </div>
</div>

{{-- ── FILTER BAR ── --}}
<div class="filter-bar">
    <form method="GET" action="{{ route('admin.subscriptions.index') }}" class="filter-form">
        <select name="plan" class="fi fi-sel">
            <option value="">All Plans</option>
            <option value="free"     @selected(request('plan')==='free')>Free</option>
            <option value="gold"     @selected(request('plan')==='gold')>🥇 Gold</option>
            <option value="platinum" @selected(request('plan')==='platinum')>💎 Platinum</option>
        </select>
        <select name="status" class="fi fi-sel">
            <option value="">All Status</option>
            <option value="active"   @selected(request('status')==='active')>Active</option>
            <option value="inactive" @selected(request('status')==='inactive')>Inactive</option>
        </select>
        <div class="filter-actions">
            <button type="submit" class="btn btn-primary-aura" style="padding:6px 14px">⚡ Filter</button>
            <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-ghost" style="padding:6px 12px">✕ Clear</a>
        </div>
    </form>
</div>

{{-- ── RESULTS META ── --}}
<div class="results-meta">
    <span class="results-count">
        Showing <strong>{{ $subscriptions->firstItem() ?? 0 }}–{{ $subscriptions->lastItem() ?? 0 }}</strong>
        of <strong>{{ $subscriptions->total() ?? 0 }}</strong> subscriptions
    </span>
    <span class="bd bd-plat">💎 Subscription List</span>
</div>

{{-- ── TABLE ── --}}
<div class="panel">
    <div style="overflow-x:auto">
        <table class="dt">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Plan</th>
                    <th>Stripe ID</th>
                    <th>Started</th>
                    <th>Expires</th>
                    <th>Auto Renew</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subscriptions as $sub)
                    @php
                        $palettes = [
                            ['#8b5cf6','#ec4899'],
                            ['#3b9eff','#8b5cf6'],
                            ['#ffbe0b','#ff9500'],
                            ['#06d6a0','#3b9eff'],
                            ['#ff3d7f','#8b5cf6'],
                            ['#14b8a6','#06d6a0'],
                            ['#ec4899','#ffbe0b'],
                        ];
                        $c        = $palettes[$sub->id % 7];
                        $email    = $sub->user->email ?? $sub->user->phone ?? null;
                        $initials = strtoupper(substr($email ?? '?', 0, 2));
                    @endphp
                    <tr>
                        {{-- ID --}}
                        <td class="td-mono">{{ $sub->id }}</td>

                        {{-- User --}}
                        <td>
                            @if($sub->user)
                                <div class="u-cell">
                                    <div class="u-av" style="background:linear-gradient(135deg,{{ $c[0] }},{{ $c[1] }})">{{ $initials }}</div>
                                    <div>
                                        <a href="{{ route('admin.users.index', ['search' => $email]) }}"
                                           style="font-size:.72rem;font-weight:500;color:var(--txt);text-decoration:none"
                                           class="u-name">{{ $email }}</a>
                                        <div class="u-sub">#{{ $sub->user->id }}</div>
                                    </div>
                                </div>
                            @else
                                <span class="td-muted">—</span>
                            @endif
                        </td>

                        {{-- Plan --}}
                        <td>
                            @if($sub->plan === 'platinum')
                                <span class="bd bd-plat">💎 Platinum</span>
                            @elseif($sub->plan === 'gold')
                                <span class="bd bd-gold">🥇 Gold</span>
                            @else
                                <span class="bd bd-free">Free</span>
                            @endif
                        </td>

                        {{-- Stripe ID --}}
                        <td>
                            @if($sub->stripe_subscription_id)
                                <span class="stripe-id" title="{{ $sub->stripe_subscription_id }}">
                                    {{ substr($sub->stripe_subscription_id, 0, 18) }}…
                                </span>
                            @else
                                <span class="td-muted">—</span>
                            @endif
                        </td>

                        {{-- Started --}}
                        <td class="td-muted">{{ $sub->started_at->format('M d, Y') }}</td>

                        {{-- Expires --}}
                        <td>
                            @if($sub->expires_at)
                                <div class="expiry-wrap">
                                    <span class="expiry-date">{{ $sub->expires_at->format('M d, Y') }}</span>
                                    @if($sub->expires_at->isPast())
                                        <span class="bd bd-expired">Expired</span>
                                    @elseif($sub->expires_at->isToday())
                                        <span class="bd bd-today">⚡ Today</span>
                                    @elseif($sub->expires_at->diffInDays(now()) <= 7)
                                        <span class="bd bd-today">⏳ Soon</span>
                                    @endif
                                </div>
                            @else
                                <span class="td-muted">—</span>
                            @endif
                        </td>

                        {{-- Auto Renew --}}
                        <td>
                            @if($sub->auto_renew)
                                <span class="bd bd-renew">🔄 Yes</span>
                            @else
                                <span class="bd bd-norenew">Off</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td>
                            @if($sub->is_active)
                                <span class="bd bd-active"><span class="bddot"></span>Active</span>
                            @else
                                <span class="bd bd-inactive">Inactive</span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div class="act-btns">
                                <button class="ab ab-view" onclick="openModal('subModal{{ $sub->id }}')" title="View details">👁</button>
                                @if($sub->is_active && $sub->plan !== 'free')
                                    <form method="POST" action="{{ route('admin.subscriptions.cancel', $sub->id) }}" style="display:contents">
                                        @csrf
                                        <button type="submit" class="ab ab-cancel" title="Cancel subscription"
                                            onclick="return confirm('Cancel this subscription?')">✕</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <div class="empty-ico">💳</div>
                                <div class="empty-title">No subscriptions found</div>
                                <div class="empty-txt">Try adjusting your filters</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pag-wrap">
        <span class="pag-info">Page {{ $subscriptions->currentPage() }} of {{ $subscriptions->lastPage() }}</span>
        {{ $subscriptions->withQueryString()->links() }}
    </div>
</div>

{{-- ══════════════════════════════════════════
     DETAIL MODALS — one per subscription
══════════════════════════════════════════════ --}}
@foreach($subscriptions as $sub)
    <div id="subModal{{ $sub->id }}" class="modal-overlay" onclick="if(event.target===this)closeModal('subModal{{ $sub->id }}')">
        <div class="modal-box" onclick="event.stopPropagation()">

            <div class="modal-head">
                <div style="display:flex;align-items:center">
                    <span class="modal-tag">Sub #{{ $sub->id }}</span>
                    <span class="modal-title">Subscription Details</span>
                </div>
                <button class="modal-close" onclick="closeModal('subModal{{ $sub->id }}')">✕</button>
            </div>

            <div class="modal-body">

                {{-- Identity --}}
                <div class="d-section">
                    <div class="ds-title">Identity</div>
                    <div class="d-grid">
                        <div class="d-field">
                            <div class="d-lbl">Sub ID</div>
                            <div class="d-val">#{{ $sub->id }}</div>
                        </div>
                        <div class="d-field">
                            <div class="d-lbl">Plan</div>
                            <div class="d-val">
                                @if($sub->plan === 'platinum')
                                    <span class="bd bd-plat">💎 Platinum</span>
                                @elseif($sub->plan === 'gold')
                                    <span class="bd bd-gold">🥇 Gold</span>
                                @else
                                    <span class="bd bd-free">Free</span>
                                @endif
                            </div>
                        </div>
                        <div class="d-field">
                            <div class="d-lbl">Status</div>
                            <div class="d-val">
                                @if($sub->is_active)
                                    <span class="bd bd-active"><span class="bddot"></span>Active</span>
                                @else
                                    <span class="bd bd-inactive">Inactive</span>
                                @endif
                            </div>
                        </div>
                        <div class="d-field">
                            <div class="d-lbl">Auto Renew</div>
                            <div class="d-val">{{ $sub->auto_renew ? '🔄 Yes' : 'Off' }}</div>
                        </div>
                    </div>
                </div>

                {{-- User --}}
                <div class="d-section">
                    <div class="ds-title">User</div>
                    <div class="d-grid">
                        <div class="d-field full">
                            <div class="d-lbl">Email / Phone</div>
                            <div class="d-val">{{ $sub->user->email ?? $sub->user->phone ?? 'Unknown' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Billing --}}
                <div class="d-section">
                    <div class="ds-title">Billing</div>
                    <div class="d-grid">
                        <div class="d-field full">
                            <div class="d-lbl">Stripe Subscription ID</div>
                            <div class="d-val"><code>{{ $sub->stripe_subscription_id ?? 'N/A' }}</code></div>
                        </div>
                        <div class="d-field full">
                            <div class="d-lbl">Stripe Customer ID</div>
                            <div class="d-val"><code>{{ $sub->stripe_customer_id ?? 'N/A' }}</code></div>
                        </div>
                    </div>
                </div>

                {{-- Dates --}}
                <div class="d-section">
                    <div class="ds-title">Dates</div>
                    <div class="d-grid">
                        <div class="d-field">
                            <div class="d-lbl">Started</div>
                            <div class="d-val">{{ $sub->started_at->format('d M Y, h:i A') }}</div>
                        </div>
                        <div class="d-field">
                            <div class="d-lbl">Expires</div>
                            <div class="d-val">{{ $sub->expires_at ? $sub->expires_at->format('d M Y, h:i A') : '—' }}</div>
                        </div>
                        <div class="d-field full">
                            <div class="d-lbl">Record Created</div>
                            <div class="d-val">{{ $sub->created_at->format('d M Y, h:i A') }}</div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-foot">
                <button type="button" class="btn btn-ghost" onclick="closeModal('subModal{{ $sub->id }}')">
                    ✕ Close
                </button>
                @if($sub->is_active && $sub->plan !== 'free')
                    <form method="POST" action="{{ route('admin.subscriptions.cancel', $sub->id) }}" style="flex:1">
                        @csrf
                        <button type="submit" class="btn" style="width:100%;justify-content:center;background:rgba(255,71,87,.1);color:#ff5f6d;border:1px solid rgba(255,71,87,.2)"
                            onclick="return confirm('Cancel this subscription?')">
                            ✕ Cancel Subscription
                        </button>
                    </form>
                @endif
            </div>

        </div>
    </div>
@endforeach

@endsection

@section('scripts')
<script>
function openModal(id){
    var el = document.getElementById(id);
    if(el){ el.classList.add('open'); document.body.style.overflow='hidden'; }
}
function closeModal(id){
    var el = document.getElementById(id);
    if(el){ el.classList.remove('open'); document.body.style.overflow=''; }
}
document.addEventListener('keydown', function(e){
    if(e.key === 'Escape'){
        document.querySelectorAll('.modal-overlay.open').forEach(function(m){
            m.classList.remove('open');
        });
        document.body.style.overflow = '';
    }
});
</script>
@endsection