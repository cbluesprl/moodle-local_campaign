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

namespace local_campaign;

require_once $CFG->dirroot . '/local/campaign/lib.php';

class local_campaign_admin_setting_configtextarea extends \admin_setting_configtextarea {

    /**
     * @param $data
     * @return bool|\lang_string|mixed|string
     * @throws \coding_exception
     */
    public function validate($data) {
        $lines = local_campaign_split_lines($data);
        foreach ($lines as $line) {
            if (!empty($line) && !preg_match('/^[a-zA-Z0-9]+$/', $line)) {
                return get_string('campaigns_setting_validation_error', 'local_campaign');
            }
        }
        return true;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return bool
     * @throws \dml_exception
     */
    public function config_write($name, $value) {
        global $DB;

        // Get field "campaigns"
        $field = $DB->get_record('user_info_field', ['shortname' => 'campaigns']);

        // Sanitize new value
        $sanitized_values = [];
        foreach (local_campaign_split_lines($value) as $v) {
            $v = trim($v);
            $v = strtolower($v);
            $sanitized_values[] = $v;
        }

        // Get old value
        $old_value = get_config('local_campaign', 'campaigns');
        $sanitized_old_values = [];
        foreach (local_campaign_split_lines($old_value) as $v) {
            $v = trim($v);
            $v = strtolower($v);
            $sanitized_old_values[] = $v;
        }

        // Save new value
        parent::config_write($name, implode("\n", $sanitized_values));

        // If one or more campaign is removed, remove it from all users
        foreach ($sanitized_old_values as $ov) {
            if (!in_array($ov, $sanitized_values)) { // Old value not in new value, remove it
                // Get all user_info_data with this campaign removed
                $rs = $DB->get_recordset_sql(
                    "SELECT * FROM {user_info_data} WHERE fieldid = $field->id AND " . $DB->sql_like('data', ':data', false, false),
                    ['data' => "%$ov%"]
                );
                foreach ($rs as $r) {
                    // Clean data and remove campaign
                    $data = [];
                    foreach (explode(',', $r->data) as $d) {
                        $d = trim($d);
                        $d = strtolower($d);
                        if ($d != $ov) { // Do not add removed campaign
                            $data[] = $d;
                        }
                    }
                    // Remove campaign from user profile field
                    if (empty($data)) {
                        $DB->delete_records('user_info_data', ['id' => $r->id]);
                    } else {
                        $r->data = implode(',', $data);
                        $DB->update_record('user_info_data', $r);
                    }
                }
                $rs->close();
            }
        }

        return true;
    }
}
