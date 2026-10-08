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

namespace tool_human2human\form;

use tool_human2human\local\lti_adapter;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * The Advanced section of the Human2Human page: which Human2Human Connect uses.
 *
 * Deliberately not an admin setting. A setting with no stored value puts every
 * installing admin through the "New settings" page for a value that only
 * developers, staging sites and future private installations ever change. This
 * form stays collapsed and stores nothing unless it is used.
 *
 * @package     tool_human2human
 * @copyright   2026 eduNEXT {@link https://www.edunext.co}
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class registrationurl_form extends \moodleform {
    /**
     * A collapsed header over one URL field.
     */
    protected function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'advanced', get_string('advanced', 'tool_human2human'));
        $mform->setExpanded('advanced', false);

        $mform->addElement('text', 'registrationurl', get_string('registrationurl', 'tool_human2human'), ['size' => 60]);
        // Raw, not PARAM_URL: PARAM_URL would blank an invalid value before
        // validation() could say what is wrong with it.
        $mform->setType('registrationurl', PARAM_RAW_TRIMMED);
        $mform->setDefault('registrationurl', lti_adapter::registration_url());
        $mform->addElement(
            'static',
            'registrationurl_desc',
            '',
            get_string('registrationurl_desc', 'tool_human2human', lti_adapter::DEFAULT_REGISTRATION_URL)
        );

        // Not add_action_buttons(): that closes the section first, leaving a
        // Save button showing below a collapsed section on every visit.
        $mform->addElement('submit', 'submitbutton', get_string('savechanges'));
    }

    /**
     * Empty means the hosted service; anything else must be a web URL.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $url = (string) ($data['registrationurl'] ?? '');
        if ($url !== '' && lti_adapter::clean_registration_url($url) === null) {
            $errors['registrationurl'] = get_string('registrationurl_invalid', 'tool_human2human');
        }
        return $errors;
    }
}
