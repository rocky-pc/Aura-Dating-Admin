@extends('admin.layout')

@section('title', 'Aura — Premium Users')

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
  background:linear-gradient(135deg,#ffbe0b,#ff8c00);
  display:grid;place-items:center;font-size:1rem;
  box-shadow:0 0 18px rgba(255,190,11,.3),0 4px 10px rgba(0,0,0,.35);
  flex-shrink:0;
}
.ph-actions{display:flex;gap:8px;align-items:center}

/* ───────────── BUTTONS ───────────── */
.btn{
  display:inline-flex;align-items:center;gap:6px;
  border-radius:7px;padding:7px 14px;
  font-size:.72rem;font-weight:600;font-family:'Syne',sans-serif;
  transition:all .2s;white-space:nowrap;cursor:pointer;border:none;
  text-decoration:none;
}
.btn-gold{
  background:linear-gradient(135deg,#ffbe0b,#ff9500);
  color:#1a0f00;
  box-shadow:0 0 16px rgba(255,190,11,.35),0 4px 10px rgba(0,0,0,.25);
}
.btn-gold:hover{opacity:.9;transform:translateY(-1px);box-shadow:0 0 24px rgba(255,190,11,.5)}
.btn-ghost{
  background:var(--surface);border:1px solid var(--border);
  color:var(--txt-2);
}
.btn-ghost:hover{background:var(--surface2);color:var(--txt);border-color:rgba(255,255,255,.12)}

/* ───────────── HERO BANNER ───────────── */
.premium-hero{
  background:var(--surface);border:1px solid var(--border);
  border-radius:var(--r);padding:20px 22px;
  margin-bottom:10px;animation:fu .5s ease both;
  position:relative;overflow:hidden;
}
.premium-hero::before{
  content:'';position:absolute;inset:0;
  background:radial-gradient(ellipse at top left, rgba(255,190,11,.09), transparent 55%),
             radial-gradient(ellipse at bottom right, rgba(255,140,0,.06), transparent 50%);
  pointer-events:none;
}
.premium-hero-inner{
  position:relative;z-index:1;
  display:flex;justify-content:space-between;align-items:flex-start;gap:20px;
}
.hero-eyebrow{
  font-family:'Syne',sans-serif;font-size:.6rem;font-weight:700;
  letter-spacing:.18em;text-transform:uppercase;
  color:var(--amber);margin-bottom:6px;
}
.hero-title{
  font-family:'Syne',sans-serif;font-size:1.5rem;font-weight:800;
  letter-spacing:-.04em;color:#fff;margin-bottom:6px;line-height:1.15;
}
.hero-title span{
  background:linear-gradient(90deg,#ffbe0b,#ffd55a);
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
  background-clip:text;
}
.hero-desc{font-size:.73rem;color:var(--txt-2);line-height:1.7;max-width:600px}
.hero-pill{
  display:inline-flex;align-items:center;gap:6px;
  margin-top:12px;padding:6px 12px;border-radius:8px;
  background:rgba(255,190,11,.1);border:1px solid rgba(255,190,11,.2);
  font-size:.68rem;color:var(--amber);font-weight:600;
}
.hero-pill-dot{width:6px;height:6px;border-radius:50%;background:var(--amber);animation:pulse 2s ease infinite}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.35}}

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
  box-shadow:0 8px 28px rgba(0,0,0,.4),0 0 28px var(--glow,rgba(255,190,11,.1));
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
.warn{background:rgba(255,190,11,.12);color:var(--amber)}
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

.ca{--c1:#ffbe0b;--c2:#ffd55a;--glow:rgba(255,190,11,.12)}
.cg{--c1:#06d6a0;--c2:#00ffcc;--glow:rgba(6,214,160,.1)}
.ce{--c1:#ff4757;--c2:#ff8080;--glow:rgba(255,71,87,.1)}
.cb{--c1:#3b9eff;--c2:#80c0ff;--glow:rgba(59,158,255,.1)}

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
  color:var(--txt);font-size:.72rem;
  outline:none;transition:border-color .18s,box-shadow .18s;
  -webkit-appearance:none;appearance:none;
  font-family:'Syne',sans-serif;
}
.fi:focus{border-color:rgba(255,190,11,.4);box-shadow:0 0 0 3px rgba(255,190,11,.08)}
.fi::placeholder{color:var(--txt-3)}
.fi-search{
  flex:1;min-width:200px;padding-left:32px;
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='rgba(255,255,255,0.2)' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.099zm-5.44 1.398a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11z'/%3E%3C/svg%3E");
  background-repeat:no-repeat;background-position:10px center;
}
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
.td-mono{font-family:monospace;font-size:.63rem;color:var(--txt-3)}
.td-small{font-size:.68rem;color:var(--txt-2);margin-top:2px}

/* User cell */
.u-cell{display:flex;align-items:center;gap:9px}
.u-av{
  width:30px;height:30px;border-radius:8px;
  display:grid;place-items:center;
  font-family:'Syne',sans-serif;font-size:.58rem;font-weight:700;
  color:#fff;flex-shrink:0;
}
.u-name{font-size:.73rem;font-weight:500;color:var(--txt);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:140px}
.u-sub{font-family:monospace;font-size:.6rem;color:var(--txt-3);margin-top:1px}

/* Expiry display */
.expiry-wrap{display:flex;align-items:center;gap:6px;flex-wrap:wrap}
.expiry-date{font-size:.72rem;color:var(--txt-2)}

/* Badges */
.bd{
  display:inline-flex;align-items:center;gap:3px;
  padding:2px 7px;border-radius:100px;
  font-size:.62rem;font-weight:600;white-space:nowrap;
}
.bddot{width:4px;height:4px;border-radius:50%;background:currentColor;animation:pulse 2s ease infinite}

.bd-active  {background:rgba(6,214,160,.08);color:var(--teal);border:1px solid rgba(6,214,160,.2)}
.bd-inactive{background:rgba(255,255,255,.04);color:var(--txt-3);border:1px solid var(--border)}
.bd-expired {background:rgba(255,71,87,.1);color:#ff5f6d;border:1px solid rgba(255,71,87,.2)}
.bd-today   {background:rgba(255,190,11,.1);color:var(--amber);border:1px solid rgba(255,190,11,.22)}
.bd-verified{background:rgba(59,158,255,.08);color:var(--blue);border:1px solid rgba(59,158,255,.2)}
.bd-unverif {background:rgba(255,255,255,.03);color:var(--txt-3);border:1px solid var(--border)}
.bd-gold    {background:rgba(255,190,11,.1);color:var(--amber);border:1px solid rgba(255,190,11,.22)}

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
.pag-wrap nav span[aria-current="page"]{background:linear-gradient(135deg,#ffbe0b,#ff9500);color:#1a0f00;border-color:#ffbe0b;font-weight:700}
.pag-wrap nav span.disabled{opacity:.3;pointer-events:none}

/* ───────────── EMPTY STATE ───────────── */
.empty-state{text-align:center;padding:52px 20px}
.empty-ico{font-size:2.4rem;margin-bottom:12px;opacity:.3}
.empty-title{font-size:.88rem;font-weight:600;color:var(--txt-2);margin-bottom:4px;font-family:'Syne',sans-serif}
.empty-txt{font-size:.7rem;color:var(--txt-3)}

/* ───────────── ANIMATIONS ───────────── */
@keyframes fu{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}

/* ───────────── RESPONSIVE ───────────── */
@media(max-width:1200px){.sg{grid-template-columns:repeat(2,1fr)}}
@media(max-width:700px){
  .ph{flex-direction:column;align-items:flex-start;gap:10px}
  .sg{grid-template-columns:1fr 1fr}
  .premium-hero-inner{flex-direction:column;gap:12px}
  .dt thead th:nth-child(4),.dt td:nth-child(4){display:none}
}
@media(max-width:480px){
  .sg{grid-template-columns:1fr}
  .dt thead th:nth-child(6),.dt td:nth-child(6){display:none}
}
</style>
@endsection

@section('content')

{{-- ── PAGE HEADER ── --}}
<div class="ph">
    <div class="ph-left">
        <div class="ph-icon">⭐</div>
        <div>
            <div class="ph-title">Premium Users</div>
            <div class="ph-sub">Highest-value members — renewals, expiry &amp; trust status</div>
        </div>
    </div>
    <div class="ph-actions">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost">
            ← Dashboard
        </a>
    </div>
</div>

{{-- ── HERO BANNER ── --}}
<div class="premium-hero">
    <div class="premium-hero-inner">
        <div>
            <div class="hero-eyebrow">⭐ Premium Members</div>
            <div class="hero-title">Aura <span>Premium</span> Access</div>
            <div class="hero-desc">Keep a polished view of your highest-value users. This page highlights current premium members, renewal timing, and trusted account status.</div>
            <div class="hero-pill">
                <span class="hero-pill-dot"></span>
                {{ number_format($stats['active']) }} active premium members right now
            </div>
        </div>
        <div style="text-align:right;flex-shrink:0">
            <div style="font-family:'Syne',sans-serif;font-size:2.8rem;font-weight:800;letter-spacing:-.05em;background:linear-gradient(135deg,#ffbe0b,#ffd55a);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;line-height:1">{{ number_format($stats['total']) }}</div>
            <div style="font-size:.68rem;color:var(--txt-3);margin-top:2px;font-weight:600;letter-spacing:.06em;text-transform:uppercase">Total Premium</div>
        </div>
    </div>
</div>

{{-- ── STAT STRIP ── --}}
<div class="sg">
    <div class="sc ca">
        <div class="scblob"></div>
        <div class="sct"><div class="scico">⭐</div><span class="trnd up">All time</span></div>
        <div class="scv">{{ number_format($stats['total']) }}</div>
        <div class="scl">Total Premium</div>
        <div class="scbar"><div class="scbarf" style="--p:100%"></div></div>
    </div>
    <div class="sc cg">
        <div class="scblob"></div>
        <div class="sct"><div class="scico">✅</div><span class="trnd up">+12%</span></div>
        <div class="scv">{{ number_format($stats['active']) }}</div>
        <div class="scl">Active</div>
        <div class="scbar"><div class="scbarf" style="--p:{{ $stats['total'] > 0 ? round($stats['active']/$stats['total']*100) : 0 }}%"></div></div>
    </div>
    <div class="sc ce">
        <div class="scblob"></div>
        <div class="sct"><div class="scico">⏳</div><span class="trnd warn">7 days</span></div>
        <div class="scv">{{ number_format($stats['expiring_soon']) }}</div>
        <div class="scl">Expiring Soon</div>
        <div class="scbar"><div class="scbarf" style="--p:{{ $stats['total'] > 0 ? round($stats['expiring_soon']/$stats['total']*100) : 0 }}%"></div></div>
    </div>
    <div class="sc cb">
        <div class="scblob"></div>
        <div class="sct"><div class="scico">🛡️</div><span class="trnd up">+10%</span></div>
        <div class="scv">{{ number_format($stats['verified']) }}</div>
        <div class="scl">Verified</div>
        <div class="scbar"><div class="scbarf" style="--p:{{ $stats['total'] > 0 ? round($stats['verified']/$stats['total']*100) : 0 }}%"></div></div>
    </div>
</div>

{{-- ── FILTER BAR ── --}}
<div class="filter-bar">
    <form method="GET" action="{{ route('admin.premium.index') }}" class="filter-form">
        <input type="text" name="search" class="fi fi-search"
            placeholder="Search by email or phone…"
            value="{{ request('search') }}">
        <select name="status" class="fi fi-sel">
            <option value="">All Status</option>
            <option value="active"   @selected(request('status')==='active')>Active</option>
            <option value="inactive" @selected(request('status')==='inactive')>Inactive</option>
        </select>
        <div class="filter-actions">
            <button type="submit" class="btn btn-gold" style="padding:6px 14px">
                ⚡ Filter
            </button>
            <a href="{{ route('admin.premium.index') }}" class="btn btn-ghost" style="padding:6px 12px">
                ✕ Clear
            </a>
        </div>
    </form>
</div>

{{-- ── RESULTS META ── --}}
<div class="results-meta">
    <span class="results-count">
        Showing <strong>{{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }}</strong>
        of <strong>{{ $users->total() ?? 0 }}</strong> premium users
    </span>
    <span class="bd bd-gold">⭐ Premium List</span>
</div>

{{-- ── TABLE ── --}}
<div class="panel">
    <div style="overflow-x:auto">
        <table class="dt">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Email / Phone</th>
                    <th>Premium Expires</th>
                    <th>Status</th>
                    <th>Verified</th>
                    <th>Joined</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    @php
                        $palettes = [
                            ['#ffbe0b','#ff9500'],
                            ['#06d6a0','#3b9eff'],
                            ['#ff3d7f','#8b5cf6'],
                            ['#ffbe0b','#06d6a0'],
                            ['#8b5cf6','#ec4899'],
                            ['#3b9eff','#ffbe0b'],
                            ['#14b8a6','#8b5cf6'],
                        ];
                        $c        = $palettes[$user->id % 7];
                        $initials = strtoupper(substr($user->email ?? $user->phone ?? '?', 0, 2));
                        $fullName = optional($user->profile)->first_name
                            ? trim($user->profile->first_name . ' ' . $user->profile->last_name)
                            : null;
                    @endphp
                    <tr>
                        {{-- ID --}}
                        <td class="td-mono">{{ $user->id }}</td>

                        {{-- User avatar + name --}}
                        <td>
                            <div class="u-cell">
                                <div class="u-av" style="background:linear-gradient(135deg,{{ $c[0] }},{{ $c[1] }})">{{ $initials }}</div>
                                <div>
                                    <div class="u-name">{{ $fullName ?? '—' }}</div>
                                    <div class="u-sub">#{{ $user->id }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Email / Phone --}}
                        <td>
                            <div style="font-size:.73rem;color:var(--txt)">{{ $user->email ?? '—' }}</div>
                            <div class="td-small">{{ $user->phone ?? '—' }}</div>
                        </td>

                        {{-- Premium Expires --}}
                        <td>
                            @if($user->premium_expires_at)
                                <div class="expiry-wrap">
                                    <span class="expiry-date">{{ $user->premium_expires_at->format('M d, Y') }}</span>
                                    @if($user->premium_expires_at->isPast())
                                        <span class="bd bd-expired">Expired</span>
                                    @elseif($user->premium_expires_at->isToday())
                                        <span class="bd bd-today">⚡ Today</span>
                                    @elseif($user->premium_expires_at->diffInDays(now()) <= 7)
                                        <span class="bd bd-today">⏳ Soon</span>
                                    @endif
                                </div>
                            @else
                                <span class="td-muted">—</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td>
                            @if($user->is_active)
                                <span class="bd bd-active"><span class="bddot"></span>Active</span>
                            @else
                                <span class="bd bd-inactive">Inactive</span>
                            @endif
                        </td>

                        {{-- Verified --}}
                        <td>
                            @if($user->is_verified)
                                <span class="bd bd-verified">✔ Yes</span>
                            @else
                                <span class="bd bd-unverif">No</span>
                            @endif
                        </td>

                        {{-- Joined --}}
                        <td class="td-muted">{{ $user->created_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-ico">⭐</div>
                                <div class="empty-title">No premium users found</div>
                                <div class="empty-txt">Try adjusting your search or filter criteria</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="pag-wrap">
        <span class="pag-info">Page {{ $users->currentPage() }} of {{ $users->lastPage() }}</span>
        {{ $users->withQueryString()->links() }}
    </div>
</div>

@endsection