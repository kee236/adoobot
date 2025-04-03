<?php
/*
Addon Name: Instagram Bot and Private Reply
Unique Name: instagram_bot
Modules:
{
   "320":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"0",
      "extra_text":"",
      "module_name":"Bot - Instagram Bot"
   }
}
Project ID: 62
Version: 1.0
Description: 
*/

INSERT INTO `add_ons` (`id`, `add_on_name`, `unique_name`, `version`, `installed_at`, `update_at`, `purchase_code`, `module_folder_name`, `project_id`) VALUES

(3, 'Instagram Bot & Private Reply', 'instagram_bot', '1.0', '2022-01-19 12:00:00', '2022-01-19 12:00:00', '', 'instagram_bot', 62);

INSERT INTO `modules` (`id`, `module_name`, `add_ons_id`, `extra_text`, `limit_enabled`, `bulk_limit_enabled`, `deleted`)

(320, 'Bot - Instagram Bot', 3, '', '0', '0', '');


