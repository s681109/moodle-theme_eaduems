<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Theme helpers for eaduems.
 *
 * @package   theme_eaduems
 * @copyright 2026 EAD/UEMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Returns the main SCSS content.
 *
 * @param theme_config $theme
 * @return string
 */
function theme_eaduems_get_main_scss_content($theme) {
    global $CFG;

    $presetfile = $CFG->dirroot . '/theme/eaduems/scss/preset/default.scss';
    if (is_readable($presetfile)) {
        return file_get_contents($presetfile);
    }

    return file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/plain.scss');
}

/**
 * Returns pre SCSS variables.
 *
 * @param theme_config $theme
 * @return string
 */
function theme_eaduems_get_pre_scss($theme) {
    $scss = '';

    $brandprimary = $theme->settings->palettebrandprimary ?? '#0f6f3c';
    $brandsecondary = $theme->settings->palettebrandsecondary ?? '#0b4f2b';
    $accent = $theme->settings->paletteaccent ?? '#d6df2a';
    $pagebackground = $theme->settings->palettepagebackground ?? '#ffffff';
    $surface = $theme->settings->palettesurface ?? '#f4f8f5';

    $scss .= '$primary: ' . $brandprimary . ";\n";
    $scss .= ':root, body[data-bs-theme="light"] {' . "\n";
    $scss .= '  --eaduems-bg: ' . $pagebackground . ';' . "\n";
    $scss .= '  --eaduems-surface: ' . $surface . ';' . "\n";
    $scss .= '  --eaduems-brand: ' . $brandprimary . ';' . "\n";
    $scss .= '  --eaduems-brand-dark: ' . $brandsecondary . ';' . "\n";
    $scss .= '  --eaduems-accent: ' . $accent . ';' . "\n";
    $scss .= "}\n";

    if (defined('BEHAT_SITE_RUNNING')) {
        $scss .= "\$behatsite: true;\n";
    }

    return $scss;
}

/**
 * Returns additional SCSS.
 *
 * @param theme_config $theme
 * @return string
 */
function theme_eaduems_get_extra_scss($theme) {
    return '';
}

/**
 * Returns precompiled css.
 *
 * @return string
 */
function theme_eaduems_get_precompiled_css() {
    global $CFG;

    return file_get_contents($CFG->dirroot . '/theme/boost/style/moodle.css');
}

/**
 * Serves theme files.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_eaduems_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    if ($context->contextlevel !== CONTEXT_SYSTEM) {
        send_file_not_found();
    }

    $allowed = ['logo', 'logodark', 'favicon', 'loginlogo', 'carouselslide1', 'carouselslide2', 'carouselslide3'];
    if (!in_array($filearea, $allowed, true)) {
        send_file_not_found();
    }

    $theme = theme_config::load('eaduems');
    if (!array_key_exists('cacheability', $options)) {
        $options['cacheability'] = 'public';
    }

    return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
}
