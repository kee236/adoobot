   
/*
Addon Name: Visual Flow Builder 
Unique Name: visual_flow_builder
Modules:
{
   "315":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"0",
      "extra_text":"",
      "module_name":"Bot - Visual Flow Builder Access"
   }
}
Project ID: 59
Version: 1.6.9
Description: 

INSERT INTO `add_ons` (`id`, `add_on_name`, `unique_name`, `version`, `installed_at`, `update_at`, `purchase_code`, `module_folder_name`, `project_id`) VALUES
(2, 'Visual Flow Builder', 'visual_flow_builder', '1.6.9', '2022-01-19 12:00:00', '2022-01-19 12:00:00', '', 'visual_flow_builder', 59);


INSERT INTO `modules` (`id`, `module_name`, `add_ons_id`, `extra_text`, `limit_enabled`, `bulk_limit_enabled`, `deleted`) VALUES
(315, 'Bot - Visual Flow Builder Access', 2, '', '1', '0', '0');

 
CREATE TABLE IF NOT EXISTS `visual_flow_builder_campaign` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `user_id` int(11) NOT NULL,
                      `page_id` int(11) NOT NULL,
                      `unique_id` varchar(50) NOT NULL,
                      `reference_name` text NOT NULL,
                      `media_type` enum('fb','ig') NOT NULL DEFAULT 'fb',
                      `json_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
   PRIMARY KEY (`id`)
   ENGINE=InnoDB DEFAULT CHARSET=utf8;
        