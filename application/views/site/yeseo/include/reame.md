Okayครับ ยินดีช่วยปรับปรุง Footer ของคุณให้มีการแสดงภาษาและโลโก้เพิ่มเติมนะครับ
จากโค้ด Footer ที่คุณให้มา ผมจะปรับปรุงในส่วนของการแสดงภาษาและเพิ่มพื้นที่สำหรับแสดงโลโก้ โดยมีแนวทางดังนี้ครับ:
1. ปรับปรุงการแสดงภาษา:
 * แทนที่จะใช้ <select> ธรรมดา จะเปลี่ยนเป็น List ที่มีรูปธงชาติและชื่อภาษา เพื่อให้ดูสวยงามและเป็นมิตรกับผู้ใช้มากขึ้น
 * เพิ่มภาษาตามที่คุณต้องการ (ไทย, มาเลเซีย, เวียดนาม, กัมพูชา, ลาว, อินโดนีเซีย, อังกฤษ)
2. เพิ่มพื้นที่แสดงโลโก้:
 * เพิ่ม <div> สำหรับแสดงโลโก้ของแต่ละประเทศ
 * กำหนดให้แสดงโลโก้ตามภาษาที่เลือก
นี่คือโค้ด HTML ที่ปรับปรุงแล้ว:
<footer id="footer2-2" class="p-y footer f2 bg-navy">
    <div class="container">
        <div class="row text-white">
            <div class="col-sm-3 col-xs-12">
                <p><?php echo $this->lang->line("Copyright"); ?> &copy; <?php echo date("Y"); ?> <?php echo $this->config->item("product_short_name"); ?>.</p>
            </div>
            <div class="col-sm-3 col-xs-12 text-center">
                <ul class="footer-social" dir="<?php echo $lang['RTL']; ?>">
                    <?php if($this->config->item('facebook') != ''): ?>
                        <li><a href="<?php echo $this->config->item('facebook'); ?>" target="_blank" class="inverse"><i class="fa fa-facebook-square"></i></a></li>
                    <?php endif; ?>
                    <?php if($this->config->item('twitter') != ''): ?>
                        <li><a href="<?php echo $this->config->item('twitter'); ?>" target="_blank" class="inverse"><i class="fa fa-twitter-square"></i></a></li>
                    <?php endif; ?>
                    <?php if($this->config->item('youtube') != ''): ?>
                        <li><a href="<?php echo $this->config->item('youtube'); ?>" target="_blank" class="inverse"><i class="fa fa-youtube-square"></i></a></li>
                    <?php endif; ?>
                    <?php if($this->config->item('linkedin') != ''): ?>
                        <li><a href="<?php echo $this->config->item('linkedin'); ?>" target="_blank" class="inverse"><i class="fa fa-linkedin-square"></i></a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="col-sm-5 col-xs-12" dir="<?php echo $lang['RTL']; ?>">
                <ul class="footer-links">
                    <li><a href="<?php echo base_url('home/privacy_policy'); ?>" title="" class="edit inverse"><?php echo $lang['PRIVACY']; ?></a></li>
                    <li><a href="<?php echo base_url('home/terms_use'); ?>" title="" class="edit inverse"><?php echo $lang['TERMS']; ?></a></li>
                    <li><a href="<?php echo base_url('home/gdpr'); ?>" title="" class="edit inverse"><?php echo $lang['GDPR']; ?></a></li>
                </ul>
            </div>
            <div class="col-sm-1 col-xs-12">
                <div class="language-selector" dir="<?php echo $lang['RTL']; ?>">
                    <div class="current-language">
                        <?php
                            $current_lang = $this->session->userdata('language');
                            if (empty($current_lang)) {
                                $current_lang = 'en'; // Default language
                            }
                            $flag_path = base_url('assets/image/flag/' . $current_lang . '.svg');
                            echo '<img src="' . $flag_path . '" alt="' . $current_lang . '"> ';
                            echo $this->lang->line('LANGUAGE');
                        ?>
                    </div>
                    <ul class="language-list">
                        <li data-lang="en"><img src="<?php echo base_url('assets/image/flag/english.svg'); ?>" alt="English"> English</li>
                        <li data-lang="th"><img src="<?php echo base_url('assets/image/flag/thailand.svg'); ?>" alt="ไทย"> ไทย</li>
                        <li data-lang="ms"><img src="<?php echo base_url('assets/image/flag/malaysia.svg'); ?>" alt="Bahasa Melayu"> Bahasa Melayu</li>
                        <li data-lang="vi"><img src="<?php echo base_url('assets/image/flag/vietnam.svg'); ?>" alt="Tiếng Việt"> Tiếng Việt</li>
                        <li data-lang="km"><img src="<?php echo base_url('assets/image/flag/cambodia.svg'); ?>" alt="ភាសាខ្មែរ"> ភាសាខ្មែរ</li>
                        <li data-lang="lo"><img src="<?php echo base_url('assets/image/flag/laos.svg'); ?>" alt="ພາສາລາວ"> ພາສາລາວ</li>
                        <li data-lang="id"><img src="<?php echo base_url('assets/image/flag/indonesia.svg'); ?>" alt="Bahasa Indonesia"> Bahasa Indonesia</li>
                    </ul>
                </div>
            </div>
            <div class="col-sm-2 col-xs-12">
                <div class="language-logo" dir="<?php echo $lang['RTL']; ?>">
                    <?php
                        //  Path to the logo for the selected language
                        $logo_path = base_url('assets/image/logo/' . $current_lang . '.png'); 
                        //  Display the logo
                        echo '<img src="' . $logo_path . '" alt="Logo">';
                    ?>
                </div>
            </div>
        </div>
    </div>
</footer>
</div>

สิ่งที่เปลี่ยนแปลงและอธิบายเพิ่มเติม:
 * <div> สำหรับแสดงภาษา:
   * <div class="language-selector">:  เป็น Container หลักสำหรับแสดงตัวเลือกภาษา
   * <div class="current-language">:  แสดงภาษาที่เลือกอยู่ในปัจจุบัน (พร้อมธงชาติ)
   * <ul class="language-list">:  แสดงรายการภาษาที่สามารถเลือกได้ (พร้อมธงชาติ)
   * <li>:  แต่ละภาษาในรายการ มี data-lang attribute เพื่อเก็บรหัสภาษา (เช่น en, th, ms)
   * <img>: แสดงรูปธงชาติของแต่ละภาษา
 * <div> สำหรับแสดงโลโก้:
   * <div class="language-logo">: เป็น Container สำหรับแสดงโลโก้
   * <img>: แสดงโลโก้ โดย src จะถูกกำหนดตามภาษาที่เลือก
 * PHP Code:
   * ผมใช้ PHP เล็กน้อยเพื่อ:
     * ดึงภาษาที่เลือกปัจจุบันจาก Session ($this->session->userdata('language'))
     * กำหนดภาษา Default เป็น 'en' หากไม่มีภาษาถูกเลือก
     * สร้าง Path ไปยังไฟล์ธงชาติและโลโก้แบบ Dynamic
     * แสดงธงชาติและชื่อภาษาปัจจุบัน
     * แสดงโลโก้ตามภาษาที่เลือก
 * CSS (เพิ่มเติม):
   * คุณจะต้องใช้ CSS เพิ่มเติมเพื่อจัดรูปแบบการแสดงผลให้สวยงาม
   * ตัวอย่าง CSS:
.language-selector {
    position: relative;
    cursor: pointer;
}

.language-selector .current-language {
    padding: 5px;
    border: 1px solid #fff;
    border-radius: 5px;
    display: flex;
    align-items: center;
}

.language-selector .language-list {
    position: absolute;
    top: 100%;
    left: 0;
    background-color: #fff;
    color: #333;
    list-style: none;
    padding: 0;
    margin: 0;
    width: 150px; /* Adjust width as needed */
    border: 1px solid #ccc;
    border-top: none;
    border-radius: 0 0 5px 5px;
    display: none; /* Hide initially */
}

.language-selector:hover .language-list {
    display: block; /* Show on hover */
}

.language-selector .language-list li {
    padding: 5px;
    display: flex;
    align-items: center;
}

.language-selector .language-list li:hover {
    background-color: #f0f0f0;
}

.language-selector img,
.language-logo img {
    width: 24px; /* Adjust flag/logo size */
    height: auto;
    margin-right: 5px;
    vertical-align: middle;
}

.language-logo {
    text-align: center;
}

คำแนะนำเพิ่มเติม:
 * ไฟล์ SVG และ PNG:
   * เตรียมไฟล์ SVG สำหรับธงชาติ และไฟล์ PNG สำหรับโลโก้ ของทุกภาษา
   * จัดเก็บไฟล์เหล่านี้ใน Directory ที่เหมาะสม (เช่น assets/image/flag/, assets/image/logo/)
   * ตั้งชื่อไฟล์ให้สื่อความหมาย (เช่น en.svg, th.png)
 * JavaScript (จำเป็น):
   * คุณต้องใช้ JavaScript เพื่อจัดการการเปลี่ยนภาษาเมื่อผู้ใช้คลิกเลือกภาษา
   * ตัวอย่าง JavaScript (ใช้ jQuery):
$(document).ready(function() {
    $('.language-list li').click(function() {
        var lang = $(this).data('lang');
        //  Send language change request (e.g., using AJAX)
        $.post('<?php echo base_url("language/change"); ?>', { language: lang }, function(response) {
            location.reload(); // Reload the page to apply changes
        });
    });
});

สรุปขั้นตอนการทำงาน:
 * ผู้ใช้คลิกเลือกภาษาจากรายการใน Footer
 * JavaScript จะดึงค่า data-lang ของภาษาที่เลือก
 * JavaScript จะส่งคำขอไปยัง Controller ของคุณ (เช่น language/change) เพื่อเปลี่ยนภาษา
 * Controller ทำการเปลี่ยนภาษา (เช่น โดยการตั้งค่า Session)
 * หน้าเว็บถูก Reload เพื่อแสดงผลด้วยภาษาใหม่
หวังว่าการปรับปรุงนี้จะช่วยให้ Footer ของคุณดูดีและใช้งานได้สะดวกยิ่งขึ้นนะครับ หากมีคำถามเพิ่มเติม บอกได้เลยครับ!
