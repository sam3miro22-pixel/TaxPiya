/**
 * Taxpiya — Firebase Auth (email/password + Google)
 */
import { initializeApp, getApps, getApp } from 'firebase/app';
import {
  getAuth,
  signInWithEmailAndPassword,
  createUserWithEmailAndPassword,
  signInWithPopup,
  signInWithRedirect,
  signInWithCredential,
  getRedirectResult,
  GoogleAuthProvider,
  onAuthStateChanged,
  setPersistence,
  browserLocalPersistence,
  browserSessionPersistence,
} from 'firebase/auth';
import { getFirestore, doc, setDoc, serverTimestamp } from 'firebase/firestore';

const AUTH_META_KEY = 'txp_fb_auth_meta';
const REDIRECT_FLAG_KEY = 'txp_fb_redirect_pending';

let app = null;
let auth = null;
let db = null;
let redirectBootStarted = false;

function cfg() {
  return window.TAXPIYA_FIREBASE_CONFIG || {};
}

async function ensureInit() {
  const c = cfg();
  if (!c.apiKey) {
    console.warn('[TaxpiyaFirebase] Config no disponible');
    return false;
  }

  app = getApps().length ? getApp() : initializeApp(c);
  auth = getAuth(app);
  db = db || getFirestore(app);

  try {
    await setPersistence(auth, browserLocalPersistence);
  } catch (_) {
    try {
      await setPersistence(auth, browserSessionPersistence);
    } catch (e2) {
      console.warn('[TaxpiyaFirebase] persistence:', e2);
    }
  }

  if (typeof auth.authStateReady === 'function') {
    await auth.authStateReady();
  }

  return true;
}

function saveAuthMeta(meta = {}) {
  try {
    localStorage.setItem(AUTH_META_KEY, JSON.stringify(meta || {}));
    localStorage.setItem(REDIRECT_FLAG_KEY, '1');
    sessionStorage.setItem(AUTH_META_KEY, JSON.stringify(meta || {}));
    sessionStorage.setItem(REDIRECT_FLAG_KEY, '1');
  } catch (_) {}
}

function loadAuthMeta(fallback = {}) {
  let meta = { ...fallback };
  try {
    const raw = localStorage.getItem(AUTH_META_KEY) || sessionStorage.getItem(AUTH_META_KEY);
    if (raw) meta = { ...meta, ...JSON.parse(raw) };
    localStorage.removeItem(AUTH_META_KEY);
    sessionStorage.removeItem(AUTH_META_KEY);
  } catch (_) {}
  return meta;
}

function clearRedirectFlag() {
  try {
    localStorage.removeItem(REDIRECT_FLAG_KEY);
    sessionStorage.removeItem(REDIRECT_FLAG_KEY);
  } catch (_) {}
}

function isRedirectPending() {
  try {
    return localStorage.getItem(REDIRECT_FLAG_KEY) === '1' || sessionStorage.getItem(REDIRECT_FLAG_KEY) === '1';
  } catch (_) { return false; }
}

export function init() {
  return ensureInit();
}

function isNativeWebView() {
  if (typeof window === 'undefined') return false;
  if (window.Capacitor?.isNativePlatform?.()) return true;
  return /Taxpiya(Pasajero|Driver)?\/Android/i.test(navigator.userAgent || '');
}

function shouldUseGoogleRedirect() {
  if (isNativeWebView()) return true;
  if (typeof window === 'undefined') return false;
  const ua = navigator.userAgent || '';
  if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(ua)) {
    return true;
  }
  const touch = (navigator.maxTouchPoints || 0) > 0;
  const narrow = window.matchMedia?.('(max-width: 900px)')?.matches;
  return touch && narrow;
}

export function formatFirebaseError(err) {
  const code = err?.code || '';
  const msg = err?.message || String(err || 'Error de autenticación');
  if (code === 'auth/unauthorized-domain' || msg.includes('auth/unauthorized-domain')) {
    const host = typeof window !== 'undefined' ? window.location.hostname : 'este dominio';
    return `Google no está autorizado para ${host}. En Firebase Console → Authentication → Configuración → Dominios autorizados, agrega "${host}" y guarda.`;
  }
  if (code === 'auth/popup-blocked') {
    return 'El navegador bloqueó la ventana de Google. Intenta de nuevo o usa correo y contraseña.';
  }
  if (code === 'auth/popup-closed-by-user') {
    return 'Cerraste la ventana de Google antes de completar el inicio de sesión.';
  }
  if (/disallowed_useragent/i.test(msg)) {
    return 'Google no permite iniciar sesión dentro del navegador embebido. Actualiza la app Taxpiya a la última versión.';
  }
  if (code === 'auth/email-already-in-use' || /EMAIL_EXISTS/i.test(msg)) {
    return 'Ese correo ya tiene cuenta. Usa «Iniciar sesión» o recupera la contraseña.';
  }
  if (code === 'auth/weak-password' || /WEAK_PASSWORD/i.test(msg)) {
    return 'La contraseña debe tener al menos 6 caracteres.';
  }
  if (code === 'auth/invalid-email' || /INVALID_EMAIL/i.test(msg)) {
    return 'Correo electrónico no válido.';
  }
  return msg.replace(/^Firebase:\s*/i, '').replace(/^Error\s*\([^)]+\)\.\s*/i, '');
}

function getCsrfToken() {
  const meta = document.querySelector('meta[name="csrf-token"]')?.content || '';
  if (meta) return meta;
  const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);
  if (match?.[1]) {
    try {
      return decodeURIComponent(match[1]);
    } catch (_) {
      return match[1];
    }
  }
  return '';
}

async function syncWithLaravel(idToken, extra = {}) {
  const url = window.TAXPIYA_FIREBASE_SYNC_URL;
  if (!url) throw new Error('URL de sincronización no configurada');

  const res = await fetch(url, {
    method: 'POST',
    credentials: 'same-origin',
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      'X-CSRF-TOKEN': getCsrfToken(),
      'X-Requested-With': 'XMLHttpRequest',
    },
    body: JSON.stringify({ id_token: idToken, ...extra }),
  });

  let data = {};
  const raw = await res.text().catch(() => '');
  try {
    data = raw ? JSON.parse(raw) : {};
  } catch (_) {
    if (res.status >= 500) {
      throw new Error('Error del servidor al iniciar sesión. Intenta de nuevo en unos segundos.');
    }
  }
  if (!res.ok || !data.ok) {
    const detail = data.message
      || (res.status === 419 ? 'CSRF token mismatch. Recarga la página e intenta de nuevo.' : 'No se pudo sincronizar la sesión');
    throw new Error(detail);
  }
  return data;
}

async function upsertFirestoreProfile(user, profile = {}) {
  if (!db || !user?.uid) return;
  try {
    await setDoc(
      doc(db, 'users', user.uid),
      {
        email: user.email || null,
        name: profile.name || user.displayName || null,
        telefono: profile.telefono || null,
        role: profile.app || profile.role || 'pasajero',
        updated_at: serverTimestamp(),
        created_at: serverTimestamp(),
      },
      { merge: true }
    );
  } catch (e) {
    console.warn('[TaxpiyaFirebase] Firestore profile:', e);
  }
}

async function finalizeFirebaseUser(user, meta = {}) {
  const enriched = {
    ...meta,
    name: meta.name || user.displayName || user.email?.split('@')[0] || null,
  };
  await upsertFirestoreProfile(user, enriched);
  const token = await user.getIdToken(true);
  return syncWithLaravel(token, enriched);
}

export async function loginEmail(email, password, meta = {}) {
  if (!(await ensureInit())) throw new Error('Firebase no inicializado');
  try {
    const cred = await signInWithEmailAndPassword(auth, email, password);
    return finalizeFirebaseUser(cred.user, meta);
  } catch (e) {
    throw new Error(formatFirebaseError(e));
  }
}

export async function registerEmail(email, password, profile = {}) {
  if (!(await ensureInit())) throw new Error('Firebase no inicializado');
  try {
    const cred = await createUserWithEmailAndPassword(auth, email, password);
    return finalizeFirebaseUser(cred.user, { ...profile, is_register: true });
  } catch (e) {
    const code = e?.code || '';
    if (code === 'auth/email-already-in-use' || /EMAIL_EXISTS/i.test(e?.message || '')) {
      return loginEmail(email, password, { ...profile, is_register: false });
    }
    throw new Error(formatFirebaseError(e));
  }
}

function nativeFirebaseAuthPlugin() {
  if (!isNativeWebView()) return null;
  const plugins = window.Capacitor?.Plugins;
  return plugins?.FirebaseAuthentication || null;
}

async function getNativeFirebaseIdToken(FA) {
  for (let attempt = 0; attempt < 4; attempt++) {
    try {
      const tokenResult = await FA.getIdToken({ forceRefresh: attempt < 2 });
      if (tokenResult?.token) {
        return tokenResult.token;
      }
    } catch (_) {}
    await new Promise((r) => setTimeout(r, 350));
  }
  return null;
}

async function loginGoogleNative(meta = {}) {
  const FA = nativeFirebaseAuthPlugin();
  if (!FA?.signInWithGoogle) {
    throw new Error('Inicio con Google nativo no disponible. Actualiza la app Taxpiya.');
  }

  await ensureInit();
  const result = await FA.signInWithGoogle();

  let idToken = null;

  if (typeof FA.getIdToken === 'function') {
    idToken = await getNativeFirebaseIdToken(FA);
  }

  if (!idToken && result?.credential?.idToken && auth) {
    try {
      const cred = GoogleAuthProvider.credential(result.credential.idToken);
      const userCred = await signInWithCredential(auth, cred);
      idToken = await userCred.user.getIdToken(true);
    } catch (_) {}
  }

  if (!idToken) {
    throw new Error('No se pudo obtener el token de Firebase. Cierra la app, ábrela de nuevo e intenta otra vez.');
  }

  return syncWithLaravel(idToken, meta);
}

export async function loginGoogle(meta = {}) {
  if (!(await ensureInit())) throw new Error('Firebase no inicializado');

  if (isNativeWebView()) {
    try {
      return await loginGoogleNative(meta);
    } catch (e) {
      throw new Error(formatFirebaseError(e));
    }
  }

  const provider = new GoogleAuthProvider();
  provider.setCustomParameters({ prompt: 'select_account' });

  try {
    if (shouldUseGoogleRedirect()) {
      saveAuthMeta(meta);
      await signInWithRedirect(auth, provider);
      return { ok: true, redirect: true };
    }
    const cred = await signInWithPopup(auth, provider);
    return finalizeFirebaseUser(cred.user, meta);
  } catch (e) {
    clearRedirectFlag();
    throw new Error(formatFirebaseError(e));
  }
}

export async function resyncSession(meta = {}) {
  if (!(await ensureInit())) return null;
  const user = auth.currentUser;
  if (!user) return null;
  return finalizeFirebaseUser(user, meta);
}

export async function completeGoogleRedirect(meta = {}) {
  if (!(await ensureInit())) return null;

  const mergedMeta = loadAuthMeta(meta);
  const pending = isRedirectPending();

  try {
    let cred = await getRedirectResult(auth);

    if (!cred?.user && auth.currentUser) {
      cred = { user: auth.currentUser };
    }

    if (!cred?.user) {
      if (pending) {
        throw new Error('No se pudo completar el inicio con Google. Intenta de nuevo.');
      }
      return null;
    }

    clearRedirectFlag();
    const data = await finalizeFirebaseUser(cred.user, mergedMeta);
    return data;
  } catch (e) {
    clearRedirectFlag();
    throw new Error(formatFirebaseError(e));
  }
}

export function onAuthChange(cb) {
  if (!auth) {
    ensureInit().then((ok) => { if (ok) onAuthStateChanged(auth, cb); });
    return () => {};
  }
  return onAuthStateChanged(auth, cb);
}

function readPageAuthMeta() {
  const wrap = document.querySelector('.txp-firebase-auth');
  const app = wrap?.dataset?.app || null;
  return app ? { app } : {};
}

async function bootGoogleRedirectHandler() {
  if (redirectBootStarted) return;
  redirectBootStarted = true;

  if (!isRedirectPending()) {
    return;
  }

  const overlay = document.getElementById('txp-fb-redirect-busy');
  if (overlay) overlay.style.display = 'flex';

    try {
      const data = await completeGoogleRedirect(readPageAuthMeta());
      if (data?.ok) {
        window.dispatchEvent(new CustomEvent('txp-firebase-auth-done', { detail: data }));
        window.location.replace(data.redirect || '/home');
        return;
      }
    } catch (e) {
      const msg = e?.message || String(e);
      window.__txpFbRedirectError = msg;
      window.dispatchEvent(new CustomEvent('txp-firebase-auth-error', { detail: msg }));
    } finally {
      if (overlay) overlay.style.display = 'none';
    }
  }

if (typeof window !== 'undefined') {
  window.TaxpiyaFirebase = {
    init: ensureInit,
    loginEmail,
    registerEmail,
    loginGoogle,
    completeGoogleRedirect,
    resyncSession,
    onAuthChange,
    formatFirebaseError,
    bootGoogleRedirectHandler,
  };

  bootGoogleRedirectHandler();
}
