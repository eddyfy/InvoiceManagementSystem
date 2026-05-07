<?php
declare(strict_types=1);
$logoutMessage = '';
if (isset($_SESSION['message'])) {
    $logoutMessage = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>InvoiceManager — Create & manage invoices the simple way</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap');

*{box-sizing:border-box;margin:0;padding:0;}

:root{
  --font-sans:'Inter',sans-serif;
  --color-text-primary:#0f172a;
  --color-text-secondary:#64748b;
  --color-bg:#f8fafc;
  --color-surface:#ffffff;
  --color-border:#e2e8f0;
  --color-border-mid:#cbd5e1;
  --color-accent:#1e2937;
  --color-accent-hover:#334155;
  --color-blue:#3b82f6;
  --radius-md:8px;
  --radius-lg:12px;
}

html,body{height:100%;}
body{
  font-family:var(--font-sans);
  background:var(--color-bg);
  color:var(--color-text-primary);
  min-height:100vh;
  display:flex;
  flex-direction:column;
}

/* ── NAV ── */
.nav{
  background:var(--color-surface);
  border-bottom:1px solid var(--color-border);
  padding:0 2.5rem;
  height:56px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  position:sticky;
  top:0;
  z-index:10;
  box-shadow:0 1px 3px rgba(0,0,0,0.04);
}
.brand{display:flex;align-items:center;gap:10px;text-decoration:none;}
.brand-logo{
  width:34px;height:34px;background:var(--color-accent);
  border-radius:8px;display:flex;align-items:center;justify-content:center;
}
.brand-logo svg{width:17px;height:17px;stroke:white;fill:none;stroke-width:1.9;stroke-linecap:round;stroke-linejoin:round;}
.brand-name{font-size:15px;font-weight:600;color:var(--color-text-primary);letter-spacing:-0.3px;}
.nav-links{display:flex;align-items:center;gap:8px;}
.nav-link{
  font-size:13.5px;font-weight:500;color:var(--color-text-secondary);
  text-decoration:none;padding:7px 14px;border-radius:var(--radius-md);
  transition:background 0.12s,color 0.12s;
}
.nav-link:hover{background:#f1f5f9;color:var(--color-text-primary);}
.nav-link.primary{
  background:var(--color-accent);color:white;
  transition:background 0.15s;
}
.nav-link.primary:hover{background:var(--color-accent-hover);}

/* ── HERO ── */
.hero{
  flex:1;
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  text-align:center;
  padding:4rem 1.5rem 3rem;
}

.hero-badge{
  display:inline-flex;align-items:center;gap:6px;
  background:var(--color-surface);border:1px solid var(--color-border);
  border-radius:99px;padding:5px 14px;
  font-size:12px;font-weight:600;color:var(--color-text-secondary);
  letter-spacing:0.3px;text-transform:uppercase;
  margin-bottom:2rem;
  box-shadow:0 1px 3px rgba(0,0,0,0.04);
}
.hero-badge-dot{
  width:6px;height:6px;border-radius:50%;
  background:#22c55e;
  box-shadow:0 0 0 2px rgba(34,197,94,0.25);
}

.hero-title{
  font-size:clamp(2rem, 5vw, 3.25rem);
  font-weight:700;
  color:var(--color-text-primary);
  letter-spacing:-1px;
  line-height:1.15;
  max-width:680px;
  margin-bottom:1.25rem;
}
.hero-title span{
  color:transparent;
  background:linear-gradient(135deg,#1e2937 0%,#3b82f6 100%);
  -webkit-background-clip:text;
  background-clip:text;
}

.hero-sub{
  font-size:clamp(15px,2vw,17px);
  color:var(--color-text-secondary);
  max-width:480px;
  line-height:1.7;
  margin-bottom:2.5rem;
}

/* ── CTAs ── */
.cta-group{
  display:flex;
  align-items:center;
  gap:12px;
  flex-wrap:wrap;
  justify-content:center;
  margin-bottom:1.5rem;
}

.btn-primary{
  display:inline-flex;align-items:center;gap:8px;
  background:var(--color-accent);color:white;
  border:none;border-radius:var(--radius-md);
  padding:12px 24px;font-size:14.5px;font-family:var(--font-sans);
  font-weight:600;cursor:pointer;text-decoration:none;
  transition:background 0.15s,transform 0.1s,box-shadow 0.15s;
  box-shadow:0 2px 8px rgba(30,41,55,0.25);
}
.btn-primary:hover{background:var(--color-accent-hover);transform:translateY(-1px);box-shadow:0 4px 14px rgba(30,41,55,0.3);}
.btn-primary:active{transform:translateY(0);}
.btn-primary svg{width:15px;height:15px;stroke:white;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;}

.btn-outline{
  display:inline-flex;align-items:center;gap:8px;
  background:var(--color-surface);color:var(--color-text-primary);
  border:1px solid var(--color-border-mid);border-radius:var(--radius-md);
  padding:12px 24px;font-size:14.5px;font-family:var(--font-sans);
  font-weight:500;cursor:pointer;text-decoration:none;
  transition:background 0.15s,transform 0.1s,border-color 0.15s;
  box-shadow:0 1px 3px rgba(0,0,0,0.05);
}
.btn-outline:hover{background:#f1f5f9;transform:translateY(-1px);border-color:#94a3b8;}
.btn-outline:active{transform:translateY(0);}
.btn-outline svg{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;}

.signup-note{
  font-size:13px;
  color:var(--color-text-secondary);
}
.signup-note a{
  color:var(--color-blue);
  text-decoration:none;
  font-weight:500;
}
.signup-note a:hover{text-decoration:underline;}

/* ── FEATURE STRIP ── */
.features{
  display:flex;
  justify-content:center;
  gap:0;
  border-top:1px solid var(--color-border);
  border-bottom:1px solid var(--color-border);
  background:var(--color-surface);
  padding:1.5rem 2rem;
  flex-wrap:wrap;
}
.feature-item{
  display:flex;align-items:center;gap:8px;
  padding:6px 24px;
  font-size:13px;font-weight:500;color:var(--color-text-secondary);
  border-right:1px solid var(--color-border);
}
.feature-item:last-child{border-right:none;}
.feature-item svg{width:15px;height:15px;stroke:#22c55e;fill:none;stroke-width:2.2;stroke-linecap:round;stroke-linejoin:round;flex-shrink:0;}

/* ── PREVIEW CARD ── */
.preview-wrap{
  display:flex;justify-content:center;
  padding:3rem 1.5rem 4rem;
}
.preview-card{
  background:var(--color-surface);
  border:1px solid var(--color-border);
  border-radius:var(--radius-lg);
  box-shadow:0 4px 24px rgba(0,0,0,0.07),0 1px 3px rgba(0,0,0,0.04);
  padding:1.5rem;
  width:100%;
  max-width:560px;
  position:relative;
  overflow:hidden;
}
.preview-card::before{
  content:'';
  position:absolute;top:0;left:0;right:0;height:3px;
  background:linear-gradient(90deg,#1e2937,#3b82f6);
}
.preview-header{
  display:flex;align-items:center;justify-content:space-between;
  margin-bottom:1.25rem;
}
.preview-title{font-size:13px;font-weight:600;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:0.5px;}
.preview-badge{
  font-size:11px;font-weight:600;padding:3px 10px;border-radius:99px;
  background:#dcfce7;color:#16a34a;
}
.preview-row{
  display:flex;align-items:center;justify-content:space-between;
  padding:10px 0;border-bottom:1px solid var(--color-border);
  font-size:13.5px;
}
.preview-row:last-child{border-bottom:none;}
.preview-row-label{color:var(--color-text-secondary);}
.preview-row-val{font-weight:600;color:var(--color-text-primary);}
.preview-row-val.big{font-size:18px;color:var(--color-accent);}

/* ── FOOTER ── */
.footer{
  border-top:1px solid var(--color-border);
  padding:1.25rem 2rem;
  text-align:center;
  font-size:12.5px;
  color:var(--color-text-secondary);
  background:var(--color-surface);
}

/* ── RESPONSIVE ── */
@media(max-width:600px){
  .nav{padding:0 1rem;}
  .nav-links .nav-link:not(.primary){display:none;}
  .hero{padding:3rem 1.25rem 2rem;}
  .features{padding:1rem;}
  .feature-item{padding:6px 12px;border-right:none;border-bottom:1px solid var(--color-border);}
  .feature-item:last-child{border-bottom:none;}
  .preview-wrap{padding:2rem 1rem 3rem;}
  .btn-primary,.btn-outline{width:100%;justify-content:center;}
  .cta-group{flex-direction:column;width:100%;max-width:320px;}
}
</style>
</head>
<body>

<!-- NAV -->
<nav class="nav">
  <a class="brand" href="#">
    <div class="brand-logo">
      <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
    </div>
    <span class="brand-name">InvoiceManager</span>
  </a>
  <div class="nav-links">
    <a class="nav-link" href="<?php echo Config::get('baseProjectFolder'); ?>/signup">Sign up</a>
    <a class="nav-link primary" href="<?php echo Config::get('baseProjectFolder');?>/login">Log in</a>
  </div>
</nav>

<!-- HERO -->
<section class="hero">
  <div class="hero-badge">
    <div class="hero-badge-dot"></div>
    Free to use · No setup required
  </div>

  <h1 class="hero-title">
    Create &amp; manage invoices<br><span>the simple way</span>
  </h1>

  <p class="hero-sub">
    Generate professional invoices in seconds, track payments, and keep your business finances organised — all in one place.
  </p>

  <div class="cta-group">
    <a href="<?php echo Config::get('baseProjectFolder'); ?>/invoice" class="btn-primary">
      <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Generate an Invoice
    </a>
    <a href="<?php echo Config::get('baseProjectFolder'); ?>/dashboard" class="btn-outline">
      <svg viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
      Log in to Dashboard
    </a>
  </div>

  <p class="signup-note">New here? <a href="<?php echo Config::get('baseProjectFolder'); ?>/signup">Create a free account</a> to save and manage your invoices.</p>
</section>

<!-- FEATURE STRIP -->
<div class="features">
  <div class="feature-item">
    <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
    Instant PDF / Print
  </div>
  <div class="feature-item">
    <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
    Tax &amp; discount support
  </div>
  <div class="feature-item">
    <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
    Track payment status
  </div>
  <div class="feature-item">
    <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
    Secure &amp; private
  </div>
</div>

<!-- PREVIEW CARD -->
<div class="preview-wrap">
  <div class="preview-card">
    <div class="preview-header">
      <span class="preview-title">Sample Invoice · INV-007</span>
      <span class="preview-badge">Paid</span>
    </div>
    <div class="preview-row">
      <span class="preview-row-label">Customer</span>
      <span class="preview-row-val">Emeka Okafor</span>
    </div>
    <div class="preview-row">
      <span class="preview-row-label">Date</span>
      <span class="preview-row-val" style="color:var(--color-text-secondary);font-weight:400">Apr 20, 2026</span>
    </div>
    <div class="preview-row">
      <span class="preview-row-label">Web Design (×1)</span>
      <span class="preview-row-val">₦120,000</span>
    </div>
    <div class="preview-row">
      <span class="preview-row-label">Branding Package (×2)</span>
      <span class="preview-row-val">₦60,000</span>
    </div>
    <div class="preview-row">
      <span class="preview-row-label" style="color:var(--color-text-primary);font-weight:600">Total</span>
      <span class="preview-row-val big">₦180,000</span>
    </div>
  </div>
</div>

<!-- FOOTER -->
<footer class="footer">
  &copy; 2026 InvoiceManager &nbsp;·&nbsp; Built by Oretade Olaoluwakitan
</footer>
<div id="toast" style="
  display:none;
  position:fixed;
  bottom:24px;
  right:24px;
  background:#1e2937;
  color:white;
  padding:12px 20px;
  border-radius:var(--radius-md);
  font-size:14px;
  box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);
  z-index:999;
  font-family:var(--font-sans);
"></div>
<script>
  function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.style.display = 'block';
    setTimeout(() => t.style.display = 'none', 2500);
  }

  <?php if ($logoutMessage): ?>
    showToast("<?php echo htmlspecialchars($logoutMessage); ?>");
    console.log("<?php echo htmlspecialchars($logoutMessage); ?>");
  <?php endif; ?>

</script>
</body>
</html>
