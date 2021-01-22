<?php
/**
 * @author    sreynders@cblue.be
 * @copyright CBlue SPRL, support@cblue.be
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   local_campaign
 */

defined('MOODLE_INTERNAL') || die;

global $GFG;

require_once $CFG->dirroot . '/user/profile/lib.php';

function  xmldb_local_campaign_install() {
    global $DB;

    $data = new stdClass();
    $data->datatype = 'text';
    $data->shortname = 'campaigns';
    $data->name = get_string('user_info_field_name', 'local_campaign');
    $data->description = get_string('user_info_field_description', 'local_campaign');
    $data->required = false;
    $data->locked = false;
    $data->forceunique = false;
    $data->signup = false;
    $data->visible = PROFILE_VISIBLE_NONE;
    $data->param1 = 30; // Display size (default 30)
    $data->param2 = 10000; // Maximum length (default 2048)
    $data->param3 = null; // Is this a password field? (default No)
    $data->param4 = null; // Link (default '')
    $data->param5 = null; // Link target (default None)
    $data->categoryid = 1; // user_info_category.id = 1 = "Other fields" // TODO: needs hardcoding or constant somewhere ?

    $field = $DB->get_record('user_info_field', array('shortname' => $data->shortname));

    if ($field === false) {
        $DB->insert_record('user_info_field', $data);
    }
}