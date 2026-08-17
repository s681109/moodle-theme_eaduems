<?php
// This file is part of Moodle - http://moodle.org/

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../locallib.php');

$PAGE->requires->js(new moodle_url('/theme/eaduems/javascript/back_to_top.js'));

$policycontext = context_system::instance();
$loginpolicies = [
    'privacy' => null,
    'terms' => null,
];

if (class_exists('\\tool_policy\\api')) {
    foreach (\tool_policy\api::list_current_versions() as $policy) {
        if ((int) $policy->type === \tool_policy\policy_version::TYPE_PRIVACY) {
            $loginpolicies['privacy'] = $policy;
        } else if ((int) $policy->type === \tool_policy\policy_version::TYPE_SITE) {
            $loginpolicies['terms'] = $policy;
        }
    }

    // Existing policy documents in this site use descriptive names and the "Other" type.
    foreach (\tool_policy\api::list_current_versions() as $policy) {
        $name = core_text::strtolower($policy->name);
        if (!$loginpolicies['privacy'] && str_contains($name, 'privacidade')) {
            $loginpolicies['privacy'] = $policy;
        }
        if (!$loginpolicies['terms'] && (str_contains($name, 'termos') || str_contains($name, 'uso'))) {
            $loginpolicies['terms'] = $policy;
        }
    }
}

$formatpolicy = static function($policy) use ($policycontext): string {
    if (!$policy) {
        return '';
    }

    return format_text($policy->content, $policy->contentformat, [
        'context' => $policycontext,
        'component' => 'tool_policy',
        'filearea' => 'policydocumentcontent',
        'itemid' => $policy->id,
    ]);
};

$policyurl = static function($policy): string {
    if (!$policy) {
        return '#';
    }

    return (new moodle_url('/admin/tool/policy/view.php', [
        'versionid' => $policy->id,
        'returnurl' => (new moodle_url('/login/index.php'))->out(false),
    ]))->out(false);
};

$templatecontext = [
    'output' => $OUTPUT,
    'bodyattributes' => $OUTPUT->body_attributes(),
    'site_name' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), 'escape' => false]),
    'site_tagline' => \theme_eaduems\local\settings\helper::text('sitetagline',
        get_string('defaultsitetagline', 'theme_eaduems')),
    'home_url' => (new moodle_url('/'))->out(false),
    'home_label' => get_string('home'),
    'login_support_url' => (new moodle_url('/user/contactsitesupport.php'))->out(false),
    'login_support_label' => get_string('footerlinksupport', 'theme_eaduems'),
    'login_privacy_label' => get_string('loginprivacylink', 'theme_eaduems'),
    'login_terms_label' => get_string('logintermsofuselnk', 'theme_eaduems'),
    'has_login_privacy_policy' => (bool) $loginpolicies['privacy'],
    'has_login_terms_policy' => (bool) $loginpolicies['terms'],
    'login_privacy_modal_title' => $loginpolicies['privacy'] ? format_string($loginpolicies['privacy']->name) :
        get_string('loginprivacymodaltitle', 'theme_eaduems'),
    'login_terms_modal_title' => $loginpolicies['terms'] ? format_string($loginpolicies['terms']->name) :
        get_string('logintermsofusemodaltitle', 'theme_eaduems'),
    'login_modal_close_label' => get_string('loginmodalclose', 'theme_eaduems'),
    'login_privacy_content' => $formatpolicy($loginpolicies['privacy']),
    'login_terms_content' => $formatpolicy($loginpolicies['terms']),
    'login_privacy_policy_url' => $policyurl($loginpolicies['privacy']),
    'login_terms_policy_url' => $policyurl($loginpolicies['terms']),
    'login_logo_url' => \theme_eaduems\local\settings\helper::login_logo_url(),
    'show_institutional_panel' => \theme_eaduems\local\settings\helper::bool('showlogininstitutionalpanel', true),
    'show_login_logo' => \theme_eaduems\local\settings\helper::bool('showloginlogo', false),
    'show_logo_watermark' => \theme_eaduems\local\settings\helper::bool('showloginlogowatermark', true),
    'show_login_institutional_eyebrow' => \theme_eaduems\local\settings\helper::bool('showlogininstitutionaleyebrow', true),
    'show_login_institutional_title' => \theme_eaduems\local\settings\helper::bool('showlogininstitutionaltitle', true),
    'show_login_institutional_intro' => \theme_eaduems\local\settings\helper::bool('showlogininstitutionalintro', true),
    'login_institutional_eyebrow' => \theme_eaduems\local\settings\helper::text('logininstitutionaleyebrow',
        get_string('logineyebrow', 'theme_eaduems')),
    'login_institutional_title' => \theme_eaduems\local\settings\helper::text('logininstitutionaltitle',
        get_string('defaultlogininstitutionaltitle', 'theme_eaduems')),
    'login_institutional_intro' => \theme_eaduems\local\settings\helper::text('logininstitutionalintro',
        get_string('defaultlogininstitutionalintro', 'theme_eaduems')),
];

echo $OUTPUT->render_from_template('theme_eaduems/login', $templatecontext);
