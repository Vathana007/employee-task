export function initDarkMode() {
  const stored = localStorage.getItem('dark-mode');
  const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
  const isDark = stored ? stored === 'dark' : prefersDark;
  document.documentElement.classList.toggle('dark', isDark);
}

export function toggleDarkMode() {
  const nowDark = document.documentElement.classList.toggle('dark');
  localStorage.setItem('dark-mode', nowDark ? 'dark' : 'light');
  return nowDark;
}

// Initialize as early as possible
if (typeof document !== 'undefined') {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDarkMode);
  } else {
    initDarkMode();
  }
}
