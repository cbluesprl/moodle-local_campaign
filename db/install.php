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
 * @author    sreynders@cblue.be
 * @copyright CBlue SPRL, support@cblue.be
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   local_campaign
 */

defined('MOODLE_INTERNAL') || die;

require_once $CFG->dirroot . '/user/profile/lib.php';

/**
 * @throws dml_exception
 */
function xmldb_local_campaign_install() {
    global $DB;

    // Insert user_info_category
    $category = new stdClass();
    $category->name = 'Campaigns';
    $category->sortorder = (int) $DB->get_record_sql('SELECT COUNT(*) + 1 AS sortorder FROM {user_info_category}')->sortorder;
    $category->id = $DB->insert_record('user_info_category', $category);

    // Insert user_info_field
    $field = new stdClass();
    $field->shortname = 'campaigns';
    $field->name = 'Campaigns';
    $field->datatype = 'text';
    $field->description = '';
    $field->required = false;
    $field->locked = true;
    $field->forceunique = false;
    $field->signup = false;
    $field->visible = PROFILE_VISIBLE_NONE;
    $field->param1 = 30; // Display size (default 30)
    $field->param2 = 10000; // Maximum length (default 2048)
    $field->param3 = null; // Is this a password field? (default No)
    $field->param4 = null; // Link (default '')
    $field->param5 = null; // Link target (default None)
    $field->categoryid = $category->id;
    $field->sortorder = 1;

    $DB->insert_record('user_info_field', $field);
}
