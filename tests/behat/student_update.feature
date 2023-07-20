Feature: Student updates form (after submitting before)
  As a student
  I want to update a form on the collection activity

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
    And I log in as "admin"
    And I am on "Test Course" course homepage with editing mode on
    And I add a "Collection" to section "1" and I fill the form with:
      | name | Test |
    And I log out
    And I log in as "student"
    And I am on "Test Course" course homepage
    And I follow "Test"
    And the field "name" should be empty
    And the field "email" should be empty
    And the field "phone" should be empty
    When I fill out the form with:
      | name  | Old Behat Test |
      | email | old@test.com   |
      | phone | 0000000000     |
    And I press "Save changes"
    And I should see "Data saved successfully" on the page
    And I log out

  @javascript
  Scenario: Student updates the form
  and teacher views student answer after submission update
    Given I log in as "student"
    And I am on "Test Course" course homepage
    And I follow "Test"
    Then the field "name" should contain "Old Behat Test"
    And the field "email" should contain "old@test.com"
    And the field "phone" should contain "0000000000"
    And I fill out the form with:
      | name  | Behat Test     |
      | email | behat@test.com |
      | phone | 1122334455     |
    And I press "Save changes"
    And I should see "Data saved successfully" on the page
    And I log out
    And I log in as "teacher"
    And I am on "Test Course" course homepage
    And I follow "Test"
    Then I should see the user answers
