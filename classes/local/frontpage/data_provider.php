<?php
// This file is part of Moodle - http://moodle.org/

namespace theme_eaduems\local\frontpage;

defined('MOODLE_INTERNAL') || die();

/**
 * Provides dynamic front page data.
 *
 * @package   theme_eaduems
 * @copyright 2026 EAD/UEMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class data_provider {
    /**
     * Return featured top-level categories.
     *
     * @param int $limit
     * @return array
     */
    public static function get_featured_paths(int $limit = 3): array {
        global $DB;

        $records = $DB->get_records_select(
            'course_categories',
            'visible = :visible AND parent = :parent',
            ['visible' => 1, 'parent' => 0],
            'sortorder ASC',
            'id, name, description',
            0,
            $limit
        );

        $items = [];
        foreach ($records as $record) {
            // Category descriptions are HTML; format_string() can corrupt accented rich text.
            $description = trim((string)preg_replace('/\s+/u', ' ', strip_tags((string)$record->description)));
            if ($description === '') {
                $description = get_string('featuredefaultdescription', 'theme_eaduems');
            } else {
                $description = shorten_text($description, 140, true);
            }

            $items[] = [
                'title' => format_string($record->name, true),
                'description' => $description,
                'url' => (new \moodle_url('/local/catalogo_eaduems/public/index.php', [
                    'category' => (int)$record->id,
                ]))->out(false),
                'linklabel' => get_string('explorecategory', 'theme_eaduems'),
            ];
        }

        if (!empty($items)) {
            return $items;
        }

        return [
            [
                'title' => get_string('fallbackfeatureone', 'theme_eaduems'),
                'description' => get_string('fallbackfeatureonedesc', 'theme_eaduems'),
                'url' => '/local/catalogo_eaduems/public/index.php',
                'linklabel' => get_string('catalogcta', 'theme_eaduems'),
            ],
            [
                'title' => get_string('fallbackfeaturetwo', 'theme_eaduems'),
                'description' => get_string('fallbackfeaturetwodesc', 'theme_eaduems'),
                'url' => '/my/',
                'linklabel' => get_string('dashboardcta', 'theme_eaduems'),
            ],
            [
                'title' => get_string('fallbackfeaturethree', 'theme_eaduems'),
                'description' => get_string('fallbackfeaturethreedesc', 'theme_eaduems'),
                'url' => '/login/index.php',
                'linklabel' => get_string('logincta', 'theme_eaduems'),
            ],
        ];
    }

    /**
     * Return latest site announcements when available.
     *
     * @param int $limit
     * @return array
     */
    public static function get_news_items(int $limit = 3): array {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/mod/forum/lib.php');

        $forum = $DB->get_record('forum', [
            'course' => SITEID,
            'type' => 'news',
        ], '*', IGNORE_MISSING);

        if (!$forum) {
            return self::get_news_fallback_items();
        }

        $modinfo = get_fast_modinfo(SITEID);
        if (empty($modinfo->instances['forum'][$forum->id])) {
            return self::get_news_fallback_items();
        }

        /** @var \cm_info $cm */
        $cm = $modinfo->instances['forum'][$forum->id];
        if (!$cm->uservisible) {
            return self::get_news_fallback_items();
        }

        $context = \context_module::instance($cm->id);
        // Preserve Moodle's native fixed-discussion priority before recency.
        $sort = forum_get_default_sort_order(true, 'p.modified', 'd', true);
        $discussions = forum_get_discussions(
            $cm,
            $sort,
            false,
            -1,
            $limit,
            false,
            -1,
            0,
            FORUM_POSTS_ALL_USER_GROUPS
        );

        if (empty($discussions)) {
            return self::get_news_fallback_items();
        }

        $items = [];
        foreach ($discussions as $discussion) {
            // Delegate group visibility to the native Forum permission contract.
            if (!forum_is_user_group_discussion($cm, (int)$discussion->groupid)) {
                continue;
            }

            $post = $DB->get_record_sql(
                "SELECT fp.*, u.firstname, u.lastname, u.firstnamephonetic, u.lastnamephonetic, u.middlename, u.alternatename
                   FROM {forum_posts} fp
                   JOIN {user} u ON u.id = fp.userid
                  WHERE fp.id = :postid",
                ['postid' => $discussion->firstpost],
                IGNORE_MISSING
            );

            if (!$post) {
                continue;
            }

            $excerpt = trim(html_to_text((string)$post->message, 0, false));
            $excerpt = preg_replace('/(?<=\p{Ll})(?=\p{Lu})/u', ' ', $excerpt);
            $excerpt = preg_replace('/(?<=\d)(?=\pL)/u', ' ', $excerpt);
            $excerpt = preg_replace('/(?<=\pL)(?=\d)/u', ' ', $excerpt);
            $excerpt = trim((string)preg_replace('/\s+/u', ' ', $excerpt));
            $title = trim(format_string((string)$discussion->name, true, ['context' => $context]));
            if ($title === '') {
                $title = get_string('newsfallbacktitle', 'theme_eaduems');
            }
            $excerptnormalized = \core_text::strtolower((string)preg_replace('/[\s\p{P}]+/u', '', $excerpt));
            $titlenormalized = \core_text::strtolower((string)preg_replace('/[\s\p{P}]+/u', '', $title));
            $excerptwords = preg_split('/\s+/u', $excerpt, -1, PREG_SPLIT_NO_EMPTY) ?: [];
            $uniqueexcerptwords = array_unique(array_map(static function(string $word): string {
                return \core_text::strtolower((string)preg_replace('/[\p{P}]+/u', '', $word));
            }, $excerptwords));
            $uniqueexcerptwords = array_values(array_filter($uniqueexcerptwords, static function(string $word): bool {
                return $word !== '';
            }));

            if ($excerptnormalized !== '' && $titlenormalized !== '' &&
                    preg_match('/^(?:' . preg_quote($titlenormalized, '/') . '){2,}$/u', $excerptnormalized)) {
                $excerpt = get_string('newsfallbackexcerpt', 'theme_eaduems');
            } else if (count($excerptwords) >= 6 && count($uniqueexcerptwords) <= 3) {
                $excerpt = get_string('newsfallbackexcerpt', 'theme_eaduems');
            }

            $imageurl = self::get_news_image_url($post, $context);
            $items[] = [
                'title' => $title,
                'hasimage' => $imageurl !== '',
                'imageurl' => $imageurl,
                'meta' => !empty($discussion->userdeleted)
                    ? get_string('newsfallbackmeta', 'theme_eaduems')
                    : trim(fullname($post)) . ' - ' .
                        userdate((int)$discussion->timemodified, get_string('strftimedatetime', 'langconfig')),
                'excerpt' => $excerpt === '' ? get_string('newsfallbackexcerpt', 'theme_eaduems') : shorten_text($excerpt, 180, true),
                'url' => (new \moodle_url('/mod/forum/discuss.php', ['d' => (int)$discussion->id]))->out(false),
                'linklabel' => get_string('newsmorelabel', 'theme_eaduems'),
            ];
        }

        return !empty($items) ? $items : self::get_news_fallback_items();
    }
    /**
     * Return the original image embedded in or attached to a forum post.
     *
     * @param \stdClass $post
     * @param \context_module $context
     * @return string
     */
    protected static function get_news_image_url(\stdClass $post, \context_module $context): string {
        // Avoid rendering a broken image when a stored editor reference no longer has a file.
        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', (string)$post->message, $matches) &&
                str_starts_with(html_entity_decode((string)$matches[1]), '@@PLUGINFILE@@/')) {
            $relativepath = substr(html_entity_decode((string)$matches[1]), strlen('@@PLUGINFILE@@'));
            $filepath = '/' . trim(str_replace('\\', '/', dirname($relativepath)), '/');
            $filepath = $filepath === '/.' ? '/' : $filepath . '/';
            $filename = basename($relativepath);
            $fs = get_file_storage();
            if (!$fs->file_exists($context->id, 'mod_forum', 'post', $post->id, $filepath, $filename)) {
                return '';
            }
        }

        $messagehtml = file_rewrite_pluginfile_urls(
            (string)$post->message,
            'pluginfile.php',
            $context->id,
            'mod_forum',
            'post',
            $post->id
        );
        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $messagehtml, $matches)) {
            return (string)$matches[1];
        }

        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'mod_forum', 'attachment', $post->id, 'filename', false);
        foreach ($files as $file) {
            if (strpos((string)$file->get_mimetype(), 'image/') !== 0) {
                continue;
            }
            return \moodle_url::make_pluginfile_url(
                $context->id,
                'mod_forum',
                'attachment',
                $post->id,
                '/',
                $file->get_filename()
            )->out(false);
        }

        return '';
    }

    /**
     * Return onboarding steps.
     *
     * @return array
     */
    public static function get_onboarding_steps(): array {
        return [
            [
                'title' => get_string('onboardingstep1title', 'theme_eaduems'),
                'description' => get_string('onboardingstep1desc', 'theme_eaduems'),
            ],
            [
                'title' => get_string('onboardingstep2title', 'theme_eaduems'),
                'description' => get_string('onboardingstep2desc', 'theme_eaduems'),
            ],
            [
                'title' => get_string('onboardingstep3title', 'theme_eaduems'),
                'description' => get_string('onboardingstep3desc', 'theme_eaduems'),
            ],
        ];
    }

    /**
     * Return category links for the footer.
     *
     * @param int $limit
     * @return array
     */
    public static function get_footer_categories(int $limit = 4): array {
        $items = self::get_featured_paths($limit);

        return array_map(static function(array $item): array {
            return [
                'label' => $item['title'],
                'url' => $item['url'],
                'icon' => 'fa-folder-open-o',
            ];
        }, $items);
    }

    /**
     * Return fallback news cards.
     *
     * @return array
     */
    protected static function get_news_fallback_items(): array {
        return [
            [
                'title' => get_string('fallbacknewstitleone', 'theme_eaduems'),
                'meta' => get_string('newsfallbackmeta', 'theme_eaduems'),
                'excerpt' => get_string('fallbacknewsexcerptone', 'theme_eaduems'),
                'url' => '/',
                'linklabel' => get_string('newsmorelabel', 'theme_eaduems'),
            ],
            [
                'title' => get_string('fallbacknewstitletwo', 'theme_eaduems'),
                'meta' => get_string('newsfallbackmeta', 'theme_eaduems'),
                'excerpt' => get_string('fallbacknewsexcerpttwo', 'theme_eaduems'),
                'url' => '/',
                'linklabel' => get_string('newsmorelabel', 'theme_eaduems'),
            ],
        ];
    }

    /**
     * Return carousel slides configured in theme settings.
     *
     * @return array
     */
    public static function get_carousel_slides(): array {
        global $CFG;

        $slides = [];
        for ($i = 1; $i <= 3; $i++) {
            $imageurl = \theme_eaduems\local\settings\helper::file_url('carouselslide' . $i);
            if ($imageurl === '') {
                continue;
            }

            $slides[] = [
                'isactive' => empty($slides),
                'image_url' => $imageurl,
                'title' => \theme_eaduems\local\settings\helper::text('carouselslide' . $i . 'title', ''),
                'caption' => \theme_eaduems\local\settings\helper::text('carouselslide' . $i . 'caption', ''),
                'url' => \theme_eaduems\local\settings\helper::url('carouselslide' . $i . 'url', '/local/catalogo_eaduems/public/index.php'),
            ];
        }

        if (!empty($slides)) {
            return $slides;
        }

        return [
            [
                'isactive' => true,
                'image_url' => $CFG->wwwroot . '/theme/image.php/eaduems/theme/1/logo',
                'title' => get_string('defaultcarouseltitle', 'theme_eaduems'),
                'caption' => get_string('defaultherointro', 'theme_eaduems'),
                'url' => $CFG->wwwroot . '/local/catalogo_eaduems/public/index.php',
            ],
        ];
    }
}
