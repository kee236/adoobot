<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <?php
        // ตั้งค่า Favicon ของเว็บไซต์
    ?>
    <link rel="icon" href="<?php echo base_url(); ?>assets/img/favicon.png">
    <?php
        // กำหนด Page Title ที่จะแสดงบน Browser tab
    ?>
    <title><?php echo $page_title; ?></title>
    <?php
        // โหลด Font Awesome สำหรับไอคอน (แบบ Offline)
    ?>
    <script src="<?php echo base_url(); ?>assets/js/fontawesome.js" crossorigin="anonymous"></script>

    <?php
        // โหลด CSS และ JavaScript files สำหรับ Flow Builder
        // ใช้ preload เพื่อให้ Browser เริ่มดาวน์โหลดไฟล์เหล่านี้ล่วงหน้า
        // ไฟล์เหล่านี้มักจะถูกสร้างโดย Vue CLI หรือเครื่องมือ Build อื่นๆ
    ?>
    <link href="<?php echo base_url(); ?>plugins/flow_builder/css/app.637921f2.css" rel="preload" as="style">
    <link href="<?php echo base_url(); ?>plugins/flow_builder/css/chunk-vendors.68aff722.css" rel="preload" as="style">
    <link href="<?php echo base_url(); ?>plugins/flow_builder/js/app.3ddc0f30.js" rel="preload" as="script">
    <link href="<?php echo base_url(); ?>plugins/flow_builder/js/chunk-vendors.0d7b1443.js" rel="preload" as="script">
    <?php
        // โหลด CSS files อีกครั้ง (แต่คราวนี้เป็น rel="stylesheet" เพื่อให้ Browser นำมาใช้งาน)
    ?>
    <link href="<?php echo base_url(); ?>plugins/flow_builder/css/chunk-vendors.68aff722.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>plugins/flow_builder/css/app.637921f2.css" rel="stylesheet">

</head>

</head> <?php // มีการปิดแท็ก head ซ้ำ ควรลบอันใดอันหนึ่งออก ?>

<body>
    <?php
        // ข้อความแจ้งเตือนสำหรับผู้ใช้ที่ปิด JavaScript
        // ข้อความนี้ควรถูกแปลเป็นภาษาไทย
    ?>
    <noscript><strong>We're sorry but flow-builder doesn't work properly without JavaScript enabled. Please enable it to continue.</strong></noscript>
    <?php
        // Root element สำหรับ Vue.js Flow Builder App
    ?>
    <div id="xit-flow-builder"></div>

    <?php
        // โหลดข้อมูล JSON สำหรับ Flow Builder
        // หาก $json_data มีค่า จะนำมาใส่ในตัวแปร JavaScript ชื่อ 'data'
        // หากไม่มี จะกำหนดให้ 'data' เป็น null
        // ใช้ sanitize_json_string() เพื่อป้องกันปัญหาเกี่ยวกับ ' และ " ใน JSON string
    ?>
    <?php if ($json_data) { ?>
        <script>
            var data = '<?php echo sanitize_json_string($json_data); ?>';
        </script>
    <?php } else { ?>
        <script>
            var data = null;
        </script>
    <?php } ?>

    <script>
        // กำหนดตัวแปร JavaScript ต่างๆ ที่ใช้สำหรับ Flow Builder
        // ค่าเหล่านี้จะถูกส่งมาจาก PHP เพื่อใช้ในการกำหนดค่าเริ่มต้นหรือฟีเจอร์ต่างๆ
        var builder_id = "xitFB@0.0.1" // ID หรือ Version ของ Builder
        var team_member_addon = '<?php echo $team_member_access; ?>' // สถานะการเข้าถึง Add-on สำหรับสมาชิกในทีม
        var messenger_bot_connectivity_thirdparty_webhook = '<?php echo $messenger_bot_connectivity_thirdparty_webhook; ?>' // สถานะการเชื่อมต่อ Webhook ของ Messenger Bot กับ Third-party
        var page_table_id = '<?php echo $page_table_id; ?>' // ID ของเพจในฐานข้อมูล
        var has_action_buttons = '<?php echo $has_action_buttons; ?>' // สถานะว่ามีปุ่ม Action หรือไม่
        var builder_table_id = '<?php echo $builder_table_id; ?>' // ID ของ Flow Builder ในฐานข้อมูล
        var sequence_addon = '<?php echo $sequence_addon; ?>' // สถานะ Add-on สำหรับ Sequence
        var user_input_flow_addon = '<?php echo $user_input_flow_addon; ?>' // สถานะ Add-on สำหรับ User Input Flow
        var messenger_bot_condition = '<?php echo $messenger_bot_condition; ?>' // สถานะเงื่อนไขของ Messenger Bot
        var go_back_link = '<?php echo $go_back_link; ?>' // ลิงก์สำหรับกลับไปหน้าก่อนหน้า
        var instagram_bot_addon = '<?php echo isset($instagram_bot_addon) ? $instagram_bot_addon : '0'; ?>' // สถานะ Add-on สำหรับ Instagram Bot
        var messenger_engagement_plugin = '<?php echo isset($messenger_engagement_plugin) ? $messenger_engagement_plugin : '0'; ?>' // สถานะ Messenger Engagement Plugin
        var recurring_notification_addon = '<?php echo isset($recurring_notification_addon) ? $recurring_notification_addon : '0'; ?>' // สถานะ Add-on สำหรับ Recurring Notification
        var instagram_recurring_notification_addon = '<?php echo isset($instagram_recurring_notification_addon,) ? $instagram_recurring_notification_addon : '1'; ?>' // สถานะ Add-on สำหรับ Instagram Recurring Notification
        var page_name_or_insta_username = '<?php echo isset($page_name_or_insta_username) ? str_replace("'", "\'", $page_name_or_insta_username) : 'Page or account name'; ?>' // ชื่อเพจหรือ Username Instagram
        var fb_page_id = '<?php echo isset($fb_page_id) ? $fb_page_id : null; ?>' // ID เพจ Facebook
        var message_sent_stat = '<?php echo $message_sent_stat; ?>' // สถิติการส่งข้อความ
        var message_sent_stat_addon = '<?php echo $message_sent_stat_addon; ?>' // สถานะ Add-on สำหรับสถิติการส่งข้อความ
        var is_system_generated_action = '<?php echo $is_system_generated_action; ?>' // สถานะว่าเป็น Action ที่สร้างโดยระบบหรือไม่
        var is_openai_addon_access = '<?php echo isset($is_openai_addon_access) ? $is_openai_addon_access : '0'; ?>' // สถานะการเข้าถึง Add-on ของ OpenAI
        var google_sheet_access = '<?php echo isset($google_sheet_access) ? $google_sheet_access : '0'; ?>' // สถานะการเข้าถึง Google Sheet
        var http_api_module = '<?php echo isset($http_api_module) ? $http_api_module : '0'; ?>' // สถานะ Http API Module
        var icon_doubletap = "<?php echo base_url(); ?>/assets/images/animated/doubletap.gif" // Path ไปยัง icon doubletap

        // Object สำหรับส่งข้อมูลไปยัง Vue.js Flow Builder App
        // ค่าที่เป็น '0' หรือ '1' ที่มาจาก PHP จะถูกแปลงเป็น Integer
        window.xitFlowBuilderData = {
            "team_member_addon": parseInt(team_member_addon, 10),
            "messenger_bot_connectivity_thirdparty_webhook": parseInt(messenger_bot_connectivity_thirdparty_webhook, 10),
            "page_table_id": parseInt(page_table_id, 10),
            "base_url": '<?php echo base_url(); ?>',
            "data": JSON.parse(data), // แปลง JSON string กลับเป็น JavaScript object
            "message_sent_stat": JSON.parse(message_sent_stat),
            "builder_id": builder_id,
            "builder_table_id": parseInt(builder_table_id, 10),
            "sequence_addon": parseInt(sequence_addon, 10),
            "user_input_flow_addon": parseInt(user_input_flow_addon, 10),
            "message_sent_stat_addon": parseInt(message_sent_stat_addon, 10),
            "is_system_generated_action": parseInt(is_system_generated_action, 10),
            "messenger_bot_condition": parseInt(messenger_bot_condition, 10),
            "has_action_buttons": parseInt(has_action_buttons, 10),
            "go_back_link": go_back_link,
            "instagram_addon": parseInt(instagram_bot_addon, 10),
            "messenger_engagement_plugin": parseInt(messenger_engagement_plugin, 10),
            "page_name_or_insta_username": page_name_or_insta_username,
            "recurring_notification_addon": parseInt(recurring_notification_addon, 10),
            "instagram_recurring_notification_addon": parseInt(instagram_recurring_notification_addon, 10),
            "fb_page_id": fb_page_id,
            "is_openai_addon_access": parseInt(is_openai_addon_access, 10),
            "google_sheet_access": parseInt(google_sheet_access, 10),
            "http_api_module": parseInt(http_api_module, 10),
            "icon_doubletap": icon_doubletap,
        }
    </script>
    <?php
        // โหลดไฟล์ JavaScript ที่ทำหน้าที่ดึงไฟล์ภาษาของ Flow Builder
        // นี่เป็นจุดสำคัญที่จะนำข้อความภาษาไทยที่เราแปลไปใช้งานใน Flow Builder
    ?>
    <script src="<?php echo base_url('visual_flow_builder/language_file'); ?>" type="text/javascript"></script>

    <?php
        // โหลด JavaScript files หลักของ Flow Builder (ที่ถูก build มาแล้ว)
    ?>
    <script src="<?php echo base_url(); ?>plugins/flow_builder/js/chunk-vendors.0d7b1443.js"></script>
    <script src="<?php echo base_url(); ?>plugins/flow_builder/js/app.3ddc0f30.js"></script>

</body>

</html>
