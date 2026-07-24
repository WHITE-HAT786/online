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
