@tool @tool_moodlebox
Feature: MoodleBox buttons appears in the footer
  Whenever I navigate to site administration page
  As an admin
  I want the GUI buttons to display at the bottom of each page

  Background:
    Given I log in as "admin"

  Scenario: Enable date and time setting buttons in the footer
    Given I navigate to "MoodleBox > MoodleBox settings" in site administration
    And I should see "Show date and time setting in footer"
    And I set the field "Show date and time setting in footer" to 1
    And I press "Save changes"
    And I navigate to "General > Notifications" in site administration
    And I should see "Date and time"

#     Then I should see "System information"
#     And I should see "Date and time setting"
#     And I should see "MoodleBox password change"
#     And I should see "Wi-Fi network password change"
#     And I should see "Restart and shutdown"
