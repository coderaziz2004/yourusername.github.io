<?php
// test.php - Mission planning prototype demo (self-contained)
declare(strict_types=1);
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Agriculture Mission Planner — Prototype</title>
  <style>
    :root{
      --bg0:#05070b;
      --bg1:#0b1220;
      --panel:#0b1020;
      --panel2:#070a12;
      --muted:#94a3b8;
      --text:#e5e7eb;
      --line:rgba(255,255,255,.10);
      --line2:rgba(255,255,255,.06);
      --accent:#7c3aed;
      --ok:#22c55e;
      --warn:#f59e0b;
      --bad:#ef4444;
      --shadow: 0 18px 60px rgba(0,0,0,.45);
      --mono: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
      --sans: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji","Segoe UI Emoji";
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      font-family:var(--sans);
      color:var(--text);
      background:
        radial-gradient(1200px 800px at 18% 18%, rgba(124,58,237,.18), transparent 55%),
        radial-gradient(900px 700px at 80% 75%, rgba(34,197,94,.10), transparent 60%),
        linear-gradient(180deg, var(--bg0), var(--bg1) 55%, var(--bg0));
      overflow:hidden;
    }
    .app{
      height:100%;
      display:grid;
      grid-template-columns: 420px 1fr;
      gap:16px;
      padding:18px;
    }
    .panel{
      background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.03));
      border:1px solid var(--line);
      border-radius:16px;
      box-shadow: var(--shadow);
      overflow:hidden;
      display:flex;
      flex-direction:column;
      min-width: 340px;
    }
    .panel header{
      padding:16px 16px 14px 16px;
      border-bottom:1px solid var(--line);
      background: linear-gradient(180deg, rgba(0,0,0,.35), rgba(0,0,0,.10));
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
    }
    .brand{
      display:flex;
      align-items:center;
      gap:10px;
      min-width:0;
    }
    .logo{
      width:34px;height:34px;border-radius:10px;
      background: rgba(255,255,255,.06);
      border:1px solid var(--line);
      display:flex;align-items:center;justify-content:center;
      font-family:var(--mono);
      font-size:13px;
      color:#cbd5e1;
    }
    .brand h1{
      margin:0;
      font-size:13px;
      letter-spacing:.2px;
      font-weight:600;
      white-space:nowrap;
      overflow:hidden;
      text-overflow:ellipsis;
    }
    .brand .sub{
      margin-top:2px;
      font-size:11px;
      color:var(--muted);
      white-space:nowrap;
      overflow:hidden;
      text-overflow:ellipsis;
    }
    .pill{
      font-size:11px;
      color:#cbd5e1;
      border:1px solid var(--line);
      background: rgba(255,255,255,.05);
      padding:6px 10px;
      border-radius:999px;
      font-family:var(--mono);
      white-space:nowrap;
    }
    .content{
      padding:14px 16px 16px 16px;
      display:flex;
      flex-direction:column;
      gap:12px;
      min-height:0;
    }
    label{
      display:block;
      font-size:12px;
      color:#cbd5e1;
      margin-bottom:8px;
      letter-spacing:.2px;
    }
    .textarea{
      width:100%;
      resize:none;
      min-height:140px;
      border-radius:14px;
      border:1px solid var(--line);
      background: rgba(0,0,0,.35);
      color:var(--text);
      padding:12px 12px;
      outline:none;
      box-shadow: inset 0 0 0 1px rgba(255,255,255,.02);
      font-size:13px;
      line-height:1.45;
    }
    .textarea:focus{
      border-color: rgba(124,58,237,.55);
      box-shadow: 0 0 0 4px rgba(124,58,237,.14);
    }
    .actions{
      display:flex;
      gap:10px;
      align-items:center;
      justify-content:space-between;
      margin-top:10px;
    }
    .btn{
      border:1px solid var(--line);
      background: rgba(255,255,255,.06);
      color:var(--text);
      padding:10px 12px;
      border-radius:12px;
      font-size:13px;
      font-weight:600;
      letter-spacing:.2px;
      cursor:pointer;
      transition: transform .06s ease, background .12s ease, border-color .12s ease;
      user-select:none;
    }
    .btn:hover{ background: rgba(255,255,255,.09); }
    .btn:active{ transform: translateY(1px); }
    .btn.primary{
      border-color: rgba(124,58,237,.55);
      background: linear-gradient(180deg, rgba(124,58,237,.90), rgba(124,58,237,.70));
      box-shadow: 0 10px 24px rgba(124,58,237,.16);
    }
    .btn.primary:hover{
      background: linear-gradient(180deg, rgba(124,58,237,.98), rgba(124,58,237,.78));
    }
    .hint{
      font-size:11px;
      color:var(--muted);
      line-height:1.4;
    }
    .fleet{
      margin-top:10px;
      border:1px solid var(--line);
      background: rgba(0,0,0,.22);
      border-radius:14px;
      padding:10px 12px;
    }
    .fleetHead{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:10px;
      margin-bottom:6px;
    }
    .fleet .t{
      font-size:12px;
      font-weight:600;
      letter-spacing:.2px;
      color:#e2e8f0;
      margin:0;
    }
    .fleetToggle{
      pointer-events:auto;
      border:1px solid rgba(255,255,255,.10);
      background: rgba(255,255,255,.05);
      color:#cbd5e1;
      width:30px;height:30px;
      border-radius:10px;
      display:flex;
      align-items:center;
      justify-content:center;
      cursor:pointer;
      transition: transform .12s ease, background .12s ease, border-color .12s ease;
    }
    .fleetToggle:hover{ background: rgba(255,255,255,.08); }
    .fleetToggle:active{ transform: translateY(1px); }
    .fleetToggle svg{ display:block; }
    .fleet.collapsed .fleetToggle svg{ transform: rotate(-90deg); }
    .fleetToggle svg{ transition: transform .12s ease; }
    .fleetBody{ }
    .fleet.collapsed .fleetBody{ display:none; }
    .fleet .small{
      font-size:11px;
      color:var(--muted);
      line-height:1.35;
      margin-bottom:8px;
    }
    .fleetList{
      display:flex;
      flex-direction:column;
      gap:6px;
      margin:0;
      padding:0;
      list-style:none;
      font-family:var(--mono);
      font-size:11px;
      color:#cbd5e1;
    }
    .fleetItem{
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap:10px;
      padding:7px 9px;
      border-radius:12px;
      border:1px solid rgba(255,255,255,.08);
      background: rgba(255,255,255,.03);
    }
    .fleetItem .cap{ color:#e2e8f0; font-weight:600; }
    .fleetItem .desc{ color:var(--muted); margin-top:2px; line-height:1.25; }
    .fleetItem .state{
      white-space:nowrap;
      font-size:10px;
      padding:3px 8px;
      border-radius:999px;
      border:1px solid rgba(255,255,255,.10);
      background: rgba(0,0,0,.18);
      color:#cbd5e1;
      margin-top:1px;
    }
    .fleetItem.disabled{
      opacity:.72;
    }
    .fleetItem.disabled .state{
      border-color: rgba(59,130,246,.25);
      color:#bfdbfe;
      background: rgba(59,130,246,.08);
    }
    .fleetActions{
      margin-top:10px;
      display:flex;
      justify-content:flex-end;
    }
    .btn.small{
      padding:8px 10px;
      border-radius:12px;
      font-size:12px;
      font-weight:600;
    }
    .breakdown{
      margin-top:4px;
      border-radius:14px;
      border:1px solid var(--line);
      background: rgba(0,0,0,.28);
      overflow:hidden;
      min-height:0;
      display:flex;
      flex-direction:column;
    }
    .breakdownActions{
      padding:10px 12px;
      border-top:1px solid var(--line2);
      display:flex;
      justify-content:flex-end;
      gap:10px;
    }
    .btn.danger{
      border-color: rgba(239,68,68,.45);
      background: rgba(239,68,68,.12);
    }
    .btn.danger:hover{
      background: rgba(239,68,68,.18);
    }
    .breakdown .hd{
      padding:10px 12px;
      border-bottom:1px solid var(--line2);
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:10px;
    }
    .breakdown .hd .title{
      font-size:12px;
      font-weight:600;
      letter-spacing:.2px;
      color:#dbeafe;
    }
    .breakdown .hd .meta{
      font-size:11px;
      color:var(--muted);
      font-family:var(--mono);
      white-space:nowrap;
    }
    .steps{
      padding:10px 12px 12px 12px;
      display:flex;
      flex-direction:column;
      gap:8px;
      overflow:auto;
    }
    .step{
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap:10px;
      padding:8px 10px;
      border-radius:12px;
      border:1px solid rgba(255,255,255,.08);
      background: rgba(255,255,255,.04);
    }
    .step .left{
      min-width:0;
    }
    .step .name{
      font-size:12px;
      font-weight:600;
      color:#e2e8f0;
      letter-spacing:.2px;
    }
    .step .desc{
      font-size:11px;
      color:var(--muted);
      margin-top:2px;
      line-height:1.35;
    }
    .step .state{
      font-size:11px;
      font-family:var(--mono);
      padding:4px 8px;
      border-radius:999px;
      border:1px solid rgba(255,255,255,.10);
      color:#cbd5e1;
      background: rgba(0,0,0,.25);
      white-space:nowrap;
    }
    .state.ok{ border-color: rgba(34,197,94,.35); color:#bbf7d0; background: rgba(34,197,94,.08); }
    .state.run{ border-color: rgba(245,158,11,.35); color:#fde68a; background: rgba(245,158,11,.08); }
    .state.wait{ opacity:.85; }
    .mapWrap{
      background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.03));
      border:1px solid var(--line);
      border-radius:16px;
      box-shadow: var(--shadow);
      overflow:hidden;
      position:relative;
      min-width: 520px;
    }
    .mapTopBar{
      position:absolute;
      top:0; left:0; right:0;
      padding:12px 14px;
      background: linear-gradient(180deg, rgba(0,0,0,.55), rgba(0,0,0,.10));
      border-bottom:1px solid var(--line);
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:10px;
      z-index:3;
      pointer-events:none;
    }
    .mapTopBar .title{
      font-size:12px;
      font-weight:600;
      letter-spacing:.2px;
      color:#e2e8f0;
      pointer-events:none;
    }
    .mapTopBar .right{
      display:flex;
      gap:10px;
      align-items:center;
      pointer-events:none;
    }
    .kv{
      display:flex;
      gap:8px;
      align-items:center;
      font-size:11px;
      color:var(--muted);
      font-family:var(--mono);
      white-space:nowrap;
    }
    .dot{
      width:7px;height:7px;border-radius:999px;
      background: rgba(148,163,184,.45);
      box-shadow: 0 0 0 4px rgba(148,163,184,.10);
    }
    .dot.live{
      background: rgba(34,197,94,.9);
      box-shadow: 0 0 0 4px rgba(34,197,94,.16);
    }
    svg{display:block; width:100%; height:100%;}
    .mapSvg{
      position:absolute;
      inset:0;
    }
    .mapPad{
      position:absolute;
      inset:54px 0 0 0;
    }
    .legend{
      position:absolute;
      right:14px;
      bottom:14px;
      z-index:4;
      display:flex;
      flex-direction:column;
      gap:8px;
      pointer-events:none;
    }
    .legendCard{
      border:1px solid var(--line);
      background: rgba(0,0,0,.35);
      backdrop-filter: blur(10px);
      border-radius:14px;
      padding:10px 12px;
      width: 260px;
    }
    .legendCard .t{
      font-size:12px;
      font-weight:600;
      letter-spacing:.2px;
      color:#e2e8f0;
      margin-bottom:6px;
    }
    .legendRow{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:10px;
      font-size:11px;
      color:var(--muted);
      font-family:var(--mono);
    }
    .legendRow strong{ color:#cbd5e1; font-weight:600; }
    .srOnly{
      position:absolute !important;
      width:1px;height:1px;
      padding:0;margin:-1px;
      overflow:hidden;
      clip:rect(0,0,0,0);
      border:0;
    }
    @media (max-width: 1040px){
      body{ overflow:auto; }
      .app{ height:auto; min-height:100%; grid-template-columns: 1fr; }
      .mapWrap{ min-height: 70vh; min-width:unset; }
    }

    /* SVG segmentation + effects (calm, technical) */
    #fields{
      opacity:0;
      transition: opacity .7s ease;
    }
    .segRect{
      fill: rgba(0,0,0,.10);
      stroke: rgba(226,232,240,.22);
      stroke-width: 1.3;
      stroke-dasharray: 10 8;
      stroke-dashoffset: 0;
      vector-effect: non-scaling-stroke;
      animation: dashMarch 2.4s linear infinite;
    }
    .segRectStrong{
      stroke: rgba(226,232,240,.30);
      stroke-width: 1.8;
      stroke-dasharray: 0;
      animation: none;
    }
    @keyframes dashMarch{ to { stroke-dashoffset: -72; } }

    .basePulse{
      transform-origin: center;
      animation: basePulse 1.8s ease-in-out infinite;
    }
    @keyframes basePulse{
      0%,100%{ opacity:.22; r: 18; }
      50%{ opacity:.42; r: 26; }
    }

    .fxSpray .sprayCone{
      animation: sprayFlicker .95s ease-in-out infinite;
    }
    @keyframes sprayFlicker{
      0%,100%{ opacity:.10; }
      45%{ opacity:.18; }
      65%{ opacity:.12; }
    }

    .fxPhoto .flashRing{
      transform-origin: center;
      animation: cameraFlash 2.0s ease-in-out infinite;
      opacity: 0;
    }
    #drone2.photoActive .flashRing{
      animation: cameraFlash .85s ease-in-out infinite;
    }
    @keyframes cameraFlash{
      0%{ opacity:0; r: 6; }
      8%{ opacity:.35; r: 26; }
      20%{ opacity:0; r: 38; }
      100%{ opacity:0; r: 38; }
    }

    .fxInspect .scanRing{
      transform-origin: center;
      animation: scanPulse 1.4s ease-in-out infinite;
      opacity: 0;
    }
    @keyframes scanPulse{
      0%{ opacity:0; r: 10; }
      18%{ opacity:.28; r: 24; }
      60%{ opacity:0; r: 46; }
      100%{ opacity:0; r: 46; }
    }
  </style>
</head>
<body>
  <div class="app">
    <aside class="panel" aria-label="Mission control panel">
      <header>
        <div class="brand">
          <div class="logo">UCM</div>
          <div class="minw">
            <h1>Mission Planner</h1>
            <div class="sub">Agriculture • Autonomous drones • Prototype</div>
          </div>
        </div>
        <div class="pill" id="sessionId">session: local</div>
      </header>

      <div class="content">
        <div>
          <label for="intent">Mission Intent</label>
          <textarea id="intent" class="textarea" spellcheck="false">Scan the agricultural area, monitor crop health, and assign drones to inspect each field.</textarea>
          <div class="actions">
            <div class="hint">
              Enter a high-level goal. The system will parse objectives, assign roles, generate waypoints, then execute.
            </div>
            <div style="display:flex; gap:10px;">
              <button class="btn" id="resetBtn" type="button">Reset</button>
              <button class="btn primary" id="submitBtn" type="button">Submit mission</button>
            </div>
          </div>
        </div>

        <section class="breakdown" aria-label="Mission breakdown">
          <div class="hd">
            <div class="title">Mission Breakdown</div>
            <div class="meta" id="breakdownMeta">idle</div>
          </div>
          <div class="steps" id="steps">
            <!-- populated by JS -->
          </div>
          <div class="breakdownActions">
            <button class="btn small danger" id="crashBtn" type="button" disabled>Simulate Random crash</button>
          </div>
        </section>

        <section class="fleet collapsed" id="fleet" aria-label="Available drones">
          <div class="fleetHead">
            <div class="t">Available drones</div>
            <button class="fleetToggle" id="fleetToggle" type="button" aria-expanded="false" aria-controls="fleetBody" title="Show/hide drones">
              <svg width="14" height="14" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M5 7.5 L10 12.5 L15 7.5" stroke="rgba(226,232,240,.85)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </button>
          </div>
          <div class="fleetBody" id="fleetBody">
            <div class="small">Capabilities summary (4 drones enabled by default).</div>
            <ul class="fleetList" id="fleetList"></ul>
            <div class="fleetActions">
              <button class="btn small" id="addDroneBtn" type="button" disabled>All drones enabled</button>
            </div>
          </div>
        </section>
      </div>
    </aside>

    <main class="mapWrap" aria-label="Satellite map">
      <div class="mapTopBar">
        <div class="title">Satellite view • Region AO-17</div>
        <div class="right">
          <div class="kv"><span class="dot" id="liveDot"></span><span id="liveText"></span></div>
          <div class="kv">camera: top-down</div>
          <div class="kv">zoom: 1.0×</div>
        </div>
      </div>

      <div class="mapPad">
        <svg class="mapSvg" id="map" viewBox="0 0 1200 800" role="img" aria-label="Agricultural region with segmented fields and drones">
          <defs>
            <!-- subtle satellite noise -->
            <filter id="noise" x="-20%" y="-20%" width="140%" height="140%">
              <feTurbulence type="fractalNoise" baseFrequency="0.9" numOctaves="2" stitchTiles="stitch" result="n"/>
              <feColorMatrix type="matrix" values="
                0.3 0 0 0 0
                0 0.3 0 0 0
                0 0 0.3 0 0
                0 0 0 0.09 0" />
              <feBlend mode="overlay" in2="SourceGraphic"/>
            </filter>
            <!-- land.jpg will be loaded from this folder (./land.jpg) -->

            <!-- Drone glow -->
            <filter id="glow" x="-80%" y="-80%" width="260%" height="260%">
              <feGaussianBlur stdDeviation="2.4" result="b"/>
              <feColorMatrix type="matrix" values="
                1 0 0 0 0
                0 1 0 0 0
                0 0 1 0 0
                0 0 0 .55 0" />
              <feMerge>
                <feMergeNode in="b"/>
                <feMergeNode in="SourceGraphic"/>
              </feMerge>
            </filter>
          </defs>

          <!-- Background (satellite image) -->
          <rect x="0" y="0" width="1200" height="800" fill="#07140e" />
          <!-- preserveAspectRatio=none keeps segmentation aligned to image coordinates -->
          <image href="./land.jpg" x="0" y="0" width="1200" height="800" preserveAspectRatio="none" opacity="0.92" />
          <rect x="0" y="0" width="1200" height="800" fill="rgba(0,0,0,.22)" />

          <!-- Roads / irrigation (minimal, technical) -->
          <path d="M70 520 C220 480, 360 470, 520 520 C690 575, 860 592, 1080 545"
                fill="none" stroke="rgba(255,255,255,.08)" stroke-width="10" stroke-linecap="round" />
          <path d="M70 520 C220 480, 360 470, 520 520 C690 575, 860 592, 1080 545"
                fill="none" stroke="rgba(0,0,0,.28)" stroke-width="6" stroke-linecap="round" />
          <path d="M210 80 L210 700" stroke="rgba(255,255,255,.05)" stroke-width="6" stroke-linecap="round"/>
          <path d="M210 80 L210 700" stroke="rgba(0,0,0,.22)" stroke-width="3" stroke-linecap="round"/>

          <!-- BASE (shared launch point) -->
          <g id="baseMarker" transform="translate(160,730)">
            <circle class="basePulse" cx="0" cy="0" r="18" fill="rgba(59,130,246,.14)"></circle>
            <circle cx="0" cy="0" r="8" fill="rgba(59,130,246,.95)"></circle>
            <circle cx="0" cy="0" r="14" fill="rgba(59,130,246,.10)"></circle>
            <path d="M-12 0 H12" stroke="rgba(226,232,240,.55)" stroke-width="1.1" stroke-linecap="round"/>
            <path d="M0 -12 V12" stroke="rgba(226,232,240,.55)" stroke-width="1.1" stroke-linecap="round"/>
            <g transform="translate(16,-22)">
              <rect x="0" y="0" width="86" height="24" rx="10" fill="rgba(0,0,0,.45)" stroke="rgba(255,255,255,.12)"/>
              <text x="10" y="16" fill="rgba(219,234,254,.95)" font-family="var(--mono)" font-size="11">BASE</text>
            </g>
          </g>

          <!-- Fields group -->
          <g id="fields" filter="url(#noise)">
            <!-- Segmentation derived from land_disected red lines -->
            <!-- Field A (top-left rectangle) -->
            <g id="fieldAGroup">
              <rect id="fieldARect" class="segRect" x="32" y="101" width="282" height="279" rx="12" />
              <rect class="segRect segRectStrong" x="32" y="101" width="282" height="279" rx="12" fill="none" />
              <text x="50" y="129" fill="rgba(226,232,240,.95)" font-family="var(--mono)" font-size="13">Field A</text>
              <text x="50" y="151" fill="rgba(148,163,184,.9)" font-family="var(--mono)" font-size="11">Crop Health Scan</text>
            </g>

            <!-- Field B (top-middle rectangle) -->
            <g id="fieldBGroup">
              <rect id="fieldBRect" class="segRect" x="314" y="101" width="284" height="279" rx="12" />
              <rect class="segRect segRectStrong" x="314" y="101" width="284" height="279" rx="12" fill="none" />
              <text x="332" y="129" fill="rgba(226,232,240,.95)" font-family="var(--mono)" font-size="13">Field B</text>
              <text x="332" y="151" fill="rgba(148,163,184,.9)" font-family="var(--mono)" font-size="11">Inspecting</text>
            </g>

            <!-- Field C (top-right rectangle) -->
            <g id="fieldCGroup">
              <rect id="fieldCRect" class="segRect" x="598" y="101" width="286" height="279" rx="12" />
              <rect class="segRect segRectStrong" x="598" y="101" width="286" height="279" rx="12" fill="none" />
              <text x="616" y="129" fill="rgba(226,232,240,.95)" font-family="var(--mono)" font-size="13">Field C</text>
              <text x="616" y="151" fill="rgba(148,163,184,.9)" font-family="var(--mono)" font-size="11">Taking Photo</text>
            </g>

            <!-- Crash marker (positioned over crashed drone) -->
            <g id="crashMarker" style="display:none;">
              <circle cx="0" cy="0" r="18" fill="rgba(239,68,68,.08)" stroke="rgba(239,68,68,.30)" stroke-width="2"/>
              <path d="M-9 -9 L9 9" stroke="rgba(239,68,68,.95)" stroke-width="4.2" stroke-linecap="round"/>
              <path d="M9 -9 L-9 9" stroke="rgba(239,68,68,.95)" stroke-width="4.2" stroke-linecap="round"/>
              <g transform="translate(16,-26)">
                <rect x="0" y="0" width="146" height="22" rx="10" fill="rgba(0,0,0,.45)" stroke="rgba(255,255,255,.12)"/>
                <text x="10" y="15" fill="rgba(254,202,202,.95)" font-family="var(--mono)" font-size="11">DRONE 2 CRASH</text>
              </g>
            </g>

            <!-- Field D (lower triangular region) -->
            <g id="fieldDGroup">
              <path id="fieldDPath" class="segRect" d="M 314 380 L 884 380 L 314 615 Z" />
              <path class="segRect segRectStrong" d="M 314 380 L 884 380 L 314 615 Z" fill="none" />
              <text x="332" y="408" fill="rgba(226,232,240,.95)" font-family="var(--mono)" font-size="13">Field D</text>
              <text x="332" y="430" fill="rgba(148,163,184,.9)" font-family="var(--mono)" font-size="11">Spraying</text>
            </g>
          </g>

          <!-- Planned paths (faint) -->
          <g id="paths" opacity="0.0">
            <path id="path1" d="" fill="none" stroke="rgba(226,232,240,.18)" stroke-width="2" stroke-dasharray="5 7" />
            <path id="path2" d="" fill="none" stroke="rgba(226,232,240,.18)" stroke-width="2" stroke-dasharray="5 7" />
            <path id="path3" d="" fill="none" stroke="rgba(226,232,240,.18)" stroke-width="2" stroke-dasharray="5 7" />
            <path id="path4" d="" fill="none" stroke="rgba(226,232,240,.18)" stroke-width="2" stroke-dasharray="5 7" />
          </g>

          <!-- Drones -->
          <g id="drones" opacity="0.0">
            <!-- Drone 1: Spraying -->
            <g id="drone1" filter="url(#glow)">
              <g id="body1">
                <g class="fxSpray">
                  <polygon class="sprayCone" points="-8,-6 -44,-18 -58,0 -44,18 -8,6" fill="rgba(59,130,246,.14)" stroke="rgba(59,130,246,.22)" stroke-width="1" />
                </g>
                <circle r="7.2" fill="rgba(59,130,246,.95)"></circle>
                <circle r="13.8" fill="rgba(59,130,246,.10)"></circle>
                <path d="M-9 0 H9" stroke="rgba(226,232,240,.55)" stroke-width="1.2" stroke-linecap="round"/>
                <path d="M0 -9 V9" stroke="rgba(226,232,240,.55)" stroke-width="1.2" stroke-linecap="round"/>
              </g>
              <g id="label1" transform="translate(14,-18)">
                <rect x="0" y="0" width="210" height="34" rx="10" fill="rgba(0,0,0,.45)" stroke="rgba(255,255,255,.12)"/>
                <text x="10" y="14" fill="rgba(226,232,240,.95)" font-family="var(--mono)" font-size="11">Drone 1 • Spraying</text>
                <text id="status1" x="10" y="28" fill="rgba(148,163,184,.95)" font-family="var(--mono)" font-size="10">Task Assigned</text>
              </g>
            </g>
            <!-- Drone 2: Taking Photo -->
            <g id="drone2" filter="url(#glow)">
              <g id="body2">
                <g class="fxPhoto">
                  <circle class="flashRing" cx="0" cy="0" r="6" fill="rgba(226,232,240,.05)" stroke="rgba(226,232,240,.18)" stroke-width="2" />
                </g>
                <circle r="7.2" fill="rgba(34,197,94,.95)"></circle>
                <circle r="13.8" fill="rgba(34,197,94,.10)"></circle>
                <path d="M-9 0 H9" stroke="rgba(226,232,240,.55)" stroke-width="1.2" stroke-linecap="round"/>
                <path d="M0 -9 V9" stroke="rgba(226,232,240,.55)" stroke-width="1.2" stroke-linecap="round"/>
              </g>
              <g id="label2" transform="translate(14,-18)">
                <rect x="0" y="0" width="210" height="34" rx="10" fill="rgba(0,0,0,.45)" stroke="rgba(255,255,255,.12)"/>
                <text x="10" y="14" fill="rgba(226,232,240,.95)" font-family="var(--mono)" font-size="11">Drone 2 • Taking Photo</text>
                <text id="status2" x="10" y="28" fill="rgba(148,163,184,.95)" font-family="var(--mono)" font-size="10">Task Assigned</text>
              </g>
            </g>
            <!-- Drone 3: Inspecting -->
            <g id="drone3" filter="url(#glow)">
              <g id="body3">
                <g class="fxInspect">
                  <circle class="scanRing" cx="0" cy="0" r="10" fill="rgba(245,158,11,.03)" stroke="rgba(245,158,11,.20)" stroke-width="2" />
                </g>
                <circle r="7.2" fill="rgba(245,158,11,.95)"></circle>
                <circle r="13.8" fill="rgba(245,158,11,.10)"></circle>
                <path d="M-9 0 H9" stroke="rgba(226,232,240,.55)" stroke-width="1.2" stroke-linecap="round"/>
                <path d="M0 -9 V9" stroke="rgba(226,232,240,.55)" stroke-width="1.2" stroke-linecap="round"/>
              </g>
              <g id="label3" transform="translate(14,-18)">
                <rect x="0" y="0" width="210" height="34" rx="10" fill="rgba(0,0,0,.45)" stroke="rgba(255,255,255,.12)"/>
                <text x="10" y="14" fill="rgba(226,232,240,.95)" font-family="var(--mono)" font-size="11">Drone 3 • Inspecting</text>
                <text id="status3" x="10" y="28" fill="rgba(148,163,184,.95)" font-family="var(--mono)" font-size="10">Task Assigned</text>
              </g>
            </g>

            <!-- Drone 4: Crop Health Scan -->
            <g id="drone4" filter="url(#glow)">
              <g id="body4">
                <circle r="7.2" fill="rgba(124,58,237,.95)"></circle>
                <circle r="13.8" fill="rgba(124,58,237,.10)"></circle>
                <path d="M-9 0 H9" stroke="rgba(226,232,240,.55)" stroke-width="1.2" stroke-linecap="round"/>
                <path d="M0 -9 V9" stroke="rgba(226,232,240,.55)" stroke-width="1.2" stroke-linecap="round"/>
              </g>
              <g id="label4" transform="translate(14,-18)">
                <rect x="0" y="0" width="210" height="34" rx="10" fill="rgba(0,0,0,.45)" stroke="rgba(255,255,255,.12)"/>
                <text x="10" y="14" fill="rgba(226,232,240,.95)" font-family="var(--mono)" font-size="11">Drone 4 • Crop Health Scan</text>
                <text id="status4" x="10" y="28" fill="rgba(148,163,184,.95)" font-family="var(--mono)" font-size="10">Task Assigned</text>
              </g>
            </g>
          </g>
        </svg>

        <div class="legend" aria-hidden="true">
          <div class="legendCard">
            <div class="t">Telemetry</div>
            <div class="legendRow"><span><strong>Link</strong>:</span><span id="linkState">standby</span></div>
            <div class="legendRow"><span><strong>BASE</strong>:</span><span id="baseState" style="color:#bfdbfe;">armed</span></div>
            <div class="legendRow"><span><strong>Drones</strong>:</span><span id="droneCount">0 active</span></div>
            <div class="legendRow"><span><strong>Planner</strong>:</span><span id="plannerState">idle</span></div>
          </div>
        </div>
      </div>

      <div class="srOnly" id="ariaLive" aria-live="polite"></div>
    </main>
  </div>

  <script>
    (function () {
      const $ = (id) => document.getElementById(id);

      const ui = {
        intent: $("intent"),
        submit: $("submitBtn"),
        reset: $("resetBtn"),
        fleetList: $("fleetList"),
        addDroneBtn: $("addDroneBtn"),
        fleet: $("fleet"),
        fleetToggle: $("fleetToggle"),
        fleetBody: $("fleetBody"),
        steps: $("steps"),
        breakdownMeta: $("breakdownMeta"),
        crashBtn: $("crashBtn"),
        liveDot: $("liveDot"),
        liveText: $("liveText"),
        linkState: $("linkState"),
        baseState: $("baseState"),
        droneCount: $("droneCount"),
        plannerState: $("plannerState"),
        ariaLive: $("ariaLive"),
        fieldsGroup: $("fields"),
        crashMarker: $("crashMarker"),
        baseMarker: $("baseMarker"),
        pathsGroup: $("paths"),
        dronesGroup: $("drones"),
        path1: $("path1"),
        path2: $("path2"),
        path3: $("path3"),
        path4: $("path4"),
        drone1: $("drone1"),
        drone2: $("drone2"),
        drone3: $("drone3"),
        drone4: $("drone4"),
        body1: $("body1"),
        body2: $("body2"),
        body3: $("body3"),
        body4: $("body4"),
        status1: $("status1"),
        status2: $("status2"),
        status3: $("status3"),
        status4: $("status4"),
      };

      const prefersReduced = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

      // All 4 drones enabled by default (as before)
      let drone4Enabled = true;
      const FLEET = [
        { id: 1, cap: "Sprayer + Camera", desc: "Variable-rate spray pass + visual confirmation.", enabled: true },
        { id: 2, cap: "Camera only", desc: "Stops at checkpoints to capture photos.", enabled: true },
        { id: 3, cap: "Sensor suite", desc: "Multispectral/thermal scan + boundary inspection.", enabled: true },
        { id: 4, cap: "Crop scanner", desc: "Dedicated grid scan for baseline health map.", enabled: true },
      ];

      function escapeHtmlLite(s) {
        return String(s)
          .replaceAll("&", "&amp;")
          .replaceAll("<", "&lt;")
          .replaceAll(">", "&gt;")
          .replaceAll('"', "&quot;")
          .replaceAll("'", "&#039;");
      }

      function renderFleet() {
        if (!ui.fleetList) return;
        const list = FLEET.map(d => ({
          ...d,
          enabled: d.id === 4 ? drone4Enabled : true
        }));
        ui.fleetList.innerHTML = "";
        for (const d of list) {
          const li = document.createElement("li");
          li.className = "fleetItem" + (d.enabled ? "" : " disabled");
          li.innerHTML = `
            <div style="min-width:0;">
              <div><span class="cap">${escapeHtmlLite(d.id)} → ${escapeHtmlLite(d.cap)}</span></div>
              <div class="desc">${escapeHtmlLite(d.desc)}</div>
            </div>
            <div class="state">${d.enabled ? "ready" : "disabled"}</div>
          `;
          ui.fleetList.appendChild(li);
        }

        if (ui.addDroneBtn) {
          ui.addDroneBtn.disabled = true;
          ui.addDroneBtn.textContent = "All drones enabled";
        }
      }

      function activeFleetCount() {
        return 3 + (drone4Enabled ? 1 : 0);
      }

      let currentMissionToken = 0;
      let crashRequestedToken = 0;
      function requestCrash() {
        if (!currentMissionToken) return;
        crashRequestedToken = currentMissionToken;
      }

      const MAP = {
        base: { x: 160, y: 730 },
        segments: {
          // Matches the red-line dissection provided by land_disected
          A: { type: "rect", x: 32,  y: 101, w: 282, h: 279 }, // top-left
          B: { type: "rect", x: 314, y: 101, w: 284, h: 279 }, // top-middle
          C: { type: "rect", x: 598, y: 101, w: 286, h: 279 }, // top-right
          D: { type: "tri",  xLeft: 314, xRight: 884, yTop: 380, yBottom: 615 }, // lower triangle
        }
      };

      /** Mission breakdown model */
      const STEPS = [
        { key: "parsed", name: "Objective parsed", desc: "Extract high-level intent and operational constraints." },
        { key: "roles", name: "Roles assigned", desc: "Assign spraying, photo capture, inspection, and crop-scan roles to fields." },
        { key: "waypoints", name: "Waypoints generated", desc: "Produce non-overlapping, deliberate flight plans." },
        { key: "executing", name: "Mission executing", desc: "Dispatch drones and track progress." },
      ];

      function renderSteps(state) {
        ui.steps.innerHTML = "";
        for (const s of STEPS) {
          const st = state[s.key] || "waiting";
          const el = document.createElement("div");
          el.className = "step";
          const label = (st === "done") ? { cls: "ok", text: "done" }
                      : (st === "running") ? { cls: "run", text: "running" }
                      : { cls: "wait", text: "pending" };
          el.innerHTML = `
            <div class="left">
              <div class="name">${escapeHtml(s.name)}</div>
              <div class="desc">${escapeHtml(s.desc)}</div>
            </div>
            <div class="state ${label.cls}">${escapeHtml(label.text)}</div>
          `;
          ui.steps.appendChild(el);
        }
      }

      function escapeHtml(str) {
        return String(str)
          .replaceAll("&", "&amp;")
          .replaceAll("<", "&lt;")
          .replaceAll(">", "&gt;")
          .replaceAll('"', "&quot;")
          .replaceAll("'", "&#039;");
      }

      /** Geometry helpers */
      function rectToBounds(x, y, w, h, inset = 18) {
        return { x: x + inset, y: y + inset, w: w - inset * 2, h: h - inset * 2 };
      }
      function clamp(v, a, b) { return Math.max(a, Math.min(b, v)); }

      function pointsToPath(points) {
        if (!points.length) return "";
        return "M " + points.map(p => `${p.x.toFixed(2)} ${p.y.toFixed(2)}`).join(" L ");
      }

      /** Path generators */
      function genLawnmower(bounds, rows = 8) {
        const pts = [];
        const y0 = bounds.y;
        const y1 = bounds.y + bounds.h;
        const x0 = bounds.x;
        const x1 = bounds.x + bounds.w;
        for (let i = 0; i < rows; i++) {
          const t = rows === 1 ? 0 : i / (rows - 1);
          // start nearer the bottom to make "launch from BASE" feel natural
          const y = y1 - t * (y1 - y0);
          const leftToRight = (i % 2 === 0);
          pts.push({ x: leftToRight ? x0 : x1, y });
          pts.push({ x: leftToRight ? x1 : x0, y });
        }
        // Close the loop with a calm boundary return (avoid "teleport" on wrap).
        if (pts.length >= 2) {
          const start = pts[0];
          const end = pts[pts.length - 1];
          const bottomY = y1;
          const eps = 0.001;
          const needsClose = (Math.abs(start.x - end.x) > eps) || (Math.abs(start.y - end.y) > eps);
          if (needsClose) {
            // Return down to bottom edge, then along bottom edge back to start.
            pts.push({ x: end.x, y: bottomY });
            pts.push({ x: start.x, y: bottomY });
            pts.push({ x: start.x, y: start.y });
          }
        }
        return pts;
      }

      function genCircleLoops(centers, radius = 34, segments = 24) {
        const pts = [];
        for (const c of centers) {
          for (let i = 0; i <= segments; i++) {
            const a = (i / segments) * Math.PI * 2;
            pts.push({ x: c.x + Math.cos(a) * radius, y: c.y + Math.sin(a) * radius });
          }
          // transit to next center with a straight line
        }
        // Close the overall route to prevent wrap jumps.
        if (pts.length >= 2) pts.push({ x: pts[0].x, y: pts[0].y });
        return pts;
      }

      function genPerimeter(bounds, rounds = 2) {
        const pts = [];
        const x0 = bounds.x, y0 = bounds.y, x1 = bounds.x + bounds.w, y1 = bounds.y + bounds.h;
        for (let r = 0; r < rounds; r++) {
          // start from bottom-left (closer to BASE)
          pts.push({ x: x0, y: y1 });
          pts.push({ x: x0, y: y0 });
          pts.push({ x: x1, y: y0 });
          pts.push({ x: x1, y: y1 });
          pts.push({ x: x0, y: y1 });
        }
        return pts;
      }

      function genTriSweep(tri, rows = 9, inset = 18) {
        // Triangle: left vertical edge, top horizontal edge, diagonal edge.
        // Points: (xLeft,yTop) -> (xRight,yTop) -> (xLeft,yBottom)
        const pts = [];
        const y0 = tri.yTop + inset;
        const y1 = tri.yBottom - inset;
        const xMin = tri.xLeft + inset;
        const denom = Math.max(1, (tri.yBottom - tri.yTop));
        for (let i = 0; i < rows; i++) {
          const t = rows === 1 ? 0 : i / (rows - 1);
          const y = y1 - t * (y1 - y0);
          const frac = (tri.yBottom - y) / denom; // 0 at bottom, 1 at top
          const xDiag = tri.xLeft + frac * (tri.xRight - tri.xLeft);
          const xMax = clamp(xDiag - inset, xMin + 10, tri.xRight - inset);
          const leftToRight = (i % 2 === 0);
          pts.push({ x: leftToRight ? xMin : xMax, y });
          pts.push({ x: leftToRight ? xMax : xMin, y });
        }
        // close loop back toward start along left edge
        if (pts.length >= 2) {
          const start = pts[0];
          pts.push({ x: xMin, y: y1 });
          pts.push({ x: xMin, y: start.y });
        }
        return pts;
      }

      /** Motion along polyline */
      function polylineLengths(points) {
        const seg = [];
        let total = 0;
        for (let i = 0; i < points.length - 1; i++) {
          const a = points[i], b = points[i+1];
          const dx = b.x - a.x, dy = b.y - a.y;
          const len = Math.hypot(dx, dy);
          seg.push(len);
          total += len;
        }
        return { seg, total };
      }

      function pointAt(points, lens, dist) {
        if (points.length < 2) return points[0] || {x:0,y:0};
        let d = clamp(dist, 0, lens.total);
        for (let i = 0; i < lens.seg.length; i++) {
          const l = lens.seg[i];
          if (d <= l || i === lens.seg.length - 1) {
            const t = (l === 0) ? 0 : d / l;
            const a = points[i], b = points[i+1];
            return { x: a.x + (b.x - a.x) * t, y: a.y + (b.y - a.y) * t };
          }
          d -= l;
        }
        return points[points.length - 1];
      }

      function distanceAtIndex(points, idx) {
        let d = 0;
        for (let i = 0; i < Math.min(idx, points.length - 1); i++) {
          const a = points[i], b = points[i + 1];
          d += Math.hypot(b.x - a.x, b.y - a.y);
        }
        return d;
      }

      function distanceAlongWithStops(tSec, speedPxPerSec, lens, checkpointDistances, dwellSec) {
        // Map time -> distance along polyline, pausing at checkpoints (in increasing order).
        if (lens.total <= 0) return { dist: 0, paused: false };
        const moveTime = lens.total / speedPxPerSec;
        const cycleTime = moveTime + checkpointDistances.length * dwellSec;
        let t = ((tSec % cycleTime) + cycleTime) % cycleTime;

        let lastD = 0;
        for (let i = 0; i <= checkpointDistances.length; i++) {
          const nextD = (i < checkpointDistances.length) ? checkpointDistances[i] : lens.total;
          const segD = Math.max(0, nextD - lastD);
          const segT = segD / speedPxPerSec;

          if (t < segT) {
            return { dist: lastD + t * speedPxPerSec, paused: false };
          }
          t -= segT;

          // pause at checkpoint (not after the final segment)
          if (i < checkpointDistances.length) {
            if (t < dwellSec) return { dist: nextD, paused: true };
            t -= dwellSec;
          }
          lastD = nextD;
        }

        return { dist: 0, paused: false };
      }

      /** State */
      let anim = null;
      let missionToken = 0;

      function setLive(live) {
        ui.liveDot.classList.toggle("live", !live);
        ui.liveText.textContent =  "live" ;
        ui.linkState.textContent = live ? "connected" : "standby";
        ui.plannerState.textContent = live ? "executing" : "idle";
        ui.baseState.textContent = live ? "active" : "armed";
      }

      function setSegmentationVisible(show) {
        ui.fieldsGroup.style.opacity = show ? "1" : "0";
      }
      function setPathsVisible(show) {
        ui.pathsGroup.style.opacity = show ? "1" : "0";
      }
      function setDronesVisible(show) {
        ui.dronesGroup.style.opacity = show ? "1" : "0";
      }

      function setDroneCount(n) {
        ui.droneCount.textContent = `${n} active`;
      }

      function setDronePose(droneEl, bodyEl, pos, angleDeg) {
        droneEl.setAttribute("transform", `translate(${pos.x.toFixed(2)},${pos.y.toFixed(2)})`);
        bodyEl.setAttribute("transform", `rotate(${angleDeg.toFixed(1)})`);
      }

      function headingDeg(points, lens, dist, lookAhead = 12) {
        const a = pointAt(points, lens, dist);
        const b = pointAt(points, lens, dist + lookAhead);
        return Math.atan2(b.y - a.y, b.x - a.x) * 180 / Math.PI;
      }

      function genTransit(base, target, laneY) {
        const lane = clamp(laneY, 30, 730);
        const xMid1 = clamp(base.x + 70, 10, 1190);
        const xMid2 = clamp(target.x - 90, 10, 1190);
        return [
          { x: base.x, y: base.y },
          { x: xMid1, y: lane },
          { x: xMid2, y: lane },
          { x: target.x, y: target.y },
        ];
      }

      function genTransitFrom(start, target, laneY) {
        const lane = clamp(laneY, 30, 770);
        const dx = target.x - start.x;
        const xMid1 = clamp(start.x + Math.sign(dx || 1) * 90, 10, 1190);
        const xMid2 = clamp(target.x - Math.sign(dx || 1) * 90, 10, 1190);
        return [
          { x: start.x, y: start.y },
          { x: xMid1, y: lane },
          { x: xMid2, y: lane },
          { x: target.x, y: target.y },
        ];
      }

      function resetMission() {
        missionToken++;
        if (anim) cancelAnimationFrame(anim);
        anim = null;

        setLive(false);
        setSegmentationVisible(false);
        setPathsVisible(false);
        setDronesVisible(false);
        setDroneCount(0);
        ui.breakdownMeta.textContent = "idle";
        renderSteps({ parsed: "waiting", roles: "waiting", waypoints: "waiting", executing: "waiting" });

        ui.status1.textContent = "Task Assigned";
        ui.status2.textContent = "Task Assigned";
        ui.status3.textContent = "Task Assigned";
        ui.status4.textContent = drone4Enabled ? "Task Assigned" : "";

        // stage drones at BASE (hidden until launch)
        setDronePose(ui.drone1, ui.body1, MAP.base, 0);
        setDronePose(ui.drone2, ui.body2, MAP.base, 0);
        setDronePose(ui.drone3, ui.body3, MAP.base, 0);
        ui.drone4.style.display = drone4Enabled ? "" : "none";
        ui.path4.style.display = drone4Enabled ? "" : "none";
        if (drone4Enabled) setDronePose(ui.drone4, ui.body4, MAP.base, 0);

        ui.path1.setAttribute("d", "");
        ui.path2.setAttribute("d", "");
        ui.path3.setAttribute("d", "");
        ui.path4.setAttribute("d", "");

        ui.ariaLive.textContent = "Mission reset.";
        ui.crashBtn.disabled = true;
        ui.crashMarker.style.display = "none";
      }

      function scheduleBreakdown(token, intentText) {
        const state = { parsed: "running", roles: "waiting", waypoints: "waiting", executing: "waiting" };
        ui.breakdownMeta.textContent = "planning";
        renderSteps(state);

        const t0 = performance.now();
        ui.ariaLive.textContent = "Parsing mission objective.";

        const timers = [];
        const later = (ms, fn) => {
          const id = window.setTimeout(() => { if (token === missionToken) fn(); }, ms);
          timers.push(id);
        };

        // 1) Parsed
        later(650, () => {
          state.parsed = "done";
          state.roles = "running";
          ui.breakdownMeta.textContent = "assigning";
          renderSteps(state);
          ui.ariaLive.textContent = "Objective parsed. Assigning drone roles.";
        });

        // 2) Roles
        later(1350, () => {
          state.roles = "done";
          state.waypoints = "running";
          ui.breakdownMeta.textContent = "routing";
          renderSteps(state);
          ui.ariaLive.textContent = "Roles assigned. Generating waypoints.";
        });

        // 3) Waypoints
        later(2150, () => {
          state.waypoints = "done";
          state.executing = "running";
          ui.breakdownMeta.textContent = "executing";
          renderSteps(state);
          ui.ariaLive.textContent = "Waypoints generated. Mission executing.";
        });

        // 4) Executing label stays running while drones move
        // (We keep it "running" to match the requested live feel.)
        return () => {
          for (const id of timers) window.clearTimeout(id);
        };
      }

      function runMission() {
        missionToken++;
        const token = missionToken;

        if (anim) cancelAnimationFrame(anim);
        anim = null;

        const intentText = ui.intent.value.trim();
        if (!intentText) return;

        // Ensure BASE marker matches configured point
        ui.baseMarker.setAttribute("transform", `translate(${MAP.base.x},${MAP.base.y})`);

        // Phase 1: segment the map (no drones yet)
        setLive(true);
        setDroneCount(0);
        ui.status1.textContent = "Task Assigned";
        ui.status2.textContent = "Task Assigned";
        ui.status3.textContent = "Task Assigned";
        ui.status4.textContent = drone4Enabled ? "Task Assigned" : "";
        setSegmentationVisible(true);
        setPathsVisible(false);
        setDronesVisible(false);

        const cancelBreakdown = scheduleBreakdown(token, intentText);
        currentMissionToken = token;
        ui.crashBtn.disabled = false;
        ui.crashMarker.style.display = "none";

        const segA = MAP.segments.A;
        const segB = MAP.segments.B;
        const segC = MAP.segments.C;
        const segD = MAP.segments.D;

        // Field bounds (match segmentation overlays)
        const fieldA = rectToBounds(segA.x, segA.y, segA.w, segA.h, 22); // crop scan
        const fieldB = rectToBounds(segB.x, segB.y, segB.w, segB.h, 22); // inspecting
        const fieldC = rectToBounds(segC.x, segC.y, segC.w, segC.h, 22); // photos

        // Core in-field paths (kept inside each segment)
        const core1 = genTriSweep(segD, 10, 18);          // Drone 1: spraying (triangle)
        const core2 = genCircleLoops([                    // Drone 2: photos (3 centers)
          { x: fieldC.x + fieldC.w * 0.30, y: fieldC.y + fieldC.h * 0.32 },
          { x: fieldC.x + fieldC.w * 0.72, y: fieldC.y + fieldC.h * 0.50 },
          { x: fieldC.x + fieldC.w * 0.44, y: fieldC.y + fieldC.h * 0.74 },
        ], 30, 24);
        const core3 = genPerimeter(fieldB, 3);            // Drone 3: inspecting (perimeter)
        const useDrone4 = drone4Enabled;
        const core4 = useDrone4 ? genLawnmower(fieldA, 9) : null; // Drone 4: crop health scan (grid)

        const entry1 = core1[0];
        const entry2 = core2[0];
        const entry3 = core3[0];
        const entry4 = (useDrone4 && core4) ? core4[0] : null;

        const lane1 = MAP.base.y - 40;
        const lane2 = MAP.base.y - 95;
        const lane3 = MAP.base.y - 150;
        const lane4 = MAP.base.y - 205;

        const transit1 = genTransit(MAP.base, entry1, lane1);
        const transit2 = genTransit(MAP.base, entry2, lane2);
        const transit3 = genTransit(MAP.base, entry3, lane3);
        const transit4 = (useDrone4 && entry4) ? genTransit(MAP.base, entry4, lane4) : null;

        // Display full planned route, but only loop inside the segment after arrival.
        const full1 = transit1.concat(core1.slice(1));
        const full2 = transit2.concat(core2.slice(1));
        const full3 = transit3.concat(core3.slice(1));
        const full4 = (useDrone4 && transit4 && core4) ? transit4.concat(core4.slice(1)) : null;

        ui.path1.setAttribute("d", pointsToPath(full1));
        ui.path2.setAttribute("d", pointsToPath(full2));
        ui.path3.setAttribute("d", pointsToPath(full3));
        ui.path4.style.display = useDrone4 ? "" : "none";
        ui.path4.setAttribute("d", full4 ? pointsToPath(full4) : "");

        const lt1 = polylineLengths(transit1);
        const lt2 = polylineLengths(transit2);
        const lt3 = polylineLengths(transit3);
        const lt4 = (useDrone4 && transit4) ? polylineLengths(transit4) : null;
        const lc1 = polylineLengths(core1);
        const lc2 = polylineLengths(core2);
        const lc3 = polylineLengths(core3);
        const lc4 = (useDrone4 && core4) ? polylineLengths(core4) : null;

        // Stage drones at BASE, then launch after segmentation
        setDronePose(ui.drone1, ui.body1, MAP.base, 0);
        setDronePose(ui.drone2, ui.body2, MAP.base, 0);
        setDronePose(ui.drone3, ui.body3, MAP.base, 0);
        ui.drone4.style.display = useDrone4 ? "" : "none";
        if (useDrone4) setDronePose(ui.drone4, ui.body4, MAP.base, 0);

        // If reduced motion, keep drones visible but static with correct labels.
        if (prefersReduced) {
          ui.breakdownMeta.textContent = "executing (reduced motion)";
          setPathsVisible(true);
          setDronesVisible(true);
          setDroneCount(activeFleetCount());
          ui.status1.textContent = "Executing Mission";
          ui.status2.textContent = "Executing Mission";
          ui.status3.textContent = "Executing Mission";
          ui.status4.textContent = useDrone4 ? "Executing Mission" : "";
          ui.ariaLive.textContent = "Mission submitted. Reduced motion enabled; drones staged at BASE.";
          return;
        }

        const speeds = {
          d1: 78,  // px/sec
          d2: 92,
          d3: 86,
          d4: 82,
        };
        const startTime = performance.now();
        const launchDelaySec = 0.9; // segment first, then take off
        let lastPhotoPaused = false;
        const TOTAL_LOOPS = 1;

        const missionState = {
          done: false,
          crashHandled: false,
          remainingPhotoLoops: 0,
          d1: { phase: "loop", loops: 0, lastLoops: -1, r: null },
          d2: { phase: "loop", loops: 0, lastLoops: -1, r: null, crashedAtLoops: 0, crashPos: null, crashHeading: 0 },
          d3: { phase: "loop", loops: 0, lastLoops: -1, r: null },
          d4: { phase: useDrone4 ? "loop" : "off", loops: 0, lastLoops: -1, r: null },
        };

        // Drone 1 contingency: after finishing its last loop, cover remaining photo loops
        const contingency = {
          active: false,
          phase: "idle", // idle | transit | photo | rtb
          transit: null,
          photo: null,
        };

        function startReturn(droneKey, currentPos, lane, speed) {
          const pts = genTransitFrom(currentPos, MAP.base, lane);
          const lens = polylineLengths(pts);
          missionState[droneKey].phase = "return";
          missionState[droneKey].r = { pts, lens, t0: performance.now(), speed };
        }

        function isAllDone() {
          const keys = ["d1", "d3"].concat(useDrone4 ? ["d4"] : []);
          const ok = keys.every(k => missionState[k].phase === "done");
          const d2ok = (missionState.d2.phase === "done" || missionState.d2.phase === "crashed");
          return ok && d2ok;
        }

        function tick(now) {
          if (token !== missionToken) return;

          const t = Math.max(0, (now - startTime) / 1000 - launchDelaySec);

          // Loop each drone at its own cadence
          const dist1 = t * speeds.d1;
          const dist2 = t * speeds.d2;
          const dist3 = t * speeds.d3;
          const dist4 = useDrone4 ? (t * speeds.d4) : 0;

          const d1InTransit = dist1 < lt1.total;
          const d2InTransit = dist2 < lt2.total;
          const d3InTransit = dist3 < lt3.total;
          const d4InTransit = useDrone4 && lt4 ? (dist4 < lt4.total) : false;

          // Handle crash request (Drone 2)
          if (!missionState.crashHandled && crashRequestedToken === token) {
            missionState.crashHandled = true;
            ui.status2.textContent = "Crashed";
            ui.ariaLive.textContent = "Simulated crash: Drone 2 is down. Re-tasking Drone 1 to cover photos after finishing its pass.";

            // Snapshot Drone 2 current position immediately
            if (d2InTransit) {
              missionState.d2.crashPos = pointAt(transit2, lt2, dist2);
              missionState.d2.crashHeading = headingDeg(transit2, lt2, dist2);
              missionState.d2.crashedAtLoops = 0;
            } else {
              // determine completed photo loops (integer)
              const segments = 24;
              const checkpointIdx = [0, 1, 2].map(j => j * (segments + 1));
              const checkpointDistances = checkpointIdx.map(idx => distanceAtIndex(core2, idx)).sort((x,y) => x - y);
              const dwellSec = 1.05;
              const tCore = (dist2 - lt2.total) / speeds.d2;
              const moveTime = lc2.total / speeds.d2;
              const cycleTime = moveTime + checkpointDistances.length * dwellSec;
              const loopsDone = Math.floor(Math.max(0, tCore) / cycleTime);
              const out = distanceAlongWithStops(tCore, speeds.d2, lc2, checkpointDistances, dwellSec);
              missionState.d2.crashPos = pointAt(core2, lc2, out.dist);
              missionState.d2.crashHeading = headingDeg(core2, lc2, out.dist);
              missionState.d2.crashedAtLoops = loopsDone;
            }

            missionState.remainingPhotoLoops = Math.max(0, TOTAL_LOOPS - missionState.d2.crashedAtLoops);
            missionState.d2.phase = "crashed";
            if (missionState.d2.crashPos) {
              ui.crashMarker.style.display = "";
              ui.crashMarker.setAttribute("transform", `translate(${missionState.d2.crashPos.x.toFixed(2)},${missionState.d2.crashPos.y.toFixed(2)})`);
            }
            ui.drone2.classList.remove("photoActive");
            ui.status1.textContent = "Finishing last loop";
            contingency.active = missionState.remainingPhotoLoops > 0;
          }

          // Drone 1: loops then RTB
          let a, ha;
          if (missionState.d1.phase === "return" && missionState.d1.r) {
            const rt = (now - missionState.d1.r.t0) / 1000;
            const rdist = rt * missionState.d1.r.speed;
            a = pointAt(missionState.d1.r.pts, missionState.d1.r.lens, rdist);
            ha = headingDeg(missionState.d1.r.pts, missionState.d1.r.lens, rdist);
            if (rdist >= missionState.d1.r.lens.total) {
              missionState.d1.phase = "done";
              ui.status1.textContent = "Done • RTB";
              a = MAP.base;
              ha = 0;
            }
          } else if (missionState.d1.phase === "done") {
            a = MAP.base; ha = 0;
          } else {
            const coreElapsed = Math.max(0, dist1 - lt1.total);
            const loops = d1InTransit ? 0 : Math.floor(coreElapsed / lc1.total);
            missionState.d1.loops = loops;
            if (!d1InTransit && loops >= TOTAL_LOOPS) {
              const curDist = (coreElapsed % lc1.total);
              const curPos = pointAt(core1, lc1, curDist);
              if (contingency.active && contingency.phase === "idle") {
                // start transit to Field C entry to take photos (Drone 2's job)
                contingency.phase = "transit";
                const pts = genTransitFrom(curPos, entry2, lane2);
                contingency.transit = { pts, lens: polylineLengths(pts), t0: performance.now(), speed: speeds.d1 };
                ui.status1.textContent = "Re-tasked: Taking Photos";
              } else if (contingency.active) {
                // already re-tasked; do not override with RTB
              } else {
                startReturn("d1", curPos, lane1, speeds.d1);
                ui.status1.textContent = "Returning to BASE";
              }
              // fall through into return next frame
            }
            const d1 = d1InTransit ? dist1 : (coreElapsed % lc1.total);
            a = d1InTransit ? pointAt(transit1, lt1, dist1) : pointAt(core1, lc1, d1);
            ha = d1InTransit ? headingDeg(transit1, lt1, dist1) : headingDeg(core1, lc1, d1);
            // keep status calm and non-verbose (no loop count)
          }

          // Drone 1 contingency behavior (after its loop)
          if (contingency.active) {
            if (contingency.phase === "transit" && contingency.transit) {
              const rt = (now - contingency.transit.t0) / 1000;
              const rdist = rt * contingency.transit.speed;
              const p = pointAt(contingency.transit.pts, contingency.transit.lens, rdist);
              const h = headingDeg(contingency.transit.pts, contingency.transit.lens, rdist);
              a = p; ha = h;
              ui.status1.textContent = "Taking Photos";
              if (rdist >= contingency.transit.lens.total) {
                contingency.phase = "photo";
                contingency.photo = { t0: performance.now() };
              }
            } else if (contingency.phase === "photo" && contingency.photo) {
              const segments = 24;
              const checkpointIdx = [0, 1, 2].map(j => j * (segments + 1));
              const checkpointDistances = checkpointIdx.map(idx => distanceAtIndex(core2, idx)).sort((x,y) => x - y);
              const dwellSec = 1.05;
              const elapsed = (now - contingency.photo.t0) / 1000;
              const moveTime = lc2.total / speeds.d1;
              const cycleTime = moveTime + checkpointDistances.length * dwellSec;
              const totalTime = missionState.remainingPhotoLoops * cycleTime;
              if (elapsed >= totalTime) {
                // RTB after finishing remaining photo loops
                // Seed RTB from the *final* photo position (avoid any snap).
                const outEnd = distanceAlongWithStops(totalTime, speeds.d1, lc2, checkpointDistances, dwellSec);
                const pEnd = pointAt(core2, lc2, outEnd.dist);
                const hEnd = headingDeg(core2, lc2, outEnd.dist);
                a = pEnd; ha = hEnd;
                startReturn("d1", pEnd, lane2, speeds.d1);
                ui.status1.textContent = "Returning to BASE";
                contingency.phase = "rtb";
                contingency.active = false;
              } else {
                const out = distanceAlongWithStops(elapsed, speeds.d1, lc2, checkpointDistances, dwellSec);
                const p = pointAt(core2, lc2, out.dist);
                const h = headingDeg(core2, lc2, out.dist);
                a = p; ha = h;
                ui.status1.textContent = out.paused ? "Capturing Photo" : "Taking Photos";
              }
            }
          }
          setDronePose(ui.drone1, ui.body1, a, ha);

          // Drone 3: loops then RTB
          let c, hc;
          if (missionState.d3.phase === "return" && missionState.d3.r) {
            const rt = (now - missionState.d3.r.t0) / 1000;
            const rdist = rt * missionState.d3.r.speed;
            c = pointAt(missionState.d3.r.pts, missionState.d3.r.lens, rdist);
            hc = headingDeg(missionState.d3.r.pts, missionState.d3.r.lens, rdist);
            if (rdist >= missionState.d3.r.lens.total) {
              missionState.d3.phase = "done";
              ui.status3.textContent = "Done • RTB";
              c = MAP.base; hc = 0;
            }
          } else if (missionState.d3.phase === "done") {
            c = MAP.base; hc = 0;
          } else {
            const coreElapsed = Math.max(0, dist3 - lt3.total);
            const loops = d3InTransit ? 0 : Math.floor(coreElapsed / lc3.total);
            missionState.d3.loops = loops;
            if (!d3InTransit && loops >= TOTAL_LOOPS) {
              const curDist = (coreElapsed % lc3.total);
              const curPos = pointAt(core3, lc3, curDist);
              startReturn("d3", curPos, lane3, speeds.d3);
              ui.status3.textContent = "Returning to BASE";
            }
            const d3 = d3InTransit ? dist3 : (coreElapsed % lc3.total);
            c = d3InTransit ? pointAt(transit3, lt3, dist3) : pointAt(core3, lc3, d3);
            hc = d3InTransit ? headingDeg(transit3, lt3, dist3) : headingDeg(core3, lc3, d3);
            // keep status calm and non-verbose (no loop count)
          }
          setDronePose(ui.drone3, ui.body3, c, hc);

          // Drone 4: loops then RTB (if enabled)
          if (useDrone4 && core4 && lc4 && lt4 && transit4) {
            let p4, h4;
            if (missionState.d4.phase === "return" && missionState.d4.r) {
              const rt = (now - missionState.d4.r.t0) / 1000;
              const rdist = rt * missionState.d4.r.speed;
              p4 = pointAt(missionState.d4.r.pts, missionState.d4.r.lens, rdist);
              h4 = headingDeg(missionState.d4.r.pts, missionState.d4.r.lens, rdist);
              if (rdist >= missionState.d4.r.lens.total) {
                missionState.d4.phase = "done";
                ui.status4.textContent = "Done • RTB";
                p4 = MAP.base; h4 = 0;
              }
            } else if (missionState.d4.phase === "done") {
              p4 = MAP.base; h4 = 0;
            } else {
              const coreElapsed = Math.max(0, dist4 - lt4.total);
              const loops = d4InTransit ? 0 : Math.floor(coreElapsed / lc4.total);
              missionState.d4.loops = loops;
              if (!d4InTransit && loops >= TOTAL_LOOPS) {
                const curDist = (coreElapsed % lc4.total);
                const curPos = pointAt(core4, lc4, curDist);
                startReturn("d4", curPos, lane4, speeds.d4);
                ui.status4.textContent = "Returning to BASE";
              }
              const dd = d4InTransit ? dist4 : (coreElapsed % lc4.total);
              p4 = d4InTransit ? pointAt(transit4, lt4, dist4) : pointAt(core4, lc4, dd);
              h4 = d4InTransit ? headingDeg(transit4, lt4, dist4) : headingDeg(core4, lc4, dd);
              // keep status calm and non-verbose (no loop count)
            }
            setDronePose(ui.drone4, ui.body4, p4, h4);
          }

          // Drone 2: pause at checkpoints to "take photos", then continue looping.
          let b, hb, photoPaused = false;
          if (missionState.d2.phase === "crashed") {
            b = missionState.d2.crashPos || MAP.base;
            hb = missionState.d2.crashHeading || 0;
            photoPaused = false;
          } else if (missionState.d2.phase === "return" && missionState.d2.r) {
            const rt = (now - missionState.d2.r.t0) / 1000;
            const rdist = rt * missionState.d2.r.speed;
            b = pointAt(missionState.d2.r.pts, missionState.d2.r.lens, rdist);
            hb = headingDeg(missionState.d2.r.pts, missionState.d2.r.lens, rdist);
            if (rdist >= missionState.d2.r.lens.total) {
              missionState.d2.phase = "done";
              ui.status2.textContent = "Done • RTB";
              b = MAP.base; hb = 0;
            }
            photoPaused = false;
          } else if (missionState.d2.phase === "done") {
            b = MAP.base; hb = 0;
            photoPaused = false;
          } else if (d2InTransit) {
            b = pointAt(transit2, lt2, dist2);
            hb = headingDeg(transit2, lt2, dist2);
            photoPaused = false;
          } else {
            // checkpoints at each loop center start (one per target point)
            const segments = 24; // must match genCircleLoops() call
            const checkpointIdx = [0, 1, 2].map(j => j * (segments + 1));
            const checkpointDistances = checkpointIdx.map(idx => distanceAtIndex(core2, idx)).sort((x,y) => x - y);
            const dwellSec = 1.05;
            const tCore = (dist2 - lt2.total) / speeds.d2; // "mission clock" in seconds
            const moveTime = lc2.total / speeds.d2;
            const cycleTime = moveTime + checkpointDistances.length * dwellSec;
            const loops = Math.floor(Math.max(0, tCore) / cycleTime);
            missionState.d2.loops = loops;

            if (loops >= TOTAL_LOOPS) {
              // snapshot current position, then RTB
              const out = distanceAlongWithStops(tCore, speeds.d2, lc2, checkpointDistances, dwellSec);
              const curPos = pointAt(core2, lc2, out.dist);
              startReturn("d2", curPos, lane2, speeds.d2);
              ui.status2.textContent = "Returning to BASE";
              photoPaused = false;
              b = curPos;
              hb = headingDeg(core2, lc2, out.dist);
            } else {
              const out = distanceAlongWithStops(tCore, speeds.d2, lc2, checkpointDistances, dwellSec);
              photoPaused = out.paused;
              b = pointAt(core2, lc2, out.dist);
              hb = headingDeg(core2, lc2, out.dist);
              // keep status calm and non-verbose (no loop count)
            }
          }

          setDronePose(ui.drone2, ui.body2, b, hb);
          ui.drone2.classList.toggle("photoActive", photoPaused);
          if (photoPaused !== lastPhotoPaused && missionState.d2.phase === "loop") {
            ui.status2.textContent = photoPaused ? "Capturing Photo" : "Executing Mission";
            lastPhotoPaused = photoPaused;
          }

          if (!missionState.done && isAllDone()) {
            missionState.done = true;
            ui.breakdownMeta.textContent = "complete";
            renderSteps({ parsed: "done", roles: "done", waypoints: "done", executing: "done" });
            ui.ariaLive.textContent = "Mission complete. All drones returned to BASE.";
            setLive(false);
            // keep drones parked; stop animation
            return;
          }

          anim = requestAnimationFrame(tick);
        }

        // After segmentation phase, show paths + launch from BASE
        window.setTimeout(() => {
          if (token !== missionToken) return;
          setPathsVisible(true);
        }, 420);
        window.setTimeout(() => {
          if (token !== missionToken) return;
          setDronesVisible(true);
          setDroneCount(activeFleetCount());
          ui.status1.textContent = "Executing Mission";
          ui.status2.textContent = "Executing Mission";
          ui.status3.textContent = "Executing Mission";
          ui.status4.textContent = useDrone4 ? "Executing Mission" : "";
          ui.ariaLive.textContent = "Segmentation complete. Drones launching from BASE.";
        }, 820);

        anim = requestAnimationFrame(tick);
      }

      // initial render
      renderSteps({ parsed: "waiting", roles: "waiting", waypoints: "waiting", executing: "waiting" });
      ui.baseMarker.setAttribute("transform", `translate(${MAP.base.x},${MAP.base.y})`);
      renderFleet();
      resetMission();

      // events
      ui.submit.addEventListener("click", runMission);
      ui.reset.addEventListener("click", () => {
        ui.intent.value = "Scan the agricultural area A, monitor crop health and take pictures of unhealthy ones in field C, also spray the watermelon field.";
        resetMission();
      });

      ui.addDroneBtn.addEventListener("click", () => {
        // (kept for future expansion; currently all 4 are enabled by default)
      });

      ui.fleetToggle.addEventListener("click", () => {
        const collapsed = ui.fleet.classList.toggle("collapsed");
        ui.fleetToggle.setAttribute("aria-expanded", collapsed ? "false" : "true");
      });

      ui.crashBtn.addEventListener("click", () => {
        requestCrash();
      });

      // keyboard submit (Cmd/Ctrl+Enter)
      ui.intent.addEventListener("keydown", (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === "Enter") runMission();
      });
    })();
  </script>
</body>
</html>

