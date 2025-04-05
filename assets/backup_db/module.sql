
INSERT INTO `add_ons` (`id`, `add_on_name`, `unique_name`, `version`, `installed_at`, `update_at`, `purchase_code`, `module_folder_name`, `project_id`) VALUES

(3, 'Instagram Bot & Private Reply', 'instagram_bot', '1.0', '2022-01-19 12:00:00', '2022-01-19 12:00:00', '', 'instagram_bot', 62);


INSERT INTO `modules` (`id`, `module_name`, `add_ons_id`, `extra_text`, `limit_enabled`, `bulk_limit_enabled`, `deleted`)
VALUES
(320, 'Bot - Instagram Bot', 3, 'month', '1', '0', '0');

INSERT INTO `add_ons` (`id`, `add_on_name`, `unique_name`, `version`, `installed_at`, `update_at`, `purchase_code`, `module_folder_name`, `project_id`) VALUES

(4,'AI Integration', 'ai_reply', '1.1', '2024-01-15 12:00:00', '2024-01-15 12:00:00', '', 'ai_reply', 67),


INSERT INTO `modules` (`id`, `module_name`, `add_ons_id`, `extra_text`, `limit_enabled`, `bulk_limit_enabled`, `deleted`) VALUES ('340', 'Bot - AI Reply', '4', '', '1', '0', '0');






INSERT INTO `add_ons` (`id`, `add_on_name`, `unique_name`, `version`, `installed_at`, `update_at`, `purchase_code`, `module_folder_name`, `project_id`) VALUES

(‘0’,‘Comment Reply Enhancers’,’comment_reply_enhancers’,’1.3’,'0000-00-00 00:00:00', '0000-00-00 00:00:00', '',’comment_reply_enhancers’,’29’)



INSERT INTO `modules` (`id`, `module_name`, `add_ons_id`, `extra_text`, `limit_enabled`, `bulk_limit_enabled`, `deleted`) VALUES
(‘88’,’Comment Reply Enhancers : Comment Hide/Delete and Reply with multimedia content’,0,’’,’1’,’0’,’0’),
(‘201’,‘Comment Reply Enhancers : Comment & Bulk Tag Campaign’,’0’,’month’,’0’,’1’,’0’),
(‘202’,‘Comment Reply Enhancers : Bulk Comment Reply Campaign’,’0’,’month’,’1’,’0’,0),
(‘204’,’Comment Reply Enhancers : Full Page Auto Reply’,’0’,’’,’1’,’0’,’0’ ),
(‘206’,’Comment Reply Enhancers : Full Page Auto Like/Share’,’0’,’’,’1’,,’0’,’0’)”

