<?php

namespace local_campaign;

require_once $CFG->dirroot . '/local/campaign/lib.php';

class local_campaign_admin_setting_configtextarea extends \admin_setting_configtextarea
{
    public function validate($data)
    {
        $lines = local_campaign_split_lines($data);
        foreach ($lines as $line) {
            if (!empty($line) && !preg_match('/^[a-zA-Z0-9]+$/', $line)) {
                return get_string('campaigns_setting_validation_error', 'local_campaign');
            }
        }
        return true;
    }

    public function config_write($name, $value)
    {
        $value = implode("\n", array_map('trim', array_filter(local_campaign_split_lines($value))));
        return parent::config_write($name, $value);
    }
}