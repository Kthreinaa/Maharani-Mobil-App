<script>
    (() => {
        const STORAGE_KEY = 'theme';
        const htmlElement = document.documentElement;

        const normalizeTheme = (value) => (value === 'dark' ? 'dark' : 'light');

        const updateThemeIndicators = (theme) => {
            document.querySelectorAll('[data-theme-toggle-label]').forEach((element) => {
                element.textContent = theme === 'dark' ? 'dark_mode' : 'light_mode';
            });

            document.querySelectorAll('[data-theme-toggle-text]').forEach((element) => {
                element.textContent = theme === 'dark' ? 'Dark' : 'Light';
            });

            document.querySelectorAll('[data-theme-toggle]').forEach((element) => {
                const label = theme === 'dark' ? 'Dark Mode' : 'Light Mode';
                element.setAttribute('title', label);
                element.setAttribute('aria-label', label);
            });
        };

        window.mmApplyTheme = (value) => {
            const theme = normalizeTheme(value);
            htmlElement.dataset.theme = theme;
            htmlElement.classList.toggle('dark', theme === 'dark');

            try {
                localStorage.setItem(STORAGE_KEY, theme);
            } catch (error) {
                // Ignore localStorage write failures.
            }

            updateThemeIndicators(theme);
        };

        window.mmToggleTheme = () => {
            const currentTheme = htmlElement.dataset.theme === 'dark' ? 'dark' : 'light';
            window.mmApplyTheme(currentTheme === 'dark' ? 'light' : 'dark');
        };

        let initialTheme = 'light';

        try {
            const urlTheme = new URLSearchParams(window.location.search).get('theme');
            const forcedTheme = (urlTheme === 'light' || urlTheme === 'dark') ? urlTheme : null;
            initialTheme = forcedTheme ?? normalizeTheme(localStorage.getItem(STORAGE_KEY));
        } catch (error) {
            initialTheme = 'light';
        }

        window.mmApplyTheme(initialTheme);
        window.addEventListener('DOMContentLoaded', () => updateThemeIndicators(initialTheme), { once: true });
    })();
</script>
