/**
 * Taxpiya — Firebase Auth (email/password + Google)
 * Expone window.TaxpiyaFirebase para login y registro.
 */
import { initializeApp } from 'firebase/app';
import {
  getAuth,
  signInWithEmailAndPassword,
  createUserWithEmailAndPassword,
  signInWithPopup,
  signInWithRedirect,
  getRedirectResult,
  GoogleAuthProvider,
  onAuthStateChanged,
} from 'firebase/auth';
import { getFirestore, doc, setDoc, serverTimestamp } from 'firebase/firestore';

let app = null;
let auth = null;
let db = null;

function cfg() {
  return window.TAXPIYA_FIREBASE_CONFIG || {};
}

export function init() {
  const c = cfg();
  if (!c.apiKey) {
    console.warn('[TaxpiyaFirebase] Config no disponible');
    return false;
  }
  app = initializeApp(c);
  auth = getAuth(app);
  db = getFirestore(app);
  return true;
}

function isNativeWebView() {
  if (typeof window === 'undefined') return false;
  if (window.Capacitor?.isNativePlatform?.()) return true;
  return /Taxpiya(Pasajero|Driver)?\/Android/i.test(navigator.userAgent || '');
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
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
      'X-Requested-With': 'XMLHttpRequest',
    },
    body: JSON.stringify({ id_token: idToken, ...extra }),
  });

  const data = await res.json().catch(() => ({}));
  if (!res.ok || !data.ok) {
    throw new Error(data.message || 'No se pudo sincronizar la sesión');
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
        role: profile.role || 'pasajero',
        updated_at: serverTimestamp(),
        created_at: serverTimestamp(),
      },
      { merge: true }
    );
  } catch (e) {
    console.warn('[TaxpiyaFirebase] Firestore profile:', e);
  }
}

export async function loginEmail(email, password, meta = {}) {
  if (!auth && !init()) throw new Error('Firebase no inicializado');
  const cred = await signInWithEmailAndPassword(auth, email, password);
  const token = await cred.user.getIdToken();
  return syncWithLaravel(token, meta);
}

export async function registerEmail(email, password, profile = {}) {
  if (!auth && !init()) throw new Error('Firebase no inicializado');
  const cred = await createUserWithEmailAndPassword(auth, email, password);
  await upsertFirestoreProfile(cred.user, profile);
  const token = await cred.user.getIdToken();
  return syncWithLaravel(token, { ...profile, is_register: true });
}

export async function loginGoogle(meta = {}) {
  if (!auth && !init()) throw new Error('Firebase no inicializado');
  const provider = new GoogleAuthProvider();
  if (isNativeWebView()) {
    await signInWithRedirect(auth, provider);
    return { ok: true, redirect: true };
  }
  const cred = await signInWithPopup(auth, provider);
  await upsertFirestoreProfile(cred.user, meta);
  const token = await cred.user.getIdToken();
  return syncWithLaravel(token, meta);
}

export async function completeGoogleRedirect(meta = {}) {
  if (!auth && !init()) return null;
  const cred = await getRedirectResult(auth);
  if (!cred?.user) return null;
  await upsertFirestoreProfile(cred.user, meta);
  const token = await cred.user.getIdToken();
  return syncWithLaravel(token, meta);
}

export function onAuthChange(cb) {
  if (!auth && !init()) return () => {};
  return onAuthStateChanged(auth, cb);
}

// IIFE global
if (typeof window !== 'undefined') {
  window.TaxpiyaFirebase = {
    init,
    loginEmail,
    registerEmail,
    loginGoogle,
    completeGoogleRedirect,
    onAuthChange,
  };
}
