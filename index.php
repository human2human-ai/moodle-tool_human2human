<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Human2Human admin page: connection status and setup.
 *
 * @package     tool_human2human
 * @copyright   2026 eduNEXT {@link https://www.edunext.co}
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

use core\output\notification;
use tool_human2human\local\lti_adapter;

$action = optional_param('action', '', PARAM_ALPHA);

// Checks login, the page's capability and sets up the admin navigation.
admin_externalpage_setup('tool_human2human');
require_capability('tool/human2human:configure', context_system::instance());

$pageurl = new moodle_url('/admin/tool/human2human/index.php');

if ($action === 'finishsetup') {
    require_sesskey();
    if (lti_adapter::finish_setup()) {
        redirect($pageurl, get_string('setupdone', 'tool_human2human'), null, notification::NOTIFY_SUCCESS);
    }
    redirect($pageurl, get_string('setupnothing', 'tool_human2human'), null, notification::NOTIFY_WARNING);
}

$type = lti_adapter::find_type();
$managetoolsurl = new moodle_url('/mod/lti/toolconfigure.php');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'tool_human2human'));
echo html_writer::tag('p', get_string('intro', 'tool_human2human'));
echo $OUTPUT->notification(get_string('alphanotice', 'tool_human2human'), notification::NOTIFY_WARNING, false);

if ($type === null) {
    // Nothing registered yet: offer the one button that starts the exchange.
    echo $OUTPUT->notification(get_string('notconnected', 'tool_human2human'), notification::NOTIFY_INFO, false);
    echo html_writer::tag('p', get_string('connectintro', 'tool_human2human'));
    echo html_writer::tag('p', get_string(
        'registrationtarget',
        'tool_human2human',
        s(lti_adapter::registration_url())
    ));

    // Moodle core owns the whole protocol from here. It requires site:config of
    // its own, and it opens in a new tab because the administrator signs in to
    // Human2Human there.
    $connecturl = new moodle_url('/mod/lti/startltiadvregistration.php', [
        'url' => lti_adapter::registration_url(),
        'sesskey' => sesskey(),
    ]);
    // formtarget, not target: single_button puts these on the <button>, where
    // `target` is not an attribute and the submit stays in the current tab. The
    // new tab has to keep its opener — that is how the tool's
    // org.imsglobal.lti.close message gets back here — so no `noopener`.
    echo $OUTPUT->single_button($connecturl, get_string('connect', 'tool_human2human'), 'get', [
        'formtarget' => '_blank',
    ]);
} else if (lti_adapter::needs_setup($type)) {
    // Registered, but Dynamic Registration leaves it pending and out of the
    // activity chooser, so teachers cannot find it yet.
    echo $OUTPUT->notification(get_string('connectedpending', 'tool_human2human'), notification::NOTIFY_WARNING, false);
    echo html_writer::tag('p', get_string('finishsetupintro', 'tool_human2human'));
    echo $OUTPUT->single_button(
        new moodle_url($pageurl, ['action' => 'finishsetup', 'sesskey' => sesskey()]),
        get_string('finishsetup', 'tool_human2human')
    );
} else {
    echo $OUTPUT->notification(
        get_string('connectedready', 'tool_human2human', format_string($type->name)),
        notification::NOTIFY_SUCCESS,
        false
    );
    echo html_writer::tag('p', get_string('readyintro', 'tool_human2human'));
}

echo html_writer::tag('p', html_writer::link($managetoolsurl, get_string('managetools', 'tool_human2human')));
echo $OUTPUT->footer();
