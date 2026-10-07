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
 * Admin tree entries.
 *
 * @package     tool_human2human
 * @copyright   2026 eduNEXT {@link https://www.edunext.co}
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $ADMIN->add('tools', new admin_externalpage(
        'tool_human2human',
        get_string('pluginname', 'tool_human2human'),
        new moodle_url('/admin/tool/human2human/index.php'),
        'tool/human2human:configure'
    ));

    $settings = new admin_settingpage(
        'tool_human2human_settings',
        get_string('settings', 'tool_human2human'),
        'tool/human2human:configure'
    );
    // Only reason this is editable: a development or staging Human2Human. The
    // default is the hosted service, so a normal site never touches it.
    $settings->add(new admin_setting_configtext(
        'tool_human2human/registrationurl',
        get_string('registrationurl', 'tool_human2human'),
        get_string('registrationurl_desc', 'tool_human2human'),
        \tool_human2human\local\lti_adapter::DEFAULT_REGISTRATION_URL,
        PARAM_URL
    ));
    $ADMIN->add('tools', $settings);
}
