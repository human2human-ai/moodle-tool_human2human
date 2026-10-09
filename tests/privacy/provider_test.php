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

namespace tool_human2human\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\metadata\types\external_location;
use core_privacy\local\request\userlist;
use core_privacy\tests\provider_testcase;

/**
 * Privacy provider tests.
 *
 * @package     tool_human2human
 * @copyright   2026 eduNEXT {@link https://www.edunext.co}
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers      \tool_human2human\privacy\provider
 */
final class provider_test extends provider_testcase {
    /**
     * The metadata declares the fields sent to Human2Human, the name among them and the email not.
     */
    public function test_get_metadata(): void {
        $collection = provider::get_metadata(new collection('tool_human2human'));
        $items = $collection->get_collection();

        $this->assertCount(1, $items);
        $location = reset($items);
        $this->assertInstanceOf(external_location::class, $location);
        $this->assertEquals('human2human', $location->get_name());

        $fields = $location->get_privacy_fields();
        $this->assertEqualsCanonicalizing(
            ['userid', 'username', 'idnumber', 'fullname', 'role', 'courseid', 'activityid', 'language'],
            array_keys($fields)
        );
        $this->assertArrayNotHasKey('email', $fields);
    }

    /**
     * Every metadata string identifier is a real language string.
     */
    public function test_metadata_strings_exist(): void {
        $collection = provider::get_metadata(new collection('tool_human2human'));
        $location = $collection->get_collection()[0];
        $identifiers = array_merge([$location->get_summary()], array_values($location->get_privacy_fields()));

        foreach ($identifiers as $identifier) {
            $this->assertTrue(
                get_string_manager()->string_exists($identifier, 'tool_human2human'),
                "Missing string {$identifier}"
            );
        }
    }

    /**
     * The plugin stores nothing in Moodle, so no context or user is ever reported.
     */
    public function test_stores_nothing(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $context = \context_system::instance();

        $this->assertEmpty(provider::get_contexts_for_userid($user->id)->get_contextids());

        $userlist = new userlist($context, 'tool_human2human');
        provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);
    }
}
