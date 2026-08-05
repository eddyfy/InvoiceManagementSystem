<?php
declare(strict_types=1);
use App\Config;
use App\Csrf;
/** @var array $errors */
/** @var array $old */
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>InvoiceManager - Login</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');

  * { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --font-sans: 'Inter', sans-serif;
    --color-text-primary: #0f172a;
    --color-text-secondary: #64748b;
    --color-background-primary: #ffffff;
    --color-background-secondary: #f8fafc;
    --color-border-tertiary: #e2e8f0;
    --color-border-secondary: #cbd5e1;
    --border-radius-md: 8px;
    --border-radius-lg: 12px;
  }

  body {
    font-family: var(--font-sans);
    background: #f8fafc;
    color: var(--color-text-primary);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem;
  }

  .login-wrap {
    width: 100%;
    max-width: 420px;
  }

  /* Brand header */
  .brand {
    text-align: center;
    margin-bottom: 2rem;
  }

  .brand-logo {
    width: 44px;
    height: 44px;
    background: #1e2937;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
  }

  .brand-logo svg {
    width: 22px;
    height: 22px;
    fill: none;
    stroke: white;
    stroke-width: 1.8;
    stroke-linecap: round;
    stroke-linejoin: round;
  }

  .brand-name {
    font-size: 18px;
    font-weight: 600;
    color: var(--color-text-primary);
    letter-spacing: -0.3px;
  }

  .brand-tagline {
    font-size: 13px;
    color: var(--color-text-secondary);
    margin-top: 3px;
  }

  /* Card — same as invoice page */
  .card {
    background: var(--color-background-primary);
    border: 1px solid var(--color-border-tertiary);
    border-radius: var(--border-radius-lg);
    padding: 1.75rem 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  }

  .section-title {
    font-size: 12px;
    font-weight: 600;
    color: var(--color-text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 18px;
  }

  /* Form fields — same patterns as invoice page */
  .field {
    margin-bottom: 14px;
  }

  label {
    font-size: 13px;
    color: var(--color-text-secondary);
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
  }

  input[type="email"],
  input[type="password"],
  input[type="text"] {
    width: 100%;
    padding: 9px 12px;
    border: 1px solid var(--color-border-tertiary);
    border-radius: var(--border-radius-md);
    font-size: 14px;
    font-family: var(--font-sans);
    background: var(--color-background-primary);
    color: var(--color-text-primary);
    transition: border-color 0.15s, box-shadow 0.15s;
  }

  input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
  }

  /* Password wrapper */
  .password-wrap {
    position: relative;
  }

  .password-wrap input {
    padding-right: 40px;
  }

  .toggle-pw {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: var(--color-text-secondary);
    padding: 4px;
    display: flex;
    align-items: center;
  }


  .toggle-pw:hover { color: var(--color-text-primary); }
  .toggle-pw svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }

  /* Remember + forgot */
  .row-between {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    margin-top: 4px;
  }

  .remember {
    display: flex;
    align-items: center;
    gap: 7px;
    font-size: 13px;
    color: var(--color-text-secondary);
    cursor: pointer;
  }

  .remember input[type="checkbox"] {
    width: 15px;
    height: 15px;
    cursor: pointer;
    accent-color: #1e2937;
    margin: 0;
    padding: 0;
  }

  .forgot-link {
    font-size: 13px;
    color: #3b82f6;
    text-decoration: none;
    font-weight: 500;
  }

  .forgot-link:hover { text-decoration: underline; }

  /* Buttons — exact match to invoice page */
  .btn-primary {
    width: 100%;
    background: #1e2937;
    border: none;
    border-radius: var(--border-radius-md);
    padding: 10px 24px;
    font-size: 14px;
    font-family: var(--font-sans);
    cursor: pointer;
    color: white;
    font-weight: 500;
    transition: background 0.15s;
    margin-top: 17px;
  }

  .btn-primary:hover { background: #334155; }

  /* Divider */
  .divider {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 20px 0;
  }

  .divider::before,
  .divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--color-border-tertiary);
  }

  .divider span {
    font-size: 12px;
    color: var(--color-text-secondary);
    white-space: nowrap;
  }

  /* Register link footer */
  .register-note {
    text-align: center;
    margin-top: 16px;
    font-size: 13px;
    color: var(--color-text-secondary);
  }

  .register-note a {
    color: #3b82f6;
    text-decoration: none;
    font-weight: 500;
  }

  .register-note a:hover { text-decoration: underline; }

  /* Payment info pill — echoes the invoice payment-details style */
  .info-pill {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: var(--border-radius-md);
    padding: 10px 14px;
    font-size: 12.5px;
    color: var(--color-text-secondary);
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 18px;
  }

  .info-pill svg {
    flex-shrink: 0;
    width: 14px;
    height: 14px;
    stroke: var(--color-text-secondary);
    fill: none;
    stroke-width: 1.8;
    stroke-linecap: round;
    stroke-linejoin: round;
  }

  /* Error state */
  .error-msg {
    font-size: 12px;
    color: #ef4444;
    margin-top: 5px;
    /* display: none; */
  }

  .field.has-error input {
    border-color: #ef4444;
    box-shadow: 0 0 0 3px rgba(239,68,68,0.12);
  }

  /* .field.has-error .error-msg { display: block; } */

  /* Toast — same as invoice */
.toast {
    opacity: 0;
    visibility: hidden;
    position: fixed;
    bottom: 24px;
    right: 24px;
    background: #1e2937;
    color: white;
    padding: 12px 20px;
    border-radius: var(--border-radius-md);
    font-size: 14px;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    z-index: 999;
    font-family: var(--font-sans);
    transition: opacity 0.3s ease, visibility 0.3s ease;
}
</style>
</head>
<body>

<div class="login-wrap">

  <!-- Brand -->
  <div class="brand">
    <div class="brand-logo">
      <svg viewBox="0 0 24 24">
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
        <polyline points="14 2 14 8 20 8"/>
        <line x1="16" y1="13" x2="8" y2="13"/>
        <line x1="16" y1="17" x2="8" y2="17"/>
        <polyline points="10 9 9 9 8 9"/>
      </svg>
    </div>
    <div class="brand-name">InvoiceManager</div>
    <div class="brand-tagline">Sign in to your account</div>
  </div>

  <!-- Login Card -->
  <form class="card" action="<?php echo Config::get('baseProjectFolder'); ?>/login" method="POST" >
    <p class="section-title">Account login</p>

    <div class="info-pill">
      <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      Enter your credentials to access your invoices and reports.
    </div>

    <div class="field <?php echo isset($errors['email']) ? ' has-error' : ''; ?>" id="field-email">
      <label for="email">Email address</label>
      <input type="email" name="email"id="email" placeholder="e.g. john.doe@example.com" autocomplete="email"  value="<?php echo $old['email'] ?? ''; ?>" required/>
      <!-- <div class="error-msg">Please enter a valid email address.</div> -->
       <?php
        if(isset($errors['email'])){
            foreach($errors['email'] as $err){
                echo "<div class='error-msg'>{$err}</div>";
            }

        }
        ?>
    </div>

    <div class="field <?php echo isset($errors['password']) ? ' has-error' : ''; ?>" id="field-password">
      <label for="password">Password</label>
      <div class="password-wrap">
        <input type="password" name="password"id="password" placeholder="Enter your password" autocomplete="current-password" required />
        <button class="toggle-pw" type="button" onclick="togglePw()" title="Show / hide password" id="pw-toggle">
          <svg id="eye-icon" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        </button>
      </div>
      <!-- <div class="error-msg">Password is required.</div> -->
       <?php
        if(isset($errors['password'])){
            foreach($errors['password'] as $err){
                echo "<div class='error-msg'>{$err}</div>";
            }
        }
        ?>
    </div>

    <!-- <div class="row-between">
      <label class="remember">
        <input type="checkbox" id="remember" />
        Remember me
      </label>
      <a href="#" class="forgot-link">Forgot password?</a>
    </div> -->
    <input type="hidden" name="csrf_token" value="<?php echo Csrf::token(); ?>" />
    <button class="btn-primary" type="submit">Sign in</button>

    <div class="divider"><span>or</span></div>

    <div class="register-note">
      Don't have an account? <a href="<?php echo Config::get('baseProjectFolder'); ?>/signup">Create one</a>
    </div>
  </form>

</div>

<div class="toast" id="toast"></div>



<script>
  function togglePw() {
    const input = document.getElementById('password');
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    document.getElementById('eye-icon').innerHTML = isHidden
      ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>'
      : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
  }

 function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.style.opacity = '1';
    t.style.visibility = 'visible';
    setTimeout(() => {
        t.style.opacity = '0';
        t.style.visibility = 'hidden';
    }, 5000);
}

  // Example toast on page load
//   function handleLogin() {
//     const email = document.getElementById('email').value.trim();
//     const pw = document.getElementById('password').value;
//     const emailField = document.getElementById('field-email');
//     const pwField = document.getElementById('field-password');

//     let valid = true;
//     const emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

//     emailField.classList.toggle('has-error', !emailOk);
//     pwField.classList.toggle('has-error', pw.length === 0);
//     if (!emailOk || pw.length === 0) return;

//     showToast('Logging in…');
//   }

  // Allow Enter key
//   document.addEventListener('keydown', e => { if (e.key === 'Enter') handleLogin(); });
</script>
<?php if (isset($_SESSION['message'])): ?>
    <script>
        showToast('<?= $_SESSION['message'] ?>');
    </script>
    <?php unset($_SESSION['message']); ?>
<?php else: ?>
    <script>
        showToast('Welcome back! Please log in to continue.');
    </script>
<?php endif; ?>
</body>
</html>
