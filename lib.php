<?php

function local_campaign_split_lines($data)
{
    return preg_split('/(\r\n|\n|\r)/', $data);
}

function local_campaign_before_http_headers()
{
    global $DB, $SESSION, $USER;

    $campaign = null;

    if (array_key_exists('campaign', $_GET)) {
        $campaign = $_GET['campaign'];
        $SESSION->local_campaign = $campaign;
    } elseif (!empty($SESSION->local_campaign)) {
        $campaign = $SESSION->local_campaign;
    }

    if (isloggedin() && !empty($campaign)) {
        $profile = profile_user_record($USER->id);
        $profile_campaigns = !empty($profile->campaigns) ? array_map('trim', explode(',', $profile->campaigns)) : [];
        $config_campaigns = local_campaign_split_lines(get_config('local_campaign', 'campaigns'));

        if (!in_array($campaign, $profile_campaigns) && in_array($campaign, $config_campaigns)) {
            $profile_campaigns[] = $campaign;
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
    }
}