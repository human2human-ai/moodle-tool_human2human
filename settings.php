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
        new moodle_url('/admin/tool/human2human/index.php')
    ));

    // No admin settings on purpose. Any registered here would put every
    // install and upgrade through the "New settings" page. The one value that
    // can change, the registration URL, is under Advanced on the page above
    // (classes/form/registrationurl_form.php) and settable with admin/cli/cfg.php.
}
