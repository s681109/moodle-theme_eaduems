<?php
// This file is part of Moodle - http://moodle.org/

namespace theme_eaduems\local\frontpage;

use theme_eaduems\local\header\context_builder as header_context_builder;
use theme_eaduems\local\settings\helper;

defined('MOODLE_INTERNAL') || die();

/**
 * Builds front page template context.
 *
 * @package   theme_eaduems
 * @copyright 2026 EAD/UEMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class context_builder {
    /**
     * Build the front page context.
     *
     * @param \renderer_base $output
     * @param \moodle_page $page
     * @param \stdClass $site
     * @return array
     */
    public static function build(\renderer_base $output, \moodle_page $page, \stdClass $site): array {
        global $CFG;

        $maincontent = $output->main_content();
        $addblockbutton = $output->addblockbutton();
        $blockshtml = $output->blocks('side-pre');
        $hasblocks = (strpos($blockshtml, 'data-block=') !== false || !empty($addblockbutton));
        $blockdraweropen = isloggedin() && (get_user_preferences('drawer-open-block') == true);
        if (!$hasblocks) {
            $blockdraweropen = false;
        }
        $extraclasses = ['theme-eaduems-frontpage'];
        if ($hasblocks) {
            $extraclasses[] = 'uses-drawers';
        }
        if ($blockdraweropen) {
            $extraclasses[] = 'show-drawer-right';
        }

        $homeurl = $CFG->wwwroot . '/';
        $supporturl = (new \moodle_url('/user/contactsitesupport.php'))->out(false);
        $isloggedin = isloggedin() && !isguestuser();
        $sitelogourl = header_context_builder::resolve_site_logo_url($output);
        $primarycta = self::build_hero_cta($isloggedin ? 'loggedinprimary' : 'guestprimary');
        $secondarycta = self::build_hero_cta($isloggedin ? 'loggedinsecondary' : 'guestsecondary');
        $catalogurl = helper::url('primaryctaurl', '/local/catalogo_eaduems/public/index.php');
        $footercatalogurl = (new \moodle_url('/local/catalogo_eaduems/public/index.php'))->out(false);
        $loginurl = helper::url('secondaryctaurl', '/login/index.php');
        $heropanel = self::build_hero_panel_context();
        $featureditems = data_provider::get_featured_paths(helper::int('featuredlimit', 3));
        $onboardingitems = data_provider::get_onboarding_steps();
        $newsperpage = helper::int('newsperpage', 4);
        $newsperpage = $newsperpage > 0 ? min(4, $newsperpage) : 4;
        $newsmaxitems = helper::int('newsmaxitems', 12);
        $newsmaxitems = $newsmaxitems > 0 ? min(24, $newsmaxitems) : 12;
        $newsmaxitems = max($newsperpage, $newsmaxitems);
        $newsitems = data_provider::get_news_items($newsmaxitems);

        return [
            'output' => $output,
            'bodyattributes' => $output->body_attributes($extraclasses),
            'hasmaincontent' => trim(strip_tags($maincontent)) !== '',
            'maincontent' => $maincontent,
            'hasblocks' => $hasblocks,
            'blockdraweropen' => $blockdraweropen,
            'sidepreblocks' => $blockshtml,
            'addblockbutton' => $addblockbutton,
            'siteheader' => header_context_builder::build($output, $page, $site),
            'hero' => [
                'eyebrow' => helper::text('heroeyebrow', get_string('defaultheroeyebrow', 'theme_eaduems')),
                'title' => helper::text('herotitle', get_string('defaultherotitle', 'theme_eaduems')),
                'intro' => helper::text('herointro', get_string('defaultherointro', 'theme_eaduems')),
                'primary_cta' => $primarycta,
                'secondary_cta' => $secondarycta,
                'panel' => $heropanel,
            ],
            'carousel' => helper::bool('showcarousel', true) ? self::build_carousel_context() : false,
            'featuredpaths' => helper::bool('showfeaturedpaths', true) ? array_merge([
                'title' => helper::text('featureheadingoverride', get_string('featureheading', 'theme_eaduems')),
                'intro' => helper::text('featureintrooverride', get_string('featureintro', 'theme_eaduems')),
                'sectionid' => 'destaques',
            ], self::build_featured_context($featureditems, 4)) : false,
            'onboarding' => helper::bool('showonboarding', true) ? [
                'title' => helper::text('onboardingheadingoverride', get_string('onboardingheading', 'theme_eaduems')),
                'intro' => helper::text('onboardingintrooverride', get_string('onboardingintro', 'theme_eaduems')),
                'rows' => self::chunk_items($onboardingitems, 4),
            ] : false,
            'newsfeed' => helper::bool('shownewsfeed', true) ? array_merge([
                'title' => helper::text('newsheadingoverride', get_string('newsheading', 'theme_eaduems')),
                'intro' => helper::text('newsintrooverride', get_string('newsintro', 'theme_eaduems')),
                'sectionid' => 'noticias',
            ], self::build_newsfeed_context($newsitems, $newsperpage)) : false,
            'finalcta' => helper::bool('showfinalcta', true) ? [
                'title' => helper::text('finalctaheadingoverride', get_string('finalctaheading', 'theme_eaduems')),
                'intro' => helper::text('finalctaintrooverride', get_string('finalctaintro', 'theme_eaduems')),
                'primary_cta' => $primarycta,
                'secondary_cta' => $secondarycta,
            ] : false,
            'institutionalfooter' => [
                'headline' => helper::text('footerheadline', get_string('defaultfooterheadline', 'theme_eaduems')),
                'body' => helper::text('footerbody', get_string('defaultfooterbody', 'theme_eaduems')),
                'site_logo_url' => $sitelogourl,
                'links_heading' => get_string('footerlinksheading', 'theme_eaduems'),
                'categories_heading' => get_string('footercategoriesheading', 'theme_eaduems'),
                'links' => self::build_footer_links($homeurl, $footercatalogurl, $loginurl, $supporturl),
                'categories' => data_provider::get_footer_categories(4),
                'catalog_link' => [
                    'label' => get_string('footerlinkcatalog', 'theme_eaduems'),
                    'url' => $footercatalogurl,
                    'icon' => 'fa-arrow-right',
                ],
            ],
            'bottomfooter' => [
                'copyright' => 'Copyright 2026 EAD/UEMS',
                'support_label' => get_string('supportlabel', 'theme_eaduems'),
                'support_url' => $supporturl,
            ],
        ];
    }


    /**
     * Build a Hero CTA according to authentication state.
     *
     * @param string $slot
     * @return array|false
     */
    protected static function build_hero_cta(string $slot) {
        $defaults = [
            'guestprimary' => [
                'show' => 'showprimarycta',
                'label' => 'primaryctalabel',
                'url' => 'primaryctaurl',
                'defaultlabel' => get_string('catalogcta', 'theme_eaduems'),
                'defaulturl' => '/local/catalogo_eaduems/public/index.php',
            ],
            'guestsecondary' => [
                'show' => 'showsecondarycta',
                'label' => 'secondaryctalabel',
                'url' => 'secondaryctaurl',
                'defaultlabel' => get_string('logincta', 'theme_eaduems'),
                'defaulturl' => '/login/index.php',
            ],
            'loggedinprimary' => [
                'show' => 'showloggedinprimarycta',
                'label' => 'primaryloggedinctalabel',
                'url' => 'primaryloggedinctaurl',
                'defaultlabel' => get_string('catalogcta', 'theme_eaduems'),
                'defaulturl' => '/local/catalogo_eaduems/public/index.php',
            ],
            'loggedinsecondary' => [
                'show' => 'showloggedinsecondarycta',
                'label' => 'secondaryloggedinctalabel',
                'url' => 'secondaryloggedinctaurl',
                'defaultlabel' => get_string('dashboardcta', 'theme_eaduems'),
                'defaulturl' => '/my/',
            ],
        ];

        if (!isset($defaults[$slot]) || !helper::bool($defaults[$slot]['show'], true)) {
            return false;
        }

        return [
            'label' => helper::text($defaults[$slot]['label'], $defaults[$slot]['defaultlabel']),
            'url' => helper::url($defaults[$slot]['url'], $defaults[$slot]['defaulturl']),
        ];
    }

    /**
     * Build the ordered footer links from theme settings.
     *
     * @param string $homeurl
     * @param string $catalogurl
     * @param string $loginurl
     * @param string $supporturl
     * @return array
     */
    protected static function build_footer_links(string $homeurl, string $catalogurl, string $loginurl, string $supporturl): array {
        global $CFG;

        $defaults = [
            ['label' => get_string('footerlinkhome', 'theme_eaduems'), 'url' => $homeurl, 'icon' => 'fa-home'],
            ['label' => get_string('footerlinkcatalog', 'theme_eaduems'), 'url' => $catalogurl, 'icon' => 'fa-graduation-cap', 'newtab' => true],
            ['label' => get_string('footerlinklogin', 'theme_eaduems'), 'url' => $loginurl, 'icon' => 'fa-sign-in'],
            ['label' => get_string('footerlinksupport', 'theme_eaduems'), 'url' => $supporturl, 'icon' => 'fa-life-ring'],
            ['label' => get_string('footerlinkabout', 'theme_eaduems'), 'url' => '#como-funciona', 'icon' => 'fa-info-circle'],
            [
                'label' => get_string('footerlinkpolicies', 'theme_eaduems'),
                'url' => (new \moodle_url('/admin/tool/policy/viewall.php'))->out(false),
                'icon' => 'fa-file-text-o',
                'requirespolicyhandler' => true,
            ],
        ];
        $count = max(0, min(8, helper::int('footerlinkcount', 6)));
        $links = [];

        for ($i = 1; $i <= $count; $i++) {
            $default = $defaults[$i - 1] ?? ['label' => '', 'url' => '', 'icon' => 'fa-link'];
            if (!helper::bool('footerlink' . $i . 'enabled', true)) {
                continue;
            }
            if (!empty($default['requirespolicyhandler']) &&
                    (empty($CFG->sitepolicyhandler) || $CFG->sitepolicyhandler !== 'tool_policy')) {
                continue;
            }

            $label = helper::text('footerlink' . $i . 'label', $default['label']);
            $url = self::footer_url('footerlink' . $i . 'url', $default['url']);
            if ($label === '' || $url === '') {
                continue;
            }

            $links[] = [
                'label' => $label,
                'url' => $url,
                'icon' => $default['icon'],
                // Institutional footer destinations must not interrupt the current Moodle session.
                'newtab' => true,
            ];
        }

        return $links;
    }

    /**
     * Normalize a configurable footer URL while retaining in-page anchors.
     *
     * @param string $name
     * @param string $default
     * @return string
     */
    protected static function footer_url(string $name, string $default): string {
        global $CFG;

        $url = helper::text($name, $default);
        if ($url === '' || strpos($url, '#') === 0 || preg_match('/^https?:\/\//i', $url)) {
            return $url;
        }

        return $CFG->wwwroot . '/' . ltrim($url, '/');
    }
    /**
     * Build configurable Hero panel context.
     *
     * @return array|false
     */
    protected static function build_hero_panel_context() {
        if (!helper::bool('showheropanel', true)) {
            return false;
        }

        $pointcount = max(0, min(6, helper::int('heropanelpointcount', 3)));
        $points = [];
        for ($i = 1; $i <= $pointcount; $i++) {
            $default = $i <= 3 ? get_string('heropanelpoint' . $i, 'theme_eaduems') : '';
            $text = helper::text('heropanelpoint' . $i . 'override', $default);
            if ($text !== '') {
                $points[] = ['text' => $text];
            }
        }

        return [
            'kicker' => helper::text('heropanelkickeroverride', get_string('heropanelkicker', 'theme_eaduems')),
            'title' => helper::text('heropaneltitleoverride', get_string('heropaneltitle', 'theme_eaduems')),
            'intro' => helper::text('heropanelintrooverride', get_string('heropanelintro', 'theme_eaduems')),
            'haspoints' => !empty($points),
            'points' => $points,
        ];
    }

    /**
     * Build carousel context.
     *
     * @return array
     */
    protected static function build_carousel_context(): array {
        $slides = data_provider::get_carousel_slides();

        return [
            'title' => helper::text('carouseltitle', get_string('defaultcarouseltitle', 'theme_eaduems')),
            'autoplay' => helper::bool('carouselautoplay', true),
            'hasmultiple' => count($slides) > 1,
            'slides' => $slides,
        ];
    }

    /**
     * Build explicit, accessible pages for the News carousel.
     *
     * @param array $items
     * @param int $size
     * @return array
     */
    protected static function build_newsfeed_context(array $items, int $size): array {
        $pages = [];
        foreach (array_chunk($items, max(1, $size)) as $index => $chunk) {
            $pages[] = [
                'index' => $index,
                'number' => $index + 1,
                'isactive' => $index === 0,
                'items' => array_values($chunk),
            ];
        }

        return [
            'pages' => $pages,
            'hasmultiplepages' => count($pages) > 1,
        ];
    }

    /**
     * Build accessible pages for the Featured carousel.
     *
     * Featured categories share the four-card navigation pattern used by News.
     *
     * @param array $items
     * @param int $size
     * @return array
     */
    protected static function build_featured_context(array $items, int $size): array {
        $pages = [];
        foreach (array_chunk($items, max(1, $size)) as $index => $chunk) {
            $pages[] = [
                'index' => $index,
                'number' => $index + 1,
                'isactive' => $index === 0,
                'items' => array_values($chunk),
            ];
        }

        return [
            'pages' => $pages,
            'hasmultiplepages' => count($pages) > 1,
        ];
    }

    /**
     * Split items into explicit rows for stable front page grids.
     *
     * @param array $items
     * @param int $size
     * @return array
     */
    protected static function chunk_items(array $items, int $size): array {
        $rows = [];
        foreach (array_chunk($items, max(1, $size)) as $chunk) {
            $rows[] = ['items' => array_values($chunk)];
        }
        return $rows;
    }

}
