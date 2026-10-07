@tool @tool_human2human
Feature: Human2Human admin page
  In order to connect my site to Human2Human
  As an admin
  I need to reach the Human2Human page from site administration

  # Access control is covered in PHPUnit (tests/admin_page_test.php): Behat fails
  # any step that lands on an "Access denied" page.

  # Only the WCAG checks: the best-practice extras flag core Boost's navigation
  # (landmark-unique) on Moodle 4.5 and 5.2.
  @javascript @accessibility
  Scenario: An admin opens the Human2Human page
    Given I log in as "admin"
    When I navigate to "Plugins > Admin tools > Human2Human" in site administration
    Then I should see "This site is not connected to Human2Human yet."
    And the page should meet accessibility standards
