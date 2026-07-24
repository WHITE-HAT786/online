<?php
require_once __DIR__ . '/../backend/config/session.php';
if (auth_check()) { header('Location: dashboard.php'); exit; }

$pageTitle    = 'Create Account';
$authSubtitle = 'Create your WebDialer account and start connecting with crystal clear calls and powerful features.';
include __DIR__ . '/../includes/auth_header.php';
?>

<div class="auth-card auth-card-wide">
  <div class="auth-top-icon"><i class="fa-solid fa-user-plus"></i></div>
  <h2 class="auth-welcome">Create Your Account</h2>
  <p class="auth-card-sub">Join WebDialer and start your journey today.</p>

  <form id="signupForm">
    <div class="auth-grid-2">
      <div class="auth-field">
        <label class="auth-label" for="fullname">Full Name</label>
        <div class="auth-input-wrap">
          <i class="fa-regular fa-user left-icon"></i>
          <input id="fullname" name="fullname" class="auth-input" type="text" placeholder="Enter your full name" />
        </div>
      </div>

      <div class="auth-field">
        <label class="auth-label" for="email">Email Address</label>
        <div class="auth-input-wrap">
          <i class="fa-regular fa-envelope left-icon"></i>
          <input id="email" name="email" class="auth-input" type="email" placeholder="Enter your email address" />
        </div>
      </div>

      <div class="auth-field">
        <label class="auth-label" for="username">Username</label>
        <div class="auth-input-wrap">
          <i class="fa-regular fa-user left-icon"></i>
          <input id="username" name="username" class="auth-input" type="text" placeholder="Choose a username" />
        </div>
      </div>

      <div class="auth-field">
        <label class="auth-label" for="phone">Phone Number</label>
        <div class="phone-wrap">
          <i class="fa-solid fa-phone phone-icon"></i>
          <div class="country" title="United States">
            <span class="flag" aria-hidden="true"></span>
            <i class="fa-solid fa-caret-down caret"></i>
          </div>
          <div class="phone-code">+1</div>
          <input id="phone" name="phone" class="phone-input" type="tel" placeholder="Enter phone number" />
        </div>
      </div>
    </div>

    <div class="auth-field">
      <label class="auth-label" for="password">Password</label>
      <div class="auth-input-wrap">
        <i class="fa-solid fa-lock left-icon"></i>
        <input id="password" name="password" class="auth-input" type="password" placeholder="Create a password" />
        <button type="button" class="toggle-eye" onclick="togglePw(this)" aria-label="Show password">
          <i class="fa-regular fa-eye"></i>
        </button>
      </div>
      <div class="auth-hint">At least 8 characters with uppercase, lowercase, number &amp; symbol</div>
    </div>

    <div class="auth-field">
      <label class="auth-label" for="confirm">Confirm Password</label>
      <div class="auth-input-wrap">
        <i class="fa-solid fa-lock left-icon"></i>
        <input id="confirm" name="confirm" class="auth-input" type="password" placeholder="Confirm your password" />
        <button type="button" class="toggle-eye" onclick="togglePw(this)" aria-label="Show password">
          <i class="fa-regular fa-eye"></i>
        </button>
      </div>
    </div>

    <div class="auth-field">
      <label class="auth-label" for="timezone">Timezone</label>
      <div class="select-wrap">
        <i class="fa-solid fa-globe left-icon"></i>
        <select id="timezone" name="timezone" class="auth-select">
          <option>(GMT-05:00) Eastern Time (US &amp; Canada)</option>
          <option>(GMT-06:00) Central Time (US &amp; Canada)</option>
          <option>(GMT-07:00) Mountain Time (US &amp; Canada)</option>
          <option>(GMT-08:00) Pacific Time (US &amp; Canada)</option>
          <option>(GMT+00:00) London, Dublin, Lisbon</option>
          <option>(GMT+01:00) Berlin, Paris, Madrid</option>
          <option>(GMT+05:30) India Standard Time</option>
          <option>(GMT+08:00) Beijing, Singapore, Hong Kong</option>
          <option>(GMT+09:00) Tokyo, Seoul</option>
          <option>(GMT+10:00) Sydney, Melbourne</option>
        </select>
        <i class="fa-solid fa-chevron-down caret-icon"></i>
      </div>
    </div>

    <label class="auth-terms">
      <input type="checkbox" name="agree" checked />
      <span>
        I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
      </span>
    </label>

    <button type="submit" class="auth-btn auth-btn-primary">
      <i class="fa-solid fa-user-plus"></i>
      Create Account
    </button>

    <div class="auth-divider">or</div>

    <button type="button" class="auth-btn auth-btn-google">
      <svg class="g-icon" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
        <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.8 32.5 29.3 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.3 6.1 29.4 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.3-.1-2.4-.4-3.5z"/>
        <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.7 15.7 19 12 24 12c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.3 6.1 29.4 4 24 4 16.3 4 9.7 8.4 6.3 14.7z"/>
        <path fill="#4CAF50" d="M24 44c5.3 0 10.1-2 13.7-5.3l-6.3-5.3C29.3 34.9 26.8 36 24 36c-5.3 0-9.8-3.5-11.3-8.3l-6.6 5.1C9.6 39.6 16.2 44 24 44z"/>
        <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-.8 2.2-2.2 4.1-4 5.4l6.3 5.3C41.1 35.8 44 30.3 44 24c0-1.3-.1-2.4-.4-3.5z"/>
      </svg>
      Sign up with Google
    </button>

    <div class="auth-signup">
      Already have an account?<a href="login.php">Sign In</a>
    </div>
  </form>
</div>

<script>
document.getElementById('signupForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const f = e.target;
  const btn = f.querySelector('button[type="submit"]');
  const orig = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Creating…';
  try {
    await API.post('/backend/auth/signup.php', {
      fullname: f.fullname.value,
      email:    f.email.value,
      username: f.username.value,
      phone:    f.phone.value,
      password: f.password.value,
      timezone: f.timezone.value,
    });
    window.location.href = 'dashboard.php';
  } catch (err) {
    toast(err.message || 'Signup failed', 'error');
    btn.disabled = false; btn.innerHTML = orig;
  }
});
</script>

<?php include __DIR__ . '/../includes/auth_footer.php'; ?>
