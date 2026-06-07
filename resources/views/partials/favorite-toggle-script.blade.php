@once
    <script>
        (() => {
            if (window.mmFavoriteToggleBound) {
                return;
            }

            window.mmFavoriteToggleBound = true;

            const csrfToken = '{{ csrf_token() }}';

            const updateFavoriteButton = (button, isFavorite) => {
                const icon = button.querySelector('[data-favorite-icon]');
                const label = button.querySelector('[data-favorite-label]');

                button.dataset.isFavorite = isFavorite ? '1' : '0';
                button.setAttribute('aria-pressed', isFavorite ? 'true' : 'false');
                button.setAttribute('aria-label', isFavorite ? 'Favorit tersimpan' : 'Tambah favorit');

                if (icon) {
                    icon.classList.toggle('text-rose-500', isFavorite);
                    icon.classList.toggle('text-slate-700', !isFavorite);
                    icon.classList.toggle('dark:text-slate-200', !isFavorite);
                    icon.classList.toggle('dark:text-rose-400', isFavorite);
                    icon.style.fontVariationSettings = isFavorite
                        ? "'FILL' 1, 'wght' 500, 'GRAD' 0, 'opsz' 24"
                        : "'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 24";
                    icon.textContent = 'favorite';
                }

                if (label) {
                    label.textContent = 'Simpan Favorit';
                }
            };

            document.addEventListener('click', async (event) => {
                const button = event.target.closest('[data-favorite-toggle]');
                if (!button) {
                    return;
                }

                event.preventDefault();

                if (button.dataset.loading === '1') {
                    return;
                }

                const isFavorite = button.dataset.isFavorite === '1';
                const url = isFavorite ? button.dataset.destroyUrl : button.dataset.storeUrl;
                const method = isFavorite ? 'DELETE' : 'POST';

                button.dataset.loading = '1';
                button.disabled = true;

                try {
                    const response = await fetch(url, {
                        method,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    });

                    if (!response.ok) {
                        throw new Error('Gagal memperbarui favorit.');
                    }

                    const data = await response.json();
                    updateFavoriteButton(button, Boolean(data.is_favorite));
                } catch (error) {
                    console.error('Gagal memperbarui favorit.', error);
                } finally {
                    button.disabled = false;
                    delete button.dataset.loading;
                }
            });

            document.querySelectorAll('[data-favorite-toggle]').forEach((button) => {
                updateFavoriteButton(button, button.dataset.isFavorite === '1');
            });
        })();
    </script>
@endonce
