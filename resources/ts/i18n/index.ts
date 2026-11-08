import { createI18n } from 'vue-i18n';
import en from './locales/en';
import km from './locales/km';

export type LocaleCode = 'en' | 'km';

const STORAGE_KEY = 'app-locale';

function getStoredLocale(): LocaleCode {
  const stored = localStorage.getItem(STORAGE_KEY);

  if (stored === 'en') {
    return 'en';
  }

  if (stored === 'km') {
    localStorage.setItem(STORAGE_KEY, 'en');
    return 'en';
  }

  localStorage.setItem(STORAGE_KEY, 'en');
  return 'en';
}

export function setDocumentLang(locale: LocaleCode): void {
  document.documentElement.lang = locale === 'km' ? 'km' : 'en';
}

const initialLocale = typeof localStorage !== 'undefined' ? getStoredLocale() : 'en';

if (typeof document !== 'undefined') {
  setDocumentLang(initialLocale);
}

const i18n = createI18n({
  legacy: false,
  locale: initialLocale,
  fallbackLocale: 'en',
  messages: { en, km },
});

export function setLocale(locale: LocaleCode): void {
  i18n.global.locale.value = locale;
  localStorage.setItem(STORAGE_KEY, locale);
  setDocumentLang(locale);
}

export function toggleLocale(): LocaleCode {
  const next: LocaleCode = i18n.global.locale.value === 'km' ? 'en' : 'km';
  setLocale(next);
  return next;
}

export default i18n;
