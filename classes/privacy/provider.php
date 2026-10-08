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

/**
 * Privacy provider.
 *
 * This plugin only configures a mod_lti tool type. Launches, grades and
 * rosters are sent by mod_lti, and mod_lti's own privacy provider declares
 * them; nothing reaches Human2Human through this plugin. A metadata provider
 * here would have to be paired with no-op request providers to stay compliant
 * (core_privacy\manager::component_is_compliant()), declaring a transfer this
 * plugin never performs. So: null_provider, until this plugin talks to
 * Human2Human itself (registration, health checks).
 *
 * @package     tool_human2human
 * @copyright   2026 eduNEXT {@link https://www.edunext.co}
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements \core_privacy\local\metadata\null_provider {
    /**
     * Get the language string identifier explaining why this plugin stores no data.
     *
     * @return string
     */
    public static function get_reason(): string {
        return 'privacy:metadata';
    }
}
