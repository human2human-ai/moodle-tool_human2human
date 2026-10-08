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
require_capability('moodle/site:config', context_system::instance());

$pageurl = new moodle_url('/admin/tool/human2human/index.php');

if ($action === 'finishsetup') {
    require_sesskey();
    if (lti_adapter::finish_setup()) {
        redirect($pageurl, get_string('setupdone', 'tool_human2human'), null, notification::NOTIFY_SUCCESS);
    }
    redirect($pageurl, get_string('setupnothing', 'tool_human2human'), null, notification::NOTIFY_WARNING);
}

$type = lti_adapter::find_type();

// The $plugin->dependencies entry catches mod_lti being uninstalled, not an
// admin disabling it under Manage activities, which breaks everything below.
$ltienabled = array_key_exists('lti', \core\plugininfo\mod::get_enabled_plugins());

// Where the connection stands decides the steps, the notice, and the one action.
if ($type === null) {
    $stage = 1;
} else if (lti_adapter::needs_setup($type)) {
    $stage = 2;
} else {
    $stage = 3;
}

$steps = [];
foreach (['stepconnect', 'stepfinish', 'stepready'] as $index => $key) {
    $number = $index + 1;
    $steps[] = [
        'number' => $number,
        'label' => get_string($key, 'tool_human2human'),
        // The last step is a state, not a task: reaching it completes it.
        'done' => $number < $stage || $stage === 3,
        'current' => $number === $stage,
    ];
}

$registrationtarget = null;
$action = '';
$advanced = '';
if ($stage === 1 && $ltienabled) {
    // Which Human2Human to connect to, collapsed: only developers, staging sites
    // and private installations change it. Offered only before connecting,
    // because find_type() recognises the tool by this URL's host, so changing it
    // afterwards would orphan the registered tool.
    $form = new \tool_human2human\form\registrationurl_form($pageurl);
    if ($data = $form->get_data()) {
        $url = (string) $data->registrationurl;
        lti_adapter::set_registration_url($url === '' ? '' : lti_adapter::clean_registration_url($url));
        redirect($pageurl, get_string('registrationurlsaved', 'tool_human2human'), null, notification::NOTIFY_SUCCESS);
    }
    $advanced = $form->render();
}

if ($stage === 1) {
    // Nothing registered yet: offer the one button that starts the exchange.
    $statusnotice = $OUTPUT->notification(get_string('notconnected', 'tool_human2human'), notification::NOTIFY_INFO, false);
    $body = [get_string('connectintro', 'tool_human2human')];
    if (lti_adapter::is_registration_url_overridden()) {
        // Silent for the hosted service; worth a line when it is anything else.
        $registrationtarget = get_string('registrationtarget', 'tool_human2human', lti_adapter::registration_url());
    }

    // Moodle core owns the whole protocol from here. It requires site:config of
    // its own, and it opens in a new tab because the administrator signs in to
    // Human2Human there.
    $connecturl = new moodle_url('/mod/lti/startltiadvregistration.php', [
        'url' => lti_adapter::registration_url(),
        'sesskey' => sesskey(),
    ]);
    // Use formtarget, not target: single_button puts these on the <button>, where
    // `target` is not an attribute and the submit stays in the current tab. The
    // new tab has to keep its opener — that is how the tool's
    // org.imsglobal.lti.close message gets back here — so no `noopener`.
    $action = $OUTPUT->single_button($connecturl, get_string('connect', 'tool_human2human'), 'get', [
        'formtarget' => '_blank',
        'type' => 'primary',
    ]);
} else if ($stage === 2) {
    // Registered, but Dynamic Registration leaves it pending and out of the
    // activity chooser, so teachers cannot find it yet.
    $statusnotice = $OUTPUT->notification(get_string('connectedpending', 'tool_human2human'), notification::NOTIFY_WARNING, false);
    $body = [get_string('finishsetupintro', 'tool_human2human')];
    $action = $OUTPUT->single_button(
        new moodle_url($pageurl, ['action' => 'finishsetup', 'sesskey' => sesskey()]),
        get_string('finishsetup', 'tool_human2human'),
        'post',
        ['type' => 'primary']
    );
} else {
    $statusnotice = $OUTPUT->notification(
        get_string('connectedready', 'tool_human2human', format_string($type->name)),
        notification::NOTIFY_SUCCESS,
        false
    );
    $body = [get_string('readyintro', 'tool_human2human')];
}

if (!$ltienabled) {
    // Nothing on this page can work without mod_lti, so say so and offer nothing.
    $statusnotice = $OUTPUT->notification(get_string('ltidisabled', 'tool_human2human'), notification::NOTIFY_ERROR, false);
    $steps = [];
    $body = [];
    $registrationtarget = null;
    $action = '';
}

$context = [
    'logourl' => $OUTPUT->image_url('logo', 'tool_human2human')->out(false),
    'intro' => get_string('intro', 'tool_human2human'),
    'steps' => $steps,
    'statusnotice' => $statusnotice,
    'body' => $body,
    'registrationtarget' => $registrationtarget,
    'action' => $action,
    'advanced' => $advanced,
    'links' => [
        [
            'url' => (new moodle_url('/admin/tool/human2human/activities.php'))->out(false),
            'label' => get_string('activities', 'tool_human2human'),
        ],
        [
            'url' => (new moodle_url('/mod/lti/toolconfigure.php'))->out(false),
            'label' => get_string('managetools', 'tool_human2human'),
        ],
    ],
];

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'tool_human2human'));
echo $OUTPUT->render_from_template('tool_human2human/index', $context);
echo $OUTPUT->footer();
