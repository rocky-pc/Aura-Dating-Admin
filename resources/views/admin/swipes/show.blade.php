@extends('admin.layout')

@section('title', 'Swipe Details')

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

.swipe-info{background:var(--sur);border:1px solid var(--bdr);border-radius:var(--r);padding:14px 16px;margin-bottom:14px;animation:fu .4s .1s ease both}
.si-row{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.si-badge{display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:100px;font-size:.7rem;font-weight:600}
.si-like{background:rgba(255,61,127,.12);color:var(--rose);border:1px solid rgba(255,61,127,.25)}
.si-dislike{background:rgba(156,163,175,.12);color:#9ca3af;border:1px solid rgba(156,163,175,.25)}
.si-super{background:rgba(139,92,246,.12);color:var(--violet);border:1px solid rgba(139,92,246,.25)}
.si-match{background:rgba(6,214,160,.12);color:var(--teal);border:1px solid rgba(6,214,160,.25)}
.si-date{color:var(--txt3);font-size:.7rem}

.profiles-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;animation:fu .4s .15s ease both}
.pcard{background:var(--sur);border:1px solid var(--bdr);border-radius:var(--r);padding:16px;position:relative;overflow:hidden}
.pcard::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,var(--pc1,transparent),transparent 60%);opacity:.06;pointer-events:none}
.pcard-header{display:flex;align-items:center;gap:10px;margin-bottom:14px;padding-bottom:12px;border-bottom:1px solid var(--bdr)}
.pcard-label{font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--txt3)}
.pcard-from{--pc1:var(--rose)}
.pcard-to{--pc1:var(--teal)}
.pp{display:flex;gap:10px;align-items:center}
.pp-avatar{width:56px;height:56px;border-radius:14px;display:grid;place-items:center;font-family:'Syne',sans-serif;font-size:1.1rem;font-weight:700;color:#fff;flex-shrink:0;background:linear-gradient(135deg,var(--c1),var(--c2))}
.pp-info{flex:1;min-width:0}
.pp-name{font-family:'Syne',sans-serif;font-size:.95rem;font-weight:700;color:var(--txt)}
.pp-email{font-size:.68rem;color:var(--txt3);margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

.pdetails{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.pditem{padding:8px 10px;background:var(--sur2);border-radius:var(--rsm)}
.pdlabel{font-size:.58rem;color:var(--txt3);text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px}
.pdval{font-size:.78rem;color:var(--txt);font-weight:500}

.pimages{display:flex;gap:8px;margin-top:12px;padding-top:12px;border-top:1px solid var(--bdr)}
.pimages-label{font-size:.58rem;color:var(--txt3);text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px}
.pi{width:48px;height:48px;border-radius:8px;background:var(--sur3);display:grid;place-items:center;color:var(--txt3);font-size:.8rem;flex-shrink:0}
.pi img{width:100%;height:100%;object-fit:cover;border-radius:8px}

.action-row{display:flex;justify-content:space-between;align-items:center;margin-top:14px;padding-top:14px;border-top:1px solid var(--bdr)}
.ar-badge{display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border-radius:100px;font-size:.72rem;font-weight:600}
.ar-match{background:rgba(6,214,160,.12);color:var(--teal);border:1px solid rgba(6,214,160,.25)}
.ar-no-match{background:rgba(156,163,175,.12);color:#9ca3af;border:1px solid rgba(156,163,175,.25)}

.btn-danger{display:inline-flex;align-items:center;gap:5px;background:rgba(255,71,87,.1);border:1px solid rgba(255,71,87,.25);border-radius:var(--rsm);padding:6px 12px;font-size:.7rem;font-weight:600;color:var(--red);transition:background .18s}
.btn-danger:hover{background:rgba(255,71,87,.2)}

@keyframes fu{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}
@media(max-width:900px){.profiles-grid{grid-template-columns:1fr}}
@media(max-width:600px){.up{padding:10px 10px 20px}}
</style>
@endsection

@section('content')
<div class="up">

  {{-- PAGE HEADER --}}
  <div class="ph">
    <div>
      <div class="ph-title">Swipe Details</div>
      <div class="ph-sub">Detailed view of swipe interaction</div>
    </div>
    <a href="{{ route('admin.swipes.index') }}" class="back-btn">
      <i class="bi bi-arrow-left"></i> Back to Swipes
    </a>
  </div>

  {{-- SWIPE INFO --}}
  <div class="swipe-info">
    <div class="si-row">
      @php
        $actionBadge = match($swipe->action) {
          'like' => 'si-like',
          'dislike' => 'si-dislike',
          'super_like' => 'si-super',
          'favorite' => 'si-like',
          default => 'si-dislike'
        };
        $actionIcon = match($swipe->action) {
          'like' => '❤️',
          'dislike' => '❌',
          'super_like' => '💫',
          'favorite' => '⭐',
          default => '❌'
        };
      @endphp
      <span class="si-badge {{ $actionBadge }}">{{ $actionIcon }} {{ ucfirst($swipe->action) }}</span>
      <span class="si-badge {{ $swipe->is_match ? 'si-match' : 'si-dislike' }}">
        {{ $swipe->is_match ? '🤝 Matched' : 'No Match' }}
      </span>
      <span class="si-date">ID: #{{ $swipe->id }} • {{ $swipe->created_at->format('F d, Y \a\t H:i') }}</span>
    </div>
  </div>

  {{-- PROFILES GRID --}}
  <div class="profiles-grid">
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
      $fromImages = $fromUser?->images ?? collect();
      $toImages = $toUser?->images ?? collect();
      $fromColor = $colors[($fromUser?->id ?? 0) % 7];
      $toColor = $colors[($toUser?->id ?? 1) % 7];
      $fromInitials = strtoupper(substr($fromProfile?->first_name ?? $fromUser?->email ?? '?', 0, 2));
      $toInitials = strtoupper(substr($toProfile?->first_name ?? $toUser?->email ?? '?', 0, 2));
      $fromName = $fromProfile?->first_name ? $fromProfile->first_name . ' ' . ($fromProfile->last_name ?? '') : ($fromUser?->email ?? 'Deleted User');
      $toName = $toProfile?->first_name ? $toProfile->first_name . ' ' . ($toProfile->last_name ?? '') : ($toUser?->email ?? 'Deleted User');
      $fromAge = $fromProfile?->date_of_birth ? now()->diffInYears($fromProfile->date_of_birth) : null;
      $toAge = $toProfile?->date_of_birth ? now()->diffInYears($toProfile->date_of_birth) : null;
    @endphp

    {{-- SWIPER (FROM) --}}
    <div class="pcard pcard-from">
      <div class="pcard-header">
        <span class="pcard-label">Swiper (From)</span>
      </div>
      <div class="pp">
        <div class="pp-avatar" style="--c1:{{ $fromColor[0] }};--c2:{{ $fromColor[1] }}">{{ $fromInitials }}</div>
        <div class="pp-info">
          <div class="pp-name">{{ $fromName }}</div>
          <div class="pp-email">{{ $fromUser?->email ?? 'N/A' }}</div>
        </div>
      </div>
      <div class="pdetails">
        <div class="pditem">
          <div class="pdlabel">Gender</div>
          <div class="pdval">{{ ucfirst($fromProfile?->gender ?? 'N/A') }}</div>
        </div>
        <div class="pditem">
          <div class="pdlabel">Age</div>
          <div class="pdval">{{ $fromAge ? $fromAge . ' years' : 'N/A' }}</div>
        </div>
        <div class="pditem">
          <div class="pdlabel">Interested In</div>
          <div class="pdval">{{ ucfirst($fromProfile?->interested_in ?? 'N/A') }}</div>
        </div>
        <div class="pditem">
          <div class="pdlabel">Verified</div>
          <div class="pdval">{{ $fromUser?->is_verified ? '✅ Yes' : '❌ No' }}</div>
        </div>
        <div class="pditem">
          <div class="pdlabel">Premium</div>
          <div class="pdval">{{ $fromUser?->is_premium ? '⭐ Yes' : 'Free' }}</div>
        </div>
        <div class="pditem">
          <div class="pdlabel">Status</div>
          <div class="pdval">{{ $fromUser?->is_active ? '🟢 Active' : '🔴 Inactive' }}</div>
        </div>
      </div>
      @if($fromProfile?->bio)
      <div class="pditem" style="margin-top:10px">
        <div class="pdlabel">Bio</div>
        <div class="pdval">{{ $fromProfile->bio }}</div>
      </div>
      @endif
      @if($fromImages->count() > 0)
      <div class="pimages">
        <div>
          <div class="pimages-label">Photos</div>
          <div style="display:flex;gap:6px;flex-wrap:wrap">
            @foreach($fromImages->take(4) as $img)
              <div class="pi">
                @if($img->image_url)
                  <img src="{{ $img->image_url }}" alt="Photo">
                @else
                  📷
                @endif
              </div>
            @endforeach
          </div>
        </div>
      </div>
      @endif
    </div>

    {{-- SWIPED (TO) --}}
    <div class="pcard pcard-to">
      <div class="pcard-header">
        <span class="pcard-label">Swiped (To)</span>
      </div>
      <div class="pp">
        <div class="pp-avatar" style="--c1:{{ $toColor[0] }};--c2:{{ $toColor[1] }}">{{ $toInitials }}</div>
        <div class="pp-info">
          <div class="pp-name">{{ $toName }}</div>
          <div class="pp-email">{{ $toUser?->email ?? 'N/A' }}</div>
        </div>
      </div>
      <div class="pdetails">
        <div class="pditem">
          <div class="pdlabel">Gender</div>
          <div class="pdval">{{ ucfirst($toProfile?->gender ?? 'N/A') }}</div>
        </div>
        <div class="pditem">
          <div class="pdlabel">Age</div>
          <div class="pdval">{{ $toAge ? $toAge . ' years' : 'N/A' }}</div>
        </div>
        <div class="pditem">
          <div class="pdlabel">Interested In</div>
          <div class="pdval">{{ ucfirst($toProfile?->interested_in ?? 'N/A') }}</div>
        </div>
        <div class="pditem">
          <div class="pdlabel">Verified</div>
          <div class="pdval">{{ $toUser?->is_verified ? '✅ Yes' : '❌ No' }}</div>
        </div>
        <div class="pditem">
          <div class="pdlabel">Premium</div>
          <div class="pdval">{{ $toUser?->is_premium ? '⭐ Yes' : 'Free' }}</div>
        </div>
        <div class="pditem">
          <div class="pdlabel">Status</div>
          <div class="pdval">{{ $toUser?->is_active ? '🟢 Active' : '🔴 Inactive' }}</div>
        </div>
      </div>
      @if($toProfile?->bio)
      <div class="pditem" style="margin-top:10px">
        <div class="pdlabel">Bio</div>
        <div class="pdval">{{ $toProfile->bio }}</div>
      </div>
      @endif
      @if($toImages->count() > 0)
      <div class="pimages">
        <div>
          <div class="pimages-label">Photos</div>
          <div style="display:flex;gap:6px;flex-wrap:wrap">
            @foreach($toImages->take(4) as $img)
              <div class="pi">
                @if($img->image_url)
                  <img src="{{ $img->image_url }}" alt="Photo">
                @else
                  📷
                @endif
              </div>
            @endforeach
          </div>
        </div>
      </div>
      @endif
    </div>
  </div>

  {{-- ACTION ROW --}}
  <div class="action-row">
    <span class="ar-badge {{ $swipe->is_match ? 'ar-match' : 'ar-no-match' }}">
      {{ $swipe->is_match ? '🤝 This swipe resulted in a match!' : '❌ No match from this swipe' }}
    </span>
    <form method="POST" action="{{ route('admin.swipes.destroy', $swipe->id) }}" onsubmit="return confirm('Are you sure you want to delete this swipe?')">
      @csrf
      @method('DELETE')
      <button type="submit" class="btn-danger">
        <i class="bi bi-trash-fill"></i> Delete Swipe
      </button>
    </form>
  </div>

</div>{{-- /up --}}
@endsection