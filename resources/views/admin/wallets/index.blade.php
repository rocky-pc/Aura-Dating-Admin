@extends('admin.layout')

@section('title', 'Aura — Wallets')

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
.dchip{
  display:flex;align-items:center;gap:5px;
  background:var(--surface);border:1px solid var(--border);
  border-radius:7px;padding:4px 10px;font-size:.68rem;color:var(--txt-2);
}
.dchip i{font-size:.7rem;color:var(--rose)}

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
  box-shadow:0 8px 28px rgba(0,0,0,.4),0 0 28px var(--glow,rgba(255,61,127,.1));
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
.up{background:rgba(6,214,160,.1);color:var(--teal)}
.dn{background:rgba(255,71,87,.1);color:var(--red)}
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

.cr{--c1:#ff3d7f;--c2:#ff8ab8;--glow:rgba(255,61,127,.12)}
.cg{--c1:#06d6a0;--c2:#00ffcc;--glow:rgba(6,214,160,.1)}
.ca{--c1:#ffbe0b;--c2:#ffd55a;--glow:rgba(255,190,11,.1)}
.cb{--c1:#3b9eff;--c2:#80c0ff;--glow:rgba(59,158,255,.1)}
.cv{--c1:#8b5cf6;--c2:#c084fc;--glow:rgba(139,92,246,.1)}
.ct{--c1:#14b8a6;--c2:#2dd4bf;--glow:rgba(20,184,166,.1)}

/* ───────────── TOOLBAR ───────────── */
.toolbar{
  display:flex;justify-content:space-between;align-items:center;
  gap:10px;margin-bottom:10px;animation:fu .5s .1s ease both;
}
.search-wrap{
  position:relative;flex:1;max-width:280px;
}
.search-wrap input{
  width:100%;background:var(--surface);border:1px solid var(--border);
  border-radius:8px;padding:7px 10px 7px 32px;
  font-size:.73rem;color:var(--txt);outline:none;transition:border-color .2s;
}
.search-wrap input:focus{border-color:rgba(255,61,127,.4)}
.search-wrap input::placeholder{color:var(--txt-3)}
.search-ico{
  position:absolute;left:10px;top:50%;transform:translateY(-50%);
  font-size:.75rem;color:var(--txt-3);pointer-events:none;
}
.filter-btns{display:flex;gap:6px}
.fbtn{
  font-size:.68rem;padding:5px 11px;border-radius:7px;font-weight:600;
  background:var(--surface);border:1px solid var(--border);color:var(--txt-2);
  cursor:pointer;transition:all .18s;
}
.fbtn:hover,.fbtn.active{
  background:rgba(255,61,127,.08);border-color:rgba(255,61,127,.3);color:var(--rose);
}
.bulk-actions{display:flex;gap:6px;align-items:center}
.bulk-btn{
  font-size:.68rem;padding:5px 11px;border-radius:7px;font-weight:600;
  cursor:pointer;transition:all .18s;display:flex;align-items:center;gap:5px;
}
.bulk-add{background:rgba(6,214,160,.08);border:1px solid rgba(6,214,160,.2);color:var(--teal)}
.bulk-add:hover{background:rgba(6,214,160,.15)}
.bulk-reset{background:rgba(255,71,87,.06);border:1px solid rgba(255,71,87,.18);color:#ff5f6d}
.bulk-reset:hover{background:rgba(255,71,87,.13)}

/* ───────────── MAIN PANEL ───────────── */
.panel{
  background:var(--surface);border:1px solid var(--border);
  border-radius:var(--r);overflow:hidden;
  animation:fu .55s .2s ease both;
}
.phead{
  display:flex;justify-content:space-between;align-items:center;
  padding:10px 14px;border-bottom:1px solid var(--border);
}
.ptitle{font-family:'Syne',sans-serif;font-size:.8rem;font-weight:700;color:var(--txt)}
.ptitle-sub{font-size:.65rem;color:var(--txt-3);margin-left:6px;font-weight:400;font-family:inherit}
.vabtn{
  font-size:.68rem;color:var(--rose);
  background:rgba(255,61,127,.08);border:1px solid rgba(255,61,127,.18);
  border-radius:6px;padding:3px 9px;font-weight:600;transition:background .2s;text-decoration:none;
}
.vabtn:hover{background:rgba(255,61,127,.15)}

/* ───────────── TABLE ───────────── */
.dt{width:100%;border-collapse:collapse}
.dt thead th{
  padding:7px 12px;text-align:left;
  font-family:'Syne',sans-serif;font-size:.58rem;font-weight:700;
  letter-spacing:.09em;text-transform:uppercase;
  color:var(--txt-3);background:rgba(255,255,255,.02);
  border-bottom:1px solid var(--border);white-space:nowrap;
}
.dt thead th.center{text-align:center}
.dt tbody tr{border-bottom:1px solid rgba(255,255,255,.03);transition:background .12s}
.dt tbody tr:last-child{border-bottom:none}
.dt tbody tr:hover{background:rgba(255,255,255,.03)}
.dt td{padding:9px 12px;color:var(--txt);vertical-align:middle;font-size:.73rem}
.dt td.m{color:var(--txt-2);font-size:.68rem}
.dt td.mn{font-family:monospace;font-size:.63rem;color:var(--txt-3)}
.dt td.center{text-align:center}

/* user cell */
.uc2{display:flex;align-items:center;gap:8px}
.uav2{
  width:28px;height:28px;border-radius:8px;flex-shrink:0;
  background:linear-gradient(135deg,var(--rose),var(--violet));
  display:grid;place-items:center;
  font-family:'Syne',sans-serif;font-size:.58rem;font-weight:700;color:#fff;
}
.uname{font-size:.73rem;color:var(--txt);font-weight:600;line-height:1.2}
.uemail{font-size:.62rem;color:var(--txt-3);margin-top:1px;max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}

/* balance pill */
.bal{
  display:inline-flex;align-items:center;gap:4px;
  font-family:'Syne',sans-serif;font-size:.75rem;font-weight:700;
}
.bal-ico{font-size:.7rem}
.bal-hi{color:#fff}
.bal-lo{color:var(--txt-2)}

/* bonus badge */
.bonus-tag{
  display:inline-flex;align-items:center;gap:3px;
  padding:2px 7px;border-radius:100px;
  font-size:.62rem;font-weight:600;
  background:rgba(255,190,11,.08);color:var(--amber);
  border:1px solid rgba(255,190,11,.18);
}

/* total pill */
.total-tag{
  display:inline-flex;align-items:center;gap:3px;
  padding:2px 8px;border-radius:100px;
  font-size:.65rem;font-weight:700;font-family:'Syne',sans-serif;
  background:rgba(139,92,246,.08);color:#c084fc;
  border:1px solid rgba(139,92,246,.2);
}

/* inline action form */
.act-row{display:flex;align-items:center;gap:5px;flex-wrap:nowrap}
.act-row input[type=number]{
  width:62px;background:var(--surface2);border:1px solid var(--border);
  border-radius:6px;padding:4px 7px;font-size:.68rem;color:var(--txt);
  text-align:center;outline:none;transition:border-color .2s;
  -moz-appearance:textfield;
}
.act-row input[type=number]:focus{border-color:rgba(6,214,160,.4)}
.act-row input::-webkit-outer-spin-button,
.act-row input::-webkit-inner-spin-button{-webkit-appearance:none}
.act-row select{
  background:var(--surface2);border:1px solid var(--border);
  border-radius:6px;padding:4px 6px;font-size:.65rem;color:var(--txt-2);
  outline:none;cursor:pointer;transition:border-color .2s;
}
.act-row select:focus{border-color:rgba(6,214,160,.4)}

.btn-add{
  padding:4px 10px;border-radius:6px;font-size:.65rem;font-weight:700;
  background:rgba(6,214,160,.1);border:1px solid rgba(6,214,160,.25);color:var(--teal);
  cursor:pointer;transition:all .18s;white-space:nowrap;
}
.btn-add:hover{background:rgba(6,214,160,.2);border-color:rgba(6,214,160,.45)}

.btn-reset{
  padding:4px 9px;border-radius:6px;font-size:.65rem;font-weight:700;
  background:rgba(255,71,87,.07);border:1px solid rgba(255,71,87,.2);color:#ff5f6d;
  cursor:pointer;transition:all .18s;white-space:nowrap;
}
.btn-reset:hover{background:rgba(255,71,87,.16);border-color:rgba(255,71,87,.4)}

/* divider between action groups */
.act-sep{width:1px;height:20px;background:var(--border);margin:0 2px;flex-shrink:0}

/* ───────────── PAGINATION ───────────── */
.pag-wrap{
  padding:10px 14px;border-top:1px solid var(--border);
  display:flex;justify-content:space-between;align-items:center;
}
.pag-info{font-size:.65rem;color:var(--txt-3)}
.pag-links{display:flex;gap:4px;align-items:center}
.pag-links .page-link{
  min-width:28px;height:28px;display:grid;place-items:center;
  border-radius:7px;font-size:.68rem;font-weight:600;
  background:var(--surface);border:1px solid var(--border);color:var(--txt-2);
  text-decoration:none;transition:all .18s;padding:0 6px;
}
.pag-links .page-link:hover,
.pag-links .page-link.active{
  background:rgba(255,61,127,.1);border-color:rgba(255,61,127,.3);color:var(--rose);
}

/* ───────────── EMPTY STATE ───────────── */
.empty{
  padding:50px 20px;text-align:center;color:var(--txt-3);
  font-size:.75rem;
}
.empty .emp-ico{font-size:2rem;margin-bottom:8px;display:block}

/* ───────────── SIDE PANEL ───────────── */
.wl-layout{display:grid;grid-template-columns:1fr 240px;gap:10px;margin-top:0}
.side-col{display:flex;flex-direction:column;gap:10px;animation:fu .55s .3s ease both}

.mp{
  background:var(--surface);border:1px solid var(--border);
  border-radius:var(--r);padding:13px;
}
.mp h4{
  font-family:'Syne',sans-serif;font-size:.76rem;font-weight:700;
  color:var(--txt);margin-bottom:10px;
}

/* Donut */
.dw{display:flex;align-items:center;gap:14px}
.dsv{transform:rotate(-90deg);flex-shrink:0}
.rbg{fill:none;stroke:rgba(255,255,255,.06);stroke-width:7}
.rfl-bal{
  fill:none;stroke-width:7;stroke-linecap:round;stroke:url(#wg1);
  stroke-dasharray:214;stroke-dashoffset:54;
  animation:rd 1.3s cubic-bezier(.4,0,.2,1) .4s both;
}
.rfl-bon{
  fill:none;stroke-width:3;stroke-linecap:round;stroke:url(#wg2);
  stroke-dasharray:214;stroke-dashoffset:150;opacity:.5;
  animation:rd2 1.3s cubic-bezier(.4,0,.2,1) .5s both;
}
@keyframes rd{from{stroke-dashoffset:214}}
@keyframes rd2{from{stroke-dashoffset:214}}
.dpct{font-family:'Syne',sans-serif;font-size:1.3rem;font-weight:800;color:#fff;line-height:1}
.dlbl{font-size:.62rem;color:var(--txt-2);margin-top:2px}
.dleg{margin-top:10px;display:flex;flex-direction:column;gap:6px}
.dli{display:flex;justify-content:space-between;align-items:center;font-size:.68rem}
.dll{display:flex;align-items:center;gap:5px;color:var(--txt-2)}
.dld{width:6px;height:6px;border-radius:50%;flex-shrink:0}
.dlv{font-weight:700;color:var(--txt);font-family:'Syne',sans-serif;font-size:.7rem}

/* Quick strip */
.qs{display:grid;grid-template-columns:1fr 1fr;gap:7px}
.qsi{
  background:var(--surface2);border:1px solid var(--border);
  border-radius:var(--rsm);padding:9px 8px;text-align:center;
}
.qsv{font-family:'Syne',sans-serif;font-size:1rem;font-weight:800;color:#fff;line-height:1}
.qsl{font-size:.6rem;color:var(--txt-3);margin-top:2px}

/* Recent txns */
.tx-list{display:flex;flex-direction:column;gap:0}
.tx-item{
  display:flex;align-items:center;justify-content:space-between;
  padding:7px 0;border-bottom:1px solid rgba(255,255,255,.03);
}
.tx-item:last-child{border-bottom:none}
.tx-left{display:flex;align-items:center;gap:7px}
.tx-ico{
  width:26px;height:26px;border-radius:7px;
  display:grid;place-items:center;font-size:.75rem;flex-shrink:0;
}
.tx-name{font-size:.7rem;color:var(--txt);font-weight:600}
.tx-time{font-size:.6rem;color:var(--txt-3)}
.tx-amt{font-family:'Syne',sans-serif;font-size:.72rem;font-weight:700}
.tx-pos{color:var(--teal)}
.tx-neg{color:#ff5f6d}

/* ───────────── ANIMATIONS ───────────── */
@keyframes fu{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:none}}

/* ───────────── MODAL ───────────── */
.modal{
  position:fixed;inset:0;background:rgba(0,0,0,.8);
  display:flex;align-items:center;justify-content:center;z-index:1000;
}
.modal-content{
  background:var(--surface);border:1px solid var(--border);
  border-radius:var(--r);padding:20px;max-width:400px;width:90%;
  animation:fu .3s ease both;
}
.modal h3{
  font-family:'Syne',sans-serif;font-size:1rem;font-weight:700;
  color:var(--txt);margin-bottom:15px;
}
.modal form{display:flex;flex-direction:column;gap:10px}
.modal label{font-size:.75rem;color:var(--txt-2);font-weight:600}
.modal input[type=number], .modal select{
  background:var(--surface2);border:1px solid var(--border);
  border-radius:6px;padding:8px;font-size:.8rem;color:var(--txt);
  outline:none;transition:border-color .2s;
}
.modal input[type=number]:focus, .modal select:focus{border-color:var(--teal)}
.modal .modal-buttons{display:flex;gap:10px;margin-top:10px}
.modal button{
  padding:8px 16px;border-radius:6px;font-size:.8rem;font-weight:600;
  transition:all .18s;flex:1;
}
.modal .btn-add{background:var(--teal);color:#fff;border:1px solid var(--teal)}
.modal .btn-add:hover{background:#05b890}
.modal .btn-cancel{background:var(--surface2);border:1px solid var(--border);color:var(--txt-2)}
.modal .btn-cancel:hover{background:var(--surface)}

/* ───────────── NOTIFICATION ───────────── */
.notification{
  position:fixed;top:20px;right:20px;max-width:300px;
  background:var(--surface);border:1px solid var(--border);
  border-radius:var(--r);padding:12px 16px;
  box-shadow:0 4px 20px rgba(0,0,0,.5);z-index:1001;
  animation:slideIn .3s ease both;
}
.notification.success{border-color:var(--teal);background:rgba(6,214,160,.1)}
.notification.error{border-color:var(--red);background:rgba(255,71,87,.1)}
.notification .notif-msg{font-size:.8rem;color:var(--txt)}
.notification .notif-close{
  position:absolute;top:8px;right:8px;width:16px;height:16px;
  display:grid;place-items:center;font-size:.7rem;color:var(--txt-3);
  cursor:pointer;border-radius:50%;transition:background .2s;
}
.notification .notif-close:hover{background:var(--surface2);color:var(--txt)}
@keyframes slideIn{from{transform:translateX(100%);opacity:0}to{transform:none;opacity:1}}

/* ───────────── RESPONSIVE ───────────── */
@media(max-width:900px){
  .wl-layout{grid-template-columns:1fr}
  .side-col{display:grid;grid-template-columns:1fr 1fr;gap:10px}
  .sg{grid-template-columns:repeat(2,1fr)}
}
@media(max-width:600px){
  .sg{grid-template-columns:1fr 1fr}
  .side-col{grid-template-columns:1fr}
  .toolbar{flex-wrap:wrap}
}
</style>
@endsection

@section('content')

{{-- ── Page Header ── --}}
<div class="ph">
    <div>
        <div class="ph-title">💳 Wallets</div>
        <div class="ph-sub">Manage user balances, bonus points &amp; rewards</div>
    </div>
    <div class="dchip">
        <i>●</i> {{ now()->format('d M Y') }}
    </div>
</div>

{{-- ── Notification ── --}}
<div id="notification" class="notification" style="display:none">
    <div class="notif-msg" id="notifMsg"></div>
    <div class="notif-close" onclick="hideNotification()">×</div>
</div>

{{-- ── Stat Cards ── --}}
<div class="sg">
    <div class="sc cg">
        <div class="scblob"></div>
        <div class="sct"><div class="scico">💰</div><span class="trnd up">+12%</span></div>
        <div class="scv">{{ number_format($totalBalance ?? 128450) }}</div>
        <div class="scl">Total Balance Pool</div>
        <div class="scbar"><div class="scbarf" style="--p:72%"></div></div>
    </div>
    <div class="sc ca">
        <div class="scblob"></div>
        <div class="sct"><div class="scico">⭐</div><span class="trnd up">+9%</span></div>
        <div class="scv">{{ number_format($totalBonus ?? 34820) }}</div>
        <div class="scl">Total Bonus Points</div>
        <div class="scbar"><div class="scbarf" style="--p:38%"></div></div>
    </div>
    <div class="sc cv">
        <div class="scblob"></div>
        <div class="sct"><div class="scico">👛</div><span class="trnd up">+5%</span></div>
        <div class="scv">{{ number_format($wallets->total() ?? 3241) }}</div>
        <div class="scl">Total Wallets</div>
        <div class="scbar"><div class="scbarf" style="--p:55%"></div></div>
    </div>
    <div class="sc cb">
        <div class="scblob"></div>
        <div class="sct"><div class="scico">📊</div><span class="trnd up">+3%</span></div>
        <div class="scv">{{ number_format(($totalBalance ?? 128450) / max($wallets->total() ?? 1, 1), 0) }}</div>
        <div class="scl">Avg. Balance / User</div>
        <div class="scbar"><div class="scbarf" style="--p:44%"></div></div>
    </div>
</div>

{{-- ── Main + Side layout ── --}}
<div class="wl-layout">

    {{-- ── LEFT: Table panel ── --}}
    <div>

        {{-- Toolbar --}}
        <div class="toolbar">
            <div class="search-wrap">
                <span class="search-ico">🔍</span>
                <input type="text" placeholder="Search by name or email…" id="walletSearch">
            </div>
            <div class="filter-btns">
                <button class="fbtn active" onclick="filterWallets('all',this)">All</button>
                <button class="fbtn" onclick="filterWallets('high',this)">High Balance</button>
                <button class="fbtn" onclick="filterWallets('bonus',this)">Has Bonus</button>
                <button class="fbtn" onclick="filterWallets('zero',this)">Zero Balance</button>
            </div>
        </div>

        {{-- Table --}}
        <div class="panel">
            <div class="phead">
                <div>
                    <span class="ptitle">All Wallets</span>
                    <span class="ptitle-sub">{{ $wallets->total() ?? 0 }} total</span>
                </div>
                <a href="{{ route('admin.wallets.export') ?? '#' }}" class="vabtn">Export CSV →</a>
            </div>

            <div style="overflow-x:auto">
                <table class="dt" id="walletsTable">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th class="center">Balance</th>
                            <th class="center">Bonus Points</th>
                            <th class="center">Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($wallets as $wallet)
                        <tr>
                             {{-- User cell --}}
                             <td>
                                 <div class="uc2">
                                     <div class="uav2">
                                         {{ $wallet->user ? strtoupper(substr($wallet->user->profile->first_name ?? $wallet->user->email ?? '?', 0, 2)) : '??' }}
                                     </div>
                                     <div>
                                         <div class="uname">
                                             {{ $wallet->user ? ($wallet->user->profile->first_name ?? 'N/A') : 'Deleted User' }}
                                             {{ $wallet->user ? ($wallet->user->profile->last_name ?? '') : '' }}
                                         </div>
                                         <div class="uemail">{{ $wallet->user ? $wallet->user->email : 'N/A' }}</div>
                                     </div>
                                 </div>
                             </td>

                            {{-- Balance --}}
                            <td class="center">
                                <div class="bal">
                                    <span class="bal-ico">💵</span>
                                    <span class="bal-hi">{{ number_format($wallet->balance) }}</span>
                                </div>
                            </td>

                            {{-- Bonus --}}
                            <td class="center">
                                @if($wallet->bonus_points > 0)
                                    <span class="bonus-tag">⭐ {{ number_format($wallet->bonus_points) }}</span>
                                @else
                                    <span style="color:var(--txt-3);font-size:.65rem">—</span>
                                @endif
                            </td>

                            {{-- Total --}}
                            <td class="center">
                                <span class="total-tag">
                                    {{ number_format($wallet->balance + $wallet->bonus_points) }}
                                </span>
                            </td>

                             {{-- Actions --}}
                             <td>
                                 <div class="act-row">
                                     {{-- Add points button --}}
                                     <button type="button" class="btn-add" onclick="openAddPointsModal({{ $wallet->id }}, '{{ addslashes($wallet->user ? ($wallet->user->profile->first_name ?? $wallet->user->email) : 'Deleted User') }}')">+ Add</button>

                                     <span class="act-sep"></span>

                                     {{-- Reset button --}}
                                     <button type="button" class="btn-reset" onclick="resetWallet({{ $wallet->id }}, '{{ addslashes($wallet->user ? ($wallet->user->profile->first_name ?? $wallet->user->email) : 'Deleted User') }}')">⟳ Reset</button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty">
                                    <span class="emp-ico">👛</span>
                                    No wallets found
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($wallets->hasPages())
            <div class="pag-wrap">
                <div class="pag-info">
                    Showing {{ $wallets->firstItem() }}–{{ $wallets->lastItem() }} of {{ $wallets->total() }}
                </div>
                <div class="pag-links">
                    @if($wallets->onFirstPage())
                        <span class="page-link" style="opacity:.35">‹</span>
                    @else
                        <a href="{{ $wallets->previousPageUrl() }}" class="page-link">‹</a>
                    @endif

                    @foreach($wallets->getUrlRange(max(1,$wallets->currentPage()-2), min($wallets->lastPage(),$wallets->currentPage()+2)) as $page => $url)
                        <a href="{{ $url }}" class="page-link {{ $page == $wallets->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                    @endforeach

                    @if($wallets->hasMorePages())
                        <a href="{{ $wallets->nextPageUrl() }}" class="page-link">›</a>
                    @else
                        <span class="page-link" style="opacity:.35">›</span>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ── RIGHT: Side column ── --}}
    <div class="side-col">

        {{-- Balance vs Bonus donut --}}
        <div class="mp">
            <h4>Balance Split</h4>
            <div class="dw">
                <svg width="86" height="86" viewBox="0 0 86 86" class="dsv">
                    <defs>
                        <linearGradient id="wg1" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#06d6a0"/>
                            <stop offset="100%" stop-color="#3b9eff"/>
                        </linearGradient>
                        <linearGradient id="wg2" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#ffbe0b"/>
                            <stop offset="100%" stop-color="#ff3d7f"/>
                        </linearGradient>
                    </defs>
                    <circle class="rbg" cx="43" cy="43" r="34"/>
                    <circle class="rfl-bal" cx="43" cy="43" r="34"/>
                    <circle class="rfl-bon" cx="43" cy="43" r="26"/>
                </svg>
                <div>
                    <div class="dpct">79%</div>
                    <div class="dlbl">Real Balance</div>
                </div>
            </div>
            <div class="dleg">
                <div class="dli">
                    <div class="dll"><div class="dld" style="background:#06d6a0"></div>Balance</div>
                    <span class="dlv">{{ number_format($totalBalance ?? 128450) }}</span>
                </div>
                <div class="dli">
                    <div class="dll"><div class="dld" style="background:#ffbe0b"></div>Bonus</div>
                    <span class="dlv">{{ number_format($totalBonus ?? 34820) }}</span>
                </div>
                <div class="dli">
                    <div class="dll"><div class="dld" style="background:var(--txt-3)"></div>Zero wallets</div>
                    <span class="dlv">{{ $zeroWallets ?? 214 }}</span>
                </div>
            </div>
        </div>

        {{-- Quick stats --}}
        <div class="mp">
            <h4>Today's Activity</h4>
            <div class="qs">
                <div class="qsi"><div class="qsv">{{ $todayAdded ?? 24 }}</div><div class="qsl">Points added</div></div>
                <div class="qsi"><div class="qsv">{{ $todayResets ?? 3 }}</div><div class="qsl">Resets done</div></div>
                <div class="qsi"><div class="qsv">{{ number_format($todayVolume ?? 4800) }}</div><div class="qsl">Pts distributed</div></div>
                <div class="qsi"><div class="qsv">{{ $newWallets ?? 12 }}</div><div class="qsl">New wallets</div></div>
            </div>
        </div>

        {{-- Recent transactions feed --}}
        <div class="mp">
            <h4>Recent Transactions</h4>
            <div class="tx-list">
                <div class="tx-item">
                    <div class="tx-left">
                        <div class="tx-ico" style="background:rgba(6,214,160,.12)">💵</div>
                        <div>
                            <div class="tx-name">Alex M.</div>
                            <div class="tx-time">2 min ago</div>
                        </div>
                    </div>
                    <span class="tx-amt tx-pos">+500</span>
                </div>
                <div class="tx-item">
                    <div class="tx-left">
                        <div class="tx-ico" style="background:rgba(255,190,11,.1)">⭐</div>
                        <div>
                            <div class="tx-name">Jordan K.</div>
                            <div class="tx-time">14 min ago</div>
                        </div>
                    </div>
                    <span class="tx-amt tx-pos">+100</span>
                </div>
                <div class="tx-item">
                    <div class="tx-left">
                        <div class="tx-ico" style="background:rgba(255,71,87,.1)">⟳</div>
                        <div>
                            <div class="tx-name">Sam R.</div>
                            <div class="tx-time">38 min ago</div>
                        </div>
                    </div>
                    <span class="tx-amt tx-neg">Reset</span>
                </div>
                <div class="tx-item">
                    <div class="tx-left">
                        <div class="tx-ico" style="background:rgba(6,214,160,.12)">💵</div>
                        <div>
                            <div class="tx-name">Priya S.</div>
                            <div class="tx-time">1 hr ago</div>
                        </div>
                    </div>
                    <span class="tx-amt tx-pos">+250</span>
                </div>
                <div class="tx-item">
                    <div class="tx-left">
                        <div class="tx-ico" style="background:rgba(255,190,11,.1)">⭐</div>
                        <div>
                            <div class="tx-name">Chris T.</div>
                            <div class="tx-time">2 hr ago</div>
                        </div>
                    </div>
                    <span class="tx-amt tx-pos">+200</span>
                </div>
            </div>
        </div>

    </div>{{-- /.side-col --}}

</div>{{-- /.wl-layout --}}

{{-- ── Add Points Modal ── --}}
<div id="addPointsModal" class="modal" style="display:none">
    <div class="modal-content">
        <h3>Add Points to <span id="modalUserName"></span></h3>
        <form id="addPointsForm">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" id="modalWalletId" name="wallet_id">
            <label for="modalPoints">Points Amount:</label>
            <input type="number" id="modalPoints" name="points" min="1" value="100" required>
            <label for="modalType">Type:</label>
            <select id="modalType" name="type">
                <option value="bonus">💵 Bonus Points</option>
                <option value="balance">💰 Balance</option>
            </select>
            <div class="modal-buttons">
                <button type="submit" class="btn-add">Add Points</button>
                <button type="button" class="btn-cancel" onclick="closeAddPointsModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
/* Live search filter */
document.getElementById('walletSearch').addEventListener('input', function(){
    const q = this.value.toLowerCase();
    document.querySelectorAll('#walletsTable tbody tr').forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(q) ? '' : 'none';
    });
});

/* Filter buttons */
function filterWallets(type, btn){
    document.querySelectorAll('.fbtn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    document.querySelectorAll('#walletsTable tbody tr').forEach(row => {
        const cells = row.querySelectorAll('td');
        if(!cells.length){ return; }

        const balText = cells[1]?.innerText?.replace(/[^0-9]/g,'') || '0';
        const bonText = cells[2]?.innerText?.replace(/[^0-9]/g,'') || '0';
        const bal = parseInt(balText, 10);
        const bon = parseInt(bonText, 10);

        let show = true;
        if(type === 'high')  show = bal >= 1000;
        if(type === 'bonus') show = bon > 0;
        if(type === 'zero')  show = bal === 0;

        row.style.display = show ? '' : 'none';
    });
}

/* Add Points Modal */
function openAddPointsModal(walletId, userName){
    document.getElementById('modalWalletId').value = walletId;
    document.getElementById('modalUserName').textContent = userName;
    document.getElementById('addPointsModal').style.display = 'flex';
    document.getElementById('modalPoints').focus();
}

function closeAddPointsModal(){
    document.getElementById('addPointsModal').style.display = 'none';
    document.getElementById('addPointsForm').reset();
}

/* Notification functions */
function showNotification(message, type = 'success'){
    const notif = document.getElementById('notification');
    const msg = document.getElementById('notifMsg');
    msg.textContent = message;
    notif.className = `notification ${type}`;
    notif.style.display = 'block';
    setTimeout(() => {
        hideNotification();
    }, 4000);
}

function hideNotification(){
    document.getElementById('notification').style.display = 'none';
}

/* Handle form submission */
document.getElementById('addPointsForm').addEventListener('submit', function(e){
    e.preventDefault();

    const formData = new FormData(this);
    const walletId = formData.get('wallet_id');

    fetch(`/admin/wallets/${walletId}/add-points`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success !== false){
            showNotification('Points added successfully!', 'success');
            closeAddPointsModal();
            setTimeout(() => location.reload(), 1000); // Reload to update the table after showing notification
        } else {
            showNotification('Error: ' + (data.message || 'Failed to add points'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
    });
});

/* Reset wallet */
function resetWallet(walletId, userName){
    if (!confirm(`Reset wallet for ${userName}?`)) {
        return;
    }

    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

    fetch(`/admin/wallets/${walletId}/reset`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success !== false){
            showNotification('Wallet reset successfully!', 'success');
            setTimeout(() => location.reload(), 1000); // Reload to update the table after showing notification
        } else {
            showNotification('Error: ' + (data.message || 'Failed to reset wallet'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
    });
}
</script>
@endsection