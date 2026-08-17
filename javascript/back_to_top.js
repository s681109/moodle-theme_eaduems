/*
 * This file is part of Moodle - http://moodle.org/
 *
 * Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

/**
 * Displays an accessible shortcut to the start of long pages.
 */
(function() {
    var selector = '[data-theme-eaduems-back-to-top]';
    var threshold = 400;

    var init = function() {
        var button = document.querySelector(selector);
        if (!button || button.getAttribute('data-theme-eaduems-back-to-top-bound') === 'true') {
            return;
        }

        button.setAttribute('data-theme-eaduems-back-to-top-bound', 'true');

        var updateVisibility = function() {
            button.hidden = window.scrollY < threshold;
        };

        var ticking = false;
        window.addEventListener('scroll', function() {
            if (ticking) {
                return;
            }

            ticking = true;
            window.requestAnimationFrame(function() {
                updateVisibility();
                ticking = false;
            });
        }, {passive: true});

        button.addEventListener('click', function() {
            var reducedmotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            if (reducedmotion) {
                window.scrollTo(0, 0);
                return;
            }

            window.scrollTo({top: 0, behavior: 'smooth'});
        });

        updateVisibility();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}());
