(function () {
  const root = document.documentElement;
  const storageKey = 'shadow-theme';
  const toggle = document.getElementById('theme-toggle');
  const icon = toggle ? toggle.querySelector('i') : null;

  const getPreferredTheme = () => {
    const stored = localStorage.getItem(storageKey);
    if (stored === 'light' || stored === 'dark') {
      return stored;
    }
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  };

  const updateToggleState = (theme) => {
    if (!toggle) {
      return;
    }
    const isDark = theme === 'dark';
    const nextLabel = isDark ? 'Switch to light theme' : 'Switch to dark theme';

    toggle.setAttribute('aria-label', nextLabel);
    toggle.setAttribute('title', nextLabel);
    toggle.dataset.theme = theme;

    if (icon) {
      icon.className = isDark ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
    }
  };

  const applyTheme = (theme) => {
    root.setAttribute('data-theme', theme);
    updateToggleState(theme);
  };

  applyTheme(getPreferredTheme());

  if (toggle) {
    toggle.addEventListener('click', function () {
      const next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
      localStorage.setItem(storageKey, next);
      applyTheme(next);
    });
  }
})();



// assets/js/code-copy.js

document.addEventListener('DOMContentLoaded', () => {
    // Select all <pre><code> blocks
    const codeBlocks = document.querySelectorAll('code');

    codeBlocks.forEach(block => {
        // Make it clear it's clickable
        block.style.cursor = 'pointer';
        block.title = 'Click to copy';

        block.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(block.innerText);
                // Optional: give visual feedback
                const originalBG = block.style.backgroundColor;
                block.style.backgroundColor = '#d4f8d4'; // light green flash
                setTimeout(() => block.style.backgroundColor = originalBG, 400);
            } catch (err) {
                console.error('Failed to copy code: ', err);
            }
        });
    });
});