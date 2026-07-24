/* --------------------------------------------------------------
   WebDialer — shared AJAX API helper
   Usage:
     API.post('/backend/auth/login.php', { username, password })
        .then(r => …)
        .catch(err => …)
   -------------------------------------------------------------- */
window.API = (() => {
  // Determine base URL — pages live under /app/, backend under /backend/
  const base = (() => {
    // e.g. http://localhost/web-dialer/app/login.php → http://localhost/web-dialer
    const p = window.location.pathname.replace(/\/app\/.*$/, '');
    return window.location.origin + p;
  })();

  async function request(method, path, body) {
    const opts = {
      method,
      credentials: 'same-origin',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
    };
    if (body instanceof FormData) {
      opts.body = body;
    } else if (body) {
      opts.headers['Content-Type'] = 'application/json';
      opts.body = JSON.stringify(body);
    }
    const url = path.startsWith('http') ? path : base + path;
    const res = await fetch(url, opts);
    let data;
    try { data = await res.json(); } catch { data = { success: false, message: 'Invalid response' }; }
    if (!res.ok || data.success === false) {
      const err = new Error(data.message || 'Request failed');
      err.data = data.data; err.status = res.status;
      throw err;
    }
    return data;
  }

  return {
    base,
    get:  (path)          => request('GET',  path),
    post: (path, body)    => request('POST', path, body),
    del:  (path, body)    => request('POST', path, body),   // DELETE via POST
  };
})();

/* -------- tiny toast --------------------------------------- */
window.toast = (message, kind = 'info') => {
  let host = document.getElementById('toastHost');
  if (!host) {
    host = document.createElement('div');
    host.id = 'toastHost';
    host.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:8px;';
    document.body.appendChild(host);
  }
  const t = document.createElement('div');
  const bg = kind === 'error' ? '#ef4444' : kind === 'success' ? '#10b981' : '#1a56ff';
  t.style.cssText = `background:${bg};color:#fff;padding:12px 18px;border-radius:10px;font:500 14px/1.3 Inter,sans-serif;box-shadow:0 6px 20px rgba(15,27,61,.20);opacity:0;transform:translateX(20px);transition:.25s;`;
  t.textContent = message;
  host.appendChild(t);
  requestAnimationFrame(() => { t.style.opacity = '1'; t.style.transform = 'translateX(0)'; });
  setTimeout(() => { t.style.opacity = '0'; t.style.transform = 'translateX(20px)'; setTimeout(() => t.remove(), 300); }, 3200);
};
