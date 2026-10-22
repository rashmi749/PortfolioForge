// theme.js - Theme Switcher for PortfolioForge (Light/Dark Mode)

(function () {
    const getStoredTheme = () => localStorage.getItem('theme');
    const setStoredTheme = theme => localStorage.setItem('theme', theme);

    const getPreferredTheme = () => {
        const storedTheme = getStoredTheme();
        if (storedTheme) {
            return storedTheme;
        }
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    };

    const setTheme = theme => {
        document.documentElement.setAttribute('data-bs-theme', theme);
    };

    const updateToggleUI = theme => {
        const toggleBtn = document.getElementById('theme-toggle');
        if (!toggleBtn) return;

        const lightIcon = toggleBtn.querySelector('.pf-theme-icon-light');
        const darkIcon = toggleBtn.querySelector('.pf-theme-icon-dark');

        if (theme === 'dark') {
            lightIcon.classList.add('d-none');
            darkIcon.classList.remove('d-none');
        } else {
            lightIcon.classList.remove('d-none');
            darkIcon.classList.add('d-none');
        }
    };

    // Initialize theme instantly before body renders to avoid white flash
    const activeTheme = getPreferredTheme();
    setTheme(activeTheme);

    window.addEventListener('DOMContentLoaded', () => {
        updateToggleUI(activeTheme);

        const toggleBtn = document.getElementById('theme-toggle');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                const currentTheme = document.documentElement.getAttribute('data-bs-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                setStoredTheme(newTheme);
                setTheme(newTheme);
                updateToggleUI(newTheme);
            });
        }
    });

    // Listen for OS system theme changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        const storedTheme = getStoredTheme();
        if (storedTheme !== 'light' && storedTheme !== 'dark') {
            const systemTheme = getPreferredTheme();
            setTheme(systemTheme);
            updateToggleUI(systemTheme);
        }
    });
})();
