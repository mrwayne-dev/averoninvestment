/* ============================================================
   Averon Investment — translations.js
   Lightweight i18n system.
   Usage: setLanguage('es') or applyStoredLanguage()
   Elements are marked with data-i18n="key" for text replacement
   and data-i18n-ph="key" for placeholder replacement.
   ============================================================ */

const TRANSLATIONS = {

  en: {
    // ─── Common ────────────────────────────────────────
    'nav.home':           'Home',
    'nav.about':          'About',
    'nav.investments':    'Investments',
    'nav.membership':     'Membership',
    'nav.contact':        'Contact',
    'nav.login':          'Sign In',
    'nav.signup':         'Get Started',

    // ─── Signup ────────────────────────────────────────
    'signup.title':           'Create Account',
    'signup.step1':           'Step 1 of 3 — Personal details',
    'signup.step2':           'Step 2 of 3 — Account credentials',
    'signup.step3':           'Step 3 of 3 — Verify your email',
    'signup.firstname':       'First Name',
    'signup.lastname':        'Last Name',
    'signup.region':          'Region / Country',
    'signup.language':        'Preferred Language',
    'signup.email':           'Email Address',
    'signup.password':        'Password',
    'signup.confirm':         'Confirm Password',
    'signup.continue':        'Continue',
    'signup.back':            'Back',
    'signup.send_code':       'Send Verification Code',
    'signup.verify':          'Verify & Activate',
    'signup.resend':          'Resend code',
    'signup.have_account':    'Already have an account?',
    'signup.signin':          'Sign in',
    'signup.code_hint':       'Enter the 6-digit code sent to',
    'ph.firstname':           'John',
    'ph.lastname':            'Smith',
    'ph.email':               'you@example.com',
    'ph.password':            'Min. 8 characters',
    'ph.confirm':             'Repeat password',

    // ─── Login ─────────────────────────────────────────
    'login.title':        'Welcome Back',
    'login.email':        'Email Address',
    'login.password':     'Password',
    'login.submit':       'Sign In',
    'login.forgot':       'Forgot password?',
    'login.no_account':   "Don't have an account?",
    'login.signup':       'Create one',

    // ─── Dashboard ─────────────────────────────────────
    'dash.balance':       'Wallet Balance',
    'dash.profit':        'Total Profit',
    'dash.invested':      'Amount Invested',
    'dash.active_plans':  'Active Plans',
    'dash.deposit':       'Deposit',
    'dash.withdraw':      'Withdraw',
    'dash.invest':        'Invest',
    'dash.transfer':      'Transfer',
    'dash.membership':    'Membership',
    'dash.quick_actions': 'Quick Actions',
    'dash.recent_tx':     'Recent Transactions',

    // ─── Account ───────────────────────────────────────
    'account.profile':    'Profile Information',
    'account.password':   'Change Password',
    'account.notifs':     'Notifications',
    'account.save':       'Save Changes',
    'account.update_pwd': 'Update Password',
    'account.2fa':        'Two-Factor Authentication',
    'account.danger':     'Danger Zone',
  },

  es: {
    'nav.home':           'Inicio',
    'nav.about':          'Nosotros',
    'nav.investments':    'Inversiones',
    'nav.membership':     'Membresía',
    'nav.contact':        'Contacto',
    'nav.login':          'Iniciar Sesión',
    'nav.signup':         'Comenzar',

    'signup.title':           'Crear Cuenta',
    'signup.step1':           'Paso 1 de 3 — Datos personales',
    'signup.step2':           'Paso 2 de 3 — Credenciales',
    'signup.step3':           'Paso 3 de 3 — Verificar correo',
    'signup.firstname':       'Nombre',
    'signup.lastname':        'Apellido',
    'signup.region':          'Región / País',
    'signup.language':        'Idioma preferido',
    'signup.email':           'Correo Electrónico',
    'signup.password':        'Contraseña',
    'signup.confirm':         'Confirmar Contraseña',
    'signup.continue':        'Continuar',
    'signup.back':            'Volver',
    'signup.send_code':       'Enviar Código de Verificación',
    'signup.verify':          'Verificar y Activar',
    'signup.resend':          'Reenviar código',
    'signup.have_account':    '¿Ya tienes una cuenta?',
    'signup.signin':          'Iniciar sesión',
    'signup.code_hint':       'Ingresa el código de 6 dígitos enviado a',
    'ph.firstname':           'Juan',
    'ph.lastname':            'García',
    'ph.email':               'tu@ejemplo.com',
    'ph.password':            'Mín. 8 caracteres',
    'ph.confirm':             'Repetir contraseña',

    'login.title':        'Bienvenido de Nuevo',
    'login.email':        'Correo Electrónico',
    'login.password':     'Contraseña',
    'login.submit':       'Iniciar Sesión',
    'login.forgot':       '¿Olvidaste tu contraseña?',
    'login.no_account':   '¿No tienes cuenta?',
    'login.signup':       'Crear una',

    'dash.balance':       'Saldo de Billetera',
    'dash.profit':        'Ganancia Total',
    'dash.invested':      'Cantidad Invertida',
    'dash.active_plans':  'Planes Activos',
    'dash.deposit':       'Depositar',
    'dash.withdraw':      'Retirar',
    'dash.invest':        'Invertir',
    'dash.transfer':      'Transferir',
    'dash.membership':    'Membresía',
    'dash.quick_actions': 'Acciones Rápidas',
    'dash.recent_tx':     'Transacciones Recientes',

    'account.profile':    'Información de Perfil',
    'account.password':   'Cambiar Contraseña',
    'account.notifs':     'Notificaciones',
    'account.save':       'Guardar Cambios',
    'account.update_pwd': 'Actualizar Contraseña',
    'account.2fa':        'Autenticación de Dos Factores',
    'account.danger':     'Zona de Peligro',
  },

  fr: {
    'nav.home':           'Accueil',
    'nav.about':          'À Propos',
    'nav.investments':    'Investissements',
    'nav.membership':     'Adhésion',
    'nav.contact':        'Contact',
    'nav.login':          'Se Connecter',
    'nav.signup':         'Commencer',

    'signup.title':           'Créer un Compte',
    'signup.step1':           'Étape 1 sur 3 — Informations personnelles',
    'signup.step2':           'Étape 2 sur 3 — Identifiants',
    'signup.step3':           'Étape 3 sur 3 — Vérifier votre e-mail',
    'signup.firstname':       'Prénom',
    'signup.lastname':        'Nom de Famille',
    'signup.region':          'Région / Pays',
    'signup.language':        'Langue Préférée',
    'signup.email':           'Adresse E-mail',
    'signup.password':        'Mot de Passe',
    'signup.confirm':         'Confirmer le Mot de Passe',
    'signup.continue':        'Continuer',
    'signup.back':            'Retour',
    'signup.send_code':       'Envoyer le Code de Vérification',
    'signup.verify':          'Vérifier et Activer',
    'signup.resend':          'Renvoyer le code',
    'signup.have_account':    'Vous avez déjà un compte ?',
    'signup.signin':          'Se connecter',
    'signup.code_hint':       'Entrez le code à 6 chiffres envoyé à',
    'ph.firstname':           'Jean',
    'ph.lastname':            'Dupont',
    'ph.email':               'vous@exemple.fr',
    'ph.password':            'Min. 8 caractères',
    'ph.confirm':             'Répéter le mot de passe',

    'login.title':        'Bon Retour',
    'login.email':        'Adresse E-mail',
    'login.password':     'Mot de Passe',
    'login.submit':       'Se Connecter',
    'login.forgot':       'Mot de passe oublié ?',
    'login.no_account':   "Pas encore de compte ?",
    'login.signup':       'En créer un',

    'dash.balance':       'Solde du Portefeuille',
    'dash.profit':        'Profit Total',
    'dash.invested':      'Montant Investi',
    'dash.active_plans':  'Plans Actifs',
    'dash.deposit':       'Déposer',
    'dash.withdraw':      'Retirer',
    'dash.invest':        'Investir',
    'dash.transfer':      'Transférer',
    'dash.membership':    'Adhésion',
    'dash.quick_actions': 'Actions Rapides',
    'dash.recent_tx':     'Transactions Récentes',

    'account.profile':    'Informations du Profil',
    'account.password':   'Changer le Mot de Passe',
    'account.notifs':     'Notifications',
    'account.save':       'Enregistrer',
    'account.update_pwd': 'Mettre à Jour le Mot de Passe',
    'account.2fa':        'Authentification à Deux Facteurs',
    'account.danger':     'Zone Dangereuse',
  },

  de: {
    'nav.home':           'Startseite',
    'nav.about':          'Über Uns',
    'nav.investments':    'Investitionen',
    'nav.membership':     'Mitgliedschaft',
    'nav.contact':        'Kontakt',
    'nav.login':          'Anmelden',
    'nav.signup':         'Loslegen',

    'signup.title':           'Konto Erstellen',
    'signup.step1':           'Schritt 1 von 3 — Persönliche Daten',
    'signup.step2':           'Schritt 2 von 3 — Anmeldedaten',
    'signup.step3':           'Schritt 3 von 3 — E-Mail bestätigen',
    'signup.firstname':       'Vorname',
    'signup.lastname':        'Nachname',
    'signup.region':          'Region / Land',
    'signup.language':        'Bevorzugte Sprache',
    'signup.email':           'E-Mail-Adresse',
    'signup.password':        'Passwort',
    'signup.confirm':         'Passwort bestätigen',
    'signup.continue':        'Weiter',
    'signup.back':            'Zurück',
    'signup.send_code':       'Bestätigungscode senden',
    'signup.verify':          'Verifizieren und Aktivieren',
    'signup.resend':          'Code erneut senden',
    'signup.have_account':    'Haben Sie bereits ein Konto?',
    'signup.signin':          'Anmelden',
    'signup.code_hint':       'Geben Sie den 6-stelligen Code ein, der gesendet wurde an',
    'ph.firstname':           'Hans',
    'ph.lastname':            'Müller',
    'ph.email':               'sie@beispiel.de',
    'ph.password':            'Mind. 8 Zeichen',
    'ph.confirm':             'Passwort wiederholen',

    'login.title':        'Willkommen Zurück',
    'login.email':        'E-Mail-Adresse',
    'login.password':     'Passwort',
    'login.submit':       'Anmelden',
    'login.forgot':       'Passwort vergessen?',
    'login.no_account':   'Noch kein Konto?',
    'login.signup':       'Jetzt erstellen',

    'dash.balance':       'Wallet-Guthaben',
    'dash.profit':        'Gesamtgewinn',
    'dash.invested':      'Investierter Betrag',
    'dash.active_plans':  'Aktive Pläne',
    'dash.deposit':       'Einzahlen',
    'dash.withdraw':      'Abheben',
    'dash.invest':        'Investieren',
    'dash.transfer':      'Übertragen',
    'dash.membership':    'Mitgliedschaft',
    'dash.quick_actions': 'Schnellaktionen',
    'dash.recent_tx':     'Letzte Transaktionen',

    'account.profile':    'Profilinformationen',
    'account.password':   'Passwort Ändern',
    'account.notifs':     'Benachrichtigungen',
    'account.save':       'Änderungen Speichern',
    'account.update_pwd': 'Passwort Aktualisieren',
    'account.2fa':        'Zwei-Faktor-Authentifizierung',
    'account.danger':     'Gefahrenzone',
  },

  pt: {
    'nav.home':           'Início',
    'nav.about':          'Sobre',
    'nav.investments':    'Investimentos',
    'nav.membership':     'Membros',
    'nav.contact':        'Contato',
    'nav.login':          'Entrar',
    'nav.signup':         'Começar',

    'signup.title':           'Criar Conta',
    'signup.step1':           'Passo 1 de 3 — Dados Pessoais',
    'signup.step2':           'Passo 2 de 3 — Credenciais',
    'signup.step3':           'Passo 3 de 3 — Verificar E-mail',
    'signup.firstname':       'Nome',
    'signup.lastname':        'Sobrenome',
    'signup.region':          'Região / País',
    'signup.language':        'Idioma Preferido',
    'signup.email':           'Endereço de E-mail',
    'signup.password':        'Senha',
    'signup.confirm':         'Confirmar Senha',
    'signup.continue':        'Continuar',
    'signup.back':            'Voltar',
    'signup.send_code':       'Enviar Código de Verificação',
    'signup.verify':          'Verificar e Ativar',
    'signup.resend':          'Reenviar código',
    'signup.have_account':    'Já tem uma conta?',
    'signup.signin':          'Entrar',
    'signup.code_hint':       'Digite o código de 6 dígitos enviado para',
    'ph.firstname':           'João',
    'ph.lastname':            'Silva',
    'ph.email':               'voce@exemplo.com',
    'ph.password':            'Mín. 8 caracteres',
    'ph.confirm':             'Repetir senha',

    'login.title':        'Bem-Vindo de Volta',
    'login.email':        'Endereço de E-mail',
    'login.password':     'Senha',
    'login.submit':       'Entrar',
    'login.forgot':       'Esqueceu a senha?',
    'login.no_account':   'Não tem uma conta?',
    'login.signup':       'Criar uma',

    'dash.balance':       'Saldo da Carteira',
    'dash.profit':        'Lucro Total',
    'dash.invested':      'Valor Investido',
    'dash.active_plans':  'Planos Ativos',
    'dash.deposit':       'Depositar',
    'dash.withdraw':      'Sacar',
    'dash.invest':        'Investir',
    'dash.transfer':      'Transferir',
    'dash.membership':    'Membros',
    'dash.quick_actions': 'Ações Rápidas',
    'dash.recent_tx':     'Transações Recentes',

    'account.profile':    'Informações do Perfil',
    'account.password':   'Alterar Senha',
    'account.notifs':     'Notificações',
    'account.save':       'Salvar Alterações',
    'account.update_pwd': 'Atualizar Senha',
    'account.2fa':        'Autenticação de Dois Fatores',
    'account.danger':     'Zona de Perigo',
  },

  zh: {
    'nav.home':           '首页',
    'nav.about':          '关于我们',
    'nav.investments':    '投资',
    'nav.membership':     '会员',
    'nav.contact':        '联系我们',
    'nav.login':          '登录',
    'nav.signup':         '开始',

    'signup.title':           '创建账户',
    'signup.step1':           '第1步共3步 — 个人信息',
    'signup.step2':           '第2步共3步 — 账户凭据',
    'signup.step3':           '第3步共3步 — 验证邮箱',
    'signup.firstname':       '名',
    'signup.lastname':        '姓',
    'signup.region':          '地区 / 国家',
    'signup.language':        '首选语言',
    'signup.email':           '电子邮箱',
    'signup.password':        '密码',
    'signup.confirm':         '确认密码',
    'signup.continue':        '继续',
    'signup.back':            '返回',
    'signup.send_code':       '发送验证码',
    'signup.verify':          '验证并激活',
    'signup.resend':          '重新发送验证码',
    'signup.have_account':    '已有账户？',
    'signup.signin':          '登录',
    'signup.code_hint':       '请输入发送至以下邮箱的6位数字验证码',
    'ph.firstname':           '明',
    'ph.lastname':            '王',
    'ph.email':               'you@example.com',
    'ph.password':            '至少8个字符',
    'ph.confirm':             '重复密码',

    'login.title':        '欢迎回来',
    'login.email':        '电子邮箱',
    'login.password':     '密码',
    'login.submit':       '登录',
    'login.forgot':       '忘记密码？',
    'login.no_account':   '还没有账户？',
    'login.signup':       '立即创建',

    'dash.balance':       '钱包余额',
    'dash.profit':        '总利润',
    'dash.invested':      '投资金额',
    'dash.active_plans':  '活跃计划',
    'dash.deposit':       '存款',
    'dash.withdraw':      '提款',
    'dash.invest':        '投资',
    'dash.transfer':      '转账',
    'dash.membership':    '会员',
    'dash.quick_actions': '快捷操作',
    'dash.recent_tx':     '最近交易',

    'account.profile':    '个人资料',
    'account.password':   '更改密码',
    'account.notifs':     '通知',
    'account.save':       '保存更改',
    'account.update_pwd': '更新密码',
    'account.2fa':        '双因素认证',
    'account.danger':     '危险区域',
  },

  ar: {
    'nav.home':           'الرئيسية',
    'nav.about':          'من نحن',
    'nav.investments':    'الاستثمارات',
    'nav.membership':     'العضوية',
    'nav.contact':        'اتصل بنا',
    'nav.login':          'تسجيل الدخول',
    'nav.signup':         'ابدأ الآن',

    'signup.title':           'إنشاء حساب',
    'signup.step1':           'الخطوة 1 من 3 — البيانات الشخصية',
    'signup.step2':           'الخطوة 2 من 3 — بيانات الحساب',
    'signup.step3':           'الخطوة 3 من 3 — تحقق من البريد',
    'signup.firstname':       'الاسم الأول',
    'signup.lastname':        'الاسم الأخير',
    'signup.region':          'المنطقة / الدولة',
    'signup.language':        'اللغة المفضلة',
    'signup.email':           'البريد الإلكتروني',
    'signup.password':        'كلمة المرور',
    'signup.confirm':         'تأكيد كلمة المرور',
    'signup.continue':        'متابعة',
    'signup.back':            'رجوع',
    'signup.send_code':       'إرسال رمز التحقق',
    'signup.verify':          'تحقق وتفعيل',
    'signup.resend':          'إعادة إرسال الرمز',
    'signup.have_account':    'هل لديك حساب بالفعل؟',
    'signup.signin':          'تسجيل الدخول',
    'signup.code_hint':       'أدخل الرمز المكون من 6 أرقام المرسل إلى',
    'ph.firstname':           'محمد',
    'ph.lastname':            'العلي',
    'ph.email':               'you@example.com',
    'ph.password':            '8 أحرف على الأقل',
    'ph.confirm':             'أعد كتابة كلمة المرور',

    'login.title':        'مرحباً بعودتك',
    'login.email':        'البريد الإلكتروني',
    'login.password':     'كلمة المرور',
    'login.submit':       'تسجيل الدخول',
    'login.forgot':       'نسيت كلمة المرور؟',
    'login.no_account':   'ليس لديك حساب؟',
    'login.signup':       'أنشئ واحداً',

    'dash.balance':       'رصيد المحفظة',
    'dash.profit':        'إجمالي الأرباح',
    'dash.invested':      'المبلغ المستثمر',
    'dash.active_plans':  'الخطط النشطة',
    'dash.deposit':       'إيداع',
    'dash.withdraw':      'سحب',
    'dash.invest':        'استثمار',
    'dash.transfer':      'تحويل',
    'dash.membership':    'العضوية',
    'dash.quick_actions': 'الإجراءات السريعة',
    'dash.recent_tx':     'المعاملات الأخيرة',

    'account.profile':    'معلومات الملف الشخصي',
    'account.password':   'تغيير كلمة المرور',
    'account.notifs':     'الإشعارات',
    'account.save':       'حفظ التغييرات',
    'account.update_pwd': 'تحديث كلمة المرور',
    'account.2fa':        'المصادقة الثنائية',
    'account.danger':     'منطقة الخطر',
  },

};

// RTL languages
const RTL_LANGS = ['ar'];

// ─── Core translation engine ──────────────────────────────────────────

let _currentLang = 'en';

/**
 * Translate a single key, falling back to English.
 */
function t(key) {
  return (TRANSLATIONS[_currentLang] && TRANSLATIONS[_currentLang][key])
    || (TRANSLATIONS['en'][key])
    || key;
}

/**
 * Apply translations to the entire document.
 * Elements with data-i18n="key" get their textContent replaced.
 * Elements with data-i18n-ph="key" get their placeholder replaced.
 */
function applyTranslations() {
  document.querySelectorAll('[data-i18n]').forEach((el) => {
    el.textContent = t(el.dataset.i18n);
  });
  document.querySelectorAll('[data-i18n-ph]').forEach((el) => {
    el.placeholder = t(el.dataset.i18nPh);
  });

  // RTL support
  const isRtl = RTL_LANGS.includes(_currentLang);
  document.documentElement.setAttribute('dir', isRtl ? 'rtl' : 'ltr');
  document.documentElement.setAttribute('lang', _currentLang);
}

/**
 * Switch to a new language, save it, and apply translations.
 * @param {string} lang - ISO language code (e.g. 'en', 'es')
 */
function setLanguage(lang) {
  if (!TRANSLATIONS[lang]) return; // unsupported language
  _currentLang = lang;
  localStorage.setItem('averon_lang', lang);
  applyTranslations();
}

/**
 * On page load, apply the user's previously saved language preference.
 * Also syncs the language dropdown (on signup or account page) to match.
 */
function applyStoredLanguage() {
  const stored = localStorage.getItem('averon_lang') || 'en';
  if (TRANSLATIONS[stored]) {
    _currentLang = stored;
    applyTranslations();
  }

  // Sync every language <select> on the page to the stored value
  document.querySelectorAll('select[name="language"], #language').forEach((sel) => {
    if (sel.querySelector(`option[value="${stored}"]`)) {
      sel.value = stored;
    }
  });
}

// ─── Auto-apply on DOMContentLoaded ──────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  applyStoredLanguage();

  // Live language switching: any <select name="language"> on the page
  document.querySelectorAll('select[name="language"], #language').forEach((sel) => {
    sel.addEventListener('change', () => {
      setLanguage(sel.value);
      // Keep all language selectors in sync if there are multiple on the page
      document.querySelectorAll('select[name="language"], #language').forEach((s) => {
        if (s !== sel && s.querySelector(`option[value="${sel.value}"]`)) {
          s.value = sel.value;
        }
      });
    });
  });
});
