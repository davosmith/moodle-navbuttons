Feature: I can use navigation buttons to navigate through a course in Moodle

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "activities" exist:
      | activity | name         | course | idnumber | content             |
      | forum    | Test forum 1 | C1     | FORUM01  |                     |
      | page     | Test page 1  | C1     | PAGE01   | Page 1 test content |
      | page     | Test page 2  | C1     | PAGE02   | Page 2 test content |
      | forum    | Test forum 2 | C1     | FORUM02  |                     |
    And the following "users" exist:
      | username |
      | student1 |
      | teacher1 |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Navigation Buttons" block
    And I log out

  Scenario: A student can navigate through the course using the navigation buttons
    Given I log in as "student1"
    When I am on "Course 1" course homepage
    Then "#navbuttons" "css_element" should not exist

    When I follow "Test forum 1"
    Then I should see "Test forum 1"
    And I should see "Add a new discussion topic"
    And "#navbuttons .prev" "css_element" should not exist
    And "#navbuttons .next" "css_element" should exist

    When I follow "Next activity: Test page 1"
    Then I should see "Page 1 test content"
    And I should not see "Add a new discussion topic"
    And "#navbuttons .prev" "css_element" should exist
    And "#navbuttons .next" "css_element" should exist

    When I follow "Next activity: Test page 2"
    Then I should see "Page 2 test content"
    And I should not see "Page 1 test content"
    And "#navbuttons .prev" "css_element" should exist
    And "#navbuttons .next" "css_element" should exist

    When I follow "Next activity: Test forum 2"
    Then I should see "Test forum 1"
    And I should see "Add a new discussion topic"
    And I should not see "Page 2 test content"
    And "#navbuttons .prev" "css_element" should exist
    And "#navbuttons .next" "css_element" should not exist

    When I follow "First activity in course: Test forum 1"
    Then I should see "Test forum 1"
    And I should see "Add a new discussion topic"
    And I should not see "test content"

    When I follow "Last activity in course: Test forum 2"
    Then I should see "Test forum 2"
    And I should see "Add a new discussion topic"
    And I should not see "test content"

    When I follow "Previous activity: Test page 2"
    Then I should see "Page 2 test content"

    When I follow "Site front page"
    Then I should see "Course overview"
