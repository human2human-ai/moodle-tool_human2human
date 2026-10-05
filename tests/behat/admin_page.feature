@tool @tool_human2human
Feature: Human2Human admin page
  In order to connect my site to Human2Human
  As an admin
  I need to reach the Human2Human page from site administration

  @javascript @accessibility
  Scenario: An admin opens the Human2Human page
    Given I log in as "admin"
    When I navigate to "Plugins > Admin tools > Human2Human" in site administration
    Then I should see "This site is not connected to Human2Human yet."
    And the page should meet accessibility standards with "best-practice" extra tests

  Scenario: A teacher cannot open the Human2Human page
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And I log in as "teacher1"
    When I visit "/admin/tool/human2human/index.php"
    Then I should not see "This site is not connected to Human2Human yet."
