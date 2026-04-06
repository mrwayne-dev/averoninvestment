/* ============================================================
   Averon Investment — main.js
   Global: loader, toast, modals, apiRequest, formatters,
           page observers, carousel, TradingView, newsletter
   ============================================================ */

// ─── Averon Loader ───────────────────────────────────────────
let _loaderInitialized = false;

function _initLoader() {
  if (_loaderInitialized) return;
  const inner = document.querySelector('#global-loader .loader-inner');
  if (!inner) return;
  inner.innerHTML = `
    <div class="loader-symbol-wrap">
      <svg class="loader-ring" viewBox="0 0 88 88" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <circle class="loader-ring-track" cx="44" cy="44" r="40"/>
        <circle class="loader-ring-arc"   cx="44" cy="44" r="40"/>
      </svg>
      <img src="/assets/images/logo/avernonlogo.png" alt="" aria-hidden="true" style="height:40px;width:auto;animation:logoPulse 1.5s ease-in-out infinite;">
    </div>
  `;
  _loaderInitialized = true;
}

function showLoader() {
  _initLoader();
  const el = document.getElementById('global-loader');
  if (el) el.style.display = 'flex';
}

function hideLoader() {
  const el = document.getElementById('global-loader');
  if (el) el.style.display = 'none';
}

// ─── Toast System ────────────────────────────────────────────
function showToast(message, type = 'success') {
  const container = document.getElementById('toast-container');
  if (!container) return;

  const colors = { success: '#002914', error: '#C47A2B', warning: '#F5A623', info: '#73BA9B' };
  const color  = colors[type] || colors.info;

  const toast = document.createElement('div');
  toast.className = 'toast-item';
  toast.style.cssText = [
    'background:var(--bg-elevated)',
    'color:var(--text-primary)',
    `border-left:3px solid ${color}`,
    'padding:12px 20px',
    'border-radius:var(--radius-md)',
    'box-shadow:var(--shadow-lg)',
    'min-width:280px',
    'max-width:360px',
    'font-size:var(--text-sm)',
    'font-family:var(--font-sans)',
    'line-height:1.5',
    'cursor:pointer',
    'animation:slideIn 0.3s ease',
    'border:1px solid var(--border-color)',
  ].join(';');

  toast.textContent = message;
  toast.addEventListener('click', () => toast.remove());
  container.appendChild(toast);

  setTimeout(() => {
    toast.style.animation = 'slideOut 0.3s ease forwards';
    toast.addEventListener('animationend', () => toast.remove(), { once: true });
  }, 4000);
}

// ─── Modal System ────────────────────────────────────────────
// Registry: modal-id → data-injection function
const _modalDataHandlers = {};

function registerModalHandler(id, fn) {
  _modalDataHandlers[id] = fn;
}

/**
 * openModal(id, data = {})
 * Opens the modal with the given id and optionally injects data
 * (e.g. pre-selected plan, balance, etc.) via a registered handler.
 */
function openModal(id, data = {}) {
  const overlay = document.getElementById(id);
  if (!overlay) return;
  overlay.style.display = 'flex';
  overlay.getBoundingClientRect(); // force reflow for animation
  overlay.classList.add('active');
  document.body.style.overflow = 'hidden';
  if (_modalDataHandlers[id]) {
    _modalDataHandlers[id](data);
  }
}

function closeModal(id) {
  const overlay = document.getElementById(id);
  if (!overlay) return;
  overlay.classList.remove('active');
  // Clear the inline display style set by openModal() so the CSS
  // display:none on .modal-overlay takes effect again.
  overlay.style.display = '';
  if (!document.querySelector('.modal-overlay.active')) {
    document.body.style.overflow = '';
  }
}

// Close on overlay backdrop click
document.addEventListener('click', (e) => {
  if (e.target.classList.contains('modal-overlay')) {
    closeModal(e.target.id);
  }
});

// Close on ESC key
document.addEventListener('keydown', (e) => {
  if (e.key !== 'Escape') return;
  const active = document.querySelector('.modal-overlay.active');
  if (active) closeModal(active.id);
});

// ─── CSRF Token helper ───────────────────────────────────────
function getCsrfToken() {
  return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

// ─── API Request ─────────────────────────────────────────────
async function apiRequest(endpoint, method = 'GET', body = null) {
  showLoader();
  try {
    const opts = {
      method,
      headers: {
        'Content-Type':     'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-Token':     getCsrfToken(),
      },
      credentials: 'same-origin',
    };
    if (body) opts.body = JSON.stringify(body);

    const res  = await fetch(endpoint, opts);
    const data = await res.json();

    if (!data.success) {
      throw new Error(data.message || 'An error occurred');
    }
    return data;
  } catch (err) {
    showToast(err.message, 'error');
    throw err;
  } finally {
    hideLoader();
  }
}

// ─── Form Helper ─────────────────────────────────────────────
function formToJSON(formElement) {
  const result = {};
  for (const [key, value] of new FormData(formElement).entries()) {
    result[key] = value;
  }
  return result;
}

// ─── Formatters ──────────────────────────────────────────────
function formatCurrency(amount, currency = 'USD') {
  return new Intl.NumberFormat('en-US', {
    style:                 'currency',
    currency,
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(parseFloat(amount) || 0);
}

function formatDate(dateString) {
  if (!dateString) return '—';
  const d = new Date(dateString);
  if (isNaN(d.getTime())) return String(dateString);
  return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: '2-digit' });
}

function timeAgo(dateString) {
  if (!dateString) return '—';
  const seconds = Math.floor((Date.now() - new Date(dateString).getTime()) / 1000);
  if (seconds < 60)   return 'Just now';
  const m = Math.floor(seconds / 60);
  if (m < 60)         return `${m} minute${m !== 1 ? 's' : ''} ago`;
  const h = Math.floor(m / 60);
  if (h < 24)         return `${h} hour${h !== 1 ? 's' : ''} ago`;
  const d = Math.floor(h / 24);
  if (d < 7)          return `${d} day${d !== 1 ? 's' : ''} ago`;
  const w = Math.floor(d / 7);
  if (w < 4)          return `${w} week${w !== 1 ? 's' : ''} ago`;
  const mo = Math.floor(d / 30);
  if (mo < 12)        return `${mo} month${mo !== 1 ? 's' : ''} ago`;
  const yr = Math.floor(d / 365);
  return `${yr} year${yr !== 1 ? 's' : ''} ago`;
}

// ─── Bottom Sheet Drag-to-Close (mobile) ─────────────────────
function initBottomSheetDrag() {
  document.querySelectorAll('.bottom-sheet').forEach((sheet) => {
    const handle = sheet.querySelector('.bottom-sheet-handle');
    if (!handle) return;

    let startY   = 0;
    let dragging = false;

    handle.addEventListener('touchstart', (e) => {
      startY   = e.touches[0].clientY;
      dragging = true;
      sheet.style.transition = 'none';
    }, { passive: true });

    handle.addEventListener('touchmove', (e) => {
      if (!dragging) return;
      const dy = e.touches[0].clientY - startY;
      if (dy > 0) sheet.style.transform = `translateY(${dy}px)`;
    }, { passive: true });

    handle.addEventListener('touchend', (e) => {
      if (!dragging) return;
      dragging = false;
      sheet.style.transition = '';
      const dy = e.changedTouches[0].clientY - startY;
      if (dy > 100) {
        const overlay = sheet.closest('.modal-overlay');
        if (overlay) closeModal(overlay.id);
      }
      sheet.style.transform = '';
    });
  });
}

// ─── Scroll Fade-in + Stagger Observers ──────────────────────
function initPageObservers() {
  // Fade-in sections
  const fadeObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        fadeObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.08 });

  document.querySelectorAll('.fade-in').forEach((el) => fadeObserver.observe(el));

  // Stagger cards
  const staggerObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const siblings = entry.target.parentElement.querySelectorAll('.stagger-item');
        siblings.forEach((item, idx) => {
          setTimeout(() => item.classList.add('visible'), idx * 100);
        });
        staggerObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  document.querySelectorAll('.stagger-item').forEach((el) => {
    const parent = el.parentElement;
    if (!parent.dataset.staggerObserved) {
      parent.dataset.staggerObserved = '1';
      staggerObserver.observe(el);
    }
  });
}

// ─── Stats Counter Animation ─────────────────────────────────
function initStatsCounters() {
  function animateCounter(el) {
    const target    = parseFloat(el.dataset.target);
    const suffix    = el.dataset.suffix || '';
    const prefix    = el.dataset.prefix || '';
    const isDecimal = String(target).includes('.');
    const duration  = 1800;
    let   start     = null;

    function step(ts) {
      if (!start) start = ts;
      const progress = Math.min((ts - start) / duration, 1);
      const ease     = 1 - Math.pow(1 - progress, 3);
      const value    = target * ease;
      el.textContent = prefix + (isDecimal ? value.toFixed(1) : Math.floor(value).toLocaleString()) + suffix;
      if (progress < 1) {
        requestAnimationFrame(step);
      } else {
        el.textContent = prefix + (isDecimal ? target.toFixed(1) : target.toLocaleString()) + suffix;
      }
    }
    requestAnimationFrame(step);
  }

  const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        animateCounter(entry.target);
        counterObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.5 });

  document.querySelectorAll('[data-target]').forEach((el) => counterObserver.observe(el));
}

// ─── TradingView Widget ───────────────────────────────────────
function initTradingViewChart() {
  const container = document.getElementById('tsla-chart');
  if (!container || typeof TradingView === 'undefined') return;

  new TradingView.widget({
    container_id:      'tsla-chart',
    symbol:            'NASDAQ:TSLA',
    interval:          'D',
    timezone:          'Etc/UTC',
    theme:             'light',
    style:             '1',
    locale:            'en',
    toolbar_bg:        '#f7f7f7',
    enable_publishing: false,
    hide_top_toolbar:  false,
    save_image:        false,
    height:            '100%',
    width:             '100%',
  });
}

// ─── Testimonials Carousel ────────────────────────────────────
function initTestimonialsCarousel() {
  const track   = document.getElementById('testimonials-track');
  const prevBtn = document.getElementById('testimonial-prev');
  const nextBtn = document.getElementById('testimonial-next');
  const dotsWrap = document.getElementById('testimonials-dots');
  if (!track || !prevBtn || !nextBtn) return;

  const cards = track.querySelectorAll('.testimonial-card');
  const total = cards.length;
  let current = 0;

  function isDesktop() {
    return window.innerWidth >= 768;
  }

  function goTo(idx) {
    current = ((idx % total) + total) % total;

    // Slide via transform on all screen sizes
    track.style.transform = `translateX(-${current * 100}%)`;

    // Update dot indicators
    if (dotsWrap) {
      dotsWrap.querySelectorAll('.testimonial-dot').forEach((dot, i) => {
        dot.classList.toggle('active', i === current);
      });
    }
  }

  prevBtn.addEventListener('click', () => goTo(current - 1));
  nextBtn.addEventListener('click', () => goTo(current + 1));

  if (dotsWrap) {
    dotsWrap.querySelectorAll('.testimonial-dot').forEach((dot, i) => {
      dot.addEventListener('click', () => goTo(i));
    });
  }

  // Touch swipe support
  let touchStartX = 0;
  track.addEventListener('touchstart', (e) => {
    touchStartX = e.touches[0].clientX;
  }, { passive: true });

  track.addEventListener('touchend', (e) => {
    const dx = e.changedTouches[0].clientX - touchStartX;
    if (Math.abs(dx) > 50) goTo(dx < 0 ? current + 1 : current - 1);
  }, { passive: true });

  // Recalculate on resize (handles orientation changes)
  window.addEventListener('resize', () => goTo(current));
}

// ─── Footer Newsletter Form ───────────────────────────────────
function initNewsletterForm() {
  const form = document.getElementById('footer-newsletter-form');
  const msg  = document.getElementById('footer-newsletter-msg');
  if (!form) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const email = form.querySelector('input[name="email"]').value.trim();
    if (!email) return;

    try {
      await apiRequest('/api/utilities/contact.php', 'POST', { type: 'newsletter', email });
      if (msg) {
        msg.textContent   = "You're subscribed!";
        msg.style.color   = 'var(--color-success)';
      }
      form.reset();
    } catch (_) {
      if (msg) {
        msg.textContent = 'Could not subscribe. Please try again.';
        msg.style.color = 'var(--color-danger)';
      }
    }
  });
}

// ─── Password Toggle ─────────────────────────────────────────
function initPasswordToggles() {
  const EYE_OPEN = `<svg viewBox="0 0 256 256" fill="currentColor" width="18" height="18" aria-hidden="true"><path d="M247.31,124.76c-.35-.79-8.82-19.58-27.65-38.41C194.57,61.26,162.88,48,128,48S61.43,61.26,36.34,86.35C17.51,105.18,9,124,8.69,124.76a8,8,0,0,0,0,6.5c.35.79,8.82,19.57,27.65,38.4C61.43,194.74,93.12,208,128,208s66.57-13.26,91.66-38.34c18.83-18.83,27.3-37.61,27.65-38.4A8,8,0,0,0,247.31,124.76ZM128,192c-30.78,0-57.67-11.19-79.93-33.25A133.47,133.47,0,0,1,25,128,133.33,133.33,0,0,1,48.07,97.25C70.33,75.19,97.22,64,128,64s57.67,11.19,79.93,33.25A133.46,133.46,0,0,1,231.05,128C223.84,141.46,192.43,192,128,192Zm0-112a48,48,0,1,0,48,48A48.05,48.05,0,0,0,128,80Zm0,80a32,32,0,1,1,32-32A32,32,0,0,1,128,160Z"/></svg>`;
  const EYE_SHUT = `<svg viewBox="0 0 256 256" fill="currentColor" width="18" height="18" aria-hidden="true"><path d="M228,175a8,8,0,0,1-10.92-3l-19-33.2A123.23,123.23,0,0,1,162,155.46l5.87,35.22a8,8,0,0,1-6.58,9.21A8.4,8.4,0,0,1,160,200a8,8,0,0,1-7.88-6.69L146.41,159c-5.94.84-12,1-18.41,1s-12.47-.16-18.41-1l-5.71,34.31A8,8,0,0,1,96,200a8.4,8.4,0,0,1-1.32-.11,8,8,0,0,1-6.58-9.21L94,155.46a123.23,123.23,0,0,1-36.06-16.69L39,172A8,8,0,1,1,25.06,164l20-35a153.47,153.47,0,0,1-19.16-18.43,8,8,0,1,1,12.25-10.29C58.81,125.33,90,144,128,144s69.19-18.67,89.85-43.72a8,8,0,1,1,12.25,10.29A153.47,153.47,0,0,1,211,129l20,35A8,8,0,0,1,228,175Z"/></svg>`;

  document.querySelectorAll('.password-toggle').forEach((btn) => {
    // Set initial icon (eye = show password)
    btn.innerHTML = EYE_OPEN;

    btn.addEventListener('click', () => {
      const wrap  = btn.closest('.password-wrap');
      const input = wrap?.querySelector('input');
      if (!input) return;

      const isHidden = input.type === 'password';
      input.type = isHidden ? 'text' : 'password';
      btn.innerHTML = isHidden ? EYE_SHUT : EYE_OPEN;
      btn.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
    });
  });
}

// ─── Global keyframe styles ───────────────────────────────────
(function injectToastStyles() {
  if (document.getElementById('toast-keyframes')) return;
  const style = document.createElement('style');
  style.id = 'toast-keyframes';
  style.textContent = `
    @keyframes slideIn  { from { opacity:0; transform:translateY(-10px) } to { opacity:1; transform:translateY(0) } }
    @keyframes slideOut { from { opacity:1; transform:translateY(0)      } to { opacity:0; transform:translateY(-10px) } }
  `;
  document.head.appendChild(style);
})();

// ─── 3-Step Signup Form ───────────────────────────────────
function initSignupForm() {
  const form = document.getElementById('signup-form');
  if (!form) return;

  const stepPanels = [
    document.getElementById('signup-step-1'),
    document.getElementById('signup-step-2'),
    document.getElementById('signup-step-3'),
  ];
  const stepItems = document.querySelectorAll('.steps .step-item');
  let currentStep = 0;

  function setStep(idx) {
    stepPanels.forEach((panel, i) => {
      if (!panel) return;
      panel.classList.toggle('hidden', i !== idx);
    });
    stepItems.forEach((el, i) => {
      el.classList.remove('active', 'done');
      if (i < idx)   el.classList.add('done');
      if (i === idx) el.classList.add('active');
    });
    currentStep = idx;
  }

  document.getElementById('signup-next-1')?.addEventListener('click', async () => {
    const firstName = form.querySelector('[name="first_name"]').value.trim();
    const lastName  = form.querySelector('[name="last_name"]').value.trim();
    const region    = form.querySelector('[name="region"]').value.trim();
    const language  = form.querySelector('[name="language"]').value.trim();

    if (!firstName) { showToast('First name is required', 'error'); return; }
    if (!lastName)  { showToast('Last name is required', 'error'); return; }
    if (!region)    { showToast('Please select your region', 'error'); return; }
    if (!language)  { showToast('Please select your language', 'error'); return; }

    try {
      await apiRequest('/api/auth/user-register.php?step=1', 'POST', {
        first_name: firstName, last_name: lastName, region, language,
      });
      setStep(1);
    } catch (_) {}
  });

  document.getElementById('signup-next-2')?.addEventListener('click', async () => {
    const email    = form.querySelector('[name="email"]').value.trim();
    const password = form.querySelector('[name="password"]').value;
    const confirm  = form.querySelector('[name="confirm_password"]').value;

    if (!email)               { showToast('Email is required', 'error'); return; }
    if (!password)            { showToast('Password is required', 'error'); return; }
    if (password !== confirm) { showToast('Passwords do not match', 'error'); return; }

    try {
      const res = await apiRequest('/api/auth/user-register.php?step=2', 'POST', {
        email, password, confirm_password: confirm,
      });
      const hint = document.getElementById('signup-email-hint');
      if (hint) hint.textContent = email;

      setStep(2);
    } catch (_) {}
  });

  document.getElementById('signup-verify-btn')?.addEventListener('click', async () => {
    const code = form.querySelector('[name="code"]').value.trim();
    if (!code || code.length !== 6) { showToast('Enter the 6-digit code sent to your email', 'error'); return; }
    try {
      const res = await apiRequest('/api/auth/user-register.php?step=3', 'POST', { code });
      showToast('Account verified! Redirecting…', 'success');
      setTimeout(() => { window.location.href = res.data?.redirect || '/dashboard'; }, 1000);
    } catch (_) {}
  });

  document.getElementById('signup-resend')?.addEventListener('click', async () => {
    const email    = form.querySelector('[name="email"]').value.trim();
    const password = form.querySelector('[name="password"]').value;
    const confirm  = form.querySelector('[name="confirm_password"]').value;
    if (!email || !password) return;
    try {
      const res = await apiRequest('/api/auth/user-register.php?step=2', 'POST', {
        email, password, confirm_password: confirm,
      });
      showToast('Verification code resent to ' + email, 'success');
    } catch (_) {}
  });

  document.getElementById('signup-back-2')?.addEventListener('click', () => setStep(0));
  document.getElementById('signup-back-3')?.addEventListener('click', () => setStep(1));

  setStep(0);
}


// ─── Forgot / Reset Password Forms ───────────────────────
function initForgotPasswordForm() {
  const emailForm = document.getElementById('forgot-email-form');
  if (emailForm) {
    emailForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const email = emailForm.querySelector('[name="email"]').value.trim();
      if (!email) { showToast('Email is required', 'error'); return; }
      try {
        const res = await apiRequest('/api/auth/user-forgot-pass.php', 'POST', { email });
        showToast(res.message, 'success');
        const card = emailForm.closest('.auth-card');
        if (card) {
          card.innerHTML = `
            <div class="auth-logo"><img src="/assets/images/logo/avernologo-dark.png" alt="Averon Investment" style="height:28px;width:auto;"></div>
            <h1 class="auth-title">Check Your Email</h1>
            <p class="auth-subtitle">If <strong>${email}</strong> is registered, a reset link has been sent. Check your inbox and spam folder.</p>
            <a href="/login" class="btn btn-primary btn-full" style="margin-top:var(--space-6)">Back to Sign In</a>
          `;
        }
      } catch (_) {}
    });
  }

  const resetForm = document.getElementById('reset-password-form');
  if (resetForm) {
    resetForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const token    = resetForm.querySelector('[name="token"]').value;
      const password = resetForm.querySelector('[name="password"]').value;
      const confirm  = resetForm.querySelector('[name="confirm_password"]').value;
      if (!password)            { showToast('Password is required', 'error'); return; }
      if (password !== confirm) { showToast('Passwords do not match', 'error'); return; }
      try {
        const res = await apiRequest('/api/auth/user-reset-pass.php', 'POST', { token, password, confirm_password: confirm });
        showToast(res.message, 'success');
        setTimeout(() => { window.location.href = '/login'; }, 1500);
      } catch (_) {}
    });
  }
}


// ─── Standalone Email Verify Form ────────────────────────
function initVerifyEmailForm() {
  const form = document.getElementById('verify-email-form');
  if (!form) return;
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const code = form.querySelector('[name="code"]').value.trim();
    if (!code) { showToast('Please enter the 6-digit code', 'error'); return; }
    try {
      const res = await apiRequest('/api/auth/user-verify-email.php', 'POST', { code });
      showToast('Email verified! Redirecting...', 'success');
      setTimeout(() => { window.location.href = res.data?.redirect || '/dashboard'; }, 1000);
    } catch (_) {}
  });
}


// ─── Login Form ───────────────────────────────────────────────
function initLoginForm() {
  const form = document.getElementById('login-form');
  if (!form) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const email    = form.querySelector('[name="email"]')?.value.trim() || '';
    const password = form.querySelector('[name="password"]')?.value     || '';

    if (!email)    { showToast('Email is required', 'error');    return; }
    if (!password) { showToast('Password is required', 'error'); return; }

    const btn = document.getElementById('login-btn');

    try {
      if (btn) { btn.disabled = true; btn.textContent = 'Signing in...'; }

      const res = await apiRequest('/api/auth/user-login.php', 'POST', { email, password });

      showToast('Login successful! Redirecting...', 'success');
      setTimeout(() => {
        window.location.href = res.data?.redirect || '/dashboard';
      }, 800);
    } catch (_) {
      // apiRequest already showed the error toast
      if (btn) { btn.disabled = false; btn.textContent = 'Sign In'; }
    }
  });
}


// ─── Header Scroll Glassmorphism ─────────────────────────────
function initHeaderScroll() {
  const header = document.getElementById('site-header');
  if (!header) return;

  function onScroll() {
    header.classList.toggle('scrolled', window.scrollY > 20);
  }

  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll(); // apply immediately in case page is pre-scrolled
}

// ─── Mobile Nav (hamburger) ───────────────────────────────────
function initMobileNav() {
  const hamburger = document.getElementById('nav-hamburger');
  const closeBtn  = document.getElementById('mobile-nav-close');
  const nav       = document.getElementById('mobile-nav');
  const overlay   = document.getElementById('mobile-nav-overlay');
  if (!hamburger || !nav || !overlay) return;

  function openNav() {
    nav.classList.add('open');
    overlay.classList.add('open');
    hamburger.classList.add('open');
    hamburger.setAttribute('aria-expanded', 'true');
    nav.setAttribute('aria-hidden', 'false');
    overlay.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }

  function closeNav() {
    nav.classList.remove('open');
    overlay.classList.remove('open');
    hamburger.classList.remove('open');
    hamburger.setAttribute('aria-expanded', 'false');
    nav.setAttribute('aria-hidden', 'true');
    overlay.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  hamburger.addEventListener('click', openNav);
  if (closeBtn) closeBtn.addEventListener('click', closeNav);
  overlay.addEventListener('click', closeNav);

  // Close on ESC key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && nav.classList.contains('open')) closeNav();
  });

  // Close when a nav link is clicked (SPA-friendly)
  nav.querySelectorAll('.mobile-nav-link').forEach((link) => {
    link.addEventListener('click', closeNav);
  });
}

// ─── DOMContentLoaded — Init All ─────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  initHeaderScroll();
  initMobileNav();
  initPasswordToggles();
  initBottomSheetDrag();
  initPageObservers();
  initStatsCounters();
  initTradingViewChart();
  initTestimonialsCarousel();
  initNewsletterForm();
  initLoginForm();
  initSignupForm();
  initForgotPasswordForm();
  initVerifyEmailForm();
  // Dashboard modals (safe to call on any page; each guard-checks its root element)
  initDepositModal();
  initWithdrawModal();
  initInvestmentModal();
  initMembershipModal();
  initTransferModal();
  initNotificationsPanel();
  initDeleteAccountModal();
});


/* ============================================================
   DASHBOARD MODAL SYSTEM
   All functions below handle dashboard modal logic.
   Each function guard-checks its root element and exits
   immediately if the modal is not present on the page.
   ============================================================ */

// ─── Shared: Copy-to-clipboard helper ────────────────────────
function copyToClipboard(text) {
  if (navigator.clipboard && window.isSecureContext) {
    return navigator.clipboard.writeText(text).then(() => {
      showToast('Copied to clipboard!', 'success');
    });
  }
  // Fallback for non-secure contexts
  const el = document.createElement('textarea');
  el.value = text;
  el.style.position = 'fixed';
  el.style.opacity  = '0';
  document.body.appendChild(el);
  el.focus();
  el.select();
  try {
    document.execCommand('copy');
    showToast('Copied to clipboard!', 'success');
  } catch (_) {
    showToast('Could not copy — please copy manually.', 'error');
  }
  document.body.removeChild(el);
}

// ─── Shared: Add N days to today, return formatted string ─────
function addDays(days) {
  const d = new Date();
  d.setDate(d.getDate() + days);
  return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: '2-digit' });
}


/* ────────────────────────────────────────────────────────────
   DEPOSIT MODAL
   Flow: amount + currency → create-payment.php → invoice_url
         → window.location.href redirect to NowPayments
         → user pays on NowPayments UI
         → NowPayments webhook confirms → balance credited
         → user returned to wallet.php?deposit=success
   ──────────────────────────────────────────────────────────── */

function initDepositModal() {
  const overlay = document.getElementById('modal-deposit');
  if (!overlay) return;

  const stage1        = document.getElementById('deposit-stage-1');
  const stage2        = document.getElementById('deposit-stage-2');
  const generateBtn   = document.getElementById('deposit-generate-btn');
  const cryptoOptions = overlay.querySelectorAll('.crypto-option');

  // Crypto selector — toggle selected class on label click
  cryptoOptions.forEach((option) => {
    option.addEventListener('click', () => {
      cryptoOptions.forEach((o) => o.classList.remove('selected'));
      option.classList.add('selected');
    });
  });

  // ── Generate payment address → redirect to NowPayments ──────────
  generateBtn.addEventListener('click', async () => {
    const amountInput = document.getElementById('deposit-amount');
    const amount      = parseFloat(amountInput.value);

    if (!amount || amount < 50) {
      showToast('Minimum deposit is $50', 'error');
      amountInput.classList.add('is-error');
      return;
    }
    if (amount > 500000) {
      showToast('Maximum deposit is $500,000', 'error');
      amountInput.classList.add('is-error');
      return;
    }
    amountInput.classList.remove('is-error');

    const currency = overlay.querySelector('input[name="deposit_currency"]:checked')?.value || 'BTC';

    try {
      const res = await apiRequest('/api/payments/create-payment.php', 'POST', {
        amount_usd: amount,
        currency,
      });

      // Show redirect stage briefly so the user sees something before leaving
      if (stage1) stage1.classList.add('hidden');
      if (stage2) stage2.classList.remove('hidden');

      // Redirect to NowPayments hosted invoice page
      window.location.href = res.data.invoice_url;

    } catch (_) {
      // apiRequest already shows a toast; reset form state
      amountInput.classList.add('is-error');
    }
  });

  // Reset modal stages when closed via overlay or close button
  overlay.addEventListener('click', (e) => {
    if (e.target === overlay) _resetDepositModal();
  });
  overlay.querySelectorAll('.modal-close').forEach((btn) => {
    btn.addEventListener('click', _resetDepositModal);
  });
}

function _resetDepositModal() {
  const stage1 = document.getElementById('deposit-stage-1');
  const stage2 = document.getElementById('deposit-stage-2');
  if (stage1) stage1.classList.remove('hidden');
  if (stage2) stage2.classList.add('hidden');
  const amountInput = document.getElementById('deposit-amount');
  if (amountInput) { amountInput.value = ''; amountInput.classList.remove('is-error'); }
}

// ── Handle return from NowPayments (success_url / cancel_url) ────────
// Fires on any dashboard page that includes main.js.
// wallet.php?deposit=success&order_id=DEP-xxx → poll until confirmed
// wallet.php?deposit=cancelled                → warning toast
(function _handleDepositReturn() {
  const params  = new URLSearchParams(window.location.search);
  const status  = params.get('deposit');
  const orderId = params.get('order_id');
  if (!status) return;

  // Clean the URL without a page reload
  window.history.replaceState({}, '', window.location.pathname);

  if (status === 'cancelled') {
    showToast('Deposit cancelled. You can try again whenever you\'re ready.', 'warning');
    return;
  }

  if (status !== 'success') return;

  // ── Poll check-payment.php until confirmed or timeout ──────────────
  // NowPayments may have fired the IPN already, or the DB was updated
  // manually — check-payment.php will call confirmDeposit() if needed.
  if (!orderId) {
    showToast('Payment submitted! Your balance will update once confirmed.', 'info');
    return;
  }

  showToast('Payment submitted — confirming your deposit…', 'info');

  const MAX_ATTEMPTS  = 24;   // 24 × 5 s = 2 minutes total
  const POLL_INTERVAL = 5000; // 5 seconds

  let attempts = 0;
  const pollTimer = setInterval(async () => {
    attempts++;
    try {
      const res = await fetch(
        '/api/payments/check-payment.php?order_id=' + encodeURIComponent(orderId),
        { headers: { 'X-Requested-With': 'XMLHttpRequest' } }
      );
      const data = await res.json();

      if (data.success && data.data && data.data.confirmed) {
        clearInterval(pollTimer);
        showToast('Deposit confirmed! Your balance has been updated.', 'success');
        // Refresh balance on the wallet page if the function exists
        if (typeof refreshWalletBalance === 'function') {
          refreshWalletBalance();
        } else {
          // Fallback: reload the page after a short delay so the user
          // sees the success toast before the numbers update
          setTimeout(() => window.location.reload(), 1500);
        }
        return;
      }
    } catch (_) {
      // Silent fail — keep polling
    }

    if (attempts >= MAX_ATTEMPTS) {
      clearInterval(pollTimer);
      showToast(
        'Deposit is still processing. Check back shortly — your balance will update once confirmed.',
        'warning'
      );
    }
  }, POLL_INTERVAL);
}());


/* ────────────────────────────────────────────────────────────
   MODAL WRAPPERS — inject live balance at open time
   ──────────────────────────────────────────────────────────── */

/**
 * Open withdraw modal pre-filled with the user's current wallet balance.
 * Falls back to reading the displayed balance from the DOM if _lastKnownBalance
 * (from dashboard.js polling) is not available.
 */
function openWithdrawModal() {
  const balance = (typeof _lastKnownBalance !== 'undefined' && _lastKnownBalance > 0)
    ? _lastKnownBalance
    : parseFloat(document.querySelector('[data-wallet-balance]')?.textContent?.replace(/[^0-9.]/g, '') || 0);
  openModal('modal-withdraw', { balance });
}

/**
 * Open transfer modal pre-filled with the user's current wallet balance.
 */
function openTransferModal() {
  const balance = (typeof _lastKnownBalance !== 'undefined' && _lastKnownBalance > 0)
    ? _lastKnownBalance
    : parseFloat(document.querySelector('[data-wallet-balance]')?.textContent?.replace(/[^0-9.]/g, '') || 0);
  openModal('modal-transfer', { balance });
}


/* ────────────────────────────────────────────────────────────
   WITHDRAW MODAL
   ──────────────────────────────────────────────────────────── */
function initWithdrawModal() {
  const overlay = document.getElementById('modal-withdraw');
  if (!overlay) return;

  const amountInput   = document.getElementById('withdraw-amount');
  const maxBtn        = document.getElementById('withdraw-max-btn');
  const feeDisplay    = document.getElementById('withdraw-fee-display');
  const receiveDisplay = document.getElementById('withdraw-receive-display');
  const submitBtn     = document.getElementById('withdraw-submit-btn');
  const addressInput  = document.getElementById('withdraw-wallet-address');
  const amountError   = document.getElementById('withdraw-amount-error');
  const addressError  = document.getElementById('withdraw-address-error');

  const FEE_RATE = 0.015;

  // Data handler: inject balance and membership speed when modal opens
  registerModalHandler('modal-withdraw', (data) => {
    const balanceEl = document.getElementById('withdraw-available-balance');
    if (balanceEl) balanceEl.textContent = formatCurrency(data.balance || 0);
    const speedEl = document.getElementById('withdraw-speed-label');
    if (speedEl && data.withdrawalSpeedHours) {
      const h = parseInt(data.withdrawalSpeedHours, 10);
      speedEl.textContent = h === 1 ? 'Within 1 hour' : `Up to ${h} hours`;
    }
    if (amountInput) amountInput.max = data.balance || 0;
    _updateWithdrawFee();
  });

  // Live fee calculation
  function _updateWithdrawFee() {
    const amount = parseFloat(amountInput?.value) || 0;
    const fee    = amount * FEE_RATE;
    const net    = Math.max(0, amount - fee);
    if (feeDisplay)     feeDisplay.textContent     = formatCurrency(fee);
    if (receiveDisplay) receiveDisplay.textContent = formatCurrency(net);
  }

  if (amountInput) amountInput.addEventListener('input', _updateWithdrawFee);

  // Max button
  if (maxBtn) {
    maxBtn.addEventListener('click', () => {
      const max = parseFloat(amountInput?.max) || 0;
      if (amountInput) { amountInput.value = max.toFixed(2); _updateWithdrawFee(); }
    });
  }

  // Submit
  if (submitBtn) {
    submitBtn.addEventListener('click', async () => {
      let valid = true;

      const amount  = parseFloat(amountInput?.value) || 0;
      const address = addressInput?.value.trim() || '';
      const currency = overlay.querySelector('#withdraw-currency')?.value || 'BTC';

      if (amountError) amountError.classList.add('hidden');
      if (addressError) addressError.classList.add('hidden');

      if (amount < 50) {
        valid = false;
        if (amountError) { amountError.textContent = 'Minimum withdrawal is $50.'; amountError.classList.remove('hidden'); }
        amountInput?.classList.add('is-error');
      } else {
        amountInput?.classList.remove('is-error');
      }

      if (!address) {
        valid = false;
        if (addressError) { addressError.textContent = 'Wallet address is required.'; addressError.classList.remove('hidden'); }
        addressInput?.classList.add('is-error');
      } else {
        addressInput?.classList.remove('is-error');
      }

      if (!valid) return;

      try {
        const res = await apiRequest('/api/user-dashboard/create-withdrawal.php', 'POST', {
          amount,
          currency,
          wallet_address: address,
        });
        showToast(res.message || 'Withdrawal request submitted.', 'success');
        closeModal('modal-withdraw');
        if (typeof refreshWalletBalance === 'function') refreshWalletBalance();
      } catch (_) { /* toast shown by apiRequest */ }
    });
  }
}


/* ────────────────────────────────────────────────────────────
   START INVESTMENT MODAL
   ──────────────────────────────────────────────────────────── */
let _investPlans = [];

function initInvestmentModal() {
  const overlay = document.getElementById('modal-start-investment');
  if (!overlay) return;

  const planSelect   = document.getElementById('invest-plan-select');
  const amountInput  = document.getElementById('invest-amount');
  const amountHint   = document.getElementById('invest-amount-hint');
  const amountError  = document.getElementById('invest-amount-error');
  const calcBox      = document.getElementById('invest-calculator');
  const submitBtn    = document.getElementById('invest-submit-btn');
  const formBody     = document.getElementById('invest-form-body');
  const formFooter   = document.getElementById('invest-form-footer');
  const limitSection = document.getElementById('invest-limit-exceeded');
  const upgradeBtn   = document.getElementById('invest-upgrade-btn');

  upgradeBtn?.addEventListener('click', () => {
    closeModal('modal-start-investment');
    openModal('modal-enroll-membership');
  });

  // Data handler: open with optional pre-selected plan
  registerModalHandler('modal-start-investment', async (data) => {
    // Hide limit warning by default, show form
    limitSection?.classList.add('hidden');
    formBody?.classList.remove('hidden');
    if (formFooter) formFooter.style.display = '';

    // Load plans if not yet loaded
    if (_investPlans.length === 0) {
      await _loadInvestmentPlans();
    }

    // Pre-select plan if planId passed
    if (data.planId && planSelect) {
      planSelect.value = String(data.planId);
      _onPlanChange();
    }

    // Check membership investment limit
    if (data.activeCount !== undefined && data.maxInvestments !== null &&
        data.activeCount >= data.maxInvestments) {
      limitSection?.classList.remove('hidden');
      formBody?.classList.add('hidden');
      if (formFooter) formFooter.style.display = 'none';
    }
  });

  async function _loadInvestmentPlans() {
    try {
      const res = await apiRequest('/api/user-dashboard/get-plans.php');
      _investPlans = res.data?.plans || [];

      if (!planSelect) return;
      planSelect.innerHTML = '<option value="">— Select a plan —</option>';
      _investPlans.forEach((plan) => {
        const opt = document.createElement('option');
        opt.value       = plan.id;
        opt.textContent = `${plan.name} ($${Number(plan.min_amount).toLocaleString()}–${plan.max_amount ? '$' + Number(plan.max_amount).toLocaleString() : '∞'})`;
        planSelect.appendChild(opt);
      });
    } catch (_) { /* toast shown by apiRequest */ }
  }

  function _getPlan(id) {
    return _investPlans.find((p) => String(p.id) === String(id)) || null;
  }

  function _renderPlanCard(plan) {
    const card = document.getElementById('invest-plan-card-inner');
    if (!card) return;
    if (!plan) { card.innerHTML = ''; return; }

    const accent = plan.color_accent || 'var(--color-primary)';
    card.innerHTML = `
      <div class="invest-plan-badge" style="border-left-color:${accent}">${plan.badge_label || ''}</div>
      <div class="invest-plan-details">
        <div class="invest-plan-detail-row">
          <span>Duration</span>
          <strong>${plan.duration_days} days</strong>
        </div>
        <div class="invest-plan-detail-row">
          <span>Daily Yield</span>
          <strong>${plan.daily_yield_min}%–${plan.daily_yield_max}%</strong>
        </div>
        <div class="invest-plan-detail-row">
          <span>Total Return</span>
          <strong>${plan.total_yield_min}%–${plan.total_yield_max}%</strong>
        </div>
        <div class="invest-plan-detail-row">
          <span>Compounding</span>
          <strong>${plan.compounding_type}</strong>
        </div>
      </div>
    `;
    document.getElementById('invest-plan-card')?.style.setProperty('border-left-color', accent);
  }

  function _updateCalculator() {
    const planId = planSelect?.value;
    const amount = parseFloat(amountInput?.value) || 0;
    const plan   = _getPlan(planId);

    if (!plan || amount <= 0) {
      calcBox?.classList.add('hidden');
      if (submitBtn) submitBtn.disabled = true;
      return;
    }

    const dailyMin = amount * (plan.daily_yield_min / 100);
    const dailyMax = amount * (plan.daily_yield_max / 100);
    const totalMin = amount * (plan.total_yield_min / 100);
    const totalMax = amount * (plan.total_yield_max / 100);
    const profitDate  = addDays(plan.profit_withdrawal_after_days || plan.duration_days);
    const maturityDate = addDays(plan.duration_days);

    document.getElementById('invest-calc-daily').textContent  = `${formatCurrency(dailyMin)} – ${formatCurrency(dailyMax)}`;
    document.getElementById('invest-calc-total').textContent  = `${formatCurrency(totalMin)} – ${formatCurrency(totalMax)}`;
    document.getElementById('invest-calc-profit-date').textContent = profitDate;
    document.getElementById('invest-calc-maturity').textContent    = maturityDate;

    calcBox?.classList.remove('hidden');

    // Validate range
    const min = parseFloat(plan.min_amount) || 0;
    const max = plan.max_amount ? parseFloat(plan.max_amount) : Infinity;
    const withinRange = amount >= min && amount <= max;
    if (submitBtn) submitBtn.disabled = !withinRange;
    if (amountError) {
      if (!withinRange) {
        amountError.textContent = plan.max_amount
          ? `Amount must be between ${formatCurrency(min)} and ${formatCurrency(max)}.`
          : `Minimum amount is ${formatCurrency(min)}.`;
        amountError.classList.remove('hidden');
      } else {
        amountError.classList.add('hidden');
      }
    }
  }

  function _onPlanChange() {
    const plan = _getPlan(planSelect?.value);
    _renderPlanCard(plan);
    if (plan && amountHint) {
      const max = plan.max_amount ? `$${Number(plan.max_amount).toLocaleString()}` : 'unlimited';
      amountHint.textContent = `Min: $${Number(plan.min_amount).toLocaleString()} · Max: ${max}`;
      if (amountInput) {
        amountInput.min = plan.min_amount;
        amountInput.max = plan.max_amount || '';
        amountInput.placeholder = `Min $${Number(plan.min_amount).toLocaleString()}`;
      }
    }
    _updateCalculator();
  }

  planSelect?.addEventListener('change', _onPlanChange);
  amountInput?.addEventListener('input', _updateCalculator);

  // Submit
  submitBtn?.addEventListener('click', async () => {
    const planId = planSelect?.value;
    const amount = parseFloat(amountInput?.value) || 0;
    const plan   = _getPlan(planId);
    if (!plan || !planId) { showToast('Please select a plan.', 'error'); return; }

    try {
      const res = await apiRequest('/api/user-dashboard/start-investment.php', 'POST', {
        plan_id: planId,
        amount,
      });
      showToast(res.message || 'Investment started!', 'success');
      closeModal('modal-start-investment');
      if (typeof refreshWalletBalance === 'function') refreshWalletBalance();
    } catch (_) { /* toast shown by apiRequest */ }
  });
}


/* ────────────────────────────────────────────────────────────
   ENROLL MEMBERSHIP MODAL
   ──────────────────────────────────────────────────────────── */
let _membershipCurrentPlan = null;

function initMembershipModal() {
  const overlay        = document.getElementById('modal-enroll-membership');
  if (!overlay) return;

  const payBtn          = document.getElementById('membership-pay-btn');
  const depositFirstBtn = document.getElementById('membership-deposit-first-btn');
  const upgradeBtn      = document.getElementById('membership-upgrade-btn');
  const manageCloseBtn  = document.getElementById('membership-manage-close-btn');
  const titleEl         = document.getElementById('membership-modal-title');

  function _setEnrollMode() {
    payBtn?.classList.remove('hidden');
    depositFirstBtn?.classList.remove('hidden');
    upgradeBtn?.classList.add('hidden');
    manageCloseBtn?.classList.add('hidden');
    if (titleEl) titleEl.textContent = 'Enroll Membership';
    document.getElementById('membership-balance-check')?.classList.remove('hidden');
  }

  function _setManageMode() {
    payBtn?.classList.add('hidden');
    depositFirstBtn?.classList.add('hidden');
    upgradeBtn?.classList.remove('hidden');
    manageCloseBtn?.classList.remove('hidden');
    if (titleEl) titleEl.textContent = 'Manage Membership';
    document.getElementById('membership-balance-check')?.classList.add('hidden');
    document.getElementById('membership-insufficient-alert')?.classList.add('hidden');
  }

  // Data handler: inject plan + wallet balance
  registerModalHandler('modal-enroll-membership', (data) => {
    _membershipCurrentPlan = data.plan || null;
    const plan          = _membershipCurrentPlan;
    const balance       = parseFloat(data.balance || 0);
    const price         = parseFloat(plan?.price  || 0);
    const shortfall     = price - balance;
    const isCurrentPlan = !!data.isCurrentPlan;

    // Plan header
    const nameEl  = document.getElementById('membership-plan-name');
    const priceEl = document.getElementById('membership-plan-price');
    const badgeEl = document.getElementById('membership-plan-badge');
    if (nameEl)  nameEl.textContent  = plan?.name || '—';
    if (priceEl) priceEl.textContent = formatCurrency(price) + '/mo';

    // Badge icon
    const iconMap = { user: '👤', medal: '🥈', crown: '👑', diamond: '💎' };
    if (badgeEl) badgeEl.textContent = iconMap[plan?.badge_icon] || '';

    // Benefits list
    const benefitsList = document.getElementById('membership-plan-benefits');
    if (benefitsList && Array.isArray(data.benefits)) {
      benefitsList.innerHTML = data.benefits.map(
        (b) => `<li class="membership-benefit-item">${b}</li>`
      ).join('');
    }

    // Meta fields
    const speedEl  = document.getElementById('membership-withdrawal-speed');
    const maxInvEl = document.getElementById('membership-max-investments');
    if (speedEl) {
      const h = parseInt(plan?.withdrawal_speed_hours || 72, 10);
      speedEl.textContent = h === 1 ? 'Within 1 hour' : `${h}h`;
    }
    if (maxInvEl) {
      maxInvEl.textContent = plan?.max_active_investments != null
        ? String(plan.max_active_investments)
        : 'Unlimited';
    }

    if (isCurrentPlan) {
      // Manage mode: plan is already active — offer upgrade navigation
      _setManageMode();
    } else {
      // Enroll mode: purchasing a new / different plan
      _setEnrollMode();

      const walletBalEl = document.getElementById('membership-wallet-balance');
      const planCostEl  = document.getElementById('membership-plan-cost');
      const payLabelEl  = document.getElementById('membership-pay-label');
      if (walletBalEl) walletBalEl.textContent = formatCurrency(balance);
      if (planCostEl)  planCostEl.textContent  = formatCurrency(price);
      if (payLabelEl)  payLabelEl.textContent  = formatCurrency(price);

      const insufficientAlert = document.getElementById('membership-insufficient-alert');
      const shortfallEl       = document.getElementById('membership-shortfall');
      if (shortfall > 0) {
        insufficientAlert?.classList.remove('hidden');
        if (shortfallEl) shortfallEl.textContent = formatCurrency(shortfall);
        if (payBtn) payBtn.disabled = true;
      } else {
        insufficientAlert?.classList.add('hidden');
        if (payBtn) payBtn.disabled = false;
      }
    }
  });

  // Pay from wallet (enroll mode)
  payBtn?.addEventListener('click', async () => {
    if (!_membershipCurrentPlan) return;
    try {
      const res = await apiRequest('/api/user-dashboard/enroll-membership.php', 'POST', {
        plan_id: _membershipCurrentPlan.id,
      });
      showToast(res.message || 'Membership activated!', 'success');
      closeModal('modal-enroll-membership');
      if (typeof refreshWalletBalance === 'function') refreshWalletBalance();
    } catch (_) { /* toast shown by apiRequest */ }
  });

  // Deposit first (enroll mode)
  depositFirstBtn?.addEventListener('click', () => {
    closeModal('modal-enroll-membership');
    openModal('modal-deposit');
  });

  // Upgrade plan (manage mode) → membership page to choose a higher tier
  upgradeBtn?.addEventListener('click', () => {
    closeModal('modal-enroll-membership');
    window.location.href = '/dashboard/membership';
  });
}



/* ────────────────────────────────────────────────────────────
   DASHBOARD POLLING (wallet balance + notifications)
   Call startDashboardPolling() from dashboard pages.
   ──────────────────────────────────────────────────────────── */
let _dashboardPollingTimer = null;

function startDashboardPolling() {
  async function poll() {
    try {
      const data = await apiRequest('/api/user-dashboard/get-overview.php');
      if (typeof updateWalletUI         === 'function') updateWalletUI(data.data?.wallet);
      if (typeof updateNotificationBadge === 'function') updateNotificationBadge(data.data?.notifications_count);
    } catch (_) { /* silent — polling continues */ }
  }

  poll(); // immediate first call
  _dashboardPollingTimer = setInterval(poll, 30000);
}

function stopDashboardPolling() {
  clearInterval(_dashboardPollingTimer);
}

/**
 * refreshWalletBalance()
 * Immediately fetches the latest overview and updates all dashboard UI sections.
 * Called after any action that changes balance (deposit, withdraw, invest, membership).
 */
async function refreshWalletBalance() {
  try {
    const data = await apiRequest('/api/user-dashboard/get-overview.php');
    const d = data.data || {};
    if (typeof updateWalletUI === 'function' && d.wallet)
      updateWalletUI(d.wallet);
    if (typeof renderRecentTransactions === 'function' && Array.isArray(d.recent_transactions))
      renderRecentTransactions(d.recent_transactions);
    if (typeof renderDashboardInvestments === 'function' && Array.isArray(d.active_investments))
      renderDashboardInvestments(d.active_investments);
  } catch (_) { /* silent */ }
}


/* ────────────────────────────────────────────────────────────
   TRANSFER MODAL
   Wallet-to-wallet transfer between platform users.
   POST → /api/user-dashboard/transfer.php
   ──────────────────────────────────────────────────────────── */

function initTransferModal() {
  const overlay = document.getElementById('modal-transfer');
  if (!overlay) return;

  const recipientInput = overlay.querySelector('#transfer-recipient');
  const amountInput    = overlay.querySelector('#transfer-amount');
  const submitBtn      = overlay.querySelector('#transfer-submit-btn');
  const summary        = overlay.querySelector('#transfer-summary');
  const summaryAmt     = overlay.querySelector('#transfer-summary-amount');
  const summaryRecip   = overlay.querySelector('#transfer-summary-recipient');
  const recipErr       = overlay.querySelector('#transfer-recipient-error');
  const amtErr         = overlay.querySelector('#transfer-amount-error');

  // Register handler to inject available balance when modal opens
  registerModalHandler('modal-transfer', (data) => {
    if (data?.balance !== undefined) {
      const balEl = overlay.querySelector('#transfer-available-balance');
      if (balEl) balEl.textContent = formatCurrency(data.balance);
    }
    // Reset form
    if (recipientInput) recipientInput.value = '';
    if (amountInput)    amountInput.value    = '';
    if (summary)        summary.classList.add('hidden');
    clearError(recipErr);
    clearError(amtErr);
  });

  function updateSummary() {
    const amt   = parseFloat(amountInput?.value || 0);
    const recip = recipientInput?.value?.trim() || '';
    if (amt >= 1 && recip) {
      if (summaryAmt)   summaryAmt.textContent   = formatCurrency(amt);
      if (summaryRecip) summaryRecip.textContent  = recip;
      summary?.classList.remove('hidden');
    } else {
      summary?.classList.add('hidden');
    }
  }

  recipientInput?.addEventListener('input', updateSummary);
  amountInput?.addEventListener('input', updateSummary);

  function clearError(el) { if (el) { el.textContent = ''; el.classList.add('hidden'); } }
  function showError(el, msg) { if (el) { el.textContent = msg; el.classList.remove('hidden'); } }

  submitBtn?.addEventListener('click', async () => {
    clearError(recipErr);
    clearError(amtErr);

    const recipient = recipientInput?.value?.trim() || '';
    const amount    = parseFloat(amountInput?.value || 0);
    let valid = true;

    if (!recipient || !recipient.includes('@')) {
      showError(recipErr, 'Please enter a valid email address.');
      valid = false;
    }
    if (!amount || amount < 1) {
      showError(amtErr, 'Minimum transfer amount is $1.00.');
      valid = false;
    }
    if (!valid) return;

    try {
      withLoadingButton(submitBtn, async () => {
        await apiRequest('/api/user-dashboard/transfer.php', 'POST', {
          recipient_email: recipient,
          amount,
        });
        showToast('Transfer successful!', 'success');
        closeModal('modal-transfer');
        refreshWalletBalance();
      });
    } catch (_) { /* toast shown by apiRequest */ }
  });
}


/* ────────────────────────────────────────────────────────────
   NOTIFICATIONS PANEL
   Right-side slide-in panel. Opened via #notif-btn in topbar.
   POST → /api/user-dashboard/get-notifications.php
   POST → /api/user-dashboard/mark-notification-read.php
   ──────────────────────────────────────────────────────────── */

function initNotificationsPanel() {
  const panel    = document.getElementById('notifications-panel');
  if (!panel) return;

  const backdrop  = document.getElementById('notif-panel-backdrop');
  const closeBtn  = document.getElementById('notif-panel-close-btn');
  const markAllBtn= document.getElementById('notif-panel-mark-all');
  const list      = document.getElementById('notif-panel-list');
  const notifBtn  = document.getElementById('notif-btn');   // topbar bell

  function openPanel() {
    panel.classList.add('open');
    document.body.classList.add('notif-panel-open');
    loadNotifications();
  }

  function closePanel() {
    panel.classList.remove('open');
    document.body.classList.remove('notif-panel-open');
  }

  notifBtn?.addEventListener('click', (e) => {
    e.stopPropagation();
    // Close any open topbar dropdown first
    document.querySelectorAll('.topbar-dropdown.active').forEach(d => d.classList.remove('active'));
    openPanel();
  });

  backdrop?.addEventListener('click', closePanel);
  closeBtn?.addEventListener('click', closePanel);

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && panel.classList.contains('open')) closePanel();
  });

  async function loadNotifications() {
    if (!list) return;
    list.innerHTML = '<p class="notif-panel-loading">Loading…</p>';
    try {
      const data = await apiRequest('/api/user-dashboard/get-notifications.php');
      const notifs = data.data?.notifications ?? [];
      renderNotifList(notifs);
      // Update badge
      if (typeof updateNotificationBadge === 'function') {
        updateNotificationBadge(notifs.filter(n => !n.is_read).length);
      }
    } catch (_) {
      if (list) list.innerHTML = '<p class="notif-panel-empty">Failed to load notifications.</p>';
    }
  }

  function renderNotifList(notifs) {
    if (!list) return;
    if (!notifs.length) {
      list.innerHTML = '<p class="notif-panel-empty">You have no notifications.</p>';
      return;
    }
    list.innerHTML = notifs.map(n => `
      <div class="notif-panel-item${n.is_read ? '' : ' unread'}" data-id="${n.id}">
        <div class="notif-panel-item-header">
          <span class="notif-panel-item-title">${escapeHtml(n.title)}</span>
          ${!n.is_read ? `<button class="notif-panel-mark-read-btn" data-id="${n.id}" title="Mark as read">✓</button>` : ''}
        </div>
        <p class="notif-panel-item-msg">${escapeHtml(n.message)}</p>
        <span class="notif-panel-item-time">${n.time_ago ?? ''}</span>
      </div>
    `).join('');

    // Bind individual mark-read buttons
    list.querySelectorAll('.notif-panel-mark-read-btn').forEach(btn => {
      btn.addEventListener('click', async (e) => {
        e.stopPropagation();
        const id = btn.dataset.id;
        try {
          await apiRequest('/api/user-dashboard/mark-notification-read.php', 'POST', { id });
          const item = list.querySelector(`.notif-panel-item[data-id="${id}"]`);
          if (item) { item.classList.remove('unread'); btn.remove(); }
          // Update badge count
          const unread = list.querySelectorAll('.notif-panel-item.unread').length;
          if (typeof updateNotificationBadge === 'function') updateNotificationBadge(unread);
        } catch (_) { /* toast shown */ }
      });
    });
  }

  markAllBtn?.addEventListener('click', async () => {
    try {
      await apiRequest('/api/user-dashboard/mark-notification-read.php', 'POST', { all: true });
      list.querySelectorAll('.notif-panel-item').forEach(el => {
        el.classList.remove('unread');
        el.querySelector('.notif-panel-mark-read-btn')?.remove();
      });
      if (typeof updateNotificationBadge === 'function') updateNotificationBadge(0);
    } catch (_) { /* toast shown */ }
  });
}

// Simple HTML-escape helper (used by notification panel renderer)
function escapeHtml(str) {
  return String(str ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}


/* ────────────────────────────────────────────────────────────
   DELETE ACCOUNT MODAL  (2-step flow)
   Step 1: Type "DELETE" exactly to enable Next button
   Step 2: Enter current password → submit request
   POST → /api/user-dashboard/delete-account-request.php
   ──────────────────────────────────────────────────────────── */

function initDeleteAccountModal() {
  const overlay = document.getElementById('modal-delete-account');
  if (!overlay) return;

  const step1      = overlay.querySelector('#delete-step-1');
  const step2      = overlay.querySelector('#delete-step-2');
  const confirmTxt = overlay.querySelector('#delete-confirm-text');
  const nextBtn    = overlay.querySelector('#delete-next-btn');
  const backBtn    = overlay.querySelector('#delete-back-btn');
  const submitBtn  = overlay.querySelector('#delete-submit-btn');
  const pwInput    = overlay.querySelector('#delete-password');
  const pwError    = overlay.querySelector('#delete-password-error');

  function resetModal() {
    if (confirmTxt) confirmTxt.value = '';
    if (pwInput)    pwInput.value    = '';
    if (pwError)    { pwError.textContent = ''; pwError.classList.add('hidden'); }
    if (nextBtn)    { nextBtn.disabled = true; nextBtn.setAttribute('aria-disabled', 'true'); }
    showStep(1);
  }

  function showStep(n) {
    step1?.classList.toggle('hidden', n !== 1);
    step2?.classList.toggle('hidden', n !== 2);
  }

  // Guard-check: only show Step 2 when "DELETE" typed exactly
  confirmTxt?.addEventListener('input', () => {
    const ok = confirmTxt.value === 'DELETE';
    if (nextBtn) {
      nextBtn.disabled = !ok;
      nextBtn.setAttribute('aria-disabled', String(!ok));
    }
  });

  nextBtn?.addEventListener('click', () => {
    if (confirmTxt?.value === 'DELETE') showStep(2);
  });

  backBtn?.addEventListener('click', () => showStep(1));

  // Reset on close
  overlay.addEventListener('click', (e) => {
    if (e.target === overlay) resetModal();
  });
  overlay.querySelectorAll('.modal-close').forEach(btn => {
    btn.addEventListener('click', resetModal);
  });

  submitBtn?.addEventListener('click', async () => {
    const password = pwInput?.value ?? '';
    if (!password) {
      if (pwError) { pwError.textContent = 'Password is required.'; pwError.classList.remove('hidden'); }
      return;
    }
    if (pwError) { pwError.textContent = ''; pwError.classList.add('hidden'); }

    try {
      await withLoadingButton(submitBtn, async () => {
        await apiRequest('/api/user-dashboard/delete-account-request.php', 'POST', { password });
        showToast('Deletion request submitted. We\'ll process it within 72 hours.', 'info');
        closeModal('modal-delete-account');
        resetModal();
      });
    } catch (_) { /* toast shown by apiRequest */ }
  });

  // Init on page load
  resetModal();
}


/* ────────────────────────────────────────────────────────────
   LOADING BUTTON HELPER
   Disables a button, shows a spinner, runs an async fn,
   then restores the button regardless of success/failure.

   Usage:
     await withLoadingButton(btn, async () => {
       await apiRequest(...);
     });
   ──────────────────────────────────────────────────────────── */

async function withLoadingButton(btn, asyncFn) {
  if (!btn) return asyncFn();

  const originalHTML = btn.innerHTML;
  const originalDisabled = btn.disabled;

  btn.disabled = true;
  btn.innerHTML = `<span class="spinner-sm" aria-hidden="true"></span> ${btn.textContent.trim()}`;

  try {
    return await asyncFn();
  } finally {
    btn.disabled = originalDisabled;
    btn.innerHTML = originalHTML;
  }
}
