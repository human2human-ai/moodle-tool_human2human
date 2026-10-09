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
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;

/**
 * Privacy provider.
 *
 * Declares the data that reaches Human2Human when a learner opens an activity.
 * The External tool activity (mod_lti) makes the launch, but this plugin
 * configures the tool: pairing sets it to always send the name and never the
 * email (lti_adapter::finish_setup()). With the name, mod_lti also sends the
 * username; the ID number goes on every launch. An administrator can change the
 * name and email settings afterwards under Manage tools, and pairing does not
 * reset them. The plugin stores no personal data in Moodle, so the request
 * providers have nothing to export or delete.
 *
 * @package     tool_human2human
 * @copyright   2026 eduNEXT {@link https://www.edunext.co}
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {
    /**
     * Describe the personal data sent to Human2Human.
     *
     * @param collection $collection The collection to add metadata to.
     * @return collection The updated collection.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_external_location_link('human2human', [
            'userid' => 'privacy:metadata:human2human:userid',
            'username' => 'privacy:metadata:human2human:username',
            'idnumber' => 'privacy:metadata:human2human:idnumber',
            'fullname' => 'privacy:metadata:human2human:fullname',
            'role' => 'privacy:metadata:human2human:role',
            'courseid' => 'privacy:metadata:human2human:courseid',
            'activityid' => 'privacy:metadata:human2human:activityid',
            'language' => 'privacy:metadata:human2human:language',
        ], 'privacy:metadata:human2human');

        return $collection;
    }

    /**
     * No contexts hold data for this plugin, which stores nothing in Moodle.
     *
     * @param int $userid The user to search.
     * @return contextlist An empty list.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        return new contextlist();
    }

    /**
     * No users have data in any context, because this plugin stores nothing in Moodle.
     *
     * @param userlist $userlist The userlist to leave unchanged.
     */
    public static function get_users_in_context(userlist $userlist) {
    }

    /**
     * Nothing to export: this plugin stores nothing in Moodle.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
    }

    /**
     * Nothing to delete: this plugin stores nothing in Moodle.
     *
     * @param \context $context The context to delete in.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
    }

    /**
     * Nothing to delete: this plugin stores nothing in Moodle.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
    }

    /**
     * Nothing to delete: this plugin stores nothing in Moodle.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
    }
}
