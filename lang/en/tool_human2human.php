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
$string['activitiesnone'] = 'No course uses Human2Human yet. Once the site is paired, teachers add it from the activity chooser.';
$string['activityadded'] = 'Added';
$string['activitycourse'] = 'Course';
$string['activityname'] = 'Activity';
$string['advanced'] = 'Advanced: pair with a different Human2Human';
$string['checkpairing'] = 'Check pairing';
$string['connect'] = 'Pair with Human2Human';
$string['connectedpending'] = 'Pairing is almost done: Human2Human is registered but not active yet, so teachers cannot find it in the activity chooser.';
$string['connectedready'] = 'Human2Human is paired and ready. Teachers can add "{$a}" from the activity chooser.';
$string['connectintro'] = 'Pairing opens Human2Human in a new tab. Sign in with an account that owns a team and choose the team this site belongs to. Nothing has to be copied between the two sites.';
$string['connectionaddress'] = 'Address';
$string['connectionstatus'] = 'Status';
$string['connectionteam'] = 'Team';
$string['connectiontool'] = 'Tool';
$string['finishsetup'] = 'Finish pairing';
$string['intro'] = 'Human2Human adds guided conversation activities to your courses. This page pairs the site with Human2Human and sets up the external tool that the activities launch through.';
$string['ltidisabled'] = 'The External tool activity (mod_lti) is disabled on this site, and Human2Human activities launch through it. Enable it under Site administration > Plugins > Activity modules > Manage activities, then come back.';
$string['manageconnection'] = 'Manage the pairing';
$string['managetools'] = 'Manage external tools';
$string['notconnected'] = 'This site is not paired with Human2Human yet.';
$string['pluginname'] = 'Human2Human';
$string['privacy:metadata:human2human'] = 'When a participant opens a Human2Human activity, the External tool activity (mod_lti) sends Human2Human the data below. Pairing sets the tool to always send the participant\'s name, so participants can be named to each other in a conversation, and never to send their email address. An administrator can change both settings under Manage tools. This plugin stores no personal data in Moodle.';
$string['privacy:metadata:human2human:activityid'] = 'The activity the participant opened.';
$string['privacy:metadata:human2human:courseid'] = 'The course the activity belongs to.';
$string['privacy:metadata:human2human:fullname'] = 'The participant\'s full name.';
$string['privacy:metadata:human2human:idnumber'] = 'The participant\'s ID number, if their Moodle profile has one.';
$string['privacy:metadata:human2human:language'] = 'The participant\'s language.';
$string['privacy:metadata:human2human:role'] = 'The participant\'s role in the course.';
$string['privacy:metadata:human2human:userid'] = 'The participant\'s Moodle user ID.';
$string['privacy:metadata:human2human:username'] = 'The participant\'s Moodle username, sent with their name.';
$string['privacynotice'] = 'By pairing, you accept these settings: Human2Human is added to the activity chooser with activity selection and grade sync, and it receives each participant\'s name and username but not their email address.';
$string['readyintro'] = 'Teachers add the activity to a course, then choose which Human2Human conversation it opens. Grades return to the gradebook.';
$string['registrationtarget'] = 'This pairs with a Human2Human other than the hosted service, at {$a}. Change it under Advanced if that is not intended.';
$string['registrationurl'] = 'Registration URL';
$string['registrationurl_desc'] = 'Only for a development, staging or private Human2Human. Leave empty to use the hosted service at {$a}. Pairing recognises the tool it registers by this address, so set it before pairing.';
$string['registrationurl_invalid'] = 'Enter a full http:// or https:// address, such as https://lti.human2human.ai/lti/1.3/register/.';
$string['registrationurlsaved'] = 'Pairing will use the new address.';
$string['relatedlinks'] = 'Related pages';
$string['setupdone'] = 'Human2Human is paired and available in the activity chooser.';
$string['setupheading'] = 'Pairing';
$string['setupnothing'] = 'Human2Human has not registered this site yet. Finish pairing in the Human2Human tab, then check again.';
$string['statuspending'] = 'Registered, but setup is not finished';
$string['statusready'] = 'Ready for teachers';
$string['stepconnect'] = 'Pair';
$string['stepdone'] = 'done';
$string['stepready'] = 'Ready for teachers';
$string['unpair'] = 'Unpair';
$string['unpairconfirm'] = 'Unpair this site from Human2Human? This removes the external tool from Moodle, and the activities in courses that launch it ({$a}) stop working. To stop launches on the Human2Human side too, deactivate the pairing on your Human2Human account page.';
$string['unpaired'] = 'This site is no longer paired with Human2Human.';
$string['unpairlink'] = 'Unpair this site';
$string['unpairnothing'] = 'This site was not paired with Human2Human, so there was nothing to unpair.';
$string['waitingintro'] = 'Finish pairing in the Human2Human tab. This page updates on its own when it can; if it does not, check the pairing.';
