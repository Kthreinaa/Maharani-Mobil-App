<div class="flex items-center gap-2">
    <label for="locale-switcher" class="sr-only">{{ __('Language') }}</label>
    <select
        id="locale-switcher"
        data-locale-switcher
        class="rounded-lg border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
    >
        <option value="id" @selected(app()->getLocale() === 'id')>ID</option>
        <option value="en" @selected(app()->getLocale() === 'en')>EN</option>
    </select>

    <button
        type="button"
        data-theme-toggle
        onclick="window.mmToggleTheme && window.mmToggleTheme()"
        class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-800"
        aria-label="{{ __('Dark Mode') }}"
        title="{{ __('Dark Mode') }}"
    >
        <span data-theme-toggle-label class="material-symbols-outlined text-base leading-none">light_mode</span>
        <span data-theme-toggle-text class="ml-1 hidden text-[11px] font-semibold sm:inline">{{ __('Light') }}</span>
    </button>
</div>
