@extends('admin.layout')

@section('title', 'Aura — Users Management')

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
  background:linear-gradient(135deg,var(--rose),var(--violet));
  display:grid;place-items:center;font-size:1rem;
  box-shadow:0 0 18px rgba(255,61,127,.25),0 4px 10px rgba(0,0,0,.35);
  flex-shrink:0;
}
.ph-actions{display:flex;gap:8px;align-items:center}

/* ───────────── BUTTONS ───────────── */
.btn{
  display:inline-flex;align-items:center;gap:6px;
  border-radius:7px;padding:7px 14px;
  font-size:.72rem;font-weight:600;
  transition:all .2s;white-space:nowrap;
  font-family:'Syne',sans-serif;cursor:pointer;border:none;
}
.btn-primary{
  background:var(--rose);color:#fff;
  box-shadow:0 0 16px rgba(255,61,127,.3),0 4px 10px rgba(0,0,0,.25);
}
.btn-primary:hover{opacity:.88;transform:translateY(-1px);box-shadow:0 0 24px rgba(255,61,127,.4)}
.btn-ghost{
  background:var(--surface);border:1px solid var(--border);
  color:var(--txt-2);
}
.btn-ghost:hover{background:var(--surface2);color:var(--txt);border-color:rgba(255,255,255,.12)}

/* ───────────── STAT STRIP ───────────── */
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
.fi:focus{border-color:rgba(255,61,127,.35);box-shadow:0 0 0 3px rgba(255,61,127,.07)}
.fi::placeholder{color:var(--txt-3)}
.fi-search{
  flex:1;min-width:180px;padding-left:32px;
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

/* User cell */
.u-cell{display:flex;align-items:center;gap:9px}
.u-av{
  width:30px;height:30px;border-radius:8px;
  display:grid;place-items:center;
  font-family:'Syne',sans-serif;font-size:.58rem;font-weight:700;
  color:#fff;flex-shrink:0;letter-spacing:0;
}
.u-name{
  font-size:.73rem;font-weight:500;color:var(--txt);
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:150px;
}
.u-id{font-family:monospace;font-size:.6rem;color:var(--txt-3);margin-top:1px}

/* Badges */
.bd{
  display:inline-flex;align-items:center;gap:3px;
  padding:2px 7px;border-radius:100px;
  font-size:.62rem;font-weight:600;white-space:nowrap;
}
.bddot{width:4px;height:4px;border-radius:50%;background:currentColor;animation:pulse 2s ease infinite}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.3}}

.bda{background:rgba(255,71,87,.1);color:#ff5f6d;border:1px solid rgba(255,71,87,.2)}
.bdm{background:rgba(255,190,11,.1);color:var(--amber);border:1px solid rgba(255,190,11,.2)}
.bdu{background:rgba(59,158,255,.1);color:var(--blue);border:1px solid rgba(59,158,255,.2)}
.bdprem{background:rgba(255,190,11,.1);color:var(--amber);border:1px solid rgba(255,190,11,.22)}
.bdfree{background:rgba(255,255,255,.04);color:var(--txt-3);border:1px solid var(--border)}
.bdon{background:rgba(6,214,160,.08);color:var(--teal);border:1px solid rgba(6,214,160,.2)}
.bdof{background:rgba(255,71,87,.08);color:#ff5f6d;border:1px solid rgba(255,71,87,.18)}
.bdvfy{background:rgba(59,158,255,.08);color:var(--blue);border:1px solid rgba(59,158,255,.2)}
.bduvfy{background:rgba(255,255,255,.03);color:var(--txt-3);border:1px solid var(--border)}

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
.ab-edit{color:var(--violet);border-color:rgba(139,92,246,.22);background:rgba(139,92,246,.07)}
.ab-edit:hover{background:rgba(139,92,246,.18);border-color:rgba(139,92,246,.4);transform:translateY(-1px)}
.ab-pause{color:var(--amber);border-color:rgba(255,190,11,.22);background:rgba(255,190,11,.07)}
.ab-pause:hover{background:rgba(255,190,11,.18);border-color:rgba(255,190,11,.4);transform:translateY(-1px)}
.ab-play{color:var(--teal);border-color:rgba(6,214,160,.22);background:rgba(6,214,160,.07)}
.ab-play:hover{background:rgba(6,214,160,.18);border-color:rgba(6,214,160,.4);transform:translateY(-1px)}
.ab-vfy{color:var(--rose);border-color:rgba(255,61,127,.22);background:rgba(255,61,127,.07)}
.ab-vfy:hover{background:rgba(255,61,127,.18);border-color:rgba(255,61,127,.4);transform:translateY(-1px)}
.ab-delete{color:var(--red);border-color:rgba(255,71,87,.22);background:rgba(255,71,87,.07)}
.ab-delete:hover{background:rgba(255,71,87,.18);border-color:rgba(255,71,87,.4);transform:translateY(-1px)}

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
.pag-wrap nav span[aria-current="page"]{background:var(--rose);color:#fff;border-color:var(--rose);font-weight:700}
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
  transition:opacity .22s ease,visibility .22s;padding:20px;
}
.modal-overlay.open{opacity:1;visibility:visible}
.modal-overlay.open .modal-box{transform:translateY(0) scale(1);opacity:1}

/* ───────────── DRAWER OVERLAY ───────────── */
.drawer-overlay{
  position:fixed;inset:0;
  background:rgba(5,7,12,.65);
  backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px);
  z-index:800;opacity:0;visibility:hidden;
  transition:opacity .22s ease,visibility .22s;
}
.drawer-overlay.open{opacity:1;visibility:visible}

/* ───────────── MODAL BOX ───────────── */
.modal-box{
  background:var(--surface);border:1px solid var(--border);
  border-radius:var(--r);width:100%;max-width:500px;max-height:90vh;
  display:flex;flex-direction:column;
  transform:translateY(16px) scale(.98);opacity:0;
  transition:transform .25s cubic-bezier(.34,1.56,.64,1),opacity .22s ease;
  box-shadow:0 24px 60px rgba(0,0,0,.5),0 0 0 1px rgba(255,255,255,.04);
}
.modal-head{
  display:flex;align-items:center;justify-content:space-between;
  padding:14px 18px;border-bottom:1px solid var(--border);flex-shrink:0;
}
.modal-tag{
  display:inline-flex;align-items:center;
  background:rgba(255,61,127,.1);color:var(--rose);
  border:1px solid rgba(255,61,127,.2);border-radius:4px;
  font-size:.6rem;font-weight:700;letter-spacing:.08em;
  text-transform:uppercase;padding:2px 7px;margin-right:10px;
}
.modal-tag.edit-tag{background:rgba(139,92,246,.1);color:var(--violet);border-color:rgba(139,92,246,.2)}
.modal-title{font-family:'Syne',sans-serif;font-size:.86rem;font-weight:700;color:var(--txt)}
.modal-close{
  width:28px;height:28px;border-radius:6px;display:grid;place-items:center;
  background:var(--surface2);border:1px solid var(--border);
  color:var(--txt-3);font-size:.8rem;transition:all .18s;cursor:pointer;
}
.modal-close:hover{background:rgba(255,255,255,.07);color:var(--txt)}
.modal-body{flex:1;overflow-y:auto;padding:18px}
.modal-foot{
  display:flex;gap:8px;padding:13px 18px;
  border-top:1px solid var(--border);flex-shrink:0;
}
.modal-foot .btn{flex:1;justify-content:center}

/* Form fields */
.f-group{margin-bottom:13px}
.f-grid{display:grid;grid-template-columns:1fr 1fr;gap:11px}
.f-lbl{
  display:block;font-size:.66rem;font-weight:700;color:var(--txt-3);
  letter-spacing:.07em;text-transform:uppercase;margin-bottom:6px;
}
.f-inp{
  display:block;width:100%;
  background:var(--surface2);border:1px solid var(--border);
  border-radius:7px;padding:8px 12px;color:var(--txt);
  font-size:.75rem;outline:none;transition:all .18s;
  -webkit-appearance:none;appearance:none;
  font-family:'Syne',sans-serif;
}
.f-inp:focus{border-color:rgba(255,61,127,.35);box-shadow:0 0 0 3px rgba(255,61,127,.07);background:var(--surface)}
.f-inp::placeholder{color:var(--txt-3)}
.f-sel{
  padding-right:30px;
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' fill='rgba(255,255,255,0.25)' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
  background-repeat:no-repeat;background-position:calc(100% - 10px) center;cursor:pointer;
}
.f-sel option{background:#0d0f14}
.f-textarea{resize:vertical;min-height:80px}

.check-group{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.check-item{
  display:flex;align-items:center;gap:8px;
  padding:9px 11px;background:var(--surface2);
  border:1px solid var(--border);border-radius:7px;
  cursor:pointer;transition:all .18s;
}
.check-item:hover{border-color:rgba(255,61,127,.25);background:rgba(255,61,127,.04)}
.check-item input[type="checkbox"]{width:14px;height:14px;accent-color:var(--rose);cursor:pointer;flex-shrink:0}
.check-item label{font-size:.71rem;color:var(--txt-2);cursor:pointer;font-weight:500}
.check-3{grid-template-columns:repeat(3,1fr)}

/* ───────────── SIDE DRAWER ───────────── */
.drawer{
  position:fixed;top:0;right:0;width:380px;height:100%;
  background:var(--surface2);border-left:1px solid var(--border);
  z-index:850;display:flex;flex-direction:column;
  transform:translateX(100%);
  transition:transform .28s cubic-bezier(.4,0,.2,1);
  box-shadow:-16px 0 40px rgba(0,0,0,.4);
}
.drawer.open{transform:translateX(0)}
.drawer-head{
  display:flex;align-items:center;justify-content:space-between;
  padding:14px 18px;border-bottom:1px solid var(--border);flex-shrink:0;
}
.drawer-tag{
  display:inline-flex;align-items:center;
  background:rgba(255,61,127,.1);color:var(--rose);
  border:1px solid rgba(255,61,127,.2);border-radius:4px;
  font-size:.6rem;font-weight:700;letter-spacing:.08em;
  text-transform:uppercase;padding:2px 7px;margin-right:10px;
}
.drawer-title{font-family:'Syne',sans-serif;font-size:.84rem;font-weight:700;color:var(--txt)}
.drawer-close{
  width:28px;height:28px;border-radius:6px;display:grid;place-items:center;
  background:var(--surface);border:1px solid var(--border);
  color:var(--txt-3);font-size:.8rem;transition:all .18s;cursor:pointer;
}
.drawer-close:hover{background:rgba(255,255,255,.07);color:var(--txt)}
.drawer-body{flex:1;overflow-y:auto;padding:18px}
.drawer-foot{
  display:flex;gap:6px;padding:13px 18px;
  border-top:1px solid var(--border);flex-shrink:0;flex-wrap:wrap;
}

/* Drawer sections */
.d-hero{
  display:flex;align-items:flex-start;gap:13px;
  margin-bottom:18px;padding-bottom:18px;border-bottom:1px solid var(--border);
}
.d-av{
  width:48px;height:48px;border-radius:12px;
  display:grid;place-items:center;
  font-family:'Syne',sans-serif;font-size:.72rem;font-weight:700;
  color:#fff;flex-shrink:0;
  box-shadow:0 4px 14px rgba(0,0,0,.4);
}
.d-hero-name{
  font-family:'Syne',sans-serif;font-size:.88rem;font-weight:700;
  color:var(--txt);word-break:break-all;line-height:1.3;
}
.d-hero-sub{font-size:.66rem;color:var(--txt-3);margin-top:3px;font-family:monospace}
.d-badges{display:flex;flex-wrap:wrap;gap:5px;margin-top:8px}

.ds{margin-bottom:16px}
.ds-title{
  font-size:.6rem;font-weight:700;letter-spacing:.1em;
  text-transform:uppercase;color:var(--txt-3);margin-bottom:9px;
  display:flex;align-items:center;gap:8px;
}
.ds-title::after{content:'';flex:1;height:1px;background:var(--border)}
.d-grid{display:grid;grid-template-columns:1fr 1fr;gap:7px}
.d-field{background:var(--surface);border:1px solid var(--border);border-radius:7px;padding:8px 11px}
.d-field.full{grid-column:1/-1}
.d-lbl{font-size:.6rem;font-weight:700;color:var(--txt-3);text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px}
.d-val{font-size:.74rem;color:var(--txt);font-weight:500;word-break:break-all}
.d-val code{font-family:monospace;font-size:.63rem;color:var(--rose)}

/* Drawer footer buttons */
.df-btn{
  display:inline-flex;align-items:center;justify-content:center;gap:6px;
  border-radius:7px;padding:7px 12px;font-size:.7rem;font-weight:600;
  border:1px solid;transition:all .18s;cursor:pointer;
  font-family:'Syne',sans-serif;
}
.df-ghost{color:var(--txt-2);border-color:var(--border);background:var(--surface)}
.df-ghost:hover{background:rgba(255,255,255,.06);color:var(--txt)}
.df-edit{color:var(--violet);border-color:rgba(139,92,246,.25);background:rgba(139,92,246,.07)}
.df-edit:hover{background:rgba(139,92,246,.15)}
.df-pause{color:var(--amber);border-color:rgba(255,190,11,.25);background:rgba(255,190,11,.07)}
.df-pause:hover{background:rgba(255,190,11,.15)}
.df-play{color:var(--teal);border-color:rgba(6,214,160,.25);background:rgba(6,214,160,.07)}
.df-play:hover{background:rgba(6,214,160,.15)}
.df-vfy{color:var(--rose);border-color:rgba(255,61,127,.25);background:rgba(255,61,127,.07)}
.df-vfy:hover{background:rgba(255,61,127,.15)}

/* ───────────── ANIMATIONS ───────────── */
@keyframes fu{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}

/* ───────────── RESPONSIVE ───────────── */
@media(max-width:1200px){.sg{grid-template-columns:repeat(2,1fr)}}
@media(max-width:900px){
  .drawer{width:100%}
  .dt thead th:nth-child(3),.dt td:nth-child(3){display:none}
}
@media(max-width:700px){
  .ph{flex-direction:column;align-items:flex-start;gap:10px}
  .sg{grid-template-columns:1fr 1fr}
  .dt thead th:nth-child(4),.dt td:nth-child(4){display:none}
  .modal-box{max-width:100%}
  .f-grid{grid-template-columns:1fr}
  .check-3{grid-template-columns:1fr 1fr}
}
@media(max-width:480px){
  .sg{grid-template-columns:1fr}
  .check-3,.check-group{grid-template-columns:1fr}
}
</style>
@endsection

@section('content')

{{-- ── PAGE HEADER ── --}}
<div class="ph">
    <div class="ph-left">
        <div class="ph-icon">👥</div>
        <div>
            <div class="ph-title">Users Management</div>
            <div class="ph-sub">Manage, filter &amp; moderate all registered members</div>
        </div>
    </div>
    <div class="ph-actions">
        <button onclick="openCreateModal()" class="btn btn-primary">
            ＋ Add User
        </button>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost">
            ← Dashboard
        </a>
    </div>
</div>

{{-- ── STAT STRIP ── --}}
<div class="sg">
    <div class="sc cr">
        <div class="scblob"></div>
        <div class="sct"><div class="scico">👥</div><span class="trnd up">Total</span></div>
        <div class="scv">{{ number_format($stats['total'] ?? 0) }}</div>
        <div class="scl">All Users</div>
        <div class="scbar"><div class="scbarf" style="--p:100%"></div></div>
    </div>
    <div class="sc cg">
        <div class="scblob"></div>
        <div class="sct"><div class="scico">✅</div><span class="trnd up">+12%</span></div>
        <div class="scv">{{ number_format($stats['active'] ?? 0) }}</div>
        <div class="scl">Active Users</div>
        <div class="scbar"><div class="scbarf" style="--p:72%"></div></div>
    </div>
    <div class="sc ca">
        <div class="scblob"></div>
        <div class="sct"><div class="scico">⭐</div><span class="trnd up">+8%</span></div>
        <div class="scv">{{ number_format($stats['premium'] ?? 0) }}</div>
        <div class="scl">Premium</div>
        <div class="scbar"><div class="scbarf" style="--p:45%"></div></div>
    </div>
    <div class="sc cb">
        <div class="scblob"></div>
        <div class="sct"><div class="scico">🛡️</div><span class="trnd up">+10%</span></div>
        <div class="scv">{{ number_format($stats['verified'] ?? 0) }}</div>
        <div class="scl">Verified</div>
        <div class="scbar"><div class="scbarf" style="--p:60%"></div></div>
    </div>
</div>

{{-- ── FILTER BAR ── --}}
<div class="filter-bar">
    <form method="GET" action="{{ route('admin.users.index') }}" class="filter-form">
        <input type="text" name="search" class="fi fi-search"
            placeholder="Search by email or phone…"
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
            <button type="submit" class="btn btn-primary" style="padding:7px 14px">
                ⚡ Filter
            </button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost" style="padding:7px 12px">
                ✕ Clear
            </a>
        </div>
    </form>
</div>

{{-- ── RESULTS META ── --}}
<div class="results-meta">
    <span class="results-count">
        Showing <strong>{{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }}</strong>
        of <strong>{{ $users->total() ?? 0 }}</strong> users
    </span>
</div>

{{-- ── USER TABLE ── --}}
<div class="panel">
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
                        $palettes = [
                            ['#ff3d7f','#8b5cf6'],
                            ['#06d6a0','#3b9eff'],
                            ['#3b9eff','#ec4899'],
                            ['#ffbe0b','#06d6a0'],
                            ['#8b5cf6','#06d6a0'],
                            ['#ec4899','#ffbe0b'],
                            ['#14b8a6','#8b5cf6'],
                        ];
                        $c        = $palettes[$user->id % 7];
                        $initials = strtoupper(substr($user->email ?? $user->phone ?? '?', 0, 2));
                    @endphp
                    <tr>
                        <td class="td-mono">{{ $user->id }}</td>
                        <td>
                            <div class="u-cell">
                                <div class="u-av" style="background:linear-gradient(135deg,{{ $c[0] }},{{ $c[1] }})">{{ $initials }}</div>
                                <div>
                                    <div class="u-name">{{ $user->email ?? '—' }}</div>
                                    <div class="u-id">#{{ $user->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="td-mono">{{ substr($user->uuid ?? '', 0, 8) }}…</td>
                        <td class="td-muted">{{ $user->phone ?? '—' }}</td>
                        <td>
                            @if($user->role === 'admin')
                                <span class="bd bda">🛡 Admin</span>
                            @elseif($user->role === 'moderator')
                                <span class="bd bdm">👁 Mod</span>
                            @else
                                <span class="bd bdu">👤 User</span>
                            @endif
                        </td>
                        <td>
                            @if($user->is_premium)
                                <span class="bd bdprem">⭐ Premium</span>
                            @else
                                <span class="bd bdfree">Free</span>
                            @endif
                        </td>
                        <td>
                            @if($user->is_verified)
                                <span class="bd bdvfy">✔ Yes</span>
                            @else
                                <span class="bd bduvfy">No</span>
                            @endif
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="bd bdon"><span class="bddot"></span>Active</span>
                            @else
                                <span class="bd bdof">Inactive</span>
                            @endif
                        </td>
                        <td class="td-muted">{{ $user->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="act-btns">
                                <button class="ab ab-view" onclick="openDrawer({{ $user->id }})" title="View profile">👁</button>
                                <button class="ab ab-edit" onclick="openEditModal({{ $user->id }})" title="Edit user">✏️</button>
                                <form method="POST" action="{{ route('admin.users.toggle-active', $user->id) }}" style="display:contents">
                                    @csrf
                                    <button type="submit" class="ab {{ $user->is_active ? 'ab-pause' : 'ab-play' }}"
                                        title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                                        {{ $user->is_active ? '⏸' : '▶' }}
                                    </button>
                                </form>
                                @unless($user->is_verified)
                                    <form method="POST" action="{{ route('admin.users.verify', $user->id) }}" style="display:contents">
                                        @csrf
                                        <button type="submit" class="ab ab-vfy" title="Verify user">🛡</button>
                                    </form>
                                @endunless
                                <form method="POST" action="{{ route('admin.users.web-destroy', $user->id) }}" style="display:contents" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ab ab-delete" title="Delete user">🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10">
                            <div class="empty-state">
                                <div class="empty-ico">🔍</div>
                                <div class="empty-title">No users found</div>
                                <div class="empty-txt">Try adjusting your filters or search terms</div>
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

{{-- ══════════════════════════════════════════════
     CREATE USER MODAL
══════════════════════════════════════════════════ --}}
<div id="createUserModal" class="modal-overlay" onclick="handleOverlayClick(event,'createUserModal')">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div class="modal-head">
            <div style="display:flex;align-items:center">
                <span class="modal-tag">New</span>
                <span class="modal-title">Create User</span>
            </div>
            <button class="modal-close" onclick="closeModal('createUserModal')">✕</button>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}" style="display:contents">
            @csrf
            <div class="modal-body">

                <div class="f-grid f-group">
                    <div>
                        <label class="f-lbl">First Name *</label>
                        <input type="text" name="first_name" class="f-inp" required placeholder="John">
                    </div>
                    <div>
                        <label class="f-lbl">Last Name</label>
                        <input type="text" name="last_name" class="f-inp" placeholder="Doe">
                    </div>
                </div>

                <div class="f-group">
                    <label class="f-lbl">Email *</label>
                    <input type="email" name="email" class="f-inp" required placeholder="user@example.com">
                </div>

                <div class="f-group">
                    <label class="f-lbl">Phone Number</label>
                    <input type="text" name="phone" class="f-inp" placeholder="+1234567890">
                </div>

                <div class="f-group">
                    <label class="f-lbl">Password *</label>
                    <input type="password" name="password" class="f-inp" required minlength="6" placeholder="Min. 6 characters">
                </div>

                <div class="f-grid f-group">
                    <div>
                        <label class="f-lbl">Gender *</label>
                        <select name="gender" class="f-inp f-sel" required>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="f-lbl">Date of Birth *</label>
                        <input type="date" name="date_of_birth" class="f-inp" required
                            max="{{ date('Y-m-d', strtotime('-18 years')) }}">
                    </div>
                </div>

                <div class="f-group">
                    <label class="f-lbl">Role *</label>
                    <select name="role" class="f-inp f-sel" required>
                        <option value="user">User</option>
                        <option value="moderator">Moderator</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="f-group">
                    <label class="f-lbl" style="margin-bottom:8px">Flags</label>
                    <div class="check-group">
                        <label class="check-item">
                            <input type="checkbox" name="is_verified" value="1">
                            <label>Verified Account</label>
                        </label>
                        <label class="check-item">
                            <input type="checkbox" name="is_premium" value="1">
                            <label>Premium Plan</label>
                        </label>
                    </div>
                </div>

                <div class="f-group">
                    <label class="f-lbl">Bio</label>
                    <textarea name="bio" class="f-inp f-textarea" placeholder="Short bio…" maxlength="500"></textarea>
                </div>

            </div>
            <div class="modal-foot">
                <button type="button" class="btn btn-ghost" onclick="closeModal('createUserModal')">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    ✓ Create User
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     EDIT USER MODAL
══════════════════════════════════════════════════ --}}
<div id="editUserModal" class="modal-overlay" onclick="handleOverlayClick(event,'editUserModal')">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div class="modal-head">
            <div style="display:flex;align-items:center">
                <span class="modal-tag edit-tag">Edit</span>
                <span class="modal-title">Edit User</span>
            </div>
            <button class="modal-close" onclick="closeModal('editUserModal')">✕</button>
        </div>

        <form method="POST" id="editUserForm" style="display:contents">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <input type="hidden" name="user_id" id="edit_user_id">

                <div class="f-grid f-group">
                    <div>
                        <label class="f-lbl">First Name *</label>
                        <input type="text" name="first_name" id="edit_first_name" class="f-inp" required placeholder="John">
                    </div>
                    <div>
                        <label class="f-lbl">Last Name</label>
                        <input type="text" name="last_name" id="edit_last_name" class="f-inp" placeholder="Doe">
                    </div>
                </div>

                <div class="f-group">
                    <label class="f-lbl">Email *</label>
                    <input type="email" name="email" id="edit_email" class="f-inp" required placeholder="user@example.com">
                </div>

                <div class="f-group">
                    <label class="f-lbl">Phone Number</label>
                    <input type="text" name="phone" id="edit_phone" class="f-inp" placeholder="+1234567890">
                </div>

                <div class="f-grid f-group">
                    <div>
                        <label class="f-lbl">Gender *</label>
                        <select name="gender" id="edit_gender" class="f-inp f-sel" required>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="f-lbl">Date of Birth *</label>
                        <input type="date" name="date_of_birth" id="edit_date_of_birth" class="f-inp" required
                            max="{{ date('Y-m-d', strtotime('-18 years')) }}">
                    </div>
                </div>

                <div class="f-group">
                    <label class="f-lbl">Role *</label>
                    <select name="role" id="edit_role" class="f-inp f-sel" required>
                        <option value="user">User</option>
                        <option value="moderator">Moderator</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="f-group">
                    <label class="f-lbl" style="margin-bottom:8px">Flags</label>
                    <div class="check-group check-3">
                        <label class="check-item">
                            <input type="checkbox" name="is_verified" id="edit_is_verified" value="1">
                            <label>Verified</label>
                        </label>
                        <label class="check-item">
                            <input type="checkbox" name="is_premium" id="edit_is_premium" value="1">
                            <label>Premium</label>
                        </label>
                        <label class="check-item">
                            <input type="checkbox" name="is_active" id="edit_is_active" value="1">
                            <label>Active</label>
                        </label>
                    </div>
                </div>

                <div class="f-group">
                    <label class="f-lbl">Bio</label>
                    <textarea name="bio" id="edit_bio" class="f-inp f-textarea" placeholder="Short bio…" maxlength="500"></textarea>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn btn-ghost" onclick="closeModal('editUserModal')">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    ✓ Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     DRAWER OVERLAY
══════════════════════════════════════════════════ --}}
<div class="drawer-overlay" id="drawerOverlay" onclick="closeDrawer()"></div>

{{-- ══════════════════════════════════════════════
     PROFILE DRAWERS — one per user on this page
══════════════════════════════════════════════════ --}}
@foreach($users as $user)
    @php
        $palettes = [
            ['#ff3d7f','#8b5cf6'],
            ['#06d6a0','#3b9eff'],
            ['#3b9eff','#ec4899'],
            ['#ffbe0b','#06d6a0'],
            ['#8b5cf6','#06d6a0'],
            ['#ec4899','#ffbe0b'],
            ['#14b8a6','#8b5cf6'],
        ];
        $c        = $palettes[$user->id % 7];
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
            <button class="drawer-close" onclick="closeDrawer()">✕</button>
        </div>

        <div class="drawer-body">

            {{-- Hero --}}
            <div class="d-hero">
                <div class="d-av" style="background:linear-gradient(135deg,{{ $c[0] }},{{ $c[1] }})">{{ $initials }}</div>
                <div style="flex:1;min-width:0">
                    <div class="d-hero-name">{{ $user->email ?? $user->phone ?? 'Unknown' }}</div>
                    <div class="d-hero-sub">ID #{{ $user->id }} · {{ $user->created_at->format('M d, Y') }}</div>
                    <div class="d-badges">
                        @if($user->role === 'admin')
                            <span class="bd bda">🛡 Admin</span>
                        @elseif($user->role === 'moderator')
                            <span class="bd bdm">👁 Mod</span>
                        @else
                            <span class="bd bdu">👤 User</span>
                        @endif
                        @if($user->is_active)
                            <span class="bd bdon"><span class="bddot"></span>Active</span>
                        @else
                            <span class="bd bdof">Inactive</span>
                        @endif
                        @if($user->is_premium)
                            <span class="bd bdprem">⭐ Premium</span>
                        @endif
                        @if($user->is_verified)
                            <span class="bd bdvfy">✔ Verified</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Identity --}}
            <div class="ds">
                <div class="ds-title">Identity</div>
                <div class="d-grid">
                    <div class="d-field">
                        <div class="d-lbl">User ID</div>
                        <div class="d-val">#{{ $user->id }}</div>
                    </div>
                    <div class="d-field">
                        <div class="d-lbl">Role</div>
                        <div class="d-val">{{ ucfirst($user->role) }}</div>
                    </div>
                    <div class="d-field full">
                        <div class="d-lbl">UUID</div>
                        <div class="d-val"><code>{{ $user->uuid }}</code></div>
                    </div>
                </div>
            </div>

            {{-- Contact --}}
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

            {{-- Account --}}
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
            <button class="df-btn df-ghost" onclick="closeDrawer()" style="flex:1">
                ✕ Close
            </button>
            <button type="button" class="df-btn df-edit" onclick="closeDrawer(); setTimeout(() => openEditModal({{ $user->id }}), 180)">
                ✏️ Edit
            </button>
            <form method="POST" action="{{ route('admin.users.toggle-active', $user->id) }}" style="display:contents">
                @csrf
                <button type="submit" class="df-btn {{ $user->is_active ? 'df-pause' : 'df-play' }}">
                    {{ $user->is_active ? '⏸ Deactivate' : '▶ Activate' }}
                </button>
            </form>
            @unless($user->is_verified)
                <form method="POST" action="{{ route('admin.users.verify', $user->id) }}" style="display:contents">
                    @csrf
                    <button type="submit" class="df-btn df-vfy">
                        🛡 Verify
                    </button>
                </form>
            @endunless
            <form method="POST" action="{{ route('admin.users.web-destroy', $user->id) }}" style="display:contents" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="df-btn df-ghost" style="color:var(--red);border-color:rgba(255,71,87,.22);background:rgba(255,71,87,.07)">
                    🗑️ Delete
                </button>
            </form>
        </div>

    </div>
@endforeach

@endsection

@section('scripts')
<script>
/* ── MODAL HELPERS ──────────────────────────────────────────── */
function openModal(id) {
    var el = document.getElementById(id);
    if (el) { el.classList.add('open'); document.body.style.overflow = 'hidden'; }
}
function closeModal(id) {
    var el = document.getElementById(id);
    if (el) { el.classList.remove('open'); document.body.style.overflow = ''; }
}
function handleOverlayClick(e, id) {
    if (e.target === e.currentTarget) closeModal(id);
}

/* ── CREATE MODAL ───────────────────────────────────────────── */
function openCreateModal() { openModal('createUserModal'); }
function closeCreateModal() { closeModal('createUserModal'); }

/* ── DRAWER ─────────────────────────────────────────────────── */
var activeDrawer = null;

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
    if (activeDrawer) { activeDrawer.classList.remove('open'); activeDrawer = null; }
    if (restoreScroll) document.body.style.overflow = '';
}

/* ── EDIT MODAL ─────────────────────────────────────────────── */
function openEditModal(userId) {
    var modal     = document.getElementById('editUserModal');
    var submitBtn = modal.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '⏳ Loading…';

    fetch('/admin/users/' + userId + '/edit-data')
        .then(function(r) { if (!r.ok) throw new Error('Network error'); return r.json(); })
        .then(function(data) {
            var user    = data.user;
            var profile = user.profile || {};

            document.getElementById('edit_user_id').value       = user.id;
            document.getElementById('edit_first_name').value    = profile.first_name  || '';
            document.getElementById('edit_last_name').value     = profile.last_name   || '';
            document.getElementById('edit_email').value         = user.email          || '';
            document.getElementById('edit_phone').value         = user.phone          || '';
            document.getElementById('edit_gender').value        = profile.gender      || 'male';
            
            // Format date for input[type=date] (expects YYYY-MM-DD)
            var dob = profile.date_of_birth || '';
            if (dob) {
                // Handle both ISO format and date string
                dob = dob.split('T')[0]; // Remove time part if present
            }
            document.getElementById('edit_date_of_birth').value = dob;
            
            document.getElementById('edit_role').value          = user.role           || 'user';
            document.getElementById('edit_bio').value           = profile.bio         || '';
            document.getElementById('edit_is_verified').checked = !!user.is_verified;
            document.getElementById('edit_is_premium').checked  = !!user.is_premium;
            document.getElementById('edit_is_active').checked   = !!user.is_active;

            document.getElementById('editUserForm').action = '/admin/users/' + user.id;
            submitBtn.disabled = false;
            submitBtn.innerHTML = '✓ Save Changes';
            openModal('editUserModal');
        })
        .catch(function() {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '✓ Save Changes';
            alert('Error loading user data. Please try again.');
        });
}

function closeEditModal(e) {
    if (e && e.target !== e.currentTarget) return;
    closeModal('editUserModal');
}

document.getElementById('editUserForm').addEventListener('submit', function() {
    var submitBtn = document.querySelector('#editUserModal button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '⏳ Saving…';
    setTimeout(function() {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '✓ Save Changes';
    }, 3000);
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDrawer();
        closeModal('createUserModal');
        closeModal('editUserModal');
    }
});
</script>
@endsection