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

namespace tool_human2human\local;

/**
 * Tests for the mod_lti adapter.
 *
 * This is the class that reaches into mod_lti, so these tests are the early
 * warning when its internals change on a supported branch.
 *
 * @package     tool_human2human
 * @copyright   2026 eduNEXT {@link https://www.edunext.co}
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers      \tool_human2human\local\lti_adapter
 */
final class lti_adapter_test extends \advanced_testcase {
    /** @var string A Human2Human that is not the hosted default. */
    const TOOL_URL = 'https://h2h.example.net/lti/1.3/register/';

    /**
     * Create a site-level LTI 1.3 tool type the way Dynamic Registration leaves one.
     *
     * @param string $launchurl The tool's launch URL.
     * @return int The new type's id.
     */
    private function create_registered_type(string $launchurl): int {
        global $CFG, $SITE;
        require_once($CFG->dirroot . '/mod/lti/locallib.php');

        $type = (object) [
            'state' => LTI_TOOL_STATE_PENDING,
            'course' => $SITE->id,
        ];
        $config = (object) [
            'lti_typename' => 'Human2Human',
            'lti_toolurl' => $launchurl,
            'lti_ltiversion' => LTI_VERSION_1P3,
            // Client IDs are unique per site, and some tests register two tools.
            'lti_clientid' => 'test-client-' . parse_url($launchurl, PHP_URL_HOST),
            'lti_coursevisible' => LTI_COURSEVISIBLE_PRECONFIGURED,
            'lti_forcessl' => 1,
        ];

        return lti_add_type($type, $config);
    }

    /**
     * Read a tool type straight from the table.
     *
     * @param int $typeid
     * @return \stdClass
     */
    private function get_type(int $typeid): \stdClass {
        global $DB;
        return $DB->get_record('lti_types', ['id' => $typeid], '*', MUST_EXIST);
    }

    public function test_registration_url_falls_back_to_the_hosted_service(): void {
        $this->resetAfterTest();

        $this->assertSame(lti_adapter::DEFAULT_REGISTRATION_URL, lti_adapter::registration_url());

        set_config('registrationurl', self::TOOL_URL, 'tool_human2human');
        $this->assertSame(self::TOOL_URL, lti_adapter::registration_url());
        $this->assertSame('h2h.example.net', lti_adapter::tool_domain());
    }

    public function test_find_type_matches_our_domain_and_leaves_other_tools_alone(): void {
        global $CFG;
        require_once($CFG->dirroot . '/mod/lti/locallib.php');
        $this->resetAfterTest();
        set_config('registrationurl', self::TOOL_URL, 'tool_human2human');

        $this->assertNull(lti_adapter::find_type(), 'nothing registered yet');

        $other = $this->create_registered_type('https://notus.example.org/lti/launch');
        $this->assertNull(lti_adapter::find_type(), 'another vendor tool must not match');

        $ours = $this->create_registered_type('https://h2h.example.net/lti/1.3/launch/');
        $found = lti_adapter::find_type();
        $this->assertNotNull($found);
        $this->assertEquals($ours, $found->id);

        // Finishing setup must not touch the other tool.
        $this->assertTrue(lti_adapter::finish_setup());
        $untouched = $this->get_type($other);
        $this->assertEquals(LTI_TOOL_STATE_PENDING, $untouched->state);
        $this->assertEquals(LTI_COURSEVISIBLE_PRECONFIGURED, $untouched->coursevisible);
    }

    public function test_finish_setup_activates_the_tool_and_is_idempotent(): void {
        global $CFG;
        require_once($CFG->dirroot . '/mod/lti/locallib.php');
        $this->resetAfterTest();
        set_config('registrationurl', self::TOOL_URL, 'tool_human2human');

        $this->assertFalse(lti_adapter::finish_setup(), 'nothing registered yet');

        $typeid = $this->create_registered_type('https://h2h.example.net/lti/1.3/launch/');
        $this->assertTrue(lti_adapter::needs_setup());

        $this->assertTrue(lti_adapter::finish_setup());

        $type = $this->get_type($typeid);
        $this->assertEquals(LTI_TOOL_STATE_CONFIGURED, $type->state);
        // Without this a teacher never sees the activity in the chooser, which is
        // the whole reason this action exists.
        $this->assertEquals(LTI_COURSEVISIBLE_ACTIVITYCHOOSER, $type->coursevisible);
        $this->assertFalse(lti_adapter::needs_setup());
        // The registration's identity must survive the update: a blank clientid
        // makes mod_lti mint a new one and every later launch fails.
        $this->assertEquals('test-client-h2h.example.net', $type->clientid);
        $this->assertEquals('h2h.example.net', $type->tooldomain);

        $config = lti_get_type_config($typeid);
        // Deep Linking lives in the type config: lti_types has no column for it.
        $this->assertEquals(1, $config['contentitem']);
        $this->assertEquals(LTI_LAUNCH_CONTAINER_WINDOW, $config['launchcontainer']);
        $this->assertEquals(LTI_SETTING_ALWAYS, $config['sendname']);
        $this->assertEquals(LTI_SETTING_NEVER, $config['sendemailaddr']);
        $this->assertEquals(2, $config['ltiservice_gradesynchronization']);
        $this->assertEquals(0, $config['ltiservice_memberships']);
        // The function lti_prepare_type_for_save() rewrites forcessl with no isset() guard, so
        // a partial update would quietly clear it.
        $this->assertEquals(1, $config['forcessl']);

        // Running it again changes nothing.
        $this->assertTrue(lti_adapter::finish_setup());
        $again = $this->get_type($typeid);
        $this->assertEquals($type->state, $again->state);
        $this->assertEquals($type->coursevisible, $again->coursevisible);
        $this->assertEquals($type->clientid, $again->clientid);
        $this->assertEquals($config, lti_get_type_config($typeid));
    }
}
