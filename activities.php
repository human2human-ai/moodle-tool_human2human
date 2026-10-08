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
 * Human2Human activities: what the site is connected to and which courses use it.
 *
 * Listed under Plugins > Activity modules, because what Human2Human gives a site
 * is an activity, even though it launches through the External tool activity.
 *
 * @package     tool_human2human
 * @copyright   2026 eduNEXT {@link https://www.edunext.co}
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

use tool_human2human\local\lti_adapter;

/** Activities listed per page. */
const TOOL_HUMAN2HUMAN_PERPAGE = 50;

$page = optional_param('page', 0, PARAM_INT);

admin_externalpage_setup('tool_human2human_activities');
require_capability('moodle/site:config', context_system::instance());

$pageurl = new moodle_url('/admin/tool/human2human/activities.php');
$connecturl = new moodle_url('/admin/tool/human2human/index.php');
$type = lti_adapter::find_type();

$connection = null;
if ($type !== null) {
    $team = lti_adapter::team_name($type);
    $connection = [
        // The team when Human2Human said which; otherwise the tool itself.
        'team' => $team,
        'toolname' => $team === null ? format_string($type->name) : null,
        'address' => $type->baseurl,
        'status' => get_string(lti_adapter::needs_setup($type) ? 'statuspending' : 'statusready', 'tool_human2human'),
    ];
}

[$total, $records] = lti_adapter::linked_activities($page * TOOL_HUMAN2HUMAN_PERPAGE, TOOL_HUMAN2HUMAN_PERPAGE);
$activities = [];
foreach ($records as $record) {
    $activities[] = [
        'name' => format_string($record->name),
        'url' => (new moodle_url('/mod/lti/view.php', ['id' => $record->cmid]))->out(false),
        'coursename' => format_string($record->coursename),
        'courseurl' => (new moodle_url('/course/view.php', ['id' => $record->courseid]))->out(false),
        'added' => userdate($record->timecreated, get_string('strftimedate', 'langconfig')),
    ];
}

$context = [
    'connection' => $connection,
    'notconnected' => $type === null,
    'connecturl' => $connecturl->out(false),
    'activities' => $activities,
    'total' => $total,
    'pagingbar' => $OUTPUT->paging_bar($total, $page, TOOL_HUMAN2HUMAN_PERPAGE, $pageurl),
];

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('activities', 'tool_human2human'));
echo $OUTPUT->render_from_template('tool_human2human/activities', $context);
echo $OUTPUT->footer();
