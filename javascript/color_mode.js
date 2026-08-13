(function() {
    var storageKey = 'theme_eaduems_color_mode';

    var getPreferredMode = function() {
        try {
            var stored = window.localStorage.getItem(storageKey);
            if (stored === 'dark' || stored === 'light') {
                return stored;
            }
        } catch (error) {
            // Ignore storage restrictions and fall back to current/preferred mode.
        }

        if (document.body) {
            var current = document.body.getAttribute('data-bs-theme');
            if (current === 'dark' || current === 'light') {
                return current;
            }
        }

        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            return 'dark';
        }

        return 'light';
    };

    var getToggles = function() {
        return document.querySelectorAll('[data-theme-eaduems-color-mode-toggle]');
    };

    var applyMode = function(mode, persist) {
        var isdark = mode === 'dark';

        if (!document.body) {
            return;
        }

        document.body.setAttribute('data-bs-theme', isdark ? 'dark' : 'light');

        getToggles().forEach(function(toggle) {
            toggle.setAttribute('aria-pressed', isdark ? 'true' : 'false');
            toggle.setAttribute('data-theme-eaduems-color-mode', isdark ? 'dark' : 'light');
        });

        if (persist) {
            try {
                window.localStorage.setItem(storageKey, isdark ? 'dark' : 'light');
            } catch (error) {
                // The visual state still changes even when storage is blocked.
            }
        }
    };

    var init = function() {
        var toggles = getToggles();
        if (!toggles.length || !document.body) {
            return;
        }

        applyMode(getPreferredMode(), false);

        toggles.forEach(function(toggle) {
            if (toggle.getAttribute('data-theme-eaduems-color-mode-bound') === 'true') {
                return;
            }

            toggle.setAttribute('data-theme-eaduems-color-mode-bound', 'true');
            toggle.addEventListener('click', function() {
                var current = document.body.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light';
                applyMode(current === 'dark' ? 'light' : 'dark', true);
            });
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}());
