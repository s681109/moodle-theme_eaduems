<?php
// This file is part of Moodle - http://moodle.org/

namespace theme_eaduems\local\header;

use theme_eaduems\local\settings\helper;

defined('MOODLE_INTERNAL') || die();

/**
 * Builds the shared institutional header context.
 *
 * @package   theme_eaduems
 * @copyright 2026 EAD/UEMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class context_builder {
    /**
     * Build the context consumed by navigation/site_header.mustache.
     *
     * @param \renderer_base $output
     * @param \moodle_page $page
     * @param \stdClass $site
     * @return array
     */
    public static function build(\renderer_base $output, \moodle_page $page, \stdClass $site): array {
        global $CFG;

        $isloggedin = isloggedin() && !isguestuser();
        $catalogurl = helper::url('primaryctaurl', '/local/catalogo_eaduems/public/index.php');
        $loginurl = helper::url('headerloginurl', '/login/index.php');
        $certificatevalidationurl = (new \moodle_url('/admin/tool/certificate/index.php'))->out(false);
        $signupurl = (new \moodle_url('/login/signup.php'))->out(false);
        $showsignup = !$isloggedin && !empty($CFG->registerauth);
        $primary = new \core\navigation\output\primary($page);
        $renderer = $page->get_renderer('core');
        $primarymenu = $primary->export_for_template($renderer);

        return [
            'home_url' => $CFG->wwwroot . '/',
            'site_name' => self::site_shortname($site),
            'site_tagline' => helper::text('sitetagline', get_string('defaultsitetagline', 'theme_eaduems')),
            'site_logo_url' => self::resolve_site_logo_url($output),
            'login_url' => $loginurl,
            'login_label' => helper::text('headerloginlabel', get_string('logincta', 'theme_eaduems')),
            'signup_url' => $signupurl,
            'signup_label' => helper::text('headersignuplabel', get_string('createaccountcta', 'theme_eaduems')),
            'showsignup' => $showsignup,
            'isloggedin' => $isloggedin,
            'navbarpluginoutput' => $isloggedin ? $output->navbar_plugin_output() : '',
            'usermenu' => self::value($primarymenu, 'user', false),
            'editswitch' => $isloggedin ? $output->edit_switch() : '',
            'primary_links' => self::primary_links($primarymenu, $isloggedin, self::is_catalog_page($page)),
        ];
    }

    /**
     * Resolve the formatted site shortname with the same context used by the front page.
     *
     * @param \stdClass $site
     * @return string
     */
    public static function site_shortname(\stdClass $site): string {
        return format_string($site->shortname, true, [
            'context' => \context_course::instance(SITEID),
            'escape' => false,
        ]);
    }

    /**
     * Resolve the best available institutional logo URL from Moodle branding.
     *
     * @param \renderer_base $output
     * @return string
     */
    public static function resolve_site_logo_url(\renderer_base $output): string {
        if (method_exists($output, 'get_logo_url')) {
            $logourl = $output->get_logo_url();
            if (!empty($logourl)) {
                return (string)$logourl;
            }
        }

        if (method_exists($output, 'get_compact_logo_url')) {
            $compactlogourl = $output->get_compact_logo_url();
            if (!empty($compactlogourl)) {
                return (string)$compactlogourl;
            }
        }

        return helper::file_url('logo');
    }

    /**
     * Build stable primary links for the shared institutional header.
     *
     * @param string $catalogurl
     * @param string $certificatevalidationurl
     * @param mixed $primarymenu
     * @param bool $isloggedin
     * @return array
     */
    protected static function primary_links($primarymenu = [], bool $isloggedin = false, bool $iscatalogpage = false): array {
        $configured = self::configured_primary_links($isloggedin);
        $links = $configured['configured'] ? $configured['links'] : self::default_primary_links();

        if ($isloggedin) {
            $links = array_merge($links, self::native_primary_links($primarymenu, $links));
        }

        foreach ($links as &$link) {
            $link['isactive'] = $iscatalogpage && self::is_catalog_url((string) ($link['url'] ?? ''));
        }
        unset($link);

        return $links;
    }

    /**
     * Checks whether the current request is a public catalog page.
     *
     * @param \moodle_page $page
     * @return bool
     */
    protected static function is_catalog_page(\moodle_page $page): bool {
        return strpos($page->url->get_path(), '/local/catalogo_eaduems/public/') === 0;
    }

    /**
     * Checks whether a primary navigation URL points to the public catalog.
     *
     * @param string $url
     * @return bool
     */
    protected static function is_catalog_url(string $url): bool {
        $path = (string) parse_url($url, PHP_URL_PATH);

        return strpos($path, '/local/catalogo_eaduems/public/') === 0;
    }

    /**
     * Build configurable institutional navband links.
     *
     * @param bool $isloggedin
     * @return array{configured: bool, links: array}
     */
    protected static function configured_primary_links(bool $isloggedin): array {
        $count = max(0, min(8, helper::int('navbanditemcount', 4)));
        $defaults = self::default_primary_links();
        $links = [];

        if ($count === 0) {
            return ['configured' => true, 'links' => []];
        }

        for ($i = 1; $i <= $count; $i++) {
            $default = $defaults[$i - 1] ?? ['label' => '', 'url' => ''];
            $label = helper::text('navbanditem' . $i . 'label', $default['label']);
            $url = self::navband_url('navbanditem' . $i . 'url', $default['url']);
            $visibility = helper::text('navbanditem' . $i . 'visibility', 'all');

            if ($label === '' || $url === '' || !self::is_visible_for_user($visibility, $isloggedin)) {
                continue;
            }

            $links[] = [
                'label' => $label,
                'url' => $url,
            ];
        }

        return [
            'configured' => !empty($links),
            'links' => $links,
        ];
    }

    /**
     * Build default institutional navband links.
     *
     * @return array
     */
    protected static function default_primary_links(): array {
        return [
            [
                'label' => get_string('catalogcta', 'theme_eaduems'),
                'url' => self::navband_url('', '/local/catalogo_eaduems/public/index.php'),
            ],
            [
                'label' => get_string('certificatevalidationcta', 'theme_eaduems'),
                'url' => (new \moodle_url('/admin/tool/certificate/index.php'))->out(false),
            ],
            [
                'label' => get_string('featureheading', 'theme_eaduems'),
                'url' => '#destaques',
            ],
            [
                'label' => get_string('newsheading', 'theme_eaduems'),
                'url' => '#noticias',
            ],
        ];
    }

    /**
     * Normalize a navband URL while preserving anchors.
     *
     * @param string $name
     * @param string $default
     * @return string
     */
    protected static function navband_url(string $name, string $default): string {
        global $CFG;

        $raw = $name === '' ? trim($default) : helper::text($name, $default);
        if ($raw === '') {
            return '';
        }

        if (strpos($raw, '#') === 0 || preg_match('/^https?:\/\//i', $raw)) {
            return $raw;
        }

        return $CFG->wwwroot . '/' . ltrim($raw, '/');
    }
    /**
     * Check whether a configured link is visible for the current auth state.
     *
     * @param string $visibility
     * @param bool $isloggedin
     * @return bool
     */
    protected static function is_visible_for_user(string $visibility, bool $isloggedin): bool {
        if ($visibility === 'guest') {
            return !$isloggedin;
        }

        if ($visibility === 'loggedin') {
            return $isloggedin;
        }

        return true;
    }

    /**
     * Extract safe Moodle primary navigation links without reintroducing the Boost navbar.
     *
     * @param mixed $primarymenu
     * @param array $existinglinks
     * @return array
     */
    protected static function native_primary_links($primarymenu, array $existinglinks): array {
        $moremenu = self::value($primarymenu, 'moremenu', []);
        $nodes = self::value($moremenu, 'nodearray', []);
        $existinglabels = [];
        $existingurls = [];
        foreach ($existinglinks as $link) {
            $existinglabels[self::normalize_label($link['label'] ?? '')] = true;
            $existingurls[(string)($link['url'] ?? '')] = true;
        }

        $links = [];
        foreach ($nodes as $node) {
            $label = trim(strip_tags((string)(self::value($node, 'text', self::value($node, 'title', '')))));
            $url = (string)self::value($node, 'url', '');
            $normalized = self::normalize_label($label);

            if ($label === '' || $url === '' || isset($existinglabels[$normalized]) || isset($existingurls[$url])) {
                continue;
            }
            if (in_array($normalized, ['pagina inicial', 'home'], true)) {
                continue;
            }

            $links[] = [
                'label' => $label,
                'url' => $url,
            ];
            $existinglabels[$normalized] = true;
            $existingurls[$url] = true;

            if (count($links) >= 5) {
                break;
            }
        }

        return $links;
    }

    /**
     * Normalize labels for simple duplicate detection.
     *
     * @param string $label
     * @return string
     */
    protected static function normalize_label(string $label): string {
        return \core_text::strtolower(trim($label));
    }

    /**
     * Read exported Moodle template data that may arrive as an array or an object.
     *
     * @param mixed $source
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected static function value($source, string $key, $default = null) {
        if (is_array($source)) {
            return $source[$key] ?? $default;
        }

        if (is_object($source)) {
            return $source->{$key} ?? $default;
        }

        return $default;
    }
}
