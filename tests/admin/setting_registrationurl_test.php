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

/**
 * Tests for the Registration URL setting.
 *
 * @package     tool_human2human
 * @copyright   2026 eduNEXT {@link https://www.edunext.co}
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers      \tool_human2human\admin\setting_registrationurl
 */
final class setting_registrationurl_test extends \advanced_testcase {
    /**
     * The setting as settings.php declares it.
     *
     * @return setting_registrationurl
     */
    private function setting(): setting_registrationurl {
        return new setting_registrationurl(
            'tool_human2human/registrationurl',
            'Registration URL',
            '',
            \tool_human2human\local\lti_adapter::DEFAULT_REGISTRATION_URL,
            PARAM_URL
        );
    }

    /**
     * A URL pasted with whitespace around it is saved without it.
     */
    public function test_pasted_whitespace_is_trimmed(): void {
        $this->resetAfterTest();

        $error = $this->setting()->write_setting("  https://h2h.example.net/lti/1.3/register/ \n");

        $this->assertSame('', $error);
        $this->assertSame('https://h2h.example.net/lti/1.3/register/', get_config('tool_human2human', 'registrationurl'));
    }

    /**
     * Anything Moodle's Dynamic Registration page could not open is refused.
     *
     * @dataProvider refused_provider
     * @param string $value The submitted value.
     */
    public function test_a_value_that_is_not_a_web_url_is_refused(string $value): void {
        $this->resetAfterTest();
        $before = get_config('tool_human2human', 'registrationurl');

        $error = $this->setting()->write_setting($value);

        $this->assertSame(get_string('registrationurl_invalid', 'tool_human2human'), $error);
        $this->assertSame($before, get_config('tool_human2human', 'registrationurl'));
    }

    /**
     * Values the setting must refuse.
     *
     * @return array
     */
    public static function refused_provider(): array {
        return [
            'script' => ['javascript:alert(1)'],
            'no scheme' => ['lti.human2human.ai/lti/1.3/register/'],
            'not a url' => ['not a url'],
        ];
    }
}
