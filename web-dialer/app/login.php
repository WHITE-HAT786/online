<?php
require_once __DIR__ . '/../backend/config/session.php';
if (auth_check()) { header('Location: dashboard.php'); exit; }

$pageTitle    = 'Sign In';
$authSubtitle = 'WebDialer is a powerful cloud telephony solution that helps you connect, communicate and grow your business.';
include __DIR__ . '/../includes/auth_header.php';
?>

<div class="auth-card">
  <h2 class="auth-welcome">Welcome Back! <span class="wave">👋</span></h2>
  <p class="auth-card-sub">Sign in to your WebDialer account</p>

  <form id="loginForm">
    <div class="auth-field">
      <label class="auth-label" for="username">Username / Email</label>
      <div class="auth-input-wrap">
        <i class="fa-regular fa-user left-icon"></i>
        <input id="username" name="username" class="auth-input" type="text"
               placeholder="Enter your username or email" autocomplete="username" required />
      </div>
    </div>

    <div class="auth-field">
      <label class="auth-label" for="password">Password</label>
      <div class="auth-input-wrap">
        <i class="fa-solid fa-lock left-icon"></i>
        <input id="password" name="password" class="auth-input" type="password"
               placeholder="Enter your password" autocomplete="current-password" required />
        <button type="button" class="toggle-eye" onclick="togglePw(this)" aria-label="Show password">
          <i class="fa-regular fa-eye"></i>
        </button>
      </div>
    </div>

    <div class="auth-row">
      <label class="auth-remember">
        <input type="checkbox" name="remember" checked />
        <span>Remember me</span>
      </label>
      <a href="forgot-password.php" class="auth-forgot">Forgot Password?</a>
    </div>

    <button type="submit" class="auth-btn auth-btn-primary" id="loginBtn">
      <i class="fa-solid fa-right-to-bracket"></i>
      Sign In
    </button>

    <div class="auth-divider">or</div>

    <button type="button" class="auth-btn auth-btn-google">
      <svg class="g-icon" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
        <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.8 32.5 29.3 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.3 6.1 29.4 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.3-.1-2.4-.4-3.5z"/>
        <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.7 15.7 19 12 24 12c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.3 6.1 29.4 4 24 4 16.3 4 9.7 8.4 6.3 14.7z"/>
        <path fill="#4CAF50" d="M24 44c5.3 0 10.1-2 13.7-5.3l-6.3-5.3C29.3 34.9 26.8 36 24 36c-5.3 0-9.8-3.5-11.3-8.3l-6.6 5.1C9.6 39.6 16.2 44 24 44z"/>
        <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-.8 2.2-2.2 4.1-4 5.4l6.3 5.3C41.1 35.8 44 30.3 44 24c0-1.3-.1-2.4-.4-3.5z"/>
      </svg>
      Sign in with Google
    </button>

    <div class="auth-signup">
      Don't have an account?<a href="signup.php">Sign Up</a>
    </div>
  </form>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const btn = document.getElementById('loginBtn');
  btn.disabled = true;
  const orig = btn.innerHTML;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Signing in…';
  try {
    await API.post('/backend/auth/login.php', {
      username: document.getElementById('username').value,
      password: document.getElementById('password').value,
    });
    window.location.href = 'dashboard.php';
  } catch (err) {
    toast(err.message || 'Login failed', 'error');
    btn.disabled = false; btn.innerHTML = orig;
  }
});
</script>

<?php include __DIR__ . '/../includes/auth_footer.php'; ?>
