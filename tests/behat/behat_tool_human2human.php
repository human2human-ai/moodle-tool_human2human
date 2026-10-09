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

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../../lib/behat/behat_base.php');

use tool_human2human\local\lti_adapter;

/**
 * Steps that stand in for the Human2Human side of pairing.
 *
 * @package     tool_human2human
 * @copyright   2026 eduNEXT {@link https://www.edunext.co}
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_tool_human2human extends behat_base {
    /**
     * Leave a tool type the way Dynamic Registration does: pending and out of the activity chooser.
     *
     * Human2Human cannot answer a registration from a test site, so this skips
     * the exchange and keeps only its result.
     *
     * @Given /^Human2Human has registered this site for the team "(?P<team_string>(?:[^"]|\\")*)"$/
     * @param string $team The team name Human2Human sends as a custom parameter.
     */
    public function human2human_has_registered_this_site(string $team): void {
        global $CFG, $SITE;
        require_once($CFG->dirroot . '/mod/lti/locallib.php');

        $type = (object) [
            'state' => LTI_TOOL_STATE_PENDING,
            'course' => $SITE->id,
        ];
        $config = (object) [
            'lti_typename' => 'Human2Human',
            'lti_toolurl' => 'https://' . lti_adapter::tool_domain() . '/lti/1.3/launch/',
            'lti_ltiversion' => LTI_VERSION_1P3,
            'lti_clientid' => 'behat-human2human',
            'lti_coursevisible' => LTI_COURSEVISIBLE_PRECONFIGURED,
            'lti_customparameters' => lti_adapter::TEAM_CUSTOM_PARAMETER . '=' . $team,
        ];
        lti_add_type($type, $config);
    }

    /**
     * Add an External tool activity that launches the registered Human2Human.
     *
     * @Given /^the course "(?P<shortname_string>(?:[^"]|\\")*)" has a Human2Human activity "(?P<name_string>(?:[^"]|\\")*)"$/
     * @param string $shortname The course's short name.
     * @param string $name The activity name.
     */
    public function the_course_has_a_human2human_activity(string $shortname, string $name): void {
        global $DB;

        $type = lti_adapter::find_type();
        if ($type === null) {
            throw new coding_exception('Register Human2Human before adding an activity that launches it.');
        }
        $course = $DB->get_record('course', ['shortname' => $shortname], '*', MUST_EXIST);
        testing_util::get_data_generator()->create_module('lti', [
            'course' => $course->id,
            'name' => $name,
            'typeid' => $type->id,
        ]);
    }
}
