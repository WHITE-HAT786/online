/* --------------------------------------------------------------
   WebDialer — shared JS
   -------------------------------------------------------------- */

/* ----- Sidebar toggle ----- */
document.addEventListener('click', (e) => {
  if (e.target.closest('#sidebarToggle')) {
    const app = document.querySelector('.app');
    if (window.innerWidth <= 900) app.classList.toggle('sidebar-open');
    else                          app.classList.toggle('sidebar-collapsed');
  }
});

/* ----- Modal open/close ----- */
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

/* ----- Simple dialer keypad ----- */
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
