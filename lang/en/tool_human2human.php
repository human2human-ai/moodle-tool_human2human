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

/**
 * English strings for tool_human2human.
 *
 * @package     tool_human2human
 * @copyright   2026 eduNEXT {@link https://www.edunext.co}
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['activities'] = 'Human2Human activities';
$string['activitiescount'] = 'Activities in courses that launch Human2Human: {$a}';
$string['activitiesheading'] = 'Activities in courses';
$string['activitiesnone'] = 'No course uses Human2Human yet. Once the site is connected, teachers add it from the activity chooser.';
$string['activityadded'] = 'Added';
$string['activitycourse'] = 'Course';
$string['activityname'] = 'Activity';
$string['advanced'] = 'Advanced: connect to a different Human2Human';
$string['connect'] = 'Connect Human2Human';
$string['connectedpending'] = 'Human2Human is registered but not active yet, so teachers cannot find it in the activity chooser.';
$string['connectedready'] = 'Human2Human is connected and ready. Teachers can add "{$a}" from the activity chooser.';
$string['connectintro'] = 'Connecting opens Human2Human in a new tab, where you sign in and choose the team the site belongs to. Nothing has to be copied between the two sites. You need a Human2Human account that owns a team.';
$string['connectionaddress'] = 'Address';
$string['connectionstatus'] = 'Status';
$string['connectionteam'] = 'Team';
$string['connectiontool'] = 'Tool';
$string['finishsetup'] = 'Finish setup';
$string['finishsetupintro'] = 'Review the privacy and service settings under Manage tools if you want to, then finish the setup. This activates the tool, adds it to the activity chooser, turns on activity selection and grade sync, and sends each participant\'s name but not their email address.';
$string['intro'] = 'Human2Human adds guided conversation activities to your courses. This page connects the site to Human2Human and sets up the external tool that the activities launch through.';
$string['ltidisabled'] = 'The External tool activity (mod_lti) is disabled on this site, and Human2Human activities launch through it. Enable it under Site administration > Plugins > Activity modules > Manage activities, then come back.';
$string['manageconnection'] = 'Manage the connection';
$string['managetools'] = 'Manage external tools';
$string['notconnected'] = 'This site is not connected to Human2Human yet.';
$string['pluginname'] = 'Human2Human';
$string['privacy:metadata'] = 'The Human2Human plugin stores no personal data and sends none itself. It configures an external tool, and everything a Human2Human activity sends or receives is sent by the External tool activity (mod_lti), which declares it in its own privacy metadata.';
$string['readyintro'] = 'Teachers add the activity to a course, then choose which Human2Human conversation it opens. Grades return to the gradebook.';
$string['registrationtarget'] = 'This connects to a Human2Human other than the hosted service, at {$a}. Change it under Advanced if that is not intended.';
$string['registrationurl'] = 'Registration URL';
$string['registrationurl_desc'] = 'Only for a development, staging or private Human2Human. Leave empty to use the hosted service at {$a}. Connect recognises the tool it registers by this address, so set it before connecting.';
$string['registrationurl_invalid'] = 'Enter a full http:// or https:// address, such as https://lti.human2human.ai/lti/1.3/register/.';
$string['registrationurlsaved'] = 'Connect will use the new address.';
$string['relatedlinks'] = 'Related pages';
$string['setupdone'] = 'Human2Human is active and available in the activity chooser.';
$string['setupheading'] = 'Connection';
$string['setupnothing'] = 'There is nothing to set up yet. Connect Human2Human first.';
$string['statuspending'] = 'Registered, but setup is not finished';
$string['statusready'] = 'Ready for teachers';
$string['stepconnect'] = 'Connect';
$string['stepdone'] = 'done';
$string['stepfinish'] = 'Finish setup';
$string['stepready'] = 'Ready for teachers';
