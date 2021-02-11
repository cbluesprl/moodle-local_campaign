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

/**
 * @param $data
 * @return array|false|string[]
 */
function local_campaign_split_lines($data) {
    return preg_split('/(\r\n|\n|\r)/', $data);
}

/**
 * @throws dml_exception
 */
function local_campaign_before_http_headers() {
    global $DB, $SESSION, $USER;

    if (!isloggedin()) {
        if (array_key_exists('campaign', $_GET)) {
            $SESSION->local_campaign = $_GET['campaign'];
        }

        return true;
    }

    if (!empty($SESSION->local_campaign)) {
        $_GET['campaign'] = $SESSION->local_campaign;
        unset($SESSION->local_campaign);
    }
    if (!array_key_exists('campaign', $_GET)) {
        return true;
    }

    $profile = profile_user_record($USER->id);
    $profile_campaigns = !empty($profile->campaigns) ? array_map('trim', explode(',', $profile->campaigns)) : [];
    $config_campaigns = local_campaign_split_lines(get_config('local_campaign', 'campaigns'));

    if (!in_array($_GET['campaign'], $profile_campaigns) && in_array($_GET['campaign'], $config_campaigns)) {
        $profile_campaigns[] = $_GET['campaign'];
        $field = $DB->get_record('user_info_field', ['shortname' => 'campaigns']);

        $conditions = ['userid' => $USER->id, 'fieldid' => $field->id];
        $user_info_data = $DB->get_record('user_info_data', $conditions);
        $data = implode(',', $profile_campaigns);

        $dataobject = new stdClass();
        $dataobject->userid = $USER->id;
        $dataobject->fieldid = $field->id;
        $dataobject->data = $data;

        if ($user_info_data) {
            $dataobject->id = $user_info_data->id;
            $DB->update_record('user_info_data', $dataobject, $conditions);
        } else {
            $DB->insert_record('user_info_data', $dataobject);
        }
    }

    return true;
}
