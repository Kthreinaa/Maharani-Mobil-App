<div id="login-required-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/60 p-4">
    <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl dark:bg-slate-900">
        <h3 class="mb-2 text-lg font-bold text-slate-900 dark:text-slate-100">{{ __('Login Required') }}</h3>
        <p id="login-required-message" class="mb-6 text-sm text-slate-600 dark:text-slate-300">{{ __('Please login first') }}</p>
        <div class="flex justify-end gap-2">
            <button
                type="button"
                data-login-modal-cancel
                class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-100 dark:hover:bg-slate-800"
            >
                {{ __('Cancel') }}
            </button>
            <a
                href="{{ route('login') }}"
                data-login-modal-go
                class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900"
            >
                {{ __('Continue to Login') }}
            </a>
        </div>
    </div>
</div>

<script>
    (() => {
        const loginModal = document.getElementById('login-required-modal');
        const loginModalMessage = document.getElementById('login-required-message');
        const loginModalGo = document.querySelector('[data-login-modal-go]');
        const loginModalCancel = document.querySelector('[data-login-modal-cancel]');
        const searchModal = document.getElementById('mm-search-modal');

        const closeModal = () => {
            if (!loginModal) {
                return;
            }

            loginModal.classList.add('hidden');
            loginModal.classList.remove('flex');
        };

        if (loginModalCancel) {
            loginModalCancel.addEventListener('click', closeModal);
        }

        if (loginModal) {
            loginModal.addEventListener('click', (event) => {
                if (event.target === loginModal) {
                    closeModal();
                }
            });
        }

        document.querySelectorAll('.js-login-required').forEach((element) => {
            element.addEventListener('click', (event) => {
                event.preventDefault();

                const loginUrl = element.getAttribute('href') || '{{ route('login') }}';
                const customMessage = element.getAttribute('data-popup-message');

                if (loginModalMessage && customMessage) {
                    loginModalMessage.textContent = customMessage;
                }

                if (loginModalGo) {
                    loginModalGo.setAttribute('href', loginUrl);
                }

                if (loginModal) {
                    loginModal.classList.remove('hidden');
                    loginModal.classList.add('flex');
                }
            });
        });

        // Search modal (landing/home): open from any element with [data-open-search-modal]
        const openSearchModal = () => {
            if (!searchModal) return;
            searchModal.classList.remove('hidden');
            searchModal.classList.add('flex');
        };

        const closeSearchModal = () => {
            if (!searchModal) return;
            searchModal.classList.add('hidden');
            searchModal.classList.remove('flex');
        };

        document.querySelectorAll('[data-open-search-modal]').forEach((element) => {
            element.addEventListener('click', (event) => {
                // Allow fallback navigation if modal not present on this page.
                if (!searchModal) {
                    return;
                }
                event.preventDefault();
                openSearchModal();
            });
        });

        if (searchModal) {
            searchModal.querySelectorAll('[data-mm-search-close]').forEach((el) => {
                el.addEventListener('click', closeSearchModal);
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeSearchModal();
                }
            });
        }

        const digitOnlySelector = '[data-digits-only]';
        const sanitizeDigits = (value) => String(value ?? '').replace(/\D+/g, '');

        document.querySelectorAll(digitOnlySelector).forEach((input) => {
            const warning = input.parentElement?.querySelector('[data-digits-warning]');
            const label = input.getAttribute('data-digits-label') || 'Kolom ini';

            const showWarning = (message) => {
                if (!warning) {
                    return;
                }

                warning.textContent = message;
                warning.classList.remove('hidden');
            };

            const hideWarning = () => {
                if (!warning) {
                    return;
                }

                warning.textContent = '';
                warning.classList.add('hidden');
            };

            const normalize = () => {
                const rawValue = input.value;
                const sanitized = sanitizeDigits(input.value);
                const hadInvalidChars = rawValue !== sanitized;

                if (hadInvalidChars) {
                    input.value = sanitized;
                    input.dataset.invalidAttempt = '1';
                    showWarning(`${label} hanya boleh diisi angka.`);
                    return;
                }

                if (input.value !== '' || input.dataset.invalidAttempt !== '1') {
                    input.dataset.invalidAttempt = '0';
                    hideWarning();
                }
            };

            input.addEventListener('input', normalize);
            input.addEventListener('blur', normalize);
            input.addEventListener('focus', () => {
                if (input.value === '' && input.dataset.invalidAttempt === '1') {
                    input.dataset.invalidAttempt = '0';
                    hideWarning();
                }
            });
            normalize();
        });

        document.querySelectorAll('form').forEach((form) => {
            if (!form.querySelector(digitOnlySelector)) {
                return;
            }

            form.addEventListener('submit', (event) => {
                let blockedInput = null;

                form.querySelectorAll(digitOnlySelector).forEach((input) => {
                    input.value = sanitizeDigits(input.value);

                    if (!blockedInput && input.dataset.invalidAttempt === '1' && input.value === '') {
                        blockedInput = input;
                    }
                });

                if (blockedInput) {
                    const warning = blockedInput.parentElement?.querySelector('[data-digits-warning]');
                    if (warning) {
                        warning.textContent = `${blockedInput.getAttribute('data-digits-label') || 'Kolom ini'} harus diisi dengan angka yang benar.`;
                        warning.classList.remove('hidden');
                    }
                    blockedInput.focus();
                    event.preventDefault();
                }
            });
        });
    })();
</script>
