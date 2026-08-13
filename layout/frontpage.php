<?php
// This file is part of Moodle - http://moodle.org/

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../locallib.php');

$PAGE->requires->js(new moodle_url('/theme/eaduems/javascript/color_mode.js'));

$templatecontext = \theme_eaduems\local\frontpage\context_builder::build($OUTPUT, $PAGE, $SITE);

echo $OUTPUT->render_from_template('theme_eaduems/frontpage/page', $templatecontext);

