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

namespace tool_human2human\admin;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/adminlib.php');

/**
 * The Registration URL setting: a web URL, forgiving of pasted whitespace.
 *
 * admin_setting_configtext with PARAM_URL rejects any value that cleaning would
 * change, so a URL copied with a trailing space or newline fails with "This value
 * is not valid" and no hint why. Trimming first removes that trap; the scheme
 * check refuses what Moodle's Dynamic Registration page could not open anyway.
 *
 * @package     tool_human2human
 * @copyright   2026 eduNEXT {@link https://www.edunext.co}
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class setting_registrationurl extends \admin_setting_configtext {
    /**
     * Store the trimmed value.
     *
     * @param string $data The submitted value.
     * @return string Empty on success, otherwise the error message.
     */
    public function write_setting($data) {
        return parent::write_setting(trim((string) $data));
    }

    /**
     * Accept only an http(s) URL that PARAM_URL leaves unchanged.
     *
     * @param string $data The submitted value, already trimmed by write_setting().
     * @return bool|string True when valid, otherwise the error message.
     */
    public function validate($data) {
        $data = trim((string) $data);
        $scheme = strtolower((string) parse_url($data, PHP_URL_SCHEME));
        if (!in_array($scheme, ['http', 'https'], true)) {
            return get_string('registrationurl_invalid', 'tool_human2human');
        }
        $valid = parent::validate($data);
        return $valid === true ? true : get_string('registrationurl_invalid', 'tool_human2human');
    }
}
