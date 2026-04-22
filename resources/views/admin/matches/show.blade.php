@extends('admin.layout')

@section('title', 'Match Details — Aura Admin')

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

/* ───────────── ACTION BUTTONS ───────────── */
.actn{display:flex;gap:8px;align-items:center}
.abtn{
  display:inline-flex;align-items:center;gap:5px;
  padding:5px 12px;border-radius:7px;font-size:.72rem;font-weight:600;
  border:1px solid var(--border);background:var(--surface2);
  color:var(--txt-2);cursor:pointer;text-decoration:none;
  transition:all .2s;white-space:nowrap;
}
.abtn:hover{background:rgba(255,255,255,.05);color:var(--txt);transform:translateY(-1px)}
.abtn-del{border-color:rgba(255,71,87,.25);color:var(--red)}
.abtn-del:hover{background:rgba(255,71,87,.1);border-color:rgba(255,71,87,.5)}

/* ───────────── MATCH HERO CARD ───────────── */
.mhero{
  background:var(--surface);border:1px solid var(--border);
  border-radius:var(--r);padding:20px;
  display:flex;align-items:center;gap:16px;
  margin-bottom:10px;animation:fu .5s ease both;
  position:relative;overflow:hidden;
}
.mhero::before{
  content:'';position:absolute;inset:0;
  background:linear-gradient(135deg,rgba(255,61,127,.04),rgba(139,92,246,.04));
  pointer-events:none;
}

.ublock{display:flex;align-items:center;gap:12px;flex:1}
.uav{
  width:52px;height:52px;border-radius:14px;
  background:linear-gradient(135deg,var(--rose),var(--violet));
  display:grid;place-items:center;
  font-family:'Syne',sans-serif;font-size:1.1rem;font-weight:800;
  color:#fff;flex-shrink:0;
  box-shadow:0 4px 16px rgba(255,61,127,.25);
}
.uav-recv{background:linear-gradient(135deg,var(--violet),#3b9eff);
          box-shadow:0 4px 16px rgba(139,92,246,.25)}
.un{font-family:'Syne',sans-serif;font-size:.88rem;font-weight:700;color:var(--txt);margin-bottom:2px}
.ue{font-size:.68rem;color:var(--txt-3)}
.urole{font-size:.6rem;color:var(--txt-3);margin-top:2px;
       background:var(--surface2);border:1px solid var(--border);
       border-radius:100px;padding:1px 6px;display:inline-block}

.marr{
  display:flex;flex-direction:column;align-items:center;gap:3px;
  color:var(--txt-3);flex-shrink:0;padding:0 8px;
}
.marr i{font-size:1rem}
.marr span{font-size:.55rem;letter-spacing:.08em;text-transform:uppercase}

/* Status badge */
.sbadge{
  display:inline-flex;align-items:center;gap:5px;
  padding:5px 12px;border-radius:100px;
  font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;
  flex-shrink:0;
}
.sbadge-dot{width:5px;height:5px;border-radius:50%;background:currentColor}
.sbp{background:rgba(255,190,11,.1);color:var(--amber);border:1px solid rgba(255,190,11,.2)}
.sba{background:rgba(6,214,160,.08);color:var(--teal);border:1px solid rgba(6,214,160,.2)}
.sbr{background:rgba(255,71,87,.1);color:var(--red);border:1px solid rgba(255,71,87,.2)}

/* ───────────── META STRIP ───────────── */
.meta{
  display:grid;grid-template-columns:repeat(4,1fr);gap:10px;
  margin-bottom:10px;animation:fu .5s .08s ease both;
}
.mi{
  background:var(--surface);border:1px solid var(--border);
  border-radius:var(--r);padding:12px;text-align:center;
}
.mil{font-size:.6rem;color:var(--txt-3);text-transform:uppercase;letter-spacing:.07em;margin-bottom:5px}
.miv{font-family:'Syne',sans-serif;font-size:.95rem;font-weight:700;color:var(--txt)}

/* ───────────── PROFILE PANELS ───────────── */
.bg2{display:grid;grid-template-columns:1fr 1fr;gap:10px;animation:fu .5s .14s ease both}

.panel{
  background:var(--surface);border:1px solid var(--border);
  border-radius:var(--r);overflow:hidden;
}
.phead{
  display:flex;align-items:center;gap:8px;
  padding:10px 14px;border-bottom:1px solid var(--border);
}
.phead-ico{
  width:22px;height:22px;border-radius:6px;
  display:grid;place-items:center;font-size:.7rem;flex-shrink:0;
}
.phead-send{background:rgba(255,61,127,.12)}
.phead-recv{background:rgba(139,92,246,.12)}
.ptitle{font-family:'Syne',sans-serif;font-size:.78rem;font-weight:700;color:var(--txt)}
.ptag{
  margin-left:auto;font-size:.62rem;color:var(--txt-3);
  background:var(--surface2);border:1px solid var(--border);
  border-radius:100px;padding:1px 7px;
}

/* Detail grid */
.dg{display:grid;grid-template-columns:1fr 1fr;gap:12px;padding:16px}
.di{display:flex;flex-direction:column;gap:3px}
.dl{font-size:.6rem;color:var(--txt-3);text-transform:uppercase;letter-spacing:.07em;font-weight:600}
.dv{font-size:.75rem;color:var(--txt);font-weight:500}
.dv-null{color:var(--txt-3);font-style:italic}

/* completed chip */
.chip-yes{
  display:inline-flex;align-items:center;gap:3px;
  padding:1px 7px;border-radius:100px;font-size:.62rem;font-weight:600;
  background:rgba(6,214,160,.08);color:var(--teal);border:1px solid rgba(6,214,160,.2);
}
.chip-no{
  display:inline-flex;align-items:center;gap:3px;
  padding:1px 7px;border-radius:100px;font-size:.62rem;font-weight:600;
  background:rgba(255,255,255,.04);color:var(--txt-3);border:1px solid var(--border);
}

.no-profile{
  padding:28px;text-align:center;
  font-size:.72rem;color:var(--txt-3);
}

@keyframes fu{from{opacity:0;transform:translateY(6px)}}
</style>
@endsection

@section('content')

{{-- ── Page Header ── --}}
<div class="ph">
    <div>
        <div class="ph-title">Match Details</div>
        <div class="ph-sub">Like #{{ $like->id }}</div>
    </div>
    <div class="actn">
        <a href="{{ route('admin.matches.index') }}" class="abtn">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <form method="POST"
              action="{{ route('admin.matches.destroy', $like->id) }}"
              onsubmit="return confirm('Delete this like/match?')"
              style="display:inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="abtn abtn-del">
                <i class="bi bi-trash"></i> Delete
            </button>
        </form>
    </div>
</div>

{{-- ── Match Hero ── --}}
<div class="mhero">

    {{-- Sender --}}
    <div class="ublock">
        <div class="uav">{{ strtoupper(substr($like->sender->email, 0, 2)) }}</div>
        <div>
            <div class="un">{{ $like->sender->profile->first_name ?? 'Unknown' }} {{ $like->sender->profile->last_name ?? '' }}</div>
            <div class="ue">{{ $like->sender->email }}</div>
            <div class="urole">Sender</div>
        </div>
    </div>

    {{-- Arrow --}}
    <div class="marr">
        <i class="bi bi-arrow-right"></i>
        <span>liked</span>
    </div>

    {{-- Receiver --}}
    <div class="ublock">
        <div class="uav uav-recv">{{ strtoupper(substr($like->receiver->email, 0, 2)) }}</div>
        <div>
            <div class="un">{{ $like->receiver->profile->first_name ?? 'Unknown' }} {{ $like->receiver->profile->last_name ?? '' }}</div>
            <div class="ue">{{ $like->receiver->email }}</div>
            <div class="urole">Receiver</div>
        </div>
    </div>

    {{-- Status --}}
    @if($like->status === 'pending')
        <span class="sbadge sbp"><span class="sbadge-dot"></span>Pending</span>
    @elseif($like->status === 'accepted')
        <span class="sbadge sba"><span class="sbadge-dot"></span>Matched</span>
    @else
        <span class="sbadge sbr"><span class="sbadge-dot"></span>Rejected</span>
    @endif

</div>

{{-- ── Meta Strip ── --}}
<div class="meta">
    <div class="mi">
        <div class="mil">ID</div>
        <div class="miv">#{{ $like->id }}</div>
    </div>
    <div class="mi">
        <div class="mil">Status</div>
        <div class="miv">{{ ucfirst($like->status) }}</div>
    </div>
    <div class="mi">
        <div class="mil">Created</div>
        <div class="miv" style="font-size:.78rem">{{ $like->created_at->format('M j, Y') }}</div>
    </div>
    <div class="mi">
        <div class="mil">Updated</div>
        <div class="miv" style="font-size:.78rem">{{ $like->updated_at->diffForHumans() }}</div>
    </div>
</div>

{{-- ── Profile Detail Panels ── --}}
<div class="bg2">

    {{-- Sender Profile --}}
    <div class="panel">
        <div class="phead">
            <div class="phead-ico phead-send">💞</div>
            <span class="ptitle">Sender Profile</span>
            <span class="ptag">{{ $like->sender->email }}</span>
        </div>
        @if($like->sender->profile)
        <div class="dg">
            <div class="di">
                <div class="dl">First Name</div>
                <div class="dv">{{ $like->sender->profile->first_name ?? '—' }}</div>
            </div>
            <div class="di">
                <div class="dl">Last Name</div>
                <div class="dv">{{ $like->sender->profile->last_name ?? '—' }}</div>
            </div>
            <div class="di" style="grid-column:1/-1">
                <div class="dl">Bio</div>
                <div class="dv {{ !$like->sender->profile->bio ? 'dv-null' : '' }}">
                    {{ $like->sender->profile->bio ?? 'Not set' }}
                </div>
            </div>
            <div class="di">
                <div class="dl">Gender</div>
                <div class="dv">{{ ucfirst($like->sender->profile->gender ?? '—') }}</div>
            </div>
            <div class="di">
                <div class="dl">Interested In</div>
                <div class="dv">{{ ucfirst($like->sender->profile->interested_in ?? '—') }}</div>
            </div>
            <div class="di">
                <div class="dl">Date of Birth</div>
                <div class="dv">
                    {{ $like->sender->profile->date_of_birth
                        ? $like->sender->profile->date_of_birth->format('M j, Y')
                        : '—' }}
                </div>
            </div>
            <div class="di">
                <div class="dl">Profile Completed</div>
                <div class="dv">
                    @if(($like->sender->profile->profile_completed ?? 0) > 0)
                        <span class="chip-yes">✓ Yes</span>
                    @else
                        <span class="chip-no">No</span>
                    @endif
                </div>
            </div>
        </div>
        @else
        <div class="no-profile">No profile information available.</div>
        @endif
    </div>

    {{-- Receiver Profile --}}
    <div class="panel">
        <div class="phead">
            <div class="phead-ico phead-recv">👤</div>
            <span class="ptitle">Receiver Profile</span>
            <span class="ptag">{{ $like->receiver->email }}</span>
        </div>
        @if($like->receiver->profile)
        <div class="dg">
            <div class="di">
                <div class="dl">First Name</div>
                <div class="dv">{{ $like->receiver->profile->first_name ?? '—' }}</div>
            </div>
            <div class="di">
                <div class="dl">Last Name</div>
                <div class="dv">{{ $like->receiver->profile->last_name ?? '—' }}</div>
            </div>
            <div class="di" style="grid-column:1/-1">
                <div class="dl">Bio</div>
                <div class="dv {{ !$like->receiver->profile->bio ? 'dv-null' : '' }}">
                    {{ $like->receiver->profile->bio ?? 'Not set' }}
                </div>
            </div>
            <div class="di">
                <div class="dl">Gender</div>
                <div class="dv">{{ ucfirst($like->receiver->profile->gender ?? '—') }}</div>
            </div>
            <div class="di">
                <div class="dl">Interested In</div>
                <div class="dv">{{ ucfirst($like->receiver->profile->interested_in ?? '—') }}</div>
            </div>
            <div class="di">
                <div class="dl">Date of Birth</div>
                <div class="dv">
                    {{ $like->receiver->profile->date_of_birth
                        ? $like->receiver->profile->date_of_birth->format('M j, Y')
                        : '—' }}
                </div>
            </div>
            <div class="di">
                <div class="dl">Profile Completed</div>
                <div class="dv">
                    @if(($like->receiver->profile->profile_completed ?? 0) > 0)
                        <span class="chip-yes">✓ Yes</span>
                    @else
                        <span class="chip-no">No</span>
                    @endif
                </div>
            </div>
        </div>
        @else
        <div class="no-profile">No profile information available.</div>
        @endif
    </div>

</div>

@endsection