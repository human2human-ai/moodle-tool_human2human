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
 * The only code in this plugin that touches mod_lti internals.
 *
 * Writes go through mod_lti's own functions. The one direct table read is
 * find_type(), because mod_lti exposes no lookup that answers "the site-level
 * 1.3 type for this domain": lti_get_tools_by_domain() joins
 * lti_types_categories and so hides a category-restricted type, and
 * lti_filter_get_types() returns neither tooldomain nor ltiversion.
 *
 * Either way these are another module's internals and do change between
 * releases. Keeping them in one class means one place to fix and one class to
 * cover with tests on every supported branch.
 *
 * @package     tool_human2human
 * @copyright   2026 eduNEXT {@link https://www.edunext.co}
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lti_adapter {
    /** @var string Where Dynamic Registration starts when the admin has not set another. */
    const DEFAULT_REGISTRATION_URL = 'https://app.human2human.ai/lti/1.3/register/';

    /** @var string The custom parameter Human2Human sends its team name in. */
    const TEAM_CUSTOM_PARAMETER = 'human2human_team';

    /**
     * The URL the Connect action hands to Moodle's Dynamic Registration page.
     *
     * @return string
     */
    public static function registration_url(): string {
        $url = get_config('tool_human2human', 'registrationurl');
        return !empty($url) ? $url : self::DEFAULT_REGISTRATION_URL;
    }

    /**
     * Point Connect at another Human2Human, or back at the hosted one.
     *
     * Only an override is stored. The hosted URL, or an empty value, clears it,
     * so the site follows DEFAULT_REGISTRATION_URL rather than a copy of it.
     *
     * @param string $url A URL clean_registration_url() accepted, or ''.
     */
    public static function set_registration_url(string $url): void {
        if ($url === '' || $url === self::DEFAULT_REGISTRATION_URL) {
            unset_config('registrationurl', 'tool_human2human');
            return;
        }
        set_config('registrationurl', $url, 'tool_human2human');
    }

    /**
     * Whether Connect goes somewhere other than the hosted Human2Human.
     *
     * @return bool
     */
    public static function is_registration_url_overridden(): bool {
        return self::registration_url() !== self::DEFAULT_REGISTRATION_URL;
    }

    /**
     * A typed or pasted registration URL, cleaned, or null when it is not one.
     *
     * Surrounding whitespace is dropped, since a copied URL often carries some.
     * Anything but an http(s) URL that PARAM_URL leaves unchanged is refused:
     * Moodle's Dynamic Registration page could not open it.
     *
     * @param string $value
     * @return string|null
     */
    public static function clean_registration_url(string $value): ?string {
        $value = trim($value);
        $scheme = strtolower((string) parse_url($value, PHP_URL_SCHEME));
        if (!in_array($scheme, ['http', 'https'], true)) {
            return null;
        }
        return clean_param($value, PARAM_URL) === $value ? $value : null;
    }

    /**
     * The host the registered tool launches from, used to recognise its tool type.
     *
     * @return string|null Null when the configured URL has no usable host.
     */
    public static function tool_domain(): ?string {
        global $CFG;
        require_once($CFG->dirroot . '/mod/lti/locallib.php');

        $domain = lti_get_domain_from_url(self::registration_url());
        return !empty($domain) ? $domain : null;
    }

    /**
     * The site-level LTI 1.3 tool type that Dynamic Registration created for us.
     *
     * Not lti_get_tools_by_domain(): that one is built for matching a launch, so
     * it filters by course category and would hide a category-restricted type
     * from this site-level lookup.
     *
     * @return \stdClass|null
     */
    public static function find_type(): ?\stdClass {
        global $CFG, $DB, $SITE;
        require_once($CFG->dirroot . '/mod/lti/locallib.php');

        $domain = self::tool_domain();
        if ($domain === null) {
            return null;
        }
        $types = $DB->get_records('lti_types', [
            'tooldomain' => $domain,
            'ltiversion' => LTI_VERSION_1P3,
            'course' => $SITE->id,
        ], 'id DESC');

        return $types ? reset($types) : null;
    }

    /**
     * The Human2Human team the tool was registered for, if it said.
     *
     * Human2Human sends it as a custom parameter of its registration document,
     * which Moodle stores with the type. Tools registered before it did, or by
     * hand, have none. A snapshot: a later rename on Human2Human does not show.
     *
     * @param \stdClass $type
     * @return string|null
     */
    public static function team_name(\stdClass $type): ?string {
        global $CFG;
        require_once($CFG->dirroot . '/mod/lti/locallib.php');

        $custom = lti_get_type_config($type->id)['customparameters'] ?? '';
        foreach (preg_split('/[\r\n;]+/', $custom) as $line) {
            [$name, $value] = array_pad(explode('=', $line, 2), 2, '');
            if (trim($name) === self::TEAM_CUSTOM_PARAMETER && trim($value) !== '') {
                return trim($value);
            }
        }
        return null;
    }

    /**
     * Course activities that launch Human2Human, a page at a time.
     *
     * Matched by tool domain rather than type id, so activities created under an
     * earlier registration of the same Human2Human are listed too.
     *
     * @param int $from First record, for paging.
     * @param int $limit Records per page.
     * @return array [total, records with cmid, name, courseid, coursename, timecreated]
     */
    public static function linked_activities(int $from = 0, int $limit = 0): array {
        global $CFG, $DB, $SITE;
        require_once($CFG->dirroot . '/mod/lti/locallib.php');

        $domain = self::tool_domain();
        if ($domain === null) {
            return [0, []];
        }
        $params = [
            'domain' => $domain,
            'ltiversion' => LTI_VERSION_1P3,
            'site' => $SITE->id,
            'modname' => 'lti',
        ];
        $fromwhere = "FROM {lti} l
                       JOIN {lti_types} t ON t.id = l.typeid
                       JOIN {course} c ON c.id = l.course
                       JOIN {modules} m ON m.name = :modname
                       JOIN {course_modules} cm ON cm.module = m.id AND cm.instance = l.id
                      WHERE t.tooldomain = :domain AND t.ltiversion = :ltiversion AND t.course = :site
                            AND cm.deletioninprogress = 0";

        $total = $DB->count_records_sql("SELECT COUNT(1) $fromwhere", $params);
        $records = $DB->get_records_sql(
            "SELECT cm.id AS cmid, l.name, c.id AS courseid, c.fullname AS coursename, l.timecreated
             $fromwhere
             ORDER BY l.timecreated DESC, cm.id DESC",
            $params,
            $from,
            $limit
        );
        return [$total, array_values($records)];
    }

    /**
     * Whether the tool type still needs finish_setup() run against it.
     *
     * @param \stdClass|null $type The tool type, or null to look it up.
     * @return bool
     */
    public static function needs_setup(?\stdClass $type = null): bool {
        global $CFG;
        require_once($CFG->dirroot . '/mod/lti/locallib.php');

        $type = $type ?? self::find_type();
        if ($type === null) {
            return false;
        }
        return (int) $type->state !== LTI_TOOL_STATE_CONFIGURED
            || (int) $type->coursevisible !== LTI_COURSEVISIBLE_ACTIVITYCHOOSER;
    }

    /**
     * Activate the registered tool type and put it in the activity chooser.
     *
     * Dynamic Registration leaves the type pending and only preconfigured, so
     * teachers never find it in the chooser and most never find it at all. This
     * applies the settings the registration cannot, and is safe to run twice.
     *
     * @return bool False when there is no tool type to configure yet.
     */
    public static function finish_setup(): bool {
        global $CFG;
        require_once($CFG->dirroot . '/mod/lti/locallib.php');

        $type = self::find_type();
        if ($type === null) {
            return false;
        }
        $existing = lti_get_type_config($type->id);

        $config = (object) [
            // The one setting that decides whether teachers can find this at all.
            'lti_coursevisible' => LTI_COURSEVISIBLE_ACTIVITYCHOOSER,
            // Deep Linking, so a teacher picks an activity instead of pasting an id.
            'lti_contentitem' => 1,
            // Embedded with the course blocks, so learners keep the Moodle
            // navigation around the activity. Moodle's embed iframe carries
            // `allow="microphone <tool origin>"` (mod/lti/view.php), which these
            // activities need; LaunchView still sends mobile webviews and Safari
            // out to a real browser tab, where the iframe grant does not reach.
            'lti_launchcontainer' => LTI_LAUNCH_CONTAINER_EMBED,
            // Human2Human names participants to each other, so the name is sent
            // and the email address is not. Only what the tool asked for in its
            // registration document.
            'lti_sendname' => LTI_SETTING_ALWAYS,
            'lti_sendemailaddr' => LTI_SETTING_NEVER,
            // 2 is grade sync including line item management, which the tool
            // needs to name its own column when a deep link arrived without one.
            'ltiservice_gradesynchronization' => 2,
            'ltiservice_memberships' => 0,
            'ltiservice_toolsettings' => 0,
            // The function lti_prepare_type_for_save() rewrites forcessl from whatever config
            // it is handed, with no isset() guard, so a partial update silently
            // clears it. Carry the stored value through.
            'lti_forcessl' => $existing['forcessl'] ?? 0,
        ];

        lti_update_type($type, $config);
        lti_set_state_for_type($type->id, LTI_TOOL_STATE_CONFIGURED);

        return true;
    }
}
