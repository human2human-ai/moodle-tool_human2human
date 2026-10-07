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

// Checks login, the page's capability and sets up the admin navigation.
admin_externalpage_setup('tool_human2human');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'tool_human2human'));
echo html_writer::tag('p', get_string('intro', 'tool_human2human'));
echo $OUTPUT->notification(get_string('alphanotice', 'tool_human2human'), \core\output\notification::NOTIFY_WARNING, false);
echo $OUTPUT->notification(get_string('notconnected', 'tool_human2human'), \core\output\notification::NOTIFY_INFO, false);
echo $OUTPUT->footer();
