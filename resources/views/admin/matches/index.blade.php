@extends('admin.layout')

@section('title', 'Matches & Likes — Aura Admin')

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
.ce{--c1:#ff4757;--c2:#ff8080;--glow:rgba(255,71,87,.1)}
.cb{--c1:#3b9eff;--c2:#80c0ff;--glow:rgba(59,158,255,.1)}

/* ───────────── FILTER BAR ───────────── */
.fb{
  display:flex;gap:8px;align-items:center;
  margin-bottom:12px;animation:fu .5s .1s ease both;
}
.fsel{
  background:var(--surface2);border:1px solid var(--border);
  border-radius:var(--rsm);padding:6px 10px;
  color:var(--txt);font-size:.72rem;outline:none;cursor:pointer;
  transition:border-color .2s;
}
.fsel:focus{border-color:rgba(255,61,127,.4)}
.finp{
  flex:1;background:var(--surface2);border:1px solid var(--border);
  border-radius:var(--rsm);padding:6px 10px;
  color:var(--txt);font-size:.72rem;outline:none;
  transition:border-color .2s;
}
.finp::placeholder{color:var(--txt-3)}
.finp:focus{border-color:rgba(255,61,127,.4)}

/* ───────────── MATCH LIST PANEL ───────────── */
.panel{
  background:var(--surface);border:1px solid var(--border);
  border-radius:var(--r);overflow:hidden;
  animation:fu .5s .15s ease both;
}
.phead{
  display:flex;justify-content:space-between;align-items:center;
  padding:10px 14px;border-bottom:1px solid var(--border);
}
.ptitle{font-family:'Syne',sans-serif;font-size:.8rem;font-weight:700;color:var(--txt)}
.ptag{
  font-size:.64rem;color:var(--txt-3);
  background:var(--surface2);border:1px solid var(--border);
  border-radius:100px;padding:2px 8px;
}

/* ───────────── TABLE ───────────── */
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

/* User cell */
.uc{display:flex;align-items:center;gap:7px}
.uav{
  width:26px;height:26px;border-radius:7px;
  background:linear-gradient(135deg,var(--rose),var(--violet));
  display:grid;place-items:center;
  font-family:'Syne',sans-serif;font-size:.58rem;font-weight:700;color:#fff;flex-shrink:0;
}
.ue{font-size:.72rem;color:var(--txt);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:110px}
.em{font-size:.62rem;color:var(--txt-3)}

/* Arrow connector */
.arr{
  display:flex;align-items:center;justify-content:center;
  color:var(--txt-3);font-size:.75rem;padding:0 4px;
}

/* Status badges */
.bd{
  display:inline-flex;align-items:center;gap:3px;
  padding:2px 7px;border-radius:100px;
  font-size:.62rem;font-weight:600;white-space:nowrap;
}
.bddot{width:4px;height:4px;border-radius:50%;background:currentColor}
.bdp{background:rgba(255,190,11,.1);color:var(--amber);border:1px solid rgba(255,190,11,.2)}
.bda{background:rgba(6,214,160,.08);color:var(--teal);border:1px solid rgba(6,214,160,.2)}
.bdr{background:rgba(255,71,87,.1);color:var(--red);border:1px solid rgba(255,71,87,.2)}

/* Action buttons */
.actn{display:flex;gap:6px;align-items:center}
.abtn{
  display:inline-flex;align-items:center;gap:4px;
  padding:3px 8px;border-radius:6px;font-size:.62rem;font-weight:600;
  border:1px solid var(--border);background:var(--surface2);
  color:var(--txt-2);cursor:pointer;text-decoration:none;
  transition:all .2s;white-space:nowrap;
}
.abtn:hover{background:rgba(255,255,255,.05);color:var(--txt);transform:translateY(-1px)}
.abtn-del{border-color:rgba(255,71,87,.25);color:var(--red)}
.abtn-del:hover{background:rgba(255,71,87,.1);border-color:rgba(255,71,87,.5)}

/* ───────────── EMPTY STATE ───────────── */
.empty{
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  padding:60px 20px;color:var(--txt-3);text-align:center;
}
.empty-ico{font-size:2.5rem;margin-bottom:12px;opacity:.4}
.empty h4{font-family:'Syne',sans-serif;font-size:.85rem;color:var(--txt-2);margin-bottom:4px}
.empty p{font-size:.7rem;max-width:240px}

/* ───────────── PAGINATION ───────────── */
.pg{
  display:flex;justify-content:center;gap:6px;
  padding:12px 14px;border-top:1px solid var(--border);
}
.pg .page-link{
  padding:4px 9px;border-radius:var(--rsm);
  background:var(--surface2);border:1px solid var(--border);
  color:var(--txt-2);font-size:.7rem;text-decoration:none;
  transition:all .2s;
}
.pg .page-link:hover,.pg .page-link.active{
  background:var(--rose);color:#fff;border-color:var(--rose);
}

@keyframes fu{from{opacity:0;transform:translateY(6px)}}
</style>
@endsection

@section('content')

{{-- ── Page Header ── --}}
<div class="ph">
    <div>
        <div class="ph-title">Matches &amp; Likes</div>
        <div class="ph-sub">Manage user connections and relationships</div>
    </div>
    <div style="display:flex;align-items:center;gap:6px;font-size:.68rem;color:var(--txt-2);
                background:var(--surface);border:1px solid var(--border);border-radius:7px;padding:4px 10px;">
        <i class="bi bi-heart-fill" style="color:var(--rose);font-size:.7rem"></i>
        Live Feed
    </div>
</div>

{{-- ── Stat Cards ── --}}
<div class="sg">
    <div class="sc cr">
        <div class="scblob"></div>
        <div class="sct"><div class="scico">💞</div><span class="trnd up">+12%</span></div>
        <div class="scv">{{ number_format($stats['total']) }}</div>
        <div class="scl">Total Likes</div>
        <div class="scbar"><div class="scbarf" style="--p:80%"></div></div>
    </div>
    <div class="sc ca">
        <div class="scblob"></div>
        <div class="sct"><div class="scico">⏳</div><span class="trnd dn">-2%</span></div>
        <div class="scv">{{ number_format($stats['pending']) }}</div>
        <div class="scl">Pending</div>
        <div class="scbar"><div class="scbarf" style="--p:40%"></div></div>
    </div>
    <div class="sc cg">
        <div class="scblob"></div>
        <div class="sct"><div class="scico">✅</div><span class="trnd up">+18%</span></div>
        <div class="scv">{{ number_format($stats['accepted']) }}</div>
        <div class="scl">Matches</div>
        <div class="scbar"><div class="scbarf" style="--p:65%"></div></div>
    </div>
    <div class="sc ce">
        <div class="scblob"></div>
        <div class="sct"><div class="scico">❌</div><span class="trnd dn">+5%</span></div>
        <div class="scv">{{ number_format($stats['rejected']) }}</div>
        <div class="scl">Rejected</div>
        <div class="scbar"><div class="scbarf" style="--p:28%"></div></div>
    </div>
</div>

{{-- ── Filters ── --}}
<div class="fb">
    <form method="GET" style="display:flex;gap:8px;width:100%;align-items:center">
        <select name="status" class="fsel" onchange="this.form.submit()">
            <option value="all"      {{ $status == 'all'      ? 'selected' : '' }}>All Status</option>
            <option value="pending"  {{ $status == 'pending'  ? 'selected' : '' }}>Pending</option>
            <option value="accepted" {{ $status == 'accepted' ? 'selected' : '' }}>Accepted</option>
            <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
        <input type="text" name="search" class="finp"
               placeholder="🔍  Search by name or email…"
               value="{{ $search }}"
               onkeyup="if(event.keyCode==13)this.form.submit()">
    </form>
</div>

{{-- ── Likes Table ── --}}
<div class="panel">
    <div class="phead">
        <span class="ptitle">All Connections</span>
        <span class="ptag">{{ $likes->total() }} records</span>
    </div>

    @if($likes->count() > 0)
    <div style="overflow-x:auto">
        <table class="dt">
            <thead>
                <tr>
                    <th>Sender</th>
                    <th></th>
                    <th>Receiver</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($likes as $like)
            <tr>
                {{-- Sender --}}
                <td>
                    <div class="uc">
                        <div class="uav">{{ strtoupper(substr($like->sender->email, 0, 2)) }}</div>
                        <div>
                            <div class="ue">{{ $like->sender->profile->first_name ?? '' }} {{ $like->sender->profile->last_name ?? 'Unknown' }}</div>
                            <div class="em">{{ $like->sender->email }}</div>
                        </div>
                    </div>
                </td>

                {{-- Arrow --}}
                <td style="width:30px">
                    <div class="arr"><i class="bi bi-arrow-right"></i></div>
                </td>

                {{-- Receiver --}}
                <td>
                    <div class="uc">
                        <div class="uav" style="background:linear-gradient(135deg,var(--violet),#3b9eff)">
                            {{ strtoupper(substr($like->receiver->email, 0, 2)) }}
                        </div>
                        <div>
                            <div class="ue">{{ $like->receiver->profile->first_name ?? '' }} {{ $like->receiver->profile->last_name ?? 'Unknown' }}</div>
                            <div class="em">{{ $like->receiver->email }}</div>
                        </div>
                    </div>
                </td>

                {{-- Status --}}
                <td>
                    @if($like->status === 'pending')
                        <span class="bd bdp"><span class="bddot"></span>Pending</span>
                    @elseif($like->status === 'accepted')
                        <span class="bd bda"><span class="bddot"></span>Matched</span>
                    @else
                        <span class="bd bdr"><span class="bddot"></span>Rejected</span>
                    @endif
                </td>

                {{-- Time --}}
                <td class="m">{{ $like->created_at->diffForHumans() }}</td>

                {{-- Actions --}}
                <td>
                    <div class="actn">
                        <a href="{{ route('admin.matches.show', $like->id) }}" class="abtn">
                            <i class="bi bi-eye"></i> View
                        </a>
                        <form method="POST"
                              action="{{ route('admin.matches.destroy', $like->id) }}"
                              onsubmit="return confirm('Delete this like/match?')"
                              style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="abtn abtn-del">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="pg">
        {{ $likes->appends(request()->query())->links() }}
    </div>

    @else
    <div class="empty">
        <div class="empty-ico">💞</div>
        <h4>No likes found</h4>
        <p>Try adjusting your filters or check back later for new connections.</p>
    </div>
    @endif
</div>

@endsection