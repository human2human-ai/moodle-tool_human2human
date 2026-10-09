# This file is part of Moodle - https://moodle.org/
#
# Moodle is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# Moodle is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Moodle.  If not, see <https://www.gnu.org/licenses/>.
#
# @package     tool_human2human
# @copyright   2026 eduNEXT {@link https://www.edunext.co}
# @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@tool @tool_human2human
Feature: Human2Human admin page
  In order to pair my site with Human2Human
  As an admin
  I need to reach the Human2Human page from site administration

  # Access control is covered in PHPUnit (tests/admin_page_test.php): Behat fails
  # any step that lands on an "Access denied" page.
  #
  # Behat sites run with developer debugging shown, and a step fails on any PHP
  # warning, notice or debugging() message on the page. So each scenario here is
  # also a check that the page produces none, which the Marketplace guidelines
  # require.

  # Only the WCAG checks: the best-practice extras flag core Boost's navigation
  # (landmark-unique) on Moodle 4.5 and 5.2.
  @javascript @accessibility
  Scenario: An admin opens the Human2Human page and is offered the pair action
    Given I log in as "admin"
    When I navigate to "Plugins > Admin tools > Human2Human" in site administration
    Then I should see "This site is not paired with Human2Human yet."
    And I should see "Pair with Human2Human"
    # Nothing to finish or unpair until a registration exists.
    And "Finish pairing" "button" should not exist
    And I should not see "Unpair this site"
    # The hosted service needs no mention and no review.
    And I should not see "other than the hosted service"
    And I should not see "Registration URL"
    And the page should meet accessibility standards

  @javascript
  Scenario: An admin points pairing at a different Human2Human under Advanced
    Given I log in as "admin"
    And I navigate to "Plugins > Admin tools > Human2Human" in site administration
    When I expand all fieldsets
    And I set the field "Registration URL" to "https://h2h.example.net/lti/1.3/register/"
    And I press "Save changes"
    Then I should see "Pairing will use the new address."
    And I should see "other than the hosted service, at https://h2h.example.net/lti/1.3/register/"
    # Clearing it goes back to the hosted service.
    And I expand all fieldsets
    And I set the field "Registration URL" to ""
    And I press "Save changes"
    And I should not see "other than the hosted service"

  @javascript @accessibility
  Scenario: An admin finds Human2Human among the activity modules
    Given I log in as "admin"
    When I navigate to "Plugins > Activity modules > Human2Human" in site administration
    Then I should see "Human2Human activities"
    And I should see "This site is not paired with Human2Human yet."
    And I should see "No course uses Human2Human yet."
    And the page should meet accessibility standards
    And I click on "Pair with Human2Human" "link"
    And "Pair with Human2Human" "button" should exist

  # Without @javascript, so the automatic finish (amd/src/autofinish.js) stays out
  # of the way and each state is visited on purpose.
  Scenario: An admin finishes pairing, lists the activities and unpairs
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And Human2Human has registered this site for the team "Example University"
    And the course "C1" has a Human2Human activity "Reflect on teamwork"
    And I log in as "admin"
    When I navigate to "Plugins > Admin tools > Human2Human" in site administration
    Then I should see "Pairing is almost done"
    And I press "Finish pairing"
    And I should see "Human2Human is paired and available in the activity chooser."
    And I should see "Ready for teachers"
    And I navigate to "Plugins > Activity modules > Human2Human" in site administration
    And I should see "Example University"
    And I should see "Activities in courses that launch Human2Human: 1"
    And I should see "Reflect on teamwork"
    And I should see "Course 1"
    And I navigate to "Plugins > Admin tools > Human2Human" in site administration
    And I click on "Unpair this site" "link"
    And I should see "the activities in courses that launch it (1) stop working"
    And I press "Unpair"
    And I should see "This site is no longer paired with Human2Human."
    And I should see "Pair with Human2Human"
