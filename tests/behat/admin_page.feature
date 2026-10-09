@tool @tool_human2human
Feature: Human2Human admin page
  In order to pair my site with Human2Human
  As an admin
  I need to reach the Human2Human page from site administration

  # Access control is covered in PHPUnit (tests/admin_page_test.php): Behat fails
  # any step that lands on an "Access denied" page.

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
