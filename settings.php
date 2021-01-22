<?php
// This file is part of Moodle - https://moodle.org/
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

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    get_string_manager()->reset_caches();

    $settings = new admin_settingpage(
        'local_campaign_settings',
        get_string('pluginname', 'local_campaign'),
        'moodle/site:config'
    );

    $ADMIN->add('localplugins', $settings);

    //$settings->add(new admin_setting_configtextarea(
    $settings->add(new \local_campaign\local_campaign_admin_setting_configtextarea(
        'local_campaign/campaigns',
        get_string('campaigns_setting_name', 'local_campaign'),
        get_string('campaigns_setting_description', 'local_campaign'),
        null,
        PARAM_TEXT
    ));
}