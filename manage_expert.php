<?php
// This page is intentionally front-end driven (token is stored in localStorage by the existing web auth flow).
// All privileged operations are enforced server-side in /ucm endpoints via Bearer auth.
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Degla • Manage Expert</title>
  <meta name="theme-color" content="#0b1220" />
  <meta name="color-scheme" content="dark light" />
  <link rel="icon" href="./icon.png" />
  <link rel="stylesheet" href="./assets/manage_expert.css" />
  <style>
<?php
// Embed CSS to avoid “no style” issues if static assets aren’t being served by the environment.
@readfile(__DIR__ . '/assets/manage_expert.css');
?>
  </style>
</head>
<body>
  <div class="container">
    <header class="topbar">
      <a class="brand" href="dashboard.html">
        <img src="./assets/icon.png" alt="Degla" />
        <div class="brandTitle">
          <strong>Manage Expert</strong>
          <span id="orgName" class="muted">Organization</span>
        </div>
      </a>
      <div class="navActions">
        <span id="expertChip" class="chip" style="display:none"></span>
        <a class="btn btnSecondary btnSmall" href="dashboard.html">Back</a>
        <button class="btn btnSecondary btnSmall" id="logoutBtn">Logout</button>
      </div>
    </header>

    <section class="pageHeader">
      <h1>Expert Control Center</h1>
      <p>Connect tools, sync context, and launch meetings. Everything here applies to your organization.</p>
      <div id="banner" class="banner" style="display:none"></div>
    </section>

    <div id="modalOverlay" class="modalOverlay" aria-hidden="true">
      <div class="modal" role="dialog" aria-modal="true" aria-label="Configure Slack experts">
        <div class="modalHeader">
          <div>
            <h3>Slack slash commands</h3>
            <p>Enable experts for Slack. Users can run <b>/sam</b>, <b>/andrej</b>, etc (configured once in your Slack app) and Degla replies as that expert.</p>
          </div>
          <button class="btn btnSecondary btnSmall" id="modalClose">Close</button>
        </div>

        <div class="modalTools">
          <span class="chip" id="expertPreview">Expert: (loading…)</span>
          <span class="chip" id="slashPreview" style="display:none"></span>
        </div>

        <div class="hint">
          One-time (you): in your Slack app settings create slash commands like <b>/sam</b>, <b>/andrej</b> (all pointing to the same Request URL). Companies do NOT need to set this up.
        </div>

        <div class="sectionTitle" style="margin-top:14px">
          <h3>Allowed channels</h3>
          <span>Choose where this expert can read context and answer</span>
        </div>
        <div class="modalTools">
          <input class="input" id="channelSearch" placeholder="Search channels… (#general, #support, …)" />
          <button class="btn btnSecondary btnSmall" id="channelsRefresh">Refresh channels</button>
          <button class="btn btnSecondary btnSmall" id="joinAllPublicChannels">Auto-join public channels</button>
          <button class="btn btnPrimary btnSmall" id="channelsSave" disabled>Save channels</button>
        </div>
        <div id="channelList" class="channelList"></div>
        <div class="hint">
          Private channels: Degla cannot auto-join them from the website. To grant access, open the private channel in Slack and add the <b>Degla</b> app (or type <b>/invite @Degla</b>). Then click <b>Refresh channels</b>.
        </div>

        <div class="sectionTitle" style="margin-top:14px">
          <h3>Slash command</h3>
          <span>Auto-generated from first name</span>
        </div>
        <div id="mappingList" class="channelList"></div>
      </div>
    </div>

    <div class="sectionTitle">
      <h3>Integrations</h3>
      <span>Connect once, then configure sources (channels, repos, pages)</span>
    </div>

    <section class="grid" aria-label="Integrations">
      <div class="card" data-provider="slack">
        <div class="cardTop">
          <div class="provider">
            <div class="logo"><img src="./slack.png" alt="Slack"/></div>
            <div class="providerMeta">
              <h2>Slack</h2>
              <p>Add the Expert bot to channels to auto-sync messages, files, and links.</p>
            </div>
          </div>
          <span class="pill disconnected" data-status><span class="dot"></span><span data-status-text>Not connected</span></span>
        </div>
        <div class="cardActions">
          <button class="btn btnPrimary btnSmall" data-action="connect">Connect</button>
          <button class="btn btnSecondary btnSmall" data-action="configure" disabled>Configure channels</button>
          <button class="btn btnDanger btnSmall" data-action="disconnect" disabled>Disconnect</button>
        </div>
      </div>

      <div class="card" data-provider="notion">
        <div class="cardTop">
          <div class="provider">
            <div class="logo"><img src="./notion.png" alt="Notion"/></div>
            <div class="providerMeta">
              <h2>Notion</h2>
              <p>Sync pages/databases into context for better answers and planning.</p>
            </div>
          </div>
          <span class="pill disconnected" data-status><span class="dot"></span><span data-status-text>Not connected</span></span>
        </div>
        <div class="cardActions">
          <button class="btn btnPrimary btnSmall" data-action="connect">Connect</button>
          <button class="btn btnSecondary btnSmall" data-action="configure" disabled>Configure pages</button>
          <button class="btn btnDanger btnSmall" data-action="disconnect" disabled>Disconnect</button>
        </div>
      </div>

      <div class="card" data-provider="github">
        <div class="cardTop">
          <div class="provider">
            <div class="logo"><img src="./github.png" alt="GitHub"/></div>
            <div class="providerMeta">
              <h2>GitHub</h2>
              <p>Add repos to context; keep code knowledge fresh with periodic sync.</p>
            </div>
          </div>
          <span class="pill disconnected" data-status><span class="dot"></span><span data-status-text>Not connected</span></span>
        </div>
        <div class="cardActions">
          <button class="btn btnPrimary btnSmall" data-action="connect">Connect</button>
          <button class="btn btnSecondary btnSmall" data-action="configure" disabled>Configure repos</button>
          <button class="btn btnDanger btnSmall" data-action="disconnect" disabled>Disconnect</button>
        </div>
      </div>
    </section>

    <div class="sectionTitle">
      <h3>Meetings</h3>
      <span>Create & join in one click</span>
    </div>

    <section class="grid" aria-label="Meetings">
      <div class="card" data-provider="google">
        <div class="cardTop">
          <div class="provider">
            <div class="logo"><img src="./google_meet.png" alt="Google Meet"/></div>
            <div class="providerMeta">
              <h2>Google Meet</h2>
              <p>Create a calendar event with a Meet link, then join instantly.</p>
            </div>
          </div>
          <span class="pill disconnected" data-status><span class="dot"></span><span data-status-text>Not connected</span></span>
        </div>
        <div class="cardActions">
          <button class="btn btnPrimary btnSmall" data-action="connect">Connect</button>
          <button class="btn btnSecondary btnSmall" data-action="create_meeting" disabled>Create & join</button>
          <button class="btn btnDanger btnSmall" data-action="disconnect" disabled>Disconnect</button>
        </div>
      </div>

      <div class="card" data-provider="zoom">
        <div class="cardTop">
          <div class="provider">
            <div class="logo"><img src="./zoom.png" alt="Zoom"/></div>
            <div class="providerMeta">
              <h2>Zoom</h2>
              <p>Create a Zoom meeting and open the join link.</p>
            </div>
          </div>
          <span class="pill disconnected" data-status><span class="dot"></span><span data-status-text>Not connected</span></span>
        </div>
        <div class="cardActions">
          <button class="btn btnPrimary btnSmall" data-action="connect">Connect</button>
          <button class="btn btnSecondary btnSmall" data-action="create_meeting" disabled>Create & join</button>
          <button class="btn btnDanger btnSmall" data-action="disconnect" disabled>Disconnect</button>
        </div>
      </div>
    </section>
  </div>

  <script>
    const API = './ucm';
    const qs = new URLSearchParams(location.search);
    const expertId = qs.get('expert_id');
    const expertName = qs.get('expert_name');

    const banner = document.getElementById('banner');
    function showBanner(kind, text){
      banner.className = `banner ${kind || ''}`;
      banner.textContent = text;
      banner.style.display = 'block';
    }
    function hideBanner(){ banner.style.display = 'none'; }

    function getToken(){
      return localStorage.getItem('degla_token') || '';
    }

    function logout(){
      localStorage.removeItem('degla_token');
      localStorage.removeItem('degla_org_name');
      location.href = 'auth-login.html';
    }
    document.getElementById('logoutBtn').addEventListener('click', logout);

    const expertChip = document.getElementById('expertChip');
    if (expertId) {
      expertChip.style.display = 'inline-flex';
      expertChip.textContent = `Expert: ${expertName ? expertName : '#' + expertId}`;
    }

    async function apiFetch(path, opts = {}) {
      const token = getToken();
      if (!token) { logout(); return; }
      const headers = Object.assign({}, opts.headers || {}, { 'Authorization': `Bearer ${token}` });
      return fetch(path, Object.assign({}, opts, { headers }));
    }

    function setCardStatus(provider, status){
      const card = document.querySelector(`.card[data-provider="${provider}"]`);
      if(!card) return;
      const pill = card.querySelector('[data-status]');
      const text = card.querySelector('[data-status-text]');
      const btnConnect = card.querySelector('[data-action="connect"]');
      const btnConfigure = card.querySelector('[data-action="configure"]');
      const btnDisconnect = card.querySelector('[data-action="disconnect"]');
      const btnCreateMeeting = card.querySelector('[data-action="create_meeting"]');

      const setPill = (cls, label) => {
        pill.className = `pill ${cls}`;
        text.textContent = label;
      };

      if(status === 'connected'){
        setPill('connected', 'Connected');
        if(btnDisconnect) btnDisconnect.disabled = false;
        if(btnConfigure) btnConfigure.disabled = false;
        if(btnCreateMeeting) btnCreateMeeting.disabled = false;
        btnConnect.textContent = 'Reconnect';
        btnConnect.classList.remove('btnPrimary');
        btnConnect.classList.add('btnSecondary');
      }else if(status === 'working'){
        setPill('working', 'Working…');
        if(btnDisconnect) btnDisconnect.disabled = true;
        if(btnConfigure) btnConfigure.disabled = true;
        if(btnCreateMeeting) btnCreateMeeting.disabled = true;
        btnConnect.disabled = true;
      }else{
        setPill('disconnected', 'Not connected');
        if(btnDisconnect) btnDisconnect.disabled = true;
        if(btnConfigure) btnConfigure.disabled = true;
        if(btnCreateMeeting) btnCreateMeeting.disabled = true;
        btnConnect.textContent = 'Connect';
        btnConnect.disabled = false;
        btnConnect.classList.add('btnPrimary');
        btnConnect.classList.remove('btnSecondary');
      }
    }

    async function loadOrgName(){
      const cached = localStorage.getItem('degla_org_name') || '';
      const el = document.getElementById('orgName');
      if(cached) el.textContent = cached;
      try{
        const res = await apiFetch(`${API}/auth_me.php`);
        if(res && res.ok){
          const me = await res.json();
          if(me && me.name){
            localStorage.setItem('degla_org_name', me.name);
            el.textContent = me.name;
          }
        }
      }catch{}
    }

    async function refreshStatuses(){
      hideBanner();
      ['slack','notion','github','google','zoom'].forEach(p => setCardStatus(p, 'working'));
      try{
        const res = await apiFetch(`${API}/integrations_status.php`);
        if(!res.ok){
          const t = await res.text();
          throw new Error(t || 'Failed to load integration status');
        }
        const data = await res.json();
        for (const p of ['slack','notion','github','google','zoom']) {
          setCardStatus(p, data?.[p]?.connected ? 'connected' : 'disconnected');
        }
      }catch(e){
        ['slack','notion','github','google','zoom'].forEach(p => setCardStatus(p, 'disconnected'));
        showBanner('error', e.message || 'Could not load status');
      }
    }

    async function beginConnect(provider){
      hideBanner();
      setCardStatus(provider, 'working');
      try{
        const res = await apiFetch(`${API}/integrations/${provider}_connect.php`, { method:'POST' });
        if(!res.ok){
          const msg = (await res.json().catch(()=>null))?.detail || 'Connect failed';
          throw new Error(msg);
        }
        const data = await res.json();
        if(!data || !data.auth_url) throw new Error('Missing auth_url');
        location.href = data.auth_url;
      }catch(e){
        setCardStatus(provider, 'disconnected');
        showBanner('error', e.message || 'Connect failed');
      }
    }

    async function disconnect(provider){
      hideBanner();
      setCardStatus(provider, 'working');
      try{
        const res = await apiFetch(`${API}/integrations_disconnect.php?provider=${encodeURIComponent(provider)}`, { method:'POST' });
        if(!res.ok){
          const msg = (await res.json().catch(()=>null))?.detail || 'Disconnect failed';
          throw new Error(msg);
        }
        showBanner('success', `${provider} disconnected`);
      }catch(e){
        showBanner('error', e.message || 'Disconnect failed');
      }finally{
        await refreshStatuses();
      }
    }

    async function createMeeting(provider){
      hideBanner();
      setCardStatus(provider, 'working');
      try{
        const res = await apiFetch(`${API}/meetings_create.php?provider=${encodeURIComponent(provider)}`, { method:'POST' });
        if(!res.ok){
          const msg = (await res.json().catch(()=>null))?.detail || 'Meeting create failed';
          throw new Error(msg);
        }
        const data = await res.json();
        if(!data || !data.join_url) throw new Error('Missing join_url');
        window.open(data.join_url, '_blank', 'noopener');
        showBanner('success', 'Meeting created. Opening join link…');
      }catch(e){
        showBanner('error', e.message || 'Meeting create failed');
      }finally{
        await refreshStatuses();
      }
    }

    document.addEventListener('click', (e) => {
      const btn = e.target.closest('button[data-action]');
      if(!btn) return;
      const card = btn.closest('.card[data-provider]');
      if(!card) return;
      const provider = card.getAttribute('data-provider');
      const action = btn.getAttribute('data-action');
      if(action === 'connect') return beginConnect(provider);
      if(action === 'disconnect') return disconnect(provider);
      if(action === 'create_meeting') return createMeeting(provider);
      if(action === 'configure') {
        if (provider === 'slack') return openSlackConfigModal();
        return showBanner('', 'Configure will be enabled once the provider is connected and endpoints are implemented.');
      }
    });

    // --- Slack Configure (per-expert; auto-enable + channel allowlist) ---
    const overlay = document.getElementById('modalOverlay');
    const closeBtn = document.getElementById('modalClose');
    const expertPreview = document.getElementById('expertPreview');
    const slashPreview = document.getElementById('slashPreview');
    const mappingList = document.getElementById('mappingList');
    const channelSearchEl = document.getElementById('channelSearch');
    const channelsRefreshBtn = document.getElementById('channelsRefresh');
    const channelsSaveBtn = document.getElementById('channelsSave');
    const channelListEl = document.getElementById('channelList');
    const joinAllBtn = document.getElementById('joinAllPublicChannels');

    let experts = [];
    let mappings = [];
    let slackChannels = [];
    let selectedChannelIds = new Set();

    const currentExpertId = parseInt(expertId || '0', 10);
    const currentExpertName = (expertName || '').trim();

    function openModal(){
      overlay.style.display = 'flex';
      overlay.setAttribute('aria-hidden', 'false');
    }
    function closeModal(){
      overlay.style.display = 'none';
      overlay.setAttribute('aria-hidden', 'true');
    }
    closeBtn.addEventListener('click', closeModal);
    overlay.addEventListener('click', (e) => { if (e.target === overlay) closeModal(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && overlay.style.display === 'flex') closeModal(); });

    function renderMappings(){
      mappingList.innerHTML = '';
      const first = currentExpertName ? currentExpertName.split(/\s+/)[0].toLowerCase().replace(/[^a-z0-9_-]/g,'') : '';
      if (!first) {
        mappingList.innerHTML = '<div class="muted">Open this page from the dashboard so we know the expert.</div>';
        return;
      }
      const row = document.createElement('div');
      row.className = 'channelRow selected';
      const meta = document.createElement('div');
      meta.className = 'channelMeta';
      const title = document.createElement('strong');
      title.textContent = `/${first}`;
      const sub = document.createElement('span');
      sub.textContent = 'Users can run this command in allowed channels to talk to this expert.';
      meta.appendChild(title);
      meta.appendChild(sub);
      row.appendChild(meta);
      mappingList.appendChild(row);
    }

    function updatePreview(){
      if (currentExpertName) {
        expertPreview.textContent = `Expert: ${currentExpertName}`;
      } else {
        expertPreview.textContent = 'Expert: (unknown)';
      }
      const first = currentExpertName ? currentExpertName.split(/\s+/)[0].toLowerCase().replace(/[^a-z0-9_-]/g,'') : '';
      if (first) {
        slashPreview.style.display = 'inline-flex';
        slashPreview.textContent = `Slash command: /${first}`;
      } else {
        slashPreview.style.display = 'none';
      }
      channelsSaveBtn.disabled = !(currentExpertId > 0);
    }

    async function loadSlackConfigData(){
      const [exRes, mapRes] = await Promise.all([
        apiFetch(`${API}/experts_list.php`),
        apiFetch(`${API}/integrations/slack_triggers_list.php`),
      ]);

      if (!exRes.ok) throw new Error('Failed to load experts');
      if (!mapRes.ok) throw new Error('Failed to load current mappings');

      experts = await exRes.json();
      mappings = await mapRes.json();

      renderMappings();
      updatePreview();
    }

    async function fetchAllSlackChannels(){
      slackChannels = [];
      let cursor = '';
      for (let i = 0; i < 10; i++) {
        const url = `${API}/integrations/slack_channels_list.php${cursor ? `?cursor=${encodeURIComponent(cursor)}` : ''}`;
        const res = await apiFetch(url);
        if(!res.ok) throw new Error((await res.json().catch(()=>null))?.detail || 'Failed to load Slack channels');
        const data = await res.json();
        slackChannels = slackChannels.concat(data.channels || []);
        cursor = data.next_cursor || '';
        if (!cursor) break;
      }
    }

    async function loadExpertSelectedChannels(){
      selectedChannelIds = new Set();
      if (!(currentExpertId > 0)) return;
      const res = await apiFetch(`${API}/integrations/slack_expert_channels_get.php?expert_id=${encodeURIComponent(currentExpertId)}`);
      if (!res.ok) return;
      const data = await res.json();
      for (const c of (data.channels || [])) {
        if (c.id) selectedChannelIds.add(c.id);
      }
    }

    function renderChannelList(){
      const q = (channelSearchEl.value || '').toLowerCase().replace(/^#/, '');
      const items = slackChannels
        .filter(c => !q || (c.name || '').toLowerCase().includes(q));

      channelListEl.innerHTML = '';
      if (items.length === 0) {
        channelListEl.innerHTML = '<div class="muted">No channels found (or bot not invited yet).</div>';
        return;
      }

      for (const c of items) {
        const row = document.createElement('label');
        row.className = 'channelRow' + (selectedChannelIds.has(c.id) ? ' selected' : '');

        const cb = document.createElement('input');
        cb.type = 'checkbox';
        cb.checked = selectedChannelIds.has(c.id);
        cb.dataset.channelId = c.id;
        cb.dataset.channelName = c.name;
        cb.disabled = !c.is_member;

        const meta = document.createElement('div');
        meta.className = 'channelMeta';
        const title = document.createElement('strong');
        title.textContent = `${c.is_private ? '🔒' : '#'}${c.name}`;
        const sub = document.createElement('span');
        sub.textContent = c.is_member
          ? 'Allow this expert to read and answer in this channel'
          : (c.is_private ? 'Invite Degla app to this private channel, then refresh' : 'Not joined yet — click Auto-join public channels');
        meta.appendChild(title);
        meta.appendChild(sub);

        row.appendChild(cb);
        row.appendChild(meta);
        channelListEl.appendChild(row);
      }
    }

    channelSearchEl.addEventListener('input', renderChannelList);
    channelListEl.addEventListener('change', (e) => {
      const cb = e.target.closest('input[type="checkbox"]');
      if (!cb) return;
      const id = cb.dataset.channelId;
      if (!id) return;
      if (cb.checked) selectedChannelIds.add(id);
      else selectedChannelIds.delete(id);
      renderChannelList();
    });

    async function openSlackConfigModal(){
      hideBanner();
      if (!(currentExpertId > 0)) {
        showBanner('error', 'Open Manage Expert from the dashboard so we know which expert to configure.');
        return;
      }
      openModal();
      mappingList.innerHTML = '<div class="muted">Loading…</div>';
      try{
        // Auto-enable Slack for this expert (no UI button).
        await apiFetch(`${API}/expert_integrations_set.php`, {
          method: 'POST',
          headers: { 'Content-Type':'application/json' },
          body: JSON.stringify({ expert_id: currentExpertId, provider: 'slack', enabled: 1 })
        });
        await loadSlackConfigData();
        channelListEl.innerHTML = '<div class="muted">Loading channels…</div>';
        await fetchAllSlackChannels();
        await loadExpertSelectedChannels();
        renderChannelList();
      }catch(e){
        mappingList.innerHTML = `<div class="muted">${e.message || 'Failed to load'}</div>`;
      }
    }

    channelsRefreshBtn.addEventListener('click', async () => {
      try{
        channelListEl.innerHTML = '<div class="muted">Loading channels…</div>';
        await fetchAllSlackChannels();
        await loadExpertSelectedChannels();
        renderChannelList();
      }catch(e){
        showBanner('error', e.message || 'Failed to refresh channels');
      }
    });

    joinAllBtn.addEventListener('click', async () => {
      joinAllBtn.disabled = true;
      try{
        showBanner('', 'Joining public channels… (private channels still need an invite)');
        const res = await apiFetch(`${API}/integrations/slack_join_public_channels.php`, { method: 'POST' });
        if(!res.ok) throw new Error((await res.json().catch(()=>null))?.detail || 'Failed to join channels');
        const data = await res.json();
        showBanner('success', `Joined ${data.joined || 0} public channel(s). Refreshing list…`);
        channelListEl.innerHTML = '<div class="muted">Refreshing…</div>';
        await fetchAllSlackChannels();
        await loadExpertSelectedChannels();
        renderChannelList();
      }catch(e){
        showBanner('error', e.message || 'Failed to auto-join channels');
      }finally{
        joinAllBtn.disabled = false;
      }
    });
    channelsSaveBtn.addEventListener('click', async () => {
      if (!(currentExpertId > 0)) return;
      const selected = slackChannels
        .filter(c => selectedChannelIds.has(c.id))
        .map(c => ({ id: c.id, name: c.name }));
      channelsSaveBtn.disabled = true;
      try{
        const res = await apiFetch(`${API}/integrations/slack_expert_channels_set.php`, {
          method: 'POST',
          headers: { 'Content-Type':'application/json' },
          body: JSON.stringify({ expert_id: currentExpertId, channels: selected })
        });
        if(!res.ok) throw new Error((await res.json().catch(()=>null))?.detail || 'Failed to save channels');
        showBanner('success', `Saved ${selected.length} channel(s) for this expert.`);
      }catch(e){
        showBanner('error', e.message || 'Failed to save channels');
      }finally{
        channelsSaveBtn.disabled = false;
      }
    });

    // banner from callback redirects (?ok=... or ?error=...)
    const ok = qs.get('ok');
    const err = qs.get('error');
    if(ok) showBanner('success', ok);
    if(err) showBanner('error', err);

    (async function init(){
      const token = getToken();
      if(!token){ logout(); return; }
      await loadOrgName();
      await refreshStatuses();
    })();
  </script>
</body>
</html>


