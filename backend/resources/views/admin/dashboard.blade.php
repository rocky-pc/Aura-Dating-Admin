@extends('admin.layout')

@section('title', 'Aura — Admin Dashboard')

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

/* ───────────── STAT GRIDS ───────────── */
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
.ce{--c1:#ff4757;--c2:#ff8080;--glow:rgba(255,71,87,.1)}
.cb{--c1:#3b9eff;--c2:#80c0ff;--glow:rgba(59,158,255,.1)}
.cv{--c1:#8b5cf6;--c2:#c084fc;--glow:rgba(139,92,246,.1)}
.cp{--c1:#ec4899;--c2:#f472b6;--glow:rgba(236,72,153,.1)}
.ct{--c1:#14b8a6;--c2:#2dd4bf;--glow:rgba(20,184,166,.1)}

/* ───────────── BOTTOM GRID ───────────── */
.bg2{display:grid;grid-template-columns:1fr 265px;gap:10px;margin-top:10px}

/* ───────────── TABLE PANEL ───────────── */
.panel{
  background:var(--surface);border:1px solid var(--border);
  border-radius:var(--r);overflow:hidden;
  animation:fu .55s .25s ease both;
}
.phead{
  display:flex;justify-content:space-between;align-items:center;
  padding:10px 14px;border-bottom:1px solid var(--border);
}
.ptitle{font-family:'Syne',sans-serif;font-size:.8rem;font-weight:700;color:var(--txt)}
.vabtn{
  font-size:.68rem;color:var(--rose);
  background:rgba(255,61,127,.08);border:1px solid rgba(255,61,127,.18);
  border-radius:6px;padding:3px 9px;font-weight:600;transition:background .2s;
}
.vabtn:hover{background:rgba(255,61,127,.15)}
.dt{width:100%;border-collapse:collapse}
.dt thead th{
  padding:7px 12px;text-align:left;
  font-family:'Syne',sans-serif;font-size:.58rem;font-weight:700;
  letter-spacing:.09em;text-transform:uppercase;
  color:var(--txt-3);background:rgba(255,255,255,.02);
  border-bottom:1px solid var(--border);white-space:nowrap;
}
.dt tbody tr{border-bottom:1px solid rgba(255,255,255,.03);transition:background .12s}
.dt tbody tr:last-child{border-bottom:none}
.dt tbody tr:hover{background:rgba(255,255,255,.03)}
.dt td{padding:8px 12px;color:var(--txt);vertical-align:middle;font-size:.73rem}
.dt td.m{color:var(--txt-2);font-size:.68rem}
.dt td.mn{font-family:monospace;font-size:.63rem;color:var(--txt-3)}

.uc2{display:flex;align-items:center;gap:7px}
.uav2{
  width:26px;height:26px;border-radius:7px;
  background:linear-gradient(135deg,var(--rose),var(--violet));
  display:grid;place-items:center;
  font-family:'Syne',sans-serif;font-size:.58rem;font-weight:700;color:#fff;flex-shrink:0;
}
.ue{font-size:.72rem;color:var(--txt);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:130px}

.bd{
  display:inline-flex;align-items:center;gap:3px;
  padding:2px 7px;border-radius:100px;
  font-size:.62rem;font-weight:600;white-space:nowrap;
}
.bda{background:rgba(255,71,87,.1);color:#ff5f6d;border:1px solid rgba(255,71,87,.2)}
.bdm{background:rgba(255,190,11,.1);color:var(--amber);border:1px solid rgba(255,190,11,.2)}
.bdu{background:rgba(59,158,255,.1);color:var(--blue);border:1px solid rgba(59,158,255,.2)}
.bdon{background:rgba(6,214,160,.08);color:var(--teal);border:1px solid rgba(6,214,160,.2)}
.bdof{background:rgba(255,255,255,.04);color:var(--txt-3);border:1px solid var(--border)}
.bddot{width:4px;height:4px;border-radius:50%;background:currentColor}

/* ───────────── SIDE COLUMN ───────────── */
.sc2{display:flex;flex-direction:column;gap:10px;animation:fu .55s .3s ease both}
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
.rfl{
  fill:none;stroke-width:7;stroke-linecap:round;stroke:url(#rg);
  stroke-dasharray:214;stroke-dashoffset:53;
  animation:rd 1.3s cubic-bezier(.4,0,.2,1) .4s both;
}
@keyframes rd{from{stroke-dashoffset:214}}
.dpct{font-family:'Syne',sans-serif;font-size:1.5rem;font-weight:800;color:#fff;line-height:1}
.dlbl{font-size:.65rem;color:var(--txt-2);margin-top:2px}
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

/* Activity */
.af{display:flex;flex-direction:column}
.ai{
  display:flex;gap:8px;align-items:flex-start;
  padding:7px 0;border-bottom:1px solid rgba(255,255,255,.04);
}
.ai:last-child{border-bottom:none;padding-bottom:0}
.aico{
  width:26px;height:26px;border-radius:7px;
  display:grid;place-items:center;font-size:.75rem;flex-shrink:0;
}
.ab{font-size:.7rem;color:var(--txt-2);line-height:1.4}
.ab strong{color:var(--txt);font-weight:500}
.at{font-size:.6rem;color:var(--txt-3);margin-top:1px}

/* ───────────── RESPONSIVE ───────────── */
@media(max-width:1280px){
  .bg2{grid-template-columns:1fr 250px}
  .sg{gap:8px}
}
@media(max-width:1024px){
  .bg2{grid-template-columns:1fr 230px}
}
@media(max-width:860px){
  .sg{grid-template-columns:repeat(2,1fr)}
  .bg2{grid-template-columns:1fr}
  .sc2{display:grid;grid-template-columns:1fr 1fr;gap:10px}
}
@media(max-width:600px){
  .sg{grid-template-columns:1fr 1fr;gap:8px}
  .sc2{grid-template-columns:1fr}
}
</style>
@endsection

@section('content')

{{-- Page header --}}
<div class="ph">
    <div>
        <div class="ph-title">Dashboard</div>
        <div class="ph-sub">Welcome back, {{ auth()->user()->email ?? 'admin' }}</div>
    </div>
    <div class="dchip"><i class="bi bi-calendar3"></i> Last 30 days</div>
</div>

{{-- ── Row 1: Primary stats ── --}}
<div class="sg">
    <div class="sc cr">
        <div class="scblob"></div>
        <div class="sct"><div class="scico">👥</div><span class="trnd up">+12%</span></div>
        <div class="scv">{{ number_format($stats['total_users'] ?? 24831) }}</div>
        <div class="scl">Total Users</div>
        <div class="scbar"><div class="scbarf" style="--p:78%"></div></div>
    </div>
    <div class="sc cg">
        <div class="scblob"></div>
        <div class="sct"><div class="scico">✅</div><span class="trnd up">+5%</span></div>
        <div class="scv">{{ number_format($stats['active_users'] ?? 18204) }}</div>
        <div class="scl">Active Users</div>
        <div class="scbar"><div class="scbarf" style="--p:65%"></div></div>
    </div>
    <div class="sc ca">
        <div class="scblob"></div>
        <div class="sct"><div class="scico">⭐</div><span class="trnd up">+8%</span></div>
        <div class="scv">{{ number_format($stats['premium_users'] ?? 5419) }}</div>
        <div class="scl">Premium Users</div>
        <div class="scbar"><div class="scbarf" style="--p:45%"></div></div>
    </div>
    <div class="sc ce">
        <div class="scblob"></div>
        <div class="sct"><div class="scico">🚩</div><span class="trnd dn">+3</span></div>
        <div class="scv">{{ number_format($stats['pending_reports'] ?? 14) }}</div>
        <div class="scl">Pending Reports</div>
        <div class="scbar"><div class="scbarf" style="--p:18%"></div></div>
    </div>
</div>

{{-- ── Row 2: Secondary stats ── --}}
<div class="sg">
    <div class="sc cb">
        <div class="scblob"></div>
        <div class="sct"><div class="scico">💞</div><span class="trnd up">+18%</span></div>
        <div class="scv">{{ $stats['total_matches'] >= 1000 ? round($stats['total_matches']/1000,1).'k' : ($stats['total_matches'] ?? '312k') }}</div>
        <div class="scl">Total Matches</div>
        <div class="scbar"><div class="scbarf" style="--p:84%"></div></div>
    </div>
    <div class="sc cv">
        <div class="scblob"></div>
        <div class="sct"><div class="scico">👆</div><span class="trnd up">+24%</span></div>
        <div class="scv">{{ $stats['total_swipes'] >= 1000000 ? round($stats['total_swipes']/1000000,1).'M' : ($stats['total_swipes'] ?? '1.8M') }}</div>
        <div class="scl">Total Swipes</div>
        <div class="scbar"><div class="scbarf" style="--p:92%"></div></div>
    </div>
    <div class="sc cp">
        <div class="scblob"></div>
        <div class="sct"><div class="scico">💳</div><span class="trnd up">+7%</span></div>
        <div class="scv">{{ number_format($stats['active_subscriptions'] ?? 4987) }}</div>
        <div class="scl">Subscriptions</div>
        <div class="scbar"><div class="scbarf" style="--p:54%"></div></div>
    </div>
    <div class="sc ct">
        <div class="scblob"></div>
        <div class="sct"><div class="scico">🛡️</div><span class="trnd up">+10%</span></div>
        <div class="scv">{{ number_format($stats['verified_users'] ?? 9332) }}</div>
        <div class="scl">Verified Users</div>
        <div class="scbar"><div class="scbarf" style="--p:70%"></div></div>
    </div>
</div>

{{-- ── Bottom panels ── --}}
<div class="bg2">

    {{-- Users table --}}
    <div class="panel">
        <div class="phead">
            <span class="ptitle">Recent Users</span>
            <a href="{{ route('admin.users.index') }}" class="vabtn">View All →</a>
        </div>
        <div style="overflow-x:auto">
            <table class="dt">
                <thead>
                    <tr>
                        <th>User</th><th>UUID</th><th>Phone</th>
                        <th>Role</th><th>Status</th><th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentUsers as $user)
                    <tr>
                        <td>
                            <div class="uc2">
                                <div class="uav2">{{ strtoupper(substr($user->email ?? $user->phone ?? '?', 0, 2)) }}</div>
                                <div class="ue">{{ $user->email ?? '—' }}</div>
                            </div>
                        </td>
                        <td class="mn">{{ substr($user->uuid, 0, 8) }}…</td>
                        <td class="m">{{ $user->phone ?? '—' }}</td>
                        <td>
                            @if($user->role === 'admin') <span class="bd bda">Admin</span>
                            @elseif($user->role === 'moderator') <span class="bd bdm">Mod</span>
                            @else <span class="bd bdu">User</span>
                            @endif
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="bd bdon"><span class="bddot"></span>Active</span>
                            @else
                                <span class="bd bdof">Inactive</span>
                            @endif
                        </td>
                        <td class="m">{{ $user->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;padding:28px;color:var(--txt-3)">No users yet 💤</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Side column --}}
    <div class="sc2">

        {{-- Match rate donut --}}
        <div class="mp">
            <h4>Match Rate</h4>
            <div class="dw">
                <svg width="86" height="86" viewBox="0 0 86 86" class="dsv">
                    <defs>
                        <linearGradient id="rg" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#ff3d7f"/>
                            <stop offset="100%" stop-color="#8b5cf6"/>
                        </linearGradient>
                    </defs>
                    <circle class="rbg" cx="43" cy="43" r="34"/>
                    <circle class="rfl" cx="43" cy="43" r="34"/>
                </svg>
                <div>
                    <div class="dpct">75%</div>
                    <div class="dlbl">Swipe → Match</div>
                </div>
            </div>
            <div class="dleg">
                <div class="dli">
                    <div class="dll"><div class="dld" style="background:var(--rose)"></div>Right swipes</div>
                    <span class="dlv">1.35M</span>
                </div>
                <div class="dli">
                    <div class="dll"><div class="dld" style="background:var(--violet)"></div>Matches</div>
                    <span class="dlv">{{ number_format($stats['total_matches'] ?? 312000) }}</span>
                </div>
                <div class="dli">
                    <div class="dll"><div class="dld" style="background:var(--txt-3)"></div>Left swipes</div>
                    <span class="dlv">450k</span>
                </div>
            </div>
        </div>

        {{-- Today's pulse --}}
        <div class="mp">
            <h4>Today's Pulse</h4>
            <div class="qs">
                <div class="qsi"><div class="qsv">428</div><div class="qsl">New signups</div></div>
                <div class="qsi"><div class="qsv">1.2k</div><div class="qsl">Matches</div></div>
                <div class="qsi"><div class="qsv">34k</div><div class="qsl">Swipes</div></div>
                <div class="qsi"><div class="qsv">89</div><div class="qsl">Upgrades</div></div>
            </div>
        </div>

        {{-- Activity feed --}}
        <div class="mp">
            <h4>Live Activity</h4>
            <div class="af">
                <div class="ai">
                    <div class="aico" style="background:rgba(255,61,127,.1)">💞</div>
                    <div><div class="ab"><strong>New match</strong> Alex &amp; Jordan</div><div class="at">2 min ago</div></div>
                </div>
                <div class="ai">
                    <div class="aico" style="background:rgba(255,190,11,.1)">⭐</div>
                    <div><div class="ab"><strong>Premium upgrade</strong> #4821</div><div class="at">8 min ago</div></div>
                </div>
                <div class="ai">
                    <div class="aico" style="background:rgba(255,71,87,.1)">🚩</div>
                    <div><div class="ab"><strong>Report</strong> on profile #3390</div><div class="at">15 min ago</div></div>
                </div>
                <div class="ai">
                    <div class="aico" style="background:rgba(6,214,160,.1)">✅</div>
                    <div><div class="ab"><strong>Verified</strong> Sam K.</div><div class="at">41 min ago</div></div>
                </div>
                <div class="ai">
                    <div class="aico" style="background:rgba(59,158,255,.1)">👤</div>
                    <div><div class="ab"><strong>New signup</strong> Chennai, IN</div><div class="at">1 hr ago</div></div>
                </div>
            </div>
        </div>

    </div>{{-- /.sc2 --}}

</div>{{-- /.bg2 --}}

@endsection