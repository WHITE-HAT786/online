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
