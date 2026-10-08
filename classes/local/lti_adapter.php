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
    const DEFAULT_REGISTRATION_URL = 'https://lti.human2human.ai/lti/1.3/register/';

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
            // A new window, not an iframe: these activities use the microphone,
            // and iframe permission and focus handling make that unreliable for
            // screen reader and mobile users.
            'lti_launchcontainer' => LTI_LAUNCH_CONTAINER_WINDOW,
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
