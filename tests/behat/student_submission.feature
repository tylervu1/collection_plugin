Feature: Student submits form for first time
  As a student
  I want to submit a form on the collection activity for the first time
  
  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | idnumber |
      | Test Course | TC1 | 0 | tc1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | student | Student | User | student@example.com |
      | teacher | Teacher | User | teacher@example.com |
    And the following "course enrolments" exist:
      | user  | course | role |
      | student | TC1 | student |
      | teacher | TC1 | editingteacher |
    Given I log in as "admin"
    And I am on "Test Course" course homepage with editing mode on
    And I add a "Collection" to section "1" and I fill the form with:
      | name | Test |
    And I log out

  @javascript
  Scenario: Student fills out the form 
  and teacher views student answer after submission
    Given I log in as "student"
    And I am on "Test Course" course homepage
    And I follow "Test"
    Then the field "name" should be empty
    And the field "email" should be empty
    And the field "phone" should be empty
    When I fill out the form with:
      | name  | Behat Test     |
      | email | behat@test.com |
      | phone | 1122334455     |
    And I press "Save changes"
    Then I should see "Data saved successfully" on the page
    And I log out
    Given I log in as "teacher"
    And I am on "Test Course" course homepage
    And I follow "Test"
    Then I should see the user answers