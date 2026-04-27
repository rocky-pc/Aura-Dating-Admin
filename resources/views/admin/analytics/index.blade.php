@extends('admin.layout')

@section('title', 'Aura — Analytics Dashboard')

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
  background:linear-gradient(135deg,#3b9eff,#8b5cf6);
  display:grid;place-items:center;font-size:1rem;
  box-shadow:0 0 18px rgba(59,158,255,.3),0 4px 10px rgba(0,0,0,.35);
  flex-shrink:0;
}
.ph-actions{display:flex;gap:8px;align-items:center}

/* ───────────── BUTTON ───────────── */
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

/* ───────────── HERO BANNER ───────────── */
.analytics-hero{
  background:var(--surface);border:1px solid var(--border);
  border-radius:var(--r);padding:20px 22px;
  margin-bottom:10px;animation:fu .5s ease both;
  position:relative;overflow:hidden;
}
.analytics-hero::before{
  content:'';position:absolute;inset:0;
  background:radial-gradient(ellipse at top left, rgba(59,158,255,.08), transparent 50%),
             radial-gradient(ellipse at bottom right, rgba(139,92,246,.06), transparent 50%);
  pointer-events:none;
}
.hero-inner{
  position:relative;z-index:1;
  display:flex;justify-content:space-between;align-items:flex-start;gap:20px;
}
.hero-eyebrow{
  font-family:'Syne',sans-serif;font-size:.6rem;font-weight:700;
  letter-spacing:.18em;text-transform:uppercase;
  color:var(--blue);margin-bottom:6px;
}
.hero-title{
  font-family:'Syne',sans-serif;font-size:1.5rem;font-weight:800;
  letter-spacing:-.04em;color:#fff;margin-bottom:6px;line-height:1.15;
}
.hero-title span{
  background:linear-gradient(90deg,#3b9eff,#8b5cf6);
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
  background-clip:text;
}
.hero-desc{font-size:.73rem;color:var(--txt-2);line-height:1.7;max-width:580px}
.hero-chips{display:flex;flex-wrap:wrap;gap:7px;margin-top:12px}
.hero-chip{
  display:inline-flex;align-items:center;gap:5px;
  padding:4px 10px;border-radius:7px;
  font-size:.65rem;font-weight:600;
}
.hero-chip.blue  {background:rgba(59,158,255,.1);border:1px solid rgba(59,158,255,.2);color:var(--blue)}
.hero-chip.violet{background:rgba(139,92,246,.1);border:1px solid rgba(139,92,246,.2);color:var(--violet)}
.hero-chip.teal  {background:rgba(6,214,160,.1);border:1px solid rgba(6,214,160,.2);color:var(--teal)}
.hero-chip.amber {background:rgba(255,190,11,.1);border:1px solid rgba(255,190,11,.2);color:var(--amber)}
.hero-chip-dot{width:5px;height:5px;border-radius:50%;background:currentColor;animation:pulse 2s ease infinite}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.3}}

/* ───────────── SECTION LABEL ───────────── */
.section-head{
  display:flex;align-items:center;gap:8px;
  margin-bottom:9px;margin-top:14px;
  animation:fu .5s ease both;
}
.section-head:first-of-type{margin-top:0}
.section-label{
  font-family:'Syne',sans-serif;font-size:.6rem;font-weight:700;
  letter-spacing:.14em;text-transform:uppercase;color:var(--txt-3);
}
.section-line{flex:1;height:1px;background:var(--border)}
.section-ico{
  width:22px;height:22px;border-radius:6px;
  background:linear-gradient(135deg,var(--c1),var(--c2));
  display:grid;place-items:center;font-size:.65rem;flex-shrink:0;
}

/* ───────────── STAT GRID ───────────── */
.sg4{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:4px}
.sg3{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:4px}

.sc{
  background:var(--surface);border:1px solid var(--border);
  border-radius:var(--r);padding:13px;
  position:relative;overflow:hidden;
  transition:transform .22s,box-shadow .22s,border-color .22s;
  animation:fu .5s ease both;cursor:default;
}
.sc:hover{
  transform:translateY(-3px);
  box-shadow:0 8px 28px rgba(0,0,0,.4),0 0 28px var(--glow,rgba(59,158,255,.1));
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
.neu {background:rgba(255,255,255,.06);color:var(--txt-3)}
.scv{
  font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:800;
  letter-spacing:-.04em;line-height:1;color:#fff;
}
.scv-sm{font-size:1.1rem}
.scl{font-size:.68rem;color:var(--txt-2);margin-top:2px}
.scsub{font-size:.63rem;color:var(--txt-3);margin-top:6px;line-height:1.5}
.scbar{height:2px;background:rgba(255,255,255,.06);border-radius:2px;margin-top:10px;overflow:hidden}
.scbarf{
  height:100%;border-radius:2px;
  background:linear-gradient(90deg,var(--c1),var(--c2));
  width:var(--p,60%);animation:bg 1.2s cubic-bezier(.4,0,.2,1) both;
}
@keyframes bg{from{width:0}}

/* Color variants */
.cr {--c1:#ff3d7f;--c2:#ff8ab8;--glow:rgba(255,61,127,.12)}
.cg {--c1:#06d6a0;--c2:#00ffcc;--glow:rgba(6,214,160,.1)}
.ca {--c1:#ffbe0b;--c2:#ffd55a;--glow:rgba(255,190,11,.1)}
.ce {--c1:#ff4757;--c2:#ff8080;--glow:rgba(255,71,87,.1)}
.cb {--c1:#3b9eff;--c2:#80c0ff;--glow:rgba(59,158,255,.1)}
.cv {--c1:#8b5cf6;--c2:#c084fc;--glow:rgba(139,92,246,.1)}
.cp {--c1:#ec4899;--c2:#f472b6;--glow:rgba(236,72,153,.1)}
.ct {--c1:#14b8a6;--c2:#2dd4bf;--glow:rgba(20,184,166,.1)}
.co {--c1:#f97316;--c2:#fb923c;--glow:rgba(249,115,22,.1)}

/* Stagger delays */
.sg4 .sc:nth-child(1),.sg3 .sc:nth-child(1){animation-delay:.04s}
.sg4 .sc:nth-child(2),.sg3 .sc:nth-child(2){animation-delay:.08s}
.sg4 .sc:nth-child(3),.sg3 .sc:nth-child(3){animation-delay:.12s}
.sg4 .sc:nth-child(4)                       {animation-delay:.16s}

/* ───────────── CHART PLACEHOLDER ───────────── */
.chart-panel{
  background:var(--surface);border:1px solid var(--border);
  border-radius:var(--r);padding:32px 20px;
  margin-top:14px;text-align:center;
  animation:fu .5s .2s ease both;
  position:relative;overflow:hidden;
}
.chart-panel::before{
  content:'';position:absolute;inset:0;
  background:radial-gradient(ellipse at center, rgba(59,158,255,.04), transparent 60%);
  pointer-events:none;
}
.chart-inner{position:relative;z-index:1}
.chart-bars{
  display:flex;align-items:flex-end;justify-content:center;
  gap:6px;height:80px;margin-bottom:16px;
}
.chart-bar{
  width:16px;border-radius:4px 4px 0 0;
  background:linear-gradient(180deg,var(--c),rgba(59,158,255,.15));
  opacity:.5;animation:barrise 1s cubic-bezier(.4,0,.2,1) both;
}
@keyframes barrise{from{height:0!important}}
.chart-bar:nth-child(1){animation-delay:.05s}
.chart-bar:nth-child(2){animation-delay:.1s}
.chart-bar:nth-child(3){animation-delay:.15s}
.chart-bar:nth-child(4){animation-delay:.2s}
.chart-bar:nth-child(5){animation-delay:.25s}
.chart-bar:nth-child(6){animation-delay:.3s}
.chart-bar:nth-child(7){animation-delay:.35s}
.chart-bar:nth-child(8){animation-delay:.4s}
.chart-bar:nth-child(9){animation-delay:.45s}
.chart-bar:nth-child(10){animation-delay:.5s}
.chart-bar:nth-child(11){animation-delay:.55s}
.chart-bar:nth-child(12){animation-delay:.6s}
.chart-ico{font-size:2rem;opacity:.25;margin-bottom:8px}
.chart-title{
  font-family:'Syne',sans-serif;font-size:.88rem;font-weight:700;
  color:var(--txt);margin-bottom:5px;
}
.chart-desc{font-size:.7rem;color:var(--txt-3);line-height:1.6;max-width:440px;margin:0 auto}
.chart-badge{
  display:inline-flex;align-items:center;gap:5px;
  margin-top:14px;padding:5px 12px;border-radius:7px;
  background:rgba(59,158,255,.08);border:1px solid rgba(59,158,255,.18);
  font-size:.65rem;font-weight:600;color:var(--blue);
}

/* ───────────── ANIMATIONS ───────────── */
@keyframes fu{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}

/* ───────────── RESPONSIVE ───────────── */
@media(max-width:1200px){
  .sg4{grid-template-columns:repeat(2,1fr)}
  .sg3{grid-template-columns:repeat(2,1fr)}
}
@media(max-width:700px){
  .ph{flex-direction:column;align-items:flex-start;gap:10px}
  .sg4,.sg3{grid-template-columns:1fr 1fr}
  .hero-inner{flex-direction:column;gap:12px}
}
@media(max-width:480px){
  .sg4,.sg3{grid-template-columns:1fr}
}
</style>
@endsection

@section('content')

{{-- ── PAGE HEADER ── --}}
<div class="ph">
    <div class="ph-left">
        <div class="ph-icon">📊</div>
        <div>
            <div class="ph-title">Analytics Dashboard</div>
            <div class="ph-sub">Real-time platform performance &amp; growth metrics</div>
        </div>
    </div>
    <div class="ph-actions">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost">← Dashboard</a>
    </div>
</div>

{{-- ── HERO BANNER ── --}}
<div class="analytics-hero">
    <div class="hero-inner">
        <div>
            <div class="hero-eyebrow">📈 Analytics Overview</div>
            <div class="hero-title">Track App <span>Performance</span> &amp; Growth</div>
            <div class="hero-desc">Monitor user engagement, content metrics, and platform health. Track growth trends and identify opportunities for improvement across every dimension.</div>
            <div class="hero-chips">
                <span class="hero-chip blue"><span class="hero-chip-dot"></span>Real-time insights</span>
                <span class="hero-chip violet">👥 User metrics</span>
                <span class="hero-chip teal">💞 Engagement</span>
                <span class="hero-chip amber">🖼 Content</span>
            </div>
        </div>
        <div style="text-align:right;flex-shrink:0">
            <div style="font-family:'Syne',sans-serif;font-size:2.6rem;font-weight:800;letter-spacing:-.05em;background:linear-gradient(135deg,#3b9eff,#8b5cf6);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;line-height:1">{{ number_format($stats['users']['total']) }}</div>
            <div style="font-size:.65rem;color:var(--txt-3);margin-top:2px;font-weight:600;letter-spacing:.06em;text-transform:uppercase">Total Users</div>
            <div style="font-size:.68rem;color:var(--teal);margin-top:6px;font-weight:600">+{{ number_format($stats['users']['new_30']) }} this month</div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     SECTION: USER METRICS
══════════════════════════════════════════════ --}}
<div class="section-head">
    <div class="section-ico" style="--c1:#3b9eff;--c2:#8b5cf6">👥</div>
    <span class="section-label">User Metrics</span>
    <div class="section-line"></div>
</div>

<div class="sg4">
    <div class="sc cb">
        <div class="scblob"></div>
        <div class="sct">
            <div class="scico">👤</div>
            <span class="trnd up">+{{ number_format($stats['users']['new_30']) }}/mo</span>
        </div>
        <div class="scv">{{ number_format($stats['users']['total']) }}</div>
        <div class="scl">Total Users</div>
        <div class="scsub">Registered users on the platform</div>
        <div class="scbar"><div class="scbarf" style="--p:100%"></div></div>
    </div>
    <div class="sc cg">
        <div class="scblob"></div>
        <div class="sct">
            <div class="scico">✅</div>
            <span class="trnd up">Active</span>
        </div>
        <div class="scv">{{ number_format($stats['users']['active']) }}</div>
        <div class="scl">Active Users</div>
        <div class="scsub">Currently active user accounts</div>
        <div class="scbar"><div class="scbarf" style="--p:{{ $stats['users']['total'] > 0 ? round($stats['users']['active']/$stats['users']['total']*100) : 0 }}%"></div></div>
    </div>
    <div class="sc cv">
        <div class="scblob"></div>
        <div class="sct">
            <div class="scico">🛡️</div>
            <span class="trnd up">+10%</span>
        </div>
        <div class="scv">{{ number_format($stats['users']['verified']) }}</div>
        <div class="scl">Verified Users</div>
        <div class="scsub">Users with verified profiles</div>
        <div class="scbar"><div class="scbarf" style="--p:{{ $stats['users']['total'] > 0 ? round($stats['users']['verified']/$stats['users']['total']*100) : 0 }}%"></div></div>
    </div>
    <div class="sc ca">
        <div class="scblob"></div>
        <div class="sct">
            <div class="scico">⭐</div>
            <span class="trnd up">+8%</span>
        </div>
        <div class="scv">{{ number_format($stats['users']['premium']) }}</div>
        <div class="scl">Premium Users</div>
        <div class="scsub">Users with premium subscriptions</div>
        <div class="scbar"><div class="scbarf" style="--p:{{ $stats['users']['total'] > 0 ? round($stats['users']['premium']/$stats['users']['total']*100) : 0 }}%"></div></div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     SECTION: ENGAGEMENT METRICS
══════════════════════════════════════════════ --}}
<div class="section-head" style="animation-delay:.08s">
    <div class="section-ico" style="--c1:#ff3d7f;--c2:#8b5cf6">💞</div>
    <span class="section-label">Engagement Metrics</span>
    <div class="section-line"></div>
</div>

<div class="sg4">
    <div class="sc cp">
        <div class="scblob"></div>
        <div class="sct">
            <div class="scico">💬</div>
            <span class="trnd up">+{{ number_format($stats['engagement']['messages_30']) }}/mo</span>
        </div>
        <div class="scv {{ number_format($stats['engagement']['messages']) >= 1000000 ? 'scv-sm' : '' }}">
            @php $m = $stats['engagement']['messages'] @endphp
            {{ $m >= 1000000 ? round($m/1000000,1).'M' : ($m >= 1000 ? round($m/1000,1).'k' : number_format($m)) }}
        </div>
        <div class="scl">Total Messages</div>
        <div class="scsub">Messages sent between users</div>
        <div class="scbar"><div class="scbarf" style="--p:88%"></div></div>
    </div>
    <div class="sc cr">
        <div class="scblob"></div>
        <div class="sct">
            <div class="scico">❤️</div>
            <span class="trnd up">+18%</span>
        </div>
        <div class="scv {{ number_format($stats['engagement']['likes']) >= 1000000 ? 'scv-sm' : '' }}">
            @php $l = $stats['engagement']['likes'] @endphp
            {{ $l >= 1000000 ? round($l/1000000,1).'M' : ($l >= 1000 ? round($l/1000,1).'k' : number_format($l)) }}
        </div>
        <div class="scl">Total Likes</div>
        <div class="scsub">Profile likes and interactions</div>
        <div class="scbar"><div class="scbarf" style="--p:76%"></div></div>
    </div>
    <div class="sc cb">
        <div class="scblob"></div>
        <div class="sct">
            <div class="scico">💞</div>
            <span class="trnd up">+{{ number_format($stats['engagement']['matches_30']) }}/mo</span>
        </div>
        <div class="scv {{ number_format($stats['engagement']['matches']) >= 1000000 ? 'scv-sm' : '' }}">
            @php $ma = $stats['engagement']['matches'] @endphp
            {{ $ma >= 1000000 ? round($ma/1000000,1).'M' : ($ma >= 1000 ? round($ma/1000,1).'k' : number_format($ma)) }}
        </div>
        <div class="scl">Total Matches</div>
        <div class="scsub">Successful user matches</div>
        <div class="scbar"><div class="scbarf" style="--p:62%"></div></div>
    </div>
    <div class="sc cv">
        <div class="scblob"></div>
        <div class="sct">
            <div class="scico">👆</div>
            <span class="trnd up">+24%</span>
        </div>
        <div class="scv {{ $stats['engagement']['swipes'] >= 1000000 ? 'scv-sm' : '' }}">
            @php $sw = $stats['engagement']['swipes'] @endphp
            {{ $sw >= 1000000 ? round($sw/1000000,1).'M' : ($sw >= 1000 ? round($sw/1000,1).'k' : number_format($sw)) }}
        </div>
        <div class="scl">Total Swipes</div>
        <div class="scsub">Profile swipes (left &amp; right)</div>
        <div class="scbar"><div class="scbarf" style="--p:92%"></div></div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     SECTION: CONTENT METRICS
══════════════════════════════════════════════ --}}
<div class="section-head" style="animation-delay:.14s">
    <div class="section-ico" style="--c1:#14b8a6;--c2:#06d6a0">🖼</div>
    <span class="section-label">Content Metrics</span>
    <div class="section-line"></div>
</div>

<div class="sg3">
    <div class="sc ct">
        <div class="scblob"></div>
        <div class="sct">
            <div class="scico">🙍</div>
            <span class="trnd up">+15%</span>
        </div>
        <div class="scv">{{ number_format($stats['content']['profiles']) }}</div>
        <div class="scl">User Profiles</div>
        <div class="scsub">Completed user profiles</div>
        <div class="scbar"><div class="scbarf" style="--p:{{ $stats['users']['total'] > 0 ? round($stats['content']['profiles']/$stats['users']['total']*100) : 0 }}%"></div></div>
    </div>
    <div class="sc co">
        <div class="scblob"></div>
        <div class="sct">
            <div class="scico">🖼</div>
            <span class="trnd up">+22%</span>
        </div>
        <div class="scv">
            @php $img = $stats['content']['images'] @endphp
            {{ $img >= 1000 ? round($img/1000,1).'k' : number_format($img) }}
        </div>
        <div class="scl">Profile Images</div>
        <div class="scsub">Uploaded profile photos</div>
        <div class="scbar"><div class="scbarf" style="--p:74%"></div></div>
    </div>
    <div class="sc ce">
        <div class="scblob"></div>
        <div class="sct">
            <div class="scico">🚩</div>
            <span class="trnd dn">+3 new</span>
        </div>
        <div class="scv">{{ number_format($stats['content']['reports']) }}</div>
        <div class="scl">Reports</div>
        <div class="scsub">User reports &amp; moderation queue</div>
        <div class="scbar"><div class="scbarf" style="--p:18%"></div></div>
    </div>
</div>

{{-- ── CHART PLACEHOLDER ── --}}
<div class="chart-panel">
    <div class="chart-inner">
        <div class="chart-bars">
            @php
              $bars = [38,55,42,68,50,75,60,82,58,90,70,85];
              $colors = ['#3b9eff','#8b5cf6','#06d6a0','#ff3d7f','#ffbe0b','#14b8a6','#3b9eff','#8b5cf6','#06d6a0','#ff3d7f','#ffbe0b','#3b9eff'];
            @endphp
            @foreach($bars as $i => $h)
                <div class="chart-bar" style="height:{{ $h }}px;--c:{{ $colors[$i] }};background:linear-gradient(180deg,{{ $colors[$i] }},rgba(59,158,255,.1))"></div>
            @endforeach
        </div>
        <div style="font-size:1.6rem;opacity:.2;margin-bottom:8px">📊</div>
        <div class="chart-title">Advanced Charts Coming Soon</div>
        <div class="chart-desc">Interactive charts for user growth, engagement trends, and detailed analytics will be available in the next update.</div>
        <div class="chart-badge">🚧 In Development — Next Release</div>
    </div>
</div>

@endsection