@tool @tool_moodlebox
Feature: MoodleBox buttons appears in the footer
  Whenever I navigate to the home page
  As an admin
  I want the MoodleBox GUI buttons to display at the bottom of each page

  Background:
    Given I log in as "admin"

  Scenario: Enable date and time setting buttons in the footer
    Given I navigate to "MoodleBox > MoodleBox settings" in site administration
    When I set the field "Show date and time setting in footer" to "1"
    And I press "Save changes"
    And I am on site homepage
    Then I should see "Date and time"

  Scenario: Enable restart and shutdown buttons in the footer
    Given I navigate to "MoodleBox > MoodleBox settings" in site administration
    When I set the field "Show restart and shutdown buttons in footer" to "1"
    And I press "Save changes"
    And I am on site homepage
    And I press "Restart MoodleBox"
