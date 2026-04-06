/* ============================================================
   Averon Investment — contact.js
   Contact form AJAX handler
   ============================================================ */

function initContactForm() {
  const form = document.getElementById('contact-form');
  if (!form) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const data = {
      type:    'contact',
      name:    form.querySelector('[name="name"]').value.trim(),
      email:   form.querySelector('[name="email"]').value.trim(),
      subject: form.querySelector('[name="subject"]').value.trim(),
      message: form.querySelector('[name="message"]').value.trim(),
    };

    if (!data.name)    { showToast('Name is required', 'error'); return; }
    if (!data.email)   { showToast('Email is required', 'error'); return; }
    if (!data.message) { showToast('Message is required', 'error'); return; }

    const btn = form.querySelector('button[type="submit"]');
    if (btn) { btn.disabled = true; btn.textContent = 'Sending…'; }

    try {
      await apiRequest('/api/utilities/contact.php', 'POST', data);
      showToast("Message sent! We'll get back to you within 24 hours.", 'success');
      form.reset();
    } catch (_) {
      // error already shown by apiRequest
    } finally {
      if (btn) { btn.disabled = false; btn.textContent = 'Send Message'; }
    }
  });
}

document.addEventListener('DOMContentLoaded', initContactForm);
