# Moodle plugin local_campaign

## Description

This plugin is used to fill in a user profile field, named `Campaign`, based on a `GET` parameter.

This is used to find out which `Campaign` the user comes from.

## Technical description
- Adds a user profile category `Campaigns` and a use profile field `Campaigns` at install.
- Configurable in Admin > Local plugins > Campaigns
- Store users campaign(s) via `campaign` URL query string (https://your.moodle.org?campaign=foo).
- Also works if the user is not logged in and then logs in.
- All `Campaigns` are converted to lower case letters.

## Initial purpose

The initial purpose of this plugin is to respond to the need for a Moodle platform to identify via which `campaign` users arrived on the platform.
