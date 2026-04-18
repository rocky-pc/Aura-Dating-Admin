<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aura — Admin Panel')</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=Figtree:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
    /* ───────────── TOKENS ───────────── */
    :root {
      --bg:        #080612;
      --surface:   #100e1e;
      --surface2:  #161328;
      --border:    rgba(255,255,255,0.07);
      --rose:      #ff3d7f;
      --violet:    #8b5cf6;
      --teal:      #06d6a0;
      --amber:     #ffbe0b;
      --blue:      #3b9eff;
      --red:       #ff4757;
      --txt:       #ede9ff;
      --txt-2:     rgba(237,233,255,0.5);
      --txt-3:     rgba(237,233,255,0.2);
      --sw:        210px;   /* sidebar width */
      --hh:        50px;    /* header height */
      --r:         13px;
      --rsm:       9px;
    }
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    html,body{height:100%;overflow:hidden}
    body{font-family:'Figtree',sans-serif;font-size:13px;background:var(--bg);color:var(--txt)}
    a{text-decoration:none;color:inherit}
    button{cursor:pointer;border:none;background:none;font-family:inherit}

    /* ───────────── SHELL GRID ───────────── */
    .shell{
      display:grid;
      grid-template-columns:var(--sw) 1fr;
      grid-template-rows:var(--hh) 1fr;
      height:100vh;
      overflow:hidden;
    }

    /* ───────────── TOPBAR ───────────── */
    .topbar{
      grid-column:1/-1;
      display:flex;align-items:center;justify-content:space-between;
      background:var(--surface);
      border-bottom:1px solid var(--border);
      padding:0 16px 0 0;
      z-index:20;
    }
    .brand{
      width:var(--sw);display:flex;align-items:center;gap:8px;
      padding:0 14px;flex-shrink:0;
      border-right:1px solid var(--border);height:100%;
    }
    .brand-ico{
      width:28px;height:28px;border-radius:8px;
      background:linear-gradient(135deg,var(--rose),var(--violet));
      display:grid;place-items:center;font-size:13px;
      box-shadow:0 3px 12px rgba(255,61,127,.35);flex-shrink:0;
    }
    .brand-name{
      font-family:'Syne',sans-serif;font-weight:800;font-size:.95rem;letter-spacing:-.02em;
      background:linear-gradient(120deg,#fff,var(--rose));
      -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
    }
    .topbar-r{display:flex;align-items:center;gap:8px}
    .live-chip{
      display:flex;align-items:center;gap:5px;
      background:rgba(6,214,160,.08);border:1px solid rgba(6,214,160,.2);
      border-radius:100px;padding:3px 9px;font-size:.65rem;color:var(--teal);font-weight:600;
    }
    .ldot{width:5px;height:5px;border-radius:50%;background:var(--teal);animation:blink 1.8s ease infinite}
    @keyframes blink{0%,100%{opacity:1}50%{opacity:.2}}
    .srch{
      display:flex;align-items:center;gap:6px;
      background:var(--surface2);border:1px solid var(--border);
      border-radius:7px;padding:4px 10px;color:var(--txt-3);font-size:.72rem;
    }
    .ibtn{
      width:30px;height:30px;border-radius:7px;
      display:grid;place-items:center;
      background:var(--surface2);border:1px solid var(--border);
      color:var(--txt-2);font-size:.85rem;
      transition:background .18s,color .18s;position:relative;
    }
    .ibtn:hover{background:rgba(255,255,255,.08);color:var(--txt)}
    .ndot{position:absolute;top:5px;right:5px;width:5px;height:5px;border-radius:50%;background:var(--rose);border:1.5px solid var(--surface)}
    .av{
      width:30px;height:30px;border-radius:8px;
      background:linear-gradient(135deg,var(--rose),var(--violet));
      display:grid;place-items:center;font-family:'Syne',sans-serif;
      font-size:.65rem;font-weight:700;color:#fff;cursor:pointer;
      box-shadow:0 3px 10px rgba(139,92,246,.3);
    }

    /* ───────────── SIDEBAR ───────────── */
    .sidebar{
      background:var(--surface);border-right:1px solid var(--border);
      display:flex;flex-direction:column;
      overflow-y:auto;overflow-x:hidden;
      padding:12px 8px;
    }
    .sidebar::-webkit-scrollbar{width:2px}
    .sidebar::-webkit-scrollbar-thumb{background:var(--border)}

    .nav-lbl{
      font-size:.58rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;
      color:var(--txt-3);padding:8px 8px 5px;
    }
    .ni{
      display:flex;align-items:center;gap:8px;
      padding:7px 9px;border-radius:var(--rsm);
      color:var(--txt-2);font-size:.77rem;font-weight:500;
      transition:background .15s,color .15s;cursor:pointer;white-space:nowrap;
    }
    .ni:hover{background:rgba(255,255,255,.05);color:var(--txt)}
    .ni.active{
      background:linear-gradient(135deg,rgba(255,61,127,.14),rgba(139,92,246,.08));
      color:var(--txt);border:1px solid rgba(255,61,127,.18);
    }
    .ni.active i{color:var(--rose)}
    .ni i{font-size:.9rem;width:15px;text-align:center;flex-shrink:0}
    .nb{
      margin-left:auto;background:var(--rose);color:#fff;
      font-size:.58rem;font-weight:700;padding:1px 6px;border-radius:100px;
    }
    .sdiv{height:1px;background:var(--border);margin:8px 4px}
    .sfooter{margin-top:auto;padding:8px 0 0}
    .ucard{
      display:flex;align-items:center;gap:8px;
      background:var(--surface2);border:1px solid var(--border);
      border-radius:var(--rsm);padding:8px 9px;
    }
    .ucard .uav{
      width:26px;height:26px;border-radius:7px;
      background:linear-gradient(135deg,var(--rose),var(--violet));
      display:grid;place-items:center;
      font-family:'Syne',sans-serif;font-size:.6rem;font-weight:700;color:#fff;flex-shrink:0;
    }
    .ucard-info{overflow:hidden}
    .ucard-name{font-size:.72rem;font-weight:600;color:var(--txt);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .ucard-role{font-size:.6rem;color:var(--txt-3)}

    /* ───────────── MAIN ───────────── */
    .main{
      overflow-y:auto;overflow-x:hidden;
      background:var(--bg);position:relative;
    }
    .main::-webkit-scrollbar{width:3px}
    .main::-webkit-scrollbar-thumb{background:rgba(255,255,255,.1);border-radius:3px}
    .main::before,.main::after{
      content:'';position:fixed;border-radius:50%;pointer-events:none;
      filter:blur(100px);opacity:.1;z-index:0;
    }
    .main::before{
      width:450px;height:450px;
      background:radial-gradient(circle,var(--rose),transparent 70%);
      top:-120px;right:-60px;animation:da 20s ease-in-out infinite alternate;
    }
    .main::after{
      width:380px;height:380px;
      background:radial-gradient(circle,var(--violet),transparent 70%);
      bottom:0;left:0;animation:db 25s ease-in-out infinite alternate;
    }
    @keyframes da{to{transform:translate(-40px,60px)scale(1.1)}}
    @keyframes db{to{transform:translate(60px,-40px)scale(1.15)}}

    .page{position:relative;z-index:1;padding:16px 18px 20px}

    /* ───────────── ANIMATION ───────────── */
    @keyframes fu{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
    *::-webkit-scrollbar{width:3px;height:3px}
    *::-webkit-scrollbar-thumb{background:rgba(255,255,255,.08);border-radius:3px}

    /* ───────────── RESPONSIVE ───────────── */
    @media(max-width:1280px){
      :root{--sw:190px}
    }
    @media(max-width:1024px){
      :root{--sw:48px}
      .ni span,.nav-lbl,.ucard-info,.nb,.brand-name{display:none}
      .ni{justify-content:center;padding:7px}
      .brand{padding:0;justify-content:center}
      .srch{display:none}
    }
    @media(max-width:600px){
      :root{--sw:0px;--hh:46px}
      .sidebar{display:none}
      .shell{grid-template-columns:1fr}
    }

    @yield('styles')
    </style>
</head>
<body>
<div class="shell">

    {{-- ════ TOPBAR ════ --}}
    <header class="topbar">
        <div class="brand">
            <div class="brand-ico">🔥</div>
            <span class="brand-name">Aura</span>
        </div>
        <div class="topbar-r">
            <div class="srch"><i class="bi bi-search"></i> Search…</div>
            <div class="live-chip"><span class="ldot"></span>Live</div>
            <button class="ibtn"><i class="bi bi-bell"></i><span class="ndot"></span></button>
            <button class="ibtn"><i class="bi bi-gear"></i></button>
            <div class="av">{{ strtoupper(substr(auth()->user()->email ?? 'AD', 0, 2)) }}</div>
        </div>
    </header>

    {{-- ════ SIDEBAR ════ --}}
    <aside class="sidebar">
        <div class="nav-lbl">Main</div>
        <a class="ni {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
            <i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span>
        </a>
        <a class="ni {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
            <i class="bi bi-people-fill"></i><span>Users</span><span class="nb">1.2k</span>
        </a>
        <a class="ni {{ request()->routeIs('admin.matches.*') ? 'active' : '' }}" href="{{ route('admin.matches.index') }}">
            <i class="bi bi-heart-fill"></i><span>Matches</span>
        </a>
        <a class="ni {{ request()->routeIs('admin.swipes.*') ? 'active' : '' }}" href="#">
            <i class="bi bi-hand-thumbs-up-fill"></i><span>Swipes</span>
        </a>
        <a class="ni {{ request()->routeIs('admin.favorites.*') ? 'active' : '' }}" href="{{ route('admin.favorites.index') }}">
            <i class="bi bi-bookmark-fill"></i><span>Favorites</span>
        </a>
        <a class="ni {{ request()->routeIs('admin.messages.*') ? 'active' : '' }}" href="#">
            <i class="bi bi-chat-dots-fill"></i><span>Messages</span><span class="nb">7</span>
        </a>
        <div class="sdiv"></div>
        <div class="nav-lbl">Moderation</div>
        <a class="ni {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
            <i class="bi bi-flag-fill"></i><span>Reports</span><span class="nb">3</span>
        </a>
        <a class="ni" href="#">
            <i class="bi bi-shield-check"></i><span>Verify</span>
        </a>
        <a class="ni" href="#">
            <i class="bi bi-slash-circle"></i><span>Bans</span>
        </a>
        <div class="sdiv"></div>
        <div class="nav-lbl">Revenue</div>
        <a class="ni {{ request()->routeIs('admin.subscriptions.*') ? 'active' : '' }}" href="{{ route('admin.subscriptions.index') }}">
            <i class="bi bi-credit-card-2-front-fill"></i><span>Subscriptions</span>
        </a>
        <a class="ni {{ request()->routeIs('admin.wallets.*') ? 'active' : '' }}" href="{{ route('admin.wallets.index') }}">
            <i class="bi bi-wallet-fill"></i><span>Wallets</span>
        </a>
        <a class="ni" href="#">
            <i class="bi bi-graph-up-arrow"></i><span>Analytics</span>
        </a>
        <a class="ni" href="#">
            <i class="bi bi-star-fill"></i><span>Premium</span>
        </a>
        <div class="sdiv"></div>
        <a class="ni" href="#">
            <i class="bi bi-credit-card-2-back-fill"></i><span>Payment Requests</span><span class="nb">0</span>
        </a>
        <a class="ni" href="#">
            <i class="bi bi-gear-fill"></i><span>Settings</span>
        </a>
        <div class="sfooter">
            <div class="ucard">
                <div class="uav">{{ strtoupper(substr(auth()->user()->email ?? 'AD', 0, 2)) }}</div>
                <div class="ucard-info">
                    <div class="ucard-name">{{ auth()->user()->email ?? 'admin@aura.app' }}</div>
                    <div class="ucard-role">Super Admin</div>
                </div>
            </div>
        </div>
    </aside>

    {{-- ════ MAIN CONTENT ════ --}}
    <main class="main">
        <div class="page">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-bottom:14px">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-bottom:14px">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')

        </div>
    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>