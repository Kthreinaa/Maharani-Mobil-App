<script>
    (() => {
        document.documentElement.dataset.theme = 'light';
        document.documentElement.classList.remove('dark');

        try {
            localStorage.removeItem('theme');
        } catch (error) {
            // Browser storage may be unavailable in private sessions.
        }
    })();
</script>
