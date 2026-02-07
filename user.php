<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Degla • Profile</title>
  <link rel="icon" href="./logo1.png" />
  <meta name="theme-color" content="#0b1220" />
  <meta name="color-scheme" content="dark light" />
  <style>
    :root{
      --bg:#0b1220;
      --bg2:#0a1328;
      --card:rgba(255,255,255,.06);
      --stroke:rgba(255,255,255,.14);
      --text:#e6eaf2;
      --muted:#aab3c5;
      --accent:#60a5fa;
      --accent2:#a78bfa;
      --success:#22c55e;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
      color:var(--text);
      background:
        radial-gradient(1200px 600px at 10% -20%, rgba(96,165,250,.20), transparent 60%),
        radial-gradient(1000px 600px at 100% 0%, rgba(167,139,250,.18), transparent 60%),
        linear-gradient(180deg, var(--bg), var(--bg2));
      display:grid;
      place-items:center;
      padding:24px;
    }

    .wrap{width:min(960px,100%); display:grid; place-items:center}
    .card{
      width:min(640px,100%);
      border-radius:28px;
      padding:28px 24px;
      backdrop-filter: blur(16px);
      background: linear-gradient(180deg, rgba(255,255,255,.08), rgba(255,255,255,.04));
      border:1px solid var(--stroke);
      box-shadow: 0 30px 60px rgba(0,0,0,.45), 0 0 0 1px rgba(255,255,255,.05) inset;
      position:relative;
      overflow:hidden;
    }
    .glow{
      position:absolute; inset:-40%;
      background: radial-gradient(450px 260px at 18% 0%, rgba(96,165,250,.18), transparent 60%),
                  radial-gradient(460px 260px at 82% -10%, rgba(167,139,250,.16), transparent 60%);
      filter: blur(30px);
      z-index:0;
      pointer-events:none;
    }

    .brand{position:relative; z-index:1; display:flex; align-items:center; gap:12px; margin-bottom:8px}
    .brand img{width:44px; height:44px; border-radius:16px; display:block}
    .brand h1{margin:0; font-size:18px; letter-spacing:.2px}

    .hero{position:relative; z-index:1; display:flex; flex-direction:column; align-items:center; gap:14px; padding:10px 8px 0}
    .avatar{
      width:132px; height:132px; border-radius:50%;
      padding:4px; background: linear-gradient(135deg, var(--accent), var(--accent2));
      box-shadow: 0 14px 30px rgba(0,0,0,.35);
    }
    .avatar-inner{
      width:100%; height:100%; border-radius:50%; overflow:hidden; background:#0f172a; display:grid; place-items:center;
      border:2px solid rgba(255,255,255,.18);
    }
    .avatar img{width:100%; height:100%; object-fit:cover}

    .username{font-size:28px; font-weight:800; letter-spacing:.2px; text-align:center}
    .subtitle{font-size:14px; color:var(--muted); margin-top:-4px; text-align:center}
    .pill{display:inline-block; padding:6px 10px; border-radius:999px; font-size:12px; color:#dbe7ff;
      background:rgba(255,255,255,.08); border:1px solid var(--stroke)}

    .cta{
      margin-top:22px; display:grid; gap:12px;
      grid-template-columns:1fr;
    }
    @media (min-width:560px){ .cta{grid-template-columns:1fr 1fr;} }

    .btn{
      appearance:none; border:0; cursor:pointer; text-decoration:none; user-select:none;
      border-radius:14px; padding:14px 18px; font-weight:800; font-size:16px; letter-spacing:.2px; color:#fff;
      transition: transform .05s ease, box-shadow .2s ease, opacity .2s ease;
      display:inline-flex; align-items:center; justify-content:center; gap:10px;
      box-shadow: 0 10px 18px rgba(0,0,0,.25);
    }
    .btn:active{transform: translateY(1px)}
    .btn-primary{background:linear-gradient(180deg, var(--accent), #3b82f6);}
    .btn-secondary{background:linear-gradient(180deg, var(--success), #16a34a);}
    .btn svg{width:18px; height:18px; opacity:.95}

    .helper{margin-top:14px; text-align:center; color:var(--muted); font-size:12px}
    .meta{margin-top:8px; text-align:center; color:#aab3c5; font-size:12px}

    .footer{
      margin-top:26px; display:flex; justify-content:center; gap:10px; flex-wrap:wrap;
      color:var(--muted); font-size:12px;
    }

    /* small bounce-in */
    @keyframes pop { 0%{transform:scale(.96); opacity:0} 100%{transform:scale(1); opacity:1} }
    .card{animation: pop .35s ease both}
  </style>
</head>
<body>
  <div class="wrap">
    <section class="card">
      <div class="glow"></div>

      <div class="brand">
        <img src="./assets/icon.png" alt="UChallengeMe logo" />
        <h1>Degla</h1>
      </div>

      <div class="hero">
        <div class="avatar" aria-hidden="true">
          <div class="avatar-inner">
            <img id="profilePic" alt="Profile picture"/>
          </div>
        </div>

        <div class="username" id="username">@username</div>
        <div class="subtitle" id="subtitle">
          Join <span class="pill">@username</span> on Degla
        </div>

        <div class="cta">
          <a id="openAppBtn" class="btn btn-primary" href="#">
            <!-- launch icon -->
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M5 12h7a1 1 0 0 1 1 1v7h-2v-5.586l-8.293 8.293-1.414-1.414L9.586 13H4v-1z"></path>
              <path d="M14 3h7v7h-2V6.414l-9.293 9.293-1.414-1.414L17.586 5H14V3z"></path>
            </svg>
            Open the app
          </a>
          <a id="downloadBtn" class="btn btn-secondary" href="#" target="_blank" rel="noopener">
            <!-- download icon -->
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M12 3v10.586l3.293-3.293 1.414 1.414L12 17.414l-4.707-4.707 1.414-1.414L11 13.586V3h1z"></path>
              <path d="M5 19h14v2H5z"></path>
            </svg>
            Download Degla
          </a>
        </div>

        <div class="helper">If the app doesn’t open, install it first then tap <b>Open the app</b> again.</div>
        <div class="meta" id="meta">Powered by Degla</div>
      </div>

      <div class="footer">
        <span>© <span id="year"></span> Degla</span>
        <span>•</span>
        <span>All rights reserved</span>
      </div>
    </section>
  </div>

  <script>
    // --- Params ---
    const params = new URLSearchParams(location.search);
    const username = params.get('user') || 'username';
    const userId   = params.get('userid') || '0';

    // --- Fill UI ---
    const $ = (s)=>document.querySelector(s);
    $('#username').textContent = '@' + username;
    $('#subtitle').innerHTML   = `Join <span class="pill">@${username}</span> on Degla`;
    $('#profilePic').src       = `https://ucm-prod.s3.us-east-va.io.cloud.ovh.us/objects/profilepics/${encodeURIComponent(userId)}.jpg`;
    $('#profilePic').alt       = `@${username}'s profile picture`;
    $('#year').textContent     = new Date().getFullYear();

    // --- Store detection ---
    const ua = navigator.userAgent || navigator.vendor || window.opera;
    const isAndroid = /Android/i.test(ua);
    const isIOS     = /iPhone|iPad|iPod/i.test(ua);

    // Replace with your real Play Store link if you have the package name
    const IOS_STORE_URL     = 'https://apps.apple.com/us/app/uchallengeme/id6742088672';
    const ANDROID_STORE_URL = 'https://play.google.com/store/search?q=UChallengeMe&c=apps';

    const storeUrl = isAndroid ? ANDROID_STORE_URL : IOS_STORE_URL;
    const deepLink = `uchallengeme://home/${encodeURIComponent(userId)}`; // or use your universal link path

    const downloadBtn = $('#downloadBtn');
    const openBtn     = $('#openAppBtn');

    downloadBtn.href = storeUrl;



    openBtn.addEventListener('click', (e) => {
  e.preventDefault();
  window.location.href = deepLink; // open app only, no fallback
});


    // Optional: small auto-suggest to open app on iOS Safari after gesture (won't auto without gesture)
    // If you want auto-attempt after load on Android (common pattern), uncomment below:
    // if (isAndroid) { setTimeout(openAppWithFallback, 300); }
  </script>
</body>
</html>
