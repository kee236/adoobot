CREATE TABLE `blogger_blog_info` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `blogger_users_info_table_id` int(11) NOT NULL,
  `blog_id` varchar(200) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `deleted` enum('0','1') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `blogger_users_info`
--

CREATE TABLE `blogger_users_info` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `access_token` text NOT NULL,
  `refresh_token` text NOT NULL,
  `name` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `picture` text NOT NULL,
  `blogger_id` varchar(200) NOT NULL,
  `blog_count` int(11) NOT NULL,
  `add_date` date NOT NULL,
  `deleted` enum('0','1') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `body` longtext NOT NULL,
  `category_id` int(11) NOT NULL,
  `tags` text,
  `status` enum('0','1','2') NOT NULL DEFAULT '0',
  `views` int(11) DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `blog_posts`
--

INSERT INTO `blog_posts` (`id`, `title`, `slug`, `keywords`, `thumbnail`, `body`, `category_id`, `tags`, `status`, `views`, `user_id`, `published_at`, `created_at`, `updated_at`) VALUES
(1, 'How To build a Facebook Messenger bot', 'how-to-build-a-facebook-messenger-bot', 'Messenger-bot,Flow Builder', '1_blog_1624077637699989.png', '<p>To build a Facebook Messenger bot, Gaxa-bot has Flow Builder, an add-on, a visual drag and drop chatbot editor. With the Flow \r\nBuilder, you can build a Facebook Messenger bot very easily by dragging \r\nand dropping elements, adding data to the elements, and connecting the \r\nelements with each other.</p>\r\n\r\n\r\n<p>Recently, a new element has been presented to the GAXA-BOT Flow \r\nBuilder, the element that is called condition. With the condition \r\nelement, you can build a condition and rules based messenger bot that \r\ncan talk to people intelligently. For example, if the user is male, the \r\nbot will call him Mr. On the other hand, if the user is Female, the bot \r\nwill call her Miss/Mrs. And if the system already has the email number \r\nof a specific user, the bot will inform the user that the system already\r\n has the email number. Contrarily, if the system doesn’t have the email \r\nnumber of a specific user, the bot will ask for the email number from \r\nthe user.</p>\r\n\r\n\r\n<p>In this article, I will show you how to build condition and rules based messenger bot on the Flow Builder.</p>\r\n\r\n\r\n<p>To build a condition and rules based messenger bot, you have to use \r\nan element called condition that will work as the entry point of the \r\nconditional conversation.</p>\r\n\r\n\r\n\r\n<p>Let’s see how to build condition and rules based messenger bot.&nbsp;</p>\r\n\r\n\r\n\r\n<p>First, go to the editor of the visual Flow Builder. Now add the \r\ntrigger element to the editor. After that, double-click on the trigger \r\nelement to add data. Instantly, a form field will appear on the left \r\nside of the editor. Now enter ‘condition’ as the keyword for the bot we \r\nare going to build. Now click on the ok button. If a user writes \r\n‘condition’ in the messenger, the bot will start.</p>\r\n\r\n\r\n\r\n<p><img src=\"https://xeroneit.net/upload/blog/image_1602_1623056870488996.png\" alt=\"Trigger element\"></p>\r\n\r\n\r\n<p>Now connect the trigger element to the Start Bot Flow. Now \r\ndouble-click on the Start Bot Flow and a form field will appear on the \r\nleft side of the editor. Give a title for the bot in the title field. \r\nFor example, I write ‘condition demo’ in the title field. Others fields \r\nare optional. You can keep them blank. Now click on the ok button.</p>\r\n\r\n\r\n<p><img src=\"https://xeroneit.net/upload/blog/image_1602_1623057016186209.png\" alt=\"Start bot Flow\"></p>\r\n\r\n\r\n\r\n<p>Now add the condition element by dragging and dropping and connect it\r\n with Start Bot Flow. Now double-click on the condition element. \r\nInstantly, a form field will appear on the left side of the editor. At \r\nthe top of the form field, you will see two radio buttons-- All Match \r\nand Any Match. And you have to select All Match or Any Match. Note that \r\nif you select All Match, all the conditions have to be true to evaluate \r\nthe expression. On the other hand, if you select Any Match, at least one\r\n condition has to be true to evaluate the expression.</p>\r\n\r\n\r\n<p>Since we will configure only one condition, you can select any of them.&nbsp;</p>\r\n\r\n\r\n<p><img src=\"https://xeroneit.net/upload/blog/image_1602_1623057300103613.png\" alt=\"All match, any match\"></p>\r\n\r\n\r\n\r\n<p>Then you will see the system field and the custom field. Both the \r\nsystem field and the custom field contain initial fields to configure \r\nconditions. Of course, by clicking on the plus sign next to the system \r\nfield and custom field, you can add more fields to configure conditions \r\non them. Of course, you can remove extra condition by clicking remove \r\nbutton. &nbsp;</p>\r\n\r\n\r\n<p><img src=\"https://xeroneit.net/upload/blog/image_1602_1623058495206128.png\" alt=\"add and remove condition\"></p>\r\n\r\n\r\n\r\n\r\n\r\n<p>Let\'s set a condition to check if the user is male or female and send messages accordingly:&nbsp;</p>\r\n\r\n\r\n\r\n<p>Click on the Variable field and a drop-down menu of the different \r\nvariables will appear. Now you have to select a variable. Likewise, you \r\nhave to select an operator from the operator field and a value from the \r\nvalue field.</p>\r\n\r\n\r\n\r\n<p>I select Gender as the variable, and Equal(=) as the operator, and \r\nMale as the value. Now I click on the ok button to insert data to the \r\ncondition element.</p>\r\n\r\n\r\n<p><img src=\"https://xeroneit.net/upload/blog/image_1602_1623058638105784.png\" alt=\"configure a condition male and female\"></p>\r\n\r\n\r\n\r\n<p>On the condition element, you will see two sockets – True and False. \r\nIf the condition evaluates to true, the message connecting to the True \r\nsocket will be sent. Contrary, if the condition evaluates to false, the \r\nmessage connecting to the False socket will be sent.&nbsp;</p>\r\n\r\n\r\n<p>Well, now add a text element and connect it to the socket called \r\nTrue. Then write a message addressing the user as Mr. the message that \r\nwill be sent to the male users.&nbsp;</p>\r\n\r\n\r\n<p>Likewise, add another text element and connect it to the False socket\r\n and write a message, addressing the user as Miss/Mrs, the message that \r\nwill be sent to the female users.&nbsp;</p>\r\n\r\n\r\n<p><img src=\"https://xeroneit.net/upload/blog/image_1602_1623058772162995.png\" alt=\"condition false or true male or female\"></p>\r\n\r\n\r\n\r\n<p>Now add a button element and connect it to both text elements. Well, \r\nwrite a button text and select new postback as the button type and click\r\n the ok button. Instantly, a new postback element connected to the \r\nbutton element will appear. Now give a title for the new postback.</p>\r\n\r\n\r\n<p><img src=\"https://xeroneit.net/upload/blog/image_1602_1623059020349561.png\" alt=\"add button\"></p>\r\n\r\n\r\n<p> Now I will set another condition to see whether the system has the \r\nemail address of the user or not. If the system has the email address of\r\n a user, the bot will inform the user that the updated information will \r\nbe sent to the email address. On the other hand, if the system doesn’t \r\nhave the email address of a specific user, the bot will ask for the \r\nemail address from the user.</p>\r\n\r\n\r\n<p>Let’s set a condition to check if the system has the email address of a specific user or not and send messages accordingly:&nbsp;</p>\r\n\r\n\r\n\r\n<p>Add condition element and connect it to the new postback element. \r\nAfter that, click on the condition element to configure a condition. If \r\nyou configure one condition, it doesn’t matter whether you select All \r\nMatch or Any Match.&nbsp;</p>\r\n\r\n\r\n\r\n\r\n\r\n<p>Like before, select Email as the variable and Has Value as the \r\noperator. And click on the ok button. That’s all. The condition is set.</p>\r\n\r\n\r\n<p><img src=\"https://xeroneit.net/upload/blog/image_1602_1623059145805182.png\" alt=\"first condition for email\"></p>\r\n\r\n\r\n\r\n<p>Now add two text element and connect them to the true and false \r\nsockets of the condition element. Now click on the text element \r\nconnecting to the true element and write a text message to inform the \r\nuser that the updated information will be emailed to the user.</p>\r\n\r\n\r\n<p><img src=\"https://xeroneit.net/upload/blog/image_1602_1623059301769282.png\" alt=\"text message for true email\"></p>\r\n\r\n\r\n <p>Then click on the other text element connecting to the&nbsp; false socket\r\n and write a message to inform the user that the system doesn’t have the\r\n email address and tell the user to click on a quick reply button to \r\nsend the email address.</p>\r\n \r\n\r\n <p><img src=\"https://xeroneit.net/upload/blog/image_1602_1623059506209552.png\" alt=\"text message for false email\"></p>\r\n \r\n\r\n <p>If the condition becomes true, the text message connecting to the \r\nTrue socket will be sent. On the other hand, if the condition becomes \r\nfalse, the text message connecting to the False socket will be sent. \r\nThat is if the system has the email address of a specific user, the bot \r\nwill inform the user that the updated information will be sent. And if \r\nthe system doesn’t have the email address of a user, the bot will ask \r\nfor the email user. </p>\r\n \r\n\r\n <p><img src=\"https://xeroneit.net/upload/blog/image_1602_1623059646161029.png\" alt=\"condition becomes false or true\"></p>\r\n\r\n\r\n\r\n<p>Now add a quick reply button and connect it to the text element \r\nconnecting to the false socket. Double-click on the quick reply element,\r\n select email as the Quick Reply type and click on the ok button. With \r\nthe reply button, the bot will collect the email address from the user. </p>\r\n\r\n\r\n\r\n<p>Now click on the save button or press Ctrl + s on the keyboard to save your bot.&nbsp;</p>\r\n\r\n\r\n\r\n<p>If everything is ok, the bot will work accordingly. That is, if the \r\nuser is male, the bot will address him as Mr and if the user is female, \r\nthe bot will address her as Mrs/Miss. Moreover, if the system has the \r\nemail address of a user, the bot will inform the user that updated \r\ninformation will be emailed to the user and if the system doesn’t have \r\nthe email address of a specific user, the bot will ask for the email \r\naddress of the user.&nbsp;</p>\r\n\r\n\r\n<p>Now let’s see how the bot does work: &nbsp;</p>\r\n\r\n\r\n<p><img src=\"https://xeroneit.net/upload/blog/image_1602_1623131537524889.PNG\" alt=\"usage of the bot\"></p>\r\n\r\n\r\n<p> I write condition in the messenger. The message matches the keyword \r\nof the condition-based bot we have made and the first condition of the \r\nbot start. The condition checks if I am male or female. It finds out \r\nthat I am male and sends me the text message that addresses me as Mr. \r\nAfter that I click on the yes button, and the second condition starts. \r\nIt checks if the system has my email address and finds out that the \r\nsystem doesn’t have my email address. So it asks me for my email number.&nbsp; </p>', 1, 'Messenger-Bot', '1', 4, 1, '2021-06-19 13:40:40', '2021-06-19 13:40:40', '2021-06-19 13:44:32');

-- --------------------------------------------------------

--
-- Table structure for table `blog_post_categories`
--

CREATE TABLE `blog_post_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `blog_post_categories`
--

INSERT INTO `blog_post_categories` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'Chat-bot', 'chat-bot', '2021-06-19 13:39:32', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `blog_post_comments`
--

CREATE TABLE `blog_post_comments` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `post_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `blog_post_tags`
--

CREATE TABLE `blog_post_tags` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
