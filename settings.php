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
 * Settings for theme_eaduems.
 *
 * @package   theme_eaduems
 * @copyright 2026 EAD/UEMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings = new theme_boost_admin_settingspage_tabs('themesettingeaduems', get_string('configtitle', 'theme_eaduems'));

    $fileoptions = ['accepted_types' => ['.png', '.jpg', '.jpeg', '.gif', '.webp', '.svg'], 'maxfiles' => 1];

    $page = new admin_settingpage('theme_eaduems_branding', get_string('brandingsettings', 'theme_eaduems'));
    $page->add(new admin_setting_configstoredfile('theme_eaduems/logo', get_string('logo', 'theme_eaduems'),
        get_string('logodesc', 'theme_eaduems'), 'logo', 0, $fileoptions));
    $page->add(new admin_setting_configstoredfile('theme_eaduems/logodark', get_string('logodark', 'theme_eaduems'),
        get_string('logodarkdesc', 'theme_eaduems'), 'logodark', 0, $fileoptions));
    $page->add(new admin_setting_configstoredfile('theme_eaduems/favicon', get_string('favicon', 'theme_eaduems'),
        get_string('favicondesc', 'theme_eaduems'), 'favicon', 0, ['accepted_types' => ['.ico', '.png'], 'maxfiles' => 1]));
    $page->add(new admin_setting_configtext('theme_eaduems/sitetagline', get_string('sitetagline', 'theme_eaduems'),
        get_string('sitetaglinedesc', 'theme_eaduems'), get_string('defaultsitetagline', 'theme_eaduems'), PARAM_TEXT));
    $settings->add($page);

    $page = new admin_settingpage('theme_eaduems_palette', get_string('palettesettings', 'theme_eaduems'));
    $page->add(new admin_setting_heading('theme_eaduems/paletteinfo', '',
        get_string('palettesettingsdesc', 'theme_eaduems')));
    foreach ([
        'palettebrandprimary' => ['palettebrandprimary', 'palettebrandprimarydesc', '#0f6f3c'],
        'palettebrandsecondary' => ['palettebrandsecondary', 'palettebrandsecondarydesc', '#0b4f2b'],
        'paletteaccent' => ['paletteaccent', 'paletteaccentdesc', '#d6df2a'],
        'palettepagebackground' => ['palettepagebackground', 'palettepagebackgrounddesc', '#ffffff'],
        'palettesurface' => ['palettesurface', 'palettesurfacedesc', '#f4f8f5'],
    ] as $name => [$titlekey, $desckey, $default]) {
        $setting = new admin_setting_configcolourpicker('theme_eaduems/' . $name,
            get_string($titlekey, 'theme_eaduems'),
            get_string($desckey, 'theme_eaduems'),
            $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);
    }
    $settings->add($page);

    $page = new admin_settingpage('theme_eaduems_header', get_string('headersettings', 'theme_eaduems'));
    $page->add(new admin_setting_configtext('theme_eaduems/headerloginlabel', get_string('headerloginlabel', 'theme_eaduems'),
        get_string('headerloginlabeldesc', 'theme_eaduems'), get_string('logincta', 'theme_eaduems'), PARAM_TEXT));
    $page->add(new admin_setting_configtext('theme_eaduems/headerloginurl', get_string('headerloginurl', 'theme_eaduems'),
        get_string('headerloginurldesc', 'theme_eaduems'), '/login/index.php', PARAM_RAW_TRIMMED));
    $page->add(new admin_setting_configtext('theme_eaduems/headerdashboardlabel', get_string('headerdashboardlabel', 'theme_eaduems'),
        get_string('headerdashboardlabeldesc', 'theme_eaduems'), get_string('dashboardcta', 'theme_eaduems'), PARAM_TEXT));
    $page->add(new admin_setting_configtext('theme_eaduems/headersignuplabel', get_string('headersignuplabel', 'theme_eaduems'),
        get_string('headersignuplabeldesc', 'theme_eaduems'), get_string('createaccountcta', 'theme_eaduems'), PARAM_TEXT));
    $page->add(new admin_setting_heading('theme_eaduems/navbandheading', '',
        get_string('navbandsettings', 'theme_eaduems')));
    $page->add(new admin_setting_configselect('theme_eaduems/navbanditemcount', get_string('navbanditemcount', 'theme_eaduems'),
        get_string('navbanditemcountdesc', 'theme_eaduems'), 4, [0 => '0', 1 => '1', 2 => '2', 3 => '3', 4 => '4',
            5 => '5', 6 => '6', 7 => '7', 8 => '8']));
    $navbandvisibilityoptions = [
        'all' => get_string('navbandvisibilityall', 'theme_eaduems'),
        'guest' => get_string('navbandvisibilityguest', 'theme_eaduems'),
        'loggedin' => get_string('navbandvisibilityloggedin', 'theme_eaduems'),
    ];
    for ($i = 1; $i <= 8; $i++) {
        $page->add(new admin_setting_heading('theme_eaduems/navbanditemheading' . $i, '',
            get_string('navbanditemheading', 'theme_eaduems', $i)));
        $page->add(new admin_setting_configtext('theme_eaduems/navbanditem' . $i . 'label',
            get_string('navbanditemlabel', 'theme_eaduems'), get_string('navbanditemlabeldesc', 'theme_eaduems'),
            '', PARAM_TEXT));
        $page->add(new admin_setting_configtext('theme_eaduems/navbanditem' . $i . 'url',
            get_string('navbanditemurl', 'theme_eaduems'), get_string('navbanditemurldesc', 'theme_eaduems'),
            '', PARAM_RAW_TRIMMED));
        $page->add(new admin_setting_configselect('theme_eaduems/navbanditem' . $i . 'visibility',
            get_string('navbanditemvisibility', 'theme_eaduems'), get_string('navbanditemvisibilitydesc', 'theme_eaduems'),
            'all', $navbandvisibilityoptions));
    }
    $settings->add($page);

    $page = new admin_settingpage('theme_eaduems_hero', get_string('herosettings', 'theme_eaduems'));
    $page->add(new admin_setting_configtext('theme_eaduems/heroeyebrow', get_string('heroeyebrow', 'theme_eaduems'),
        get_string('heroeyebrowdesc', 'theme_eaduems'), get_string('defaultheroeyebrow', 'theme_eaduems'), PARAM_TEXT));
    $page->add(new admin_setting_configtext('theme_eaduems/herotitle', get_string('herotitle', 'theme_eaduems'),
        get_string('herotitledesc', 'theme_eaduems'), get_string('defaultherotitle', 'theme_eaduems'), PARAM_TEXT));
    $page->add(new admin_setting_configtextarea('theme_eaduems/herointro', get_string('herointro', 'theme_eaduems'),
        get_string('herointrodesc', 'theme_eaduems'), get_string('defaultherointro', 'theme_eaduems'), PARAM_TEXT));
    $page->add(new admin_setting_heading('theme_eaduems/heropanelheading', '',
        get_string('heropanelsettings', 'theme_eaduems')));
    $page->add(new admin_setting_configcheckbox('theme_eaduems/showheropanel', get_string('showheropanel', 'theme_eaduems'),
        get_string('showheropaneldesc', 'theme_eaduems'), 1));
    $page->add(new admin_setting_configtext('theme_eaduems/heropanelkickeroverride', get_string('heropanelkickerlabel', 'theme_eaduems'),
        get_string('heropanelkickerdesc', 'theme_eaduems'), get_string('heropanelkicker', 'theme_eaduems'), PARAM_TEXT));
    $page->add(new admin_setting_configtext('theme_eaduems/heropaneltitleoverride', get_string('heropaneltitlelabel', 'theme_eaduems'),
        get_string('heropaneltitledesc', 'theme_eaduems'), get_string('heropaneltitle', 'theme_eaduems'), PARAM_TEXT));
    $page->add(new admin_setting_configtextarea('theme_eaduems/heropanelintrooverride', get_string('heropanelintrolabel', 'theme_eaduems'),
        get_string('heropanelintrodesc', 'theme_eaduems'), get_string('heropanelintro', 'theme_eaduems'), PARAM_TEXT));
    $page->add(new admin_setting_configselect('theme_eaduems/heropanelpointcount', get_string('heropanelpointcount', 'theme_eaduems'),
        get_string('heropanelpointcountdesc', 'theme_eaduems'), 3, [0 => '0', 1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6']));
    for ($i = 1; $i <= 6; $i++) {
        $default = $i <= 3 ? get_string('heropanelpoint' . $i, 'theme_eaduems') : '';
        $page->add(new admin_setting_configtext('theme_eaduems/heropanelpoint' . $i . 'override',
            get_string('heropanelpointlabel', 'theme_eaduems', $i),
            get_string('heropanelpointdesc', 'theme_eaduems'), $default, PARAM_TEXT));
    }
    $page->add(new admin_setting_heading('theme_eaduems/heroguestctaheading', '',
        get_string('heroguestctasettings', 'theme_eaduems')));
    $page->add(new admin_setting_configcheckbox('theme_eaduems/showprimarycta', get_string('showprimarycta', 'theme_eaduems'),
        get_string('showprimaryctadesc', 'theme_eaduems'), 1));
    $page->add(new admin_setting_configtext('theme_eaduems/primaryctalabel', get_string('primaryctalabel', 'theme_eaduems'),
        get_string('primaryctalabeldesc', 'theme_eaduems'), get_string('catalogcta', 'theme_eaduems'), PARAM_TEXT));
    $page->add(new admin_setting_configtext('theme_eaduems/primaryctaurl', get_string('primaryctaurl', 'theme_eaduems'),
        get_string('primaryctaurldesc', 'theme_eaduems'), '/local/catalogo_eaduems/public/index.php', PARAM_RAW_TRIMMED));
    $page->add(new admin_setting_configcheckbox('theme_eaduems/showsecondarycta', get_string('showsecondarycta', 'theme_eaduems'),
        get_string('showsecondaryctadesc', 'theme_eaduems'), 1));
    $page->add(new admin_setting_configtext('theme_eaduems/secondaryctalabel', get_string('secondaryctalabel', 'theme_eaduems'),
        get_string('secondaryctalabeldesc', 'theme_eaduems'), get_string('logincta', 'theme_eaduems'), PARAM_TEXT));
    $page->add(new admin_setting_configtext('theme_eaduems/secondaryctaurl', get_string('secondaryctaurl', 'theme_eaduems'),
        get_string('secondaryctaurldesc', 'theme_eaduems'), '/login/index.php', PARAM_RAW_TRIMMED));
    $page->add(new admin_setting_heading('theme_eaduems/herologgedinctaheading', '',
        get_string('herologgedinctasettings', 'theme_eaduems')));
    $page->add(new admin_setting_configcheckbox('theme_eaduems/showloggedinprimarycta', get_string('showloggedinprimarycta', 'theme_eaduems'),
        get_string('showloggedinprimaryctadesc', 'theme_eaduems'), 1));
    $page->add(new admin_setting_configtext('theme_eaduems/primaryloggedinctalabel', get_string('primaryloggedinctalabel', 'theme_eaduems'),
        get_string('primaryloggedinctalabeldesc', 'theme_eaduems'), get_string('catalogcta', 'theme_eaduems'), PARAM_TEXT));
    $page->add(new admin_setting_configtext('theme_eaduems/primaryloggedinctaurl', get_string('primaryloggedinctaurl', 'theme_eaduems'),
        get_string('primaryloggedinctaurldesc', 'theme_eaduems'), '/local/catalogo_eaduems/public/index.php', PARAM_RAW_TRIMMED));
    $page->add(new admin_setting_configcheckbox('theme_eaduems/showloggedinsecondarycta', get_string('showloggedinsecondarycta', 'theme_eaduems'),
        get_string('showloggedinsecondaryctadesc', 'theme_eaduems'), 1));
    $page->add(new admin_setting_configtext('theme_eaduems/secondaryloggedinctalabel', get_string('secondaryloggedinctalabel', 'theme_eaduems'),
        get_string('secondaryloggedinctalabeldesc', 'theme_eaduems'), get_string('dashboardcta', 'theme_eaduems'), PARAM_TEXT));
    $page->add(new admin_setting_configtext('theme_eaduems/secondaryloggedinctaurl', get_string('secondaryloggedinctaurl', 'theme_eaduems'),
        get_string('secondaryloggedinctaurldesc', 'theme_eaduems'), '/my/', PARAM_RAW_TRIMMED));
    $settings->add($page);

    $page = new admin_settingpage('theme_eaduems_login', get_string('loginsettings', 'theme_eaduems'));
    $page->add(new admin_setting_configcheckbox('theme_eaduems/showlogininstitutionalpanel',
        get_string('showlogininstitutionalpanel', 'theme_eaduems'),
        get_string('showlogininstitutionalpaneldesc', 'theme_eaduems'), 1));
    $page->add(new admin_setting_configstoredfile('theme_eaduems/loginlogo',
        get_string('loginlogo', 'theme_eaduems'),
        get_string('loginlogodesc', 'theme_eaduems'), 'loginlogo', 0, $fileoptions));
$page->add(new admin_setting_configcheckbox('theme_eaduems/showloginlogo',
        get_string('showloginlogo', 'theme_eaduems'),
        get_string('showloginlogodesc', 'theme_eaduems'), 0));
    $page->add(new admin_setting_configcheckbox('theme_eaduems/showlogininstitutionaleyebrow',
        get_string('showlogininstitutionaleyebrow', 'theme_eaduems'),
        get_string('showlogininstitutionaleyebrowdesc', 'theme_eaduems'), 1));
    $page->add(new admin_setting_configtext('theme_eaduems/logininstitutionaleyebrow',
        get_string('logininstitutionaleyebrow', 'theme_eaduems'),
        get_string('logininstitutionaleyebrowdesc', 'theme_eaduems'),
        get_string('logineyebrow', 'theme_eaduems'), PARAM_TEXT));
    $page->add(new admin_setting_configcheckbox('theme_eaduems/showlogininstitutionaltitle',
        get_string('showlogininstitutionaltitle', 'theme_eaduems'),
        get_string('showlogininstitutionaltitledesc', 'theme_eaduems'), 1));
    $page->add(new admin_setting_configtext('theme_eaduems/logininstitutionaltitle',
        get_string('logininstitutionaltitle', 'theme_eaduems'),
        get_string('logininstitutionaltitledesc', 'theme_eaduems'),
        get_string('defaultlogininstitutionaltitle', 'theme_eaduems'), PARAM_TEXT));
    $page->add(new admin_setting_configcheckbox('theme_eaduems/showlogininstitutionalintro',
        get_string('showlogininstitutionalintro', 'theme_eaduems'),
        get_string('showlogininstitutionalintrodesc', 'theme_eaduems'), 1));
    $page->add(new admin_setting_configtextarea('theme_eaduems/logininstitutionalintro',
        get_string('logininstitutionalintro', 'theme_eaduems'),
        get_string('logininstitutionalintrodesc', 'theme_eaduems'),
        get_string('defaultlogininstitutionalintro', 'theme_eaduems'), PARAM_TEXT));
    $page->add(new admin_setting_configcheckbox('theme_eaduems/showloginlogowatermark',
        get_string('showloginlogowatermark', 'theme_eaduems'),
        get_string('showloginlogowatermarkdesc', 'theme_eaduems'), 1));
    $settings->add($page);

    $page = new admin_settingpage('theme_eaduems_carousel', get_string('carouselsettings', 'theme_eaduems'));
    $page->add(new admin_setting_configcheckbox('theme_eaduems/showcarousel', get_string('showcarousel', 'theme_eaduems'),
        get_string('showcarouseldesc', 'theme_eaduems'), 1));
    $page->add(new admin_setting_configtext('theme_eaduems/carouseltitle', get_string('carouseltitle', 'theme_eaduems'),
        get_string('carouseltitledesc', 'theme_eaduems'), get_string('defaultcarouseltitle', 'theme_eaduems'), PARAM_TEXT));
    $page->add(new admin_setting_configcheckbox('theme_eaduems/carouselautoplay', get_string('carouselautoplay', 'theme_eaduems'),
        get_string('carouselautoplaydesc', 'theme_eaduems'), 1));
    for ($i = 1; $i <= 3; $i++) {
        $page->add(new admin_setting_heading('theme_eaduems/carouselslideheading' . $i, '',
            get_string('carouselslideheading', 'theme_eaduems', $i)));
        $page->add(new admin_setting_configstoredfile('theme_eaduems/carouselslide' . $i, get_string('carouselslideimage', 'theme_eaduems'),
            get_string('carouselslideimagedesc', 'theme_eaduems'), 'carouselslide' . $i, 0, $fileoptions));
        $page->add(new admin_setting_configtext('theme_eaduems/carouselslide' . $i . 'title', get_string('carouselslidetitle', 'theme_eaduems'),
            get_string('carouselslidetitledesc', 'theme_eaduems'), '', PARAM_TEXT));
        $page->add(new admin_setting_configtextarea('theme_eaduems/carouselslide' . $i . 'caption', get_string('carouselslidecaption', 'theme_eaduems'),
            get_string('carouselslidecaptiondesc', 'theme_eaduems'), '', PARAM_TEXT));
        $page->add(new admin_setting_configtext('theme_eaduems/carouselslide' . $i . 'url', get_string('carouselslideurl', 'theme_eaduems'),
            get_string('carouselslideurldesc', 'theme_eaduems'), '/local/catalogo_eaduems/public/index.php', PARAM_RAW_TRIMMED));
    }
    $settings->add($page);

    $page = new admin_settingpage('theme_eaduems_featured', get_string('featuredsettings', 'theme_eaduems'));
    $page->add(new admin_setting_configcheckbox('theme_eaduems/showfeaturedpaths', get_string('sectionenabled', 'theme_eaduems'),
        get_string('sectionenableddesc', 'theme_eaduems'), 1));
    $page->add(new admin_setting_configtext('theme_eaduems/featureheadingoverride', get_string('sectiontitle', 'theme_eaduems'),
        get_string('sectiontitledesc', 'theme_eaduems'), get_string('featureheading', 'theme_eaduems'), PARAM_TEXT));
    $page->add(new admin_setting_configtextarea('theme_eaduems/featureintrooverride', get_string('sectionintro', 'theme_eaduems'),
        get_string('sectionintrodesc', 'theme_eaduems'), get_string('featureintro', 'theme_eaduems'), PARAM_TEXT));
    $page->add(new admin_setting_configtext('theme_eaduems/featuredlimit', get_string('sectionlimit', 'theme_eaduems'),
        get_string('featuredlimitdesc', 'theme_eaduems'), 3, PARAM_INT));
    $settings->add($page);

    $page = new admin_settingpage('theme_eaduems_onboarding', get_string('onboardingsettings', 'theme_eaduems'));
    $page->add(new admin_setting_configcheckbox('theme_eaduems/showonboarding', get_string('sectionenabled', 'theme_eaduems'),
        get_string('sectionenableddesc', 'theme_eaduems'), 1));
    $page->add(new admin_setting_configtext('theme_eaduems/onboardingheadingoverride', get_string('sectiontitle', 'theme_eaduems'),
        get_string('sectiontitledesc', 'theme_eaduems'), get_string('onboardingheading', 'theme_eaduems'), PARAM_TEXT));
    $page->add(new admin_setting_configtextarea('theme_eaduems/onboardingintrooverride', get_string('sectionintro', 'theme_eaduems'),
        get_string('sectionintrodesc', 'theme_eaduems'), get_string('onboardingintro', 'theme_eaduems'), PARAM_TEXT));
    $settings->add($page);

    $page = new admin_settingpage('theme_eaduems_news', get_string('newssettings', 'theme_eaduems'));
    $page->add(new admin_setting_configcheckbox('theme_eaduems/shownewsfeed', get_string('sectionenabled', 'theme_eaduems'),
        get_string('sectionenableddesc', 'theme_eaduems'), 1));
    $page->add(new admin_setting_configtext('theme_eaduems/newsheadingoverride', get_string('sectiontitle', 'theme_eaduems'),
        get_string('sectiontitledesc', 'theme_eaduems'), get_string('newsheading', 'theme_eaduems'), PARAM_TEXT));
    $page->add(new admin_setting_configtextarea('theme_eaduems/newsintrooverride', get_string('sectionintro', 'theme_eaduems'),
        get_string('sectionintrodesc', 'theme_eaduems'), get_string('newsintro', 'theme_eaduems'), PARAM_TEXT));
    $page->add(new admin_setting_configtext('theme_eaduems/newsperpage', get_string('newsperpage', 'theme_eaduems'),
        get_string('newsperpagedesc', 'theme_eaduems'), 4, PARAM_INT));
    $page->add(new admin_setting_configtext('theme_eaduems/newsmaxitems', get_string('newsmaxitems', 'theme_eaduems'),
        get_string('newsmaxitemsdesc', 'theme_eaduems'), 12, PARAM_INT));
    $settings->add($page);

    $page = new admin_settingpage('theme_eaduems_finalcta', get_string('finalctasettings', 'theme_eaduems'));
    $page->add(new admin_setting_configcheckbox('theme_eaduems/showfinalcta', get_string('sectionenabled', 'theme_eaduems'),
        get_string('sectionenableddesc', 'theme_eaduems'), 1));
    $page->add(new admin_setting_configtext('theme_eaduems/finalctaheadingoverride', get_string('sectiontitle', 'theme_eaduems'),
        get_string('sectiontitledesc', 'theme_eaduems'), get_string('finalctaheading', 'theme_eaduems'), PARAM_TEXT));
    $page->add(new admin_setting_configtextarea('theme_eaduems/finalctaintrooverride', get_string('sectionintro', 'theme_eaduems'),
        get_string('sectionintrodesc', 'theme_eaduems'), get_string('finalctaintro', 'theme_eaduems'), PARAM_TEXT));
    $settings->add($page);

    $page = new admin_settingpage('theme_eaduems_footer', get_string('footersettings', 'theme_eaduems'));
    $page->add(new admin_setting_configtext('theme_eaduems/footerheadline', get_string('footerheadline', 'theme_eaduems'),
        get_string('footerheadlinedesc', 'theme_eaduems'), get_string('defaultfooterheadline', 'theme_eaduems'), PARAM_TEXT));
    $page->add(new admin_setting_configtextarea('theme_eaduems/footerbody', get_string('footerbody', 'theme_eaduems'),
        get_string('footerbodydesc', 'theme_eaduems'), get_string('defaultfooterbody', 'theme_eaduems'), PARAM_TEXT));
    $page->add(new admin_setting_heading('theme_eaduems/footerlinksheading', '',
        get_string('footerlinkssettings', 'theme_eaduems')));
    $page->add(new admin_setting_configselect('theme_eaduems/footerlinkcount', get_string('footerlinkcount', 'theme_eaduems'),
        get_string('footerlinkcountdesc', 'theme_eaduems'), 6, [0 => '0', 1 => '1', 2 => '2', 3 => '3', 4 => '4',
            5 => '5', 6 => '6', 7 => '7', 8 => '8']));
    for ($i = 1; $i <= 8; $i++) {
        $page->add(new admin_setting_heading('theme_eaduems/footerlinkheading' . $i, '',
            get_string('footerlinkheading', 'theme_eaduems', $i)));
        $page->add(new admin_setting_configcheckbox('theme_eaduems/footerlink' . $i . 'enabled',
            get_string('footerlinkenabled', 'theme_eaduems'), get_string('footerlinkenableddesc', 'theme_eaduems'), 1));
        $page->add(new admin_setting_configtext('theme_eaduems/footerlink' . $i . 'label',
            get_string('footerlinklabel', 'theme_eaduems'), get_string('footerlinklabeldesc', 'theme_eaduems'), '', PARAM_TEXT));
        $page->add(new admin_setting_configtext('theme_eaduems/footerlink' . $i . 'url',
            get_string('footerlinkurl', 'theme_eaduems'), get_string('footerlinkurldesc', 'theme_eaduems'), '', PARAM_RAW_TRIMMED));
    }
    $settings->add($page);

    $page = new admin_settingpage('theme_eaduems_darkmode', get_string('darkmodesettings', 'theme_eaduems'));
    $page->add(new admin_setting_configcheckbox('theme_eaduems/enabledarkmode', get_string('enabledarkmode', 'theme_eaduems'),
        get_string('enabledarkmodedesc', 'theme_eaduems'), 0));
    $settings->add($page);
}
