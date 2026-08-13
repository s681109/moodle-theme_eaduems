<?php
// This file is part of Moodle - http://moodle.org/

namespace theme_eaduems\local\settings;

defined('MOODLE_INTERNAL') || die();

/**
 * Theme settings helper.
 *
 * @package   theme_eaduems
 * @copyright 2026 EAD/UEMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {
    /**
     * Returns a theme config object.
     *
     * @return \theme_config
     */
    public static function theme(): \theme_config {
        return \theme_config::load('eaduems');
    }

    /**
     * Returns a normalized text setting.
     *
     * @param string $name
     * @param string $default
     * @return string
     */
    public static function text(string $name, string $default = ''): string {
        $theme = self::theme();
        $value = $theme->settings->{$name} ?? '';
        $value = trim((string)$value);

        return $value !== '' ? $value : $default;
    }

    /**
     * Returns a boolean setting.
     *
     * @param string $name
     * @param bool $default
     * @return bool
     */
    public static function bool(string $name, bool $default = false): bool {
        $theme = self::theme();
        if (!isset($theme->settings->{$name})) {
            return $default;
        }

        // Moodle stores checkbox values as strings; casting "0" directly to bool is true in PHP.
        return (bool)(int)$theme->settings->{$name};
    }

    /**
     * Returns an integer setting.
     *
     * @param string $name
     * @param int $default
     * @return int
     */
    public static function int(string $name, int $default = 0): int {
        $theme = self::theme();
        if (!isset($theme->settings->{$name}) || $theme->settings->{$name} === '') {
            return $default;
        }

        return (int)$theme->settings->{$name};
    }

    /**
     * Returns a file URL setting when available.
     *
     * @param string $filearea
     * @return string
     */
    public static function file_url(string $filearea): string {
        $theme = self::theme();
        $url = $theme->setting_file_url($filearea, $filearea);

        return $url ? $url : '';
    }

    /**
     * Returns the best available institutional logo URL.
     *
     * Preference order:
     * 1. Core logo configured in Site administration > Appearance > Logos
     * 2. Theme-specific uploaded logo
     *
     * @return string
     */
    public static function site_logo_url(): string {
        $theme = self::theme();

        if (method_exists($theme, 'get_logo_url')) {
            $url = $theme->get_logo_url();
            if ($url) {
                return $url->out(false);
            }
        }

        if (method_exists($theme, 'get_compact_logo_url')) {
            $url = $theme->get_compact_logo_url();
            if ($url) {
                return $url->out(false);
            }
        }

        return self::file_url('logo');
    }

    /**
     * Returns the login-specific logo when configured, otherwise the site logo.
     *
     * @return string
     */
    public static function login_logo_url(): string {
        $url = self::file_url('loginlogo');

        return $url !== '' ? $url : self::site_logo_url();
    }

    /**
     * Returns a normalized internal or absolute URL.
     *
     * @param string $name
     * @param string $default
     * @return string
     */
    public static function url(string $name, string $default): string {
        global $CFG;

        $raw = trim(self::text($name, $default));
        if ($raw === '') {
            $raw = $default;
        }

        if (preg_match('/^https?:\\/\\//i', $raw)) {
            return $raw;
        }

        return $CFG->wwwroot . '/' . ltrim($raw, '/');
    }
}
