/* --------------------------------------------------------------
   WebDialer — shared JS (auth pages + dashboard pages)
   -------------------------------------------------------------- */

/* Sidebar toggle */
document.addEventListener('click', (e) => {
  if (e.target.closest('#sidebarToggle')) {
    const app = document.querySelector('.app');
    if (!app) return;
    if (window.innerWidth <= 900) app.classList.toggle('sidebar-open');
    else                          app.classList.toggle('sidebar-collapsed');
  }
});

/* Modal open/close */
document.addEventListener('click', (e) => {
  const opener = e.target.closest('[data-modal-open]');
  if (opener) {
    const id = opener.getAttribute('data-modal-open');
    const modal = document.getElementById(id);
    if (modal) modal.classList.add('open');
  }
  if (e.target.closest('[data-modal-close]')) {
    const modal = e.target.closest('.modal');
    if (modal) modal.classList.remove('open');
  }
});
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    document.querySelectorAll('.modal.open').forEach(m => m.classList.remove('open'));
  }
});

/* Dashboard mini-dialer keypad */
document.querySelectorAll('.keypad .key').forEach(k => {
  k.addEventListener('click', () => {
    const input = document.querySelector('.dial-input input');
    if (!input) return;
    input.value += k.dataset.val || k.querySelector('.num')?.textContent || '';
  });
});
document.querySelector('.dial-input .clear')?.addEventListener('click', () => {
  const input = document.querySelector('.dial-input input');
  if (input) input.value = input.value.slice(0, -1);
});

/* Dialer page (dialer.php) — big keypad */
document.querySelectorAll('.dialpad .dp-key').forEach(k => {
  k.addEventListener('click', () => {
    const input = document.getElementById('dialpadInput');
    if (!input) return;
    input.value += k.dataset.val || '';
  });
});
document.querySelector('.dialpad-input .clear-input')?.addEventListener('click', () => {
  const input = document.getElementById('dialpadInput');
  if (input) input.value = '';
});
document.getElementById('dpClearBtn')?.addEventListener('click', () => {
  const input = document.getElementById('dialpadInput');
  if (input) input.value = input.value.slice(0, -1);
});

/* Contacts page — select-all checkbox */
document.getElementById('checkAll')?.addEventListener('change', (e) => {
  document.querySelectorAll('.contacts-table tbody .checkbox input')
    .forEach(cb => cb.checked = e.target.checked);
});

/* Reports page — tab switching (visual only) */
document.querySelectorAll('.reports-tabs .reports-tab').forEach(tab => {
  tab.addEventListener('click', () => {
    document.querySelectorAll('.reports-tabs .reports-tab').forEach(t => t.classList.remove('active'));
    tab.classList.add('active');
  });
});

/* Subscription page — Monthly/Yearly toggle */
document.querySelectorAll('.cycle-toggle .ct-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    btn.parentElement.querySelectorAll('.ct-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
  });
});

/* Settings — left nav highlight */
document.querySelectorAll('.settings-nav .settings-nav-item').forEach(item => {
  item.addEventListener('click', (e) => {
    // Only prevent default if it's an in-page anchor (starts with #)
    if (item.getAttribute('href')?.startsWith('#')) e.preventDefault();
    document.querySelectorAll('.settings-nav .settings-nav-item').forEach(i => i.classList.remove('active'));
    item.classList.add('active');
  });
});

/* Settings — theme option picker */
document.querySelectorAll('.theme-grid .theme-opt').forEach(btn => {
  btn.addEventListener('click', () => {
    btn.parentElement.querySelectorAll('.theme-opt').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
  });
});

/* Settings — primary color picker */
document.querySelectorAll('.color-grid .color-swatch').forEach(sw => {
  sw.addEventListener('click', () => {
    sw.parentElement.querySelectorAll('.color-swatch').forEach(s => {
      s.classList.remove('active');
      s.innerHTML = '';
    });
    sw.classList.add('active');
    sw.innerHTML = '<i class="fa-solid fa-check"></i>';
  });
});

/* ==========================================================
   Backend wiring — modals & forms
   ========================================================== */

/* -------- Add Contact modal (contacts.php) -------- */
(() => {
  const modal = document.getElementById('addContactModal');
  if (!modal) return;
  const saveBtn = modal.querySelector('.modal-footer .btn-primary');
  if (!saveBtn) return;
  saveBtn.addEventListener('click', async () => {
    const inputs = modal.querySelectorAll('.modal-body input, .modal-body select');
    const [firstName, lastName, company, phone, phoneType, email, group] =
      Array.from(inputs).map(i => i.value.trim());
    if (!firstName || !phone) { toast('First name and phone are required', 'error'); return; }
    try {
      await API.post('/backend/contacts/create.php', {
        first_name: firstName, last_name: lastName, company,
        phone, phone_type: phoneType || 'Mobile',
        email, group_name: group || 'Customers',
      });
      toast('Contact added', 'success');
      setTimeout(() => window.location.reload(), 600);
    } catch (err) { toast(err.message, 'error'); }
  });
})();

/* -------- Add SIP Account modal (sip-accounts.php + dashboard.php) -------- */
(() => {
  const modal = document.getElementById('addSipModal') || document.getElementById('addAccountModal');
  if (!modal) return;
  const saveBtn = modal.querySelector('.modal-footer .btn-primary');
  if (!saveBtn) return;
  saveBtn.addEventListener('click', async () => {
    const inputs = modal.querySelectorAll('.modal-body input, .modal-body select');
    if (inputs.length < 4) return;
    const [nameEl, ...rest] = inputs;
    // Simpler: match by placeholder
    const q = sel => modal.querySelector(sel);
    const name    = q('input[placeholder*="Twilio"], input[placeholder*="Account Name"]')?.value.trim() ?? nameEl.value.trim();
    const server  = q('input[placeholder*="sip.provider"], input[placeholder*="provider.com"]')?.value.trim();
    const port    = q('input[value="5060"]')?.value.trim() || 5060;
    const trans   = q('select')?.value || 'UDP';
    const caller  = q('input[placeholder*="202-555"], input[placeholder*="+1"]')?.value.trim();
    const uname   = q('input[placeholder="username"]')?.value.trim();
    const upass   = q('input[type="password"]')?.value.trim();
    const isDef   = q('input[type="checkbox"]')?.checked ? 1 : 0;
    if (!name || !uname || !upass || !server) { toast('Please fill all required fields', 'error'); return; }
    try {
      await API.post('/backend/sip/create.php', {
        account_name: name, sip_server: server, sip_port: port,
        transport: trans, caller_id: caller,
        sip_username: uname, sip_password: upass, is_default: isDef,
      });
      toast('SIP account added', 'success');
      setTimeout(() => window.location.reload(), 600);
    } catch (err) { toast(err.message, 'error'); }
  });
})();

/* -------- Delete contact rows -------- */
document.querySelectorAll('.contacts-table .row-delete').forEach(btn => {
  btn.addEventListener('click', async () => {
    if (!confirm('Delete this contact?')) return;
    const tr = btn.closest('tr');
    const idx = Array.from(tr.parentNode.children).indexOf(tr);
    // For real use, embed data-id="123" on the tr. Skipping without an id.
    if (!btn.dataset.id) { tr.remove(); return; }
    try {
      await API.post('/backend/contacts/delete.php', { id: btn.dataset.id });
      tr.remove();
      toast('Contact deleted', 'success');
    } catch (err) { toast(err.message, 'error'); }
  });
});

/* -------- Settings: profile save -------- */
(() => {
  const saveBtn = document.querySelector('.settings-card .btn-primary i.fa-floppy-disk')?.parentElement;
  if (!saveBtn) return;
  saveBtn.addEventListener('click', async (e) => {
    e.preventDefault();
    const card = saveBtn.closest('.settings-card');
    const inputs = card.querySelectorAll('input, select');
    const [fullname, email, phone, language, timezone] = Array.from(inputs).slice(0, 5).map(i => i.value);
    try {
      await API.post('/backend/auth/profile.php', { fullname, email, phone, language, timezone });
      toast('Profile updated', 'success');
    } catch (err) { toast(err.message, 'error'); }
  });
})();

/* -------- Settings: password update -------- */
(() => {
  const btn = document.querySelector('.settings-card .btn-primary i.fa-key')?.parentElement;
  if (!btn) return;
  btn.addEventListener('click', async (e) => {
    e.preventDefault();
    const card = btn.closest('.settings-card');
    const [cur, np, cp] = Array.from(card.querySelectorAll('input[type="password"]')).map(i => i.value);
    if (!cur || !np) { toast('Please fill all fields', 'error'); return; }
    try {
      await API.post('/backend/settings/password.php', {
        current_password: cur, new_password: np, confirm_password: cp,
      });
      toast('Password updated', 'success');
      card.querySelectorAll('input[type="password"]').forEach(i => i.value = '');
    } catch (err) { toast(err.message, 'error'); }
  });
})();

/* -------- Dialer: place a call -------- */
document.querySelector('.dp-call')?.addEventListener('click', async () => {
  const num = document.getElementById('dialpadInput')?.value.trim();
  if (!num) { toast('Enter a number to call', 'error'); return; }
  try {
    const r = await API.post('/backend/dialer/call.php', { to_number: num });
    toast('Calling ' + num, 'success');
    window._activeCall = r.data;
  } catch (err) { toast(err.message, 'error'); }
});
document.querySelector('.ac-btn-end')?.addEventListener('click', async () => {
  if (!window._activeCall) { toast('No active call', 'error'); return; }
  try {
    await API.post('/backend/dialer/hangup.php', { call_id: window._activeCall.call_id });
    toast('Call ended', 'success');
    window._activeCall = null;
  } catch (err) { toast(err.message, 'error'); }
});

/* Password show/hide (auth pages) */
function togglePw(btn) {
  const input = btn.parentElement.querySelector('input');
  const icon  = btn.querySelector('i');
  if (input.type === 'password') {
    input.type = 'text';
    icon.className = 'fa-regular fa-eye-slash';
  } else {
    input.type = 'password';
    icon.className = 'fa-regular fa-eye';
  }
}

/* ==========================================================
   Cryptomus subscription checkout
   ========================================================== */
document.querySelectorAll('.plan-choose').forEach(btn => {
  btn.addEventListener('click', async () => {
    const plan = btn.dataset.plan;
    const orig = btn.innerHTML;
    btn.disabled = true; btn.innerHTML = 'Redirecting…';
    try {
      const r = await API.post('/backend/subscription/checkout.php', { plan });
      if (r.data?.url) { window.location.href = r.data.url; }
      else { toast('Could not start checkout', 'error'); btn.disabled = false; btn.innerHTML = orig; }
    } catch (err) { toast(err.message, 'error'); btn.disabled = false; btn.innerHTML = orig; }
  });
});

/* ==========================================================
   Auto country flag detection from phone number
   ========================================================== */
(function () {
  const codes = [
    ['+880','🇧🇩'],['+971','🇦🇪'],['+92','🇵🇰'],['+91','🇮🇳'],['+86','🇨🇳'],['+82','🇰🇷'],['+81','🇯🇵'],
    ['+63','🇵🇭'],['+62','🇮🇩'],['+61','🇦🇺'],['+55','🇧🇷'],['+52','🇲🇽'],['+49','🇩🇪'],['+44','🇬🇧'],
    ['+39','🇮🇹'],['+34','🇪🇸'],['+33','🇫🇷'],['+27','🇿🇦'],['+7','🇷🇺'],['+1','🇺🇸'],
  ];
  const detect = num => {
    if (!num) return null;
    const clean = num.replace(/[\s()\-]/g,'');
    for (const [pfx, em] of codes) if (clean.startsWith(pfx)) return em;
    return null;
  };
  document.querySelectorAll('input[type="tel"], .dialpad-input input, #dialpadInput, input[placeholder*="Enter phone"], input[placeholder*="+1"]').forEach(inp => {
    const wrap = inp.closest('.dialpad-input-row, .phone-input-group, .phone-wrap');
    if (!wrap) return;
    let host = wrap.querySelector('.flag-emoji');
    if (!host) {
      const flagEl = wrap.querySelector('.flag, .dialpad-country .flag');
      if (!flagEl) return;
      host = document.createElement('span');
      host.className = 'flag-emoji';
      host.style.cssText = 'display:inline-block;font-size:16px;line-height:1;position:absolute;transform:translate(-2px,-2px);';
      flagEl.style.position = 'relative';
      flagEl.appendChild(host);
    }
    const update = () => {
      const em = detect(inp.value);
      host.textContent = em || '';
      host.style.display = em ? 'block' : 'none';
    };
    inp.addEventListener('input', update);
    update();
  });
})();

