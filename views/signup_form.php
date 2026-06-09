<?php

use App\Config;
/** @var array $errors */
/** @var array $old */
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign Up - InvoiceManager</title>
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

  /* Brand header - same as login */
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

  /* Card — exact same as login */
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

  /* Form fields — identical to login */
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

  /* Buttons — exact match */
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

  /* Error state */
  .error-msg {
    font-size: 12px;
    color: #ef4444;
    margin-top: 5px;
  }

  .field.has-error input {
    border-color: #ef4444;
    box-shadow: 0 0 0 3px rgba(239,68,68,0.12);
  }


</style>
</head>
<body>

<div class="login-wrap">

  <!-- Brand Header - Same as login -->
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
    <div class="brand-tagline">Create your free account</div>
  </div>

  <!-- Sign Up Card -->
  <div class="card">
    <p class="section-title">Create Account</p>

    <form action="signup" method="POST" id="signupForm">

      <!-- Full Name -->
      <div class="field <?php echo isset($errors['firstname']) ? 'has-error' : ''; ?>" id="field-name">
        <label for="firstname">First Name</label>
        <input type="text" id="firstname" name="firstname" placeholder="John" value="<?php echo $old['firstname'] ?? ''; ?>"   />
        <!-- <div class="error-msg">First name is required.</div> -->
         <?php
            if (isset($errors['firstname'])) {
                foreach ($errors['firstname'] as $error) {
                    echo '<div class="error-msg">' . htmlspecialchars($error) . '</div>';
                }
            }
        ?>
      </div>

      <!-- Last Name -->
      <div class="field <?php echo isset($errors['lastname']) ? 'has-error' : ''; ?>" id="field-lastname">
        <label for="lastname">Last Name</label>
        <input type="text" id="lastname" name="lastname" placeholder="Doe" value="<?php echo $old['lastname'] ?? ''; ?>"   />
        <!-- <div class="error-msg">Last name is required.</div> -->
         <?php
            if (isset($errors['lastname'])) {
                foreach ($errors['lastname'] as $error) {
                    echo '<div class="error-msg">' . htmlspecialchars($error) . '</div>';
                }
            }
        ?>
      </div>

      <!-- Email -->
      <div class="field <?php echo isset($errors['email']) ? 'has-error' : ''; ?>" id="field-email">
        <label for="email">Email address</label>
        <input type="email" id="email" name="email" placeholder="e.g. john.doe@example.com" autocomplete="email" value="<?php echo $old['email'] ?? ''; ?>" required />
        <!-- <div class="error-msg">Please enter a valid email address.</div> -->
        <?php
            if (isset($errors['email'])) {
                foreach ($errors['email'] as $error) {
                    echo '<div class="error-msg">' . htmlspecialchars($error) . '</div>';
                }
            }
        ?>
      </div>

      <!-- Password -->
      <div class="field <?php echo isset($errors ['password']) ? 'has-error' : ''; ?>" id="field-password">
        <label for="password">Password</label>
        <div class="password-wrap">
          <input type="password" id="password" name="password" placeholder="Create a strong password" autocomplete="new-password" required />
          <button class="toggle-pw" type="button" onclick="togglePw('password', 'eye-icon1')" id="pw-toggle1">
            <svg id="eye-icon1" viewBox="0 0 24 24">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
              <circle cx="12" cy="12" r="3"/>
            </svg>
          </button>
        </div>
        <!-- <div class="error-msg">Password is required (min 6 characters).</div> -->
        <?php
            if (isset($errors ['password'])) {
                foreach ($errors ['password'] as $error) {
                    echo '<div class="error-msg">' . htmlspecialchars($error) . '</div>';
                }
            } 
          ?>
      </div>

      <!-- Verify Password -->
      <div class="field <?php echo isset($errors['confirm_password']) ? 'has-error' : ''; ?>" id="field-confirm">
        <label for="confirm_password">Verify Password</label>
        <div class="password-wrap">
          <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" autocomplete="new-password" required />
          <button class="toggle-pw" type="button" onclick="togglePw('confirm_password', 'eye-icon2')" id="pw-toggle2">
            <svg id="eye-icon2" viewBox="0 0 24 24">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
              <circle cx="12" cy="12" r="3"/>
            </svg>
          </button>
        </div>
        <!-- <div class="error-msg">Passwords do not match.</div> -->
        <?php
            if (isset($errors['confirm_password'])) {
                foreach ($errors['confirm_password'] as $error) {
                    echo '<div class="error-msg">' . htmlspecialchars($error) . '</div>';
                }
            } 
          ?>
      </div>
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>" />

      <button type="submit" class="btn-primary">Create Account</button>

    </form>

    <div class="divider"><span>or</span></div>

    <div class="register-note">
      Already have an account? <a href="<?php echo Config::get('baseProjectFolder'); ?>/login">Sign in</a>
    </div>
  </div>

</div>

<script>
  // Password toggle function (supports multiple fields)
  const confirmPassword = document.getElementById('field-confirm').querySelector('input');
  const password = document.getElementById('password');

  function togglePw(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    const isHidden = input.type === 'password';

    input.type = isHidden ? 'text' : 'password';

    icon.innerHTML = isHidden 
      ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>'
      : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
  }

  // Simple client-side validation
  // document.getElementById('signupForm').addEventListener('submit', function(e) {
  //   const name = document.getElementById('name').value.trim();
  //   const email = document.getElementById('email').value.trim();
  //   const password = document.getElementById('password').value;
  //   const confirmPassword = document.getElementById('confirm_password').value;

  //   let valid = true;

  //   // Name check
  //   if (name.length < 2) {
  //     document.getElementById('field-name').classList.add('has-error');
  //     valid = false;
  //   } else {
  //     document.getElementById('field-name').classList.remove('has-error');
  //   }

  //   // Email check
  //   const emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  //   document.getElementById('field-email').classList.toggle('has-error', !emailOk);
  //   if (!emailOk) valid = false;

  //   // Password length
  //   if (password.length < 6) {
  //     document.getElementById('field-password').classList.add('has-error');
  //     valid = false;
  //   } else {
  //     document.getElementById('field-password').classList.remove('has-error');
  //   }

  //   // Password match
  //   if (password !== confirmPassword) {
  //     document.getElementById('field-confirm').classList.add('has-error');
  //     valid = false;
  //   } else {
  //     document.getElementById('field-confirm').classList.remove('has-error');
  //   }

  //   if (!valid) {
  //     e.preventDefault();
  //   }
  // });

  confirmPassword.addEventListener('input', function() {
    const confirmField = document.getElementById('field-confirm');
    const existing = confirmField.querySelector('.error-msg'); // check first

    if (password.value !== confirmPassword.value) {
        confirmField.classList.add('has-error');

        if (!existing) { // only create if not already there
            const error = document.createElement('div');
            error.classList.add('error-msg');
            error.textContent = 'Passwords do not match.';
            confirmField.appendChild(error);
        }
    } else {
        confirmField.classList.remove('has-error');

        if (existing) { // clean up when they match
            existing.remove();
        }
    }
});
</script>

</body>
</html>


