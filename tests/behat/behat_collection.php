<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Steps definitions for collection activity.
 *
 * @package   mod_collection
 * @category  test
 * @copyright 2023 Tyler Vu
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');
require_once(__DIR__ . '/../../../../lib/behat/behat_field_manager.php');

use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ExpectationException;

class behat_collection extends behat_base {
    /**
     * @When /^I fill out the form with:$/
     * @param TableNode $table
     */
    public function i_fill_out_the_form_with(TableNode $table) {
        foreach ($table->getRowsHash() as $field => $value) {
            $this->execute("behat_forms::i_set_the_field_to", [$field, $value]);
        }
    }

    /**
     * Checks that the specified field contains the specified value.
     *
     * @Then the field :field should contain :value
     *
     * @param string $field
     * @param string $value
     */
    public function the_field_should_contain($field, $value) {
        // Get the field.
        $fieldnode = $this->find_field($field);
        // Check its current value.
        $currentvalue = $fieldnode->getValue();
        if ($currentvalue != $value) {
            throw new \Exception(sprintf('Field "%s" contains "%s", but expected "%s"', $field, $currentvalue, $value));
        }
    }

    /**
     * Checks that the specified field is empty.
     *
     * @Then the field :field should be empty
     *
     * @param string $field
     */
    public function the_field_should_be_empty($field) {
        $this->the_field_should_contain($field, '');
    }

    /**
     * @Then /^I should see "([^"]*)" on the page$/
     * @param string $text
     */
    public function i_should_see_on_the_page($text) {
        $this->execute('behat_general::assert_page_contains_text', [$text]);
    }

    /**
     * @Then /^I should see the user answers$/
     */
    public function i_should_see_the_user_answers() {
        $answers = ['Behat Test', 'behat@test.com', '1122334455'];

        foreach ($answers as $answer) {
            $this->execute('behat_general::assert_page_contains_text', [$answer]);
        }
    }
}
