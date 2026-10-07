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

namespace tool_human2human;

/**
 * Access to the Human2Human admin page.
 *
 * @package     tool_human2human
 * @copyright   2026 eduNEXT {@link https://www.edunext.co}
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversNothing
 */
final class admin_page_test extends \advanced_testcase {
    /**
     * Load adminlib for admin_get_root().
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->libdir . '/adminlib.php');
        parent::setUpBeforeClass();
    }

    /**
     * Whether the current user may open the page, as admin_externalpage_setup() decides:
     * the page must be in the user's admin tree and pass its access check.
     *
     * @return bool
     */
    private function can_open_page(): bool {
        $page = admin_get_root(true, false)->locate('tool_human2human');
        return $page instanceof \admin_externalpage && $page->check_access();
    }

    /**
     * A site admin can open the page.
     */
    public function test_admin_can_open(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->assertTrue($this->can_open_page());
    }

    /**
     * A teacher cannot open the page.
     */
    public function test_teacher_cannot_open(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $this->setUser($teacher);
        $this->assertFalse($this->can_open_page());
    }
}
