INSERT INTO `modules` (`id`, `module_name`, `add_ons_id`, `extra_text`, `limit_enabled`, `bulk_limit_enabled`, `deleted`) VALUES
(33, 'Social Poster - Account Import : Youtube', 0, '', '1', '0', '0'),
(65, 'Facebook Accounts', 0, '', '1', '0', '0'),
(78, 'Subscriber Manager : Background Lead Scan', 0, '', '0', '0', '0'),
(79, 'Conversation Promo Broadcast Send', 0, 'month', '1', '1', '0'),
(80, 'Comment Automation : Auto Reply Posts', 0, 'month', '1', '0', '0'),
(82, 'Inbox Conversation Manager', 0, '', '0', '0', '0'),
(100, 'Social Poster - Access', 0, '', '0', '0', '0'),
(101, 'Social Poster - Account Import : Pinterest', 0, '', '1', '0', '0'),
(102, 'Social Poster - Account Import : Twitter', 0, '', '1', '0', '0'),
(103, 'Social Poster - Account Import :  Linkedin', 0, '', '1', '0', '0'),
(105, 'Social Poster - Account Import : Reddit', 0, '', '1', '0', '0'),
(107, 'Social Poster - Account Import : Blogger', 0, '', '1', '0', '0'),
(108, 'Social Poster - Account Import :  WordPress', 0, '', '1', '0', '0'),
(109, 'Social Poster - Account Import :  WordPress (Self hosted) ', 0, '', '1', '0', '0'),
(110, 'Social Poster - Text Post', 0, 'month', '1', '1', '0'),
(111, 'Social Poster - Image Post', 0, 'month', '1', '1', '0'),
(112, 'Social Poster - Video Post', 0, 'month', '1', '1', '0'),
(113, 'Social Poster - Link Post', 0, 'month', '1', '1', '0'),
(114, 'Social Poster - HTML Post', 0, 'month', '1', '1', '0'),
(197, 'Messenger Bot - Persistent Menu', 0, '', '0', '0', '0'),
(198, 'Messenger Bot - Persistent Menu : Copyright Enabled', 0, '', '0', '0', '0'),
(199, 'Messenger Bot', 0, '', '0', '0', '0'),
(200, 'Facebook Pages', 0, '', '1', '0', '0'),
(220, 'Facebook Posting : CTA Post', 0, 'month', '1', '0', '0'),
(222, 'Facebook Posting : Carousel/Slider Post', 0, 'month', '1', '0', '0'),
(223, 'Facebook Posting :  Text/Image/Link/Video Post', 0, 'month', '1', '0', '0'),
(251, 'Comment Automation : Auto Comment Campaign', 0, '', '1', '0', '0'),
(256, 'RSS Auto Posting', 0, '', '1', '0', '0'),
(257, 'Messenger Bot : Export, Import & Tree View', 0, '', '1', '', '0'),
(263, 'Email Broadcast - Email Send', 0, 'month', '1', '0', '0'),
(264, 'SMS Broadcast - SMS Send', 0, 'month', '1', '0', '0'),
(265, 'Messenger Bot - Email Auto Responder', 0, '', '1', '0', '0'),
(267, 'Utility Search Tools', 0, 'month', '1', '0', '0'),
(268, 'Messenger E-commerce', 0, '', '1', '0', '0'),
(275, 'One Time Notification Send', 0, 'month', '1', '0', '0'),
(277, 'Social Poster - Account Import :  Medium', 0, '', '1', '0', '0'),
(279, 'Instagram Auto Comment Reply Enable Post', 0, 'month', '1', '0', '0'),
(296, 'Instagram Posting : Image/Video Post', 0, 'month', '1', '1', '0'),
(310, 'Whatsapp Send Order', 4, '', '0', '0', '0'),
(317, 'E-commerce Related Products', 5, '', '1', '0', '0');






CREATE TABLE `pinterest_board_info` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pinterest_table_id` int(11) NOT NULL COMMENT 'rx_pinterest_autopost table id',
  `board_name` varchar(255) NOT NULL,
  `board_url` varchar(255) NOT NULL,
  `board_id` varchar(25) NOT NULL,
  `deleted` enum('0','1') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pinterest_config`
--

CREATE TABLE `pinterest_config` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `app_name` varchar(255) NOT NULL,
  `client_id` varchar(255) NOT NULL,
  `client_secret` varchar(255) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `deleted` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pinterest_users_info`
--

CREATE TABLE `pinterest_users_info` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pinterest_user_id` varchar(30) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `pins` int(11) NOT NULL,
  `boards` int(11) NOT NULL,
  `image` text NOT NULL,
  `code` varchar(255) NOT NULL,
  `add_date` date NOT NULL,
  `pinterest_config_table_id` int(11) NOT NULL,
  `deleted` enum('0','1') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `reddit_config`
--

CREATE TABLE `reddit_config` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `app_name` varchar(255) NOT NULL,
  `client_id` varchar(255) NOT NULL,
  `client_secret` varchar(255) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `deleted` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `reddit_users_info`
--

CREATE TABLE `reddit_users_info` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `access_token` text NOT NULL,
  `refresh_token` varchar(250) NOT NULL,
  `token_type` varchar(200) NOT NULL,
  `username` varchar(200) DEFAULT NULL,
  `profile_pic` text NOT NULL,
  `url` varchar(255) NOT NULL,
  `add_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE `twitter_config` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `app_name` varchar(255) NOT NULL,
  `consumer_id` varchar(255) NOT NULL,
  `consumer_secret` varchar(255) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `deleted` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `twitter_users_info`
--

CREATE TABLE `twitter_users_info` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `oauth_token` varchar(200) NOT NULL,
  `oauth_token_secret` varchar(200) NOT NULL,
  `screen_name` varchar(200) NOT NULL,
  `twitter_user_id` varchar(200) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `profile_image` text NOT NULL,
  `followers` int(11) NOT NULL,
  `add_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `wordpress_config` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `app_name` varchar(255) NOT NULL,
  `client_id` varchar(255) NOT NULL,
  `client_secret` varchar(255) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `deleted` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `wordpress_config_self_hosted`
--

CREATE TABLE `wordpress_config_self_hosted` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `domain_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `authentication_key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `blog_category` text COLLATE utf8mb4_unicode_ci,
  `status` enum('1','0') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `deleted` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `error_message` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wordpress_users_info`
--

CREATE TABLE `wordpress_users_info` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `blog_id` varchar(200) NOT NULL,
  `blog_url` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `icon` text NOT NULL,
  `posts` int(11) NOT NULL,
  `access_token` varchar(255) NOT NULL,
  `categories` varchar(255) NOT NULL,
  `last_update_time` datetime NOT NULL,
  `deleted` enum('0','1') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `youtube_channel_info`
--

CREATE TABLE `youtube_channel_info` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `channel_id` varchar(255) NOT NULL,
  `access_token` text,
  `refresh_token` text,
  `last_update` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `youtube_channel_list`
--

CREATE TABLE `youtube_channel_list` (
  `id` int(11) NOT NULL,
  `channel_info_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `channel_id` varchar(200) NOT NULL,
  `title` text,
  `description` text,
  `profile_image` text,
  `cover_image` text,
  `view_count` varchar(250) DEFAULT NULL,
  `video_count` varchar(250) DEFAULT NULL,
  `subscriber_count` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `youtube_video_list`
--

CREATE TABLE `youtube_video_list` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `channel_id` varchar(200) DEFAULT NULL,
  `video_id` varchar(200) DEFAULT NULL,
  `title` text,
  `image_link` text,
  `publish_time` varchar(200) DEFAULT NULL,
  `tags` text,
  `categoryId` int(11) DEFAULT NULL,
  `defaultLanguage` varchar(255) NOT NULL,
  `privacyStatus` varchar(255) DEFAULT NULL,
  `localizations` text,
  `liveBroadcastContent` varchar(250) DEFAULT NULL,
  `duration` varchar(250) DEFAULT NULL,
  `dimension` varchar(200) DEFAULT NULL,
  `definition` varchar(200) DEFAULT NULL,
  `caption` text,
  `licensedContent` text,
  `projection` varchar(250) DEFAULT NULL,
  `viewCount` int(11) DEFAULT NULL,
  `likeCount` int(11) DEFAULT NULL,
  `dislikeCount` int(11) DEFAULT NULL,
  `favoriteCount` int(11) DEFAULT NULL,
  `commentCount` int(11) DEFAULT NULL,
  `description` text,
  `backlink_status` enum('0','2','1') NOT NULL DEFAULT '0' COMMENT '0 = incomplete, 2 = submitted, 1 = completed',
  `rank_status` enum('0','1') NOT NULL DEFAULT '0',
  `backlink_count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `fb_msg_manager` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `page_table_id` int(12) NOT NULL,
  `from_user` varchar(255) DEFAULT NULL,
  `from_user_id` varchar(255) DEFAULT NULL,
  `last_snippet` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message_count` varchar(255) DEFAULT NULL,
  `thread_id` varchar(255) NOT NULL,
  `inbox_link` text NOT NULL,
  `unread_count` varchar(255) DEFAULT NULL,
  `sync_time` datetime NOT NULL,
  `last_update_time` varchar(100) NOT NULL COMMENT 'this time in +00 UTC format, We need to convert it to the user time zone'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE `fb_msg_manager_notification_settings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `email_address` varchar(255) DEFAULT NULL,
  `time_zone` varchar(255) NOT NULL,
  `time_interval` varchar(100) DEFAULT NULL,
  `is_enabled` enum('yes','no') NOT NULL,
  `has_business_account` enum('yes','no') NOT NULL DEFAULT 'no',
  `last_email_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fb_simple_support_desk`
--

CREATE TABLE `fb_simple_support_desk` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ticket_title` text NOT NULL,
  `ticket_text` longtext NOT NULL,
  `ticket_status` enum('1','2','3') CHARACTER SET latin1 NOT NULL DEFAULT '1' COMMENT '1=> Open. 2 => Closed, 3 => Resolved',
  `display` enum('0','1') NOT NULL DEFAULT '1',
  `support_category` int(11) NOT NULL,
  `last_replied_by` int(11) NOT NULL,
  `last_replied_at` datetime NOT NULL,
  `last_action_at` datetime NOT NULL COMMENT 'close resolve reopen etc',
  `ticket_open_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fb_support_category`
--

CREATE TABLE `fb_support_category` (
  `id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `deleted` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `fb_support_category`
--

INSERT INTO `fb_support_category` (`id`, `category_name`, `user_id`, `deleted`) VALUES
(1, 'Billing', 1, '0'),
(2, 'Technical', 1, '0'),
(3, 'Query', 1, '0');

-- --------------------------------------------------------

--
-- Table structure for table `fb_support_desk_reply`
--

CREATE TABLE `fb_support_desk_reply` (
  `id` int(11) NOT NULL,
  `ticket_reply_text` longtext NOT NULL,
  `ticket_reply_time` datetime NOT NULL,
  `reply_id` int(11) NOT NULL COMMENT 'ticket_id',
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
