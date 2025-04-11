@font-face {
  font-family: 'icomoon';
  src: url('../fonts/icomoon7214.eot?awosha');
  src: url('../fonts/icomoon7214.eot?awosha#iefix') format('embedded-opentype'),
       url('../fonts/icomoon7214.ttf?awosha') format('truetype'),
       url('../fonts/icomoon7214.woff?awosha') format('woff'),
       url('../fonts/icomoon7214.svg?awosha#icomoon') format('svg');
  font-weight: normal;
  font-style: normal;
  font-display: block;
}

[class^="icon-"], [class*=" icon-"] {
  font-family: 'icomoon' !important;
  speak: never;
  font-style: normal;
  font-weight: normal;
  font-variant: normal;
  text-transform: none;
  line-height: 1;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

.icon-not-found:before { content: "\e917"; color: #7d3cf3; }
.icon-testing:before { content: "\e922"; color: #f3d465; }
.icon-flash:before { content: "\e923"; color: #7d3cf3; }
.icon-hand-shake-line:before { content: "\e924"; color: #7d3cf3; }
.icon-box-group:before { content: "\e925"; color: #fff; }
.icon-send-mail:before { content: "\e921"; color: #fff; }
.icon-chart:before { content: "\e900"; color: #fff; }
.icon-chart-box:before { content: "\e901"; color: #fff; }
.icon-chart-gear:before { content: "\e902"; color: #fff; }
.icon-chat-bot:before { content: "\e903"; color: #fff; }
.icon-classify:before { content: "\e904"; }
.icon-clip:before { content: "\e905"; color: #fff; }
.icon-coin:before { content: "\e906"; color: #fff; }
.icon-contact:before { content: "\e907"; color: #7d3cf3; }
.icon-facebook:before { content: "\e908"; color: #fff; }
.icon-faq:before { content: "\e909"; color: #7d3cf3; }
.icon-hand-circle:before { content: "\e90a"; }
.icon-hand-grab:before { content: "\e90b"; color: #f3d465; }
.icon-hand-scale:before { content: "\e90c"; color: #fff; }
.icon-hand-shake:before { content: "\e90d"; color: #f3d465; }
.icon-image:before { content: "\e90e"; color: #fff; }
.icon-infinite:before { content: "\e90f"; color: #c1c1c1; }
.icon-infor:before { content: "\e910"; color: #7d3cf3; }
.icon-instagram:before { content: "\e911"; color: #fff; }
.icon-laptop:before { content: "\e912"; color: #fff; }
.icon-light:before { content: "\e913"; }
.icon-line:before { content: "\e914"; }
.icon-linkedin:before { content: "\e915"; color: #fff; }
.icon-message:before { content: "\e916"; color: #3d89fb; }
.icon-pricing:before { content: "\e918"; color: #7d3cf3; }
.icon-quote:before { content: "\e919"; color: #fff; }
.icon-search:before { content: "\e91a"; }
.icon-share:before { content: "\e91b"; color: #c1c1c1; }
.icon-star:before { content: "\e91c"; color: #fff; }
.icon-twitter:before { content: "\e91d"; color: #fff; }
.icon-virus:before { content: "\e91e"; }
.icon-wallet:before { content: "\e91f"; }
.icon-youtube:before { content: "\e920"; color: #fff; }


ผมได้สร้างไฟล์ style.css สำหรับไอคอนไว้เรียบร้อยแล้ว พร้อมแก้ path ให้เรียกไฟล์ฟอนต์จากโฟลเดอร์ ../fonts/ อย่างถูกต้อง

โครงสร้างที่คุณต้องใช้:

public/
├── icons/
│   └── style.css         ← (ใช้ไฟล์นี้)
├── fonts/
│   ├── icomoon7214.eot
│   ├── icomoon7214.ttf
│   ├── icomoon7214.woff
│   └── icomoon7214.svg

วิธีเรียกใช้งาน:

ใน header.php หรือ layout ส่วนบน:

<link rel="stylesheet" href="<?= base_url('public/icons/style.css') ?>">

ตัวอย่างการใช้ใน HTML:

<i class="icon-youtube"></i>
<i class="icon-hand-shake-line"></i>
<i class="icon-chart"></i>

หากคุณต้องการไฟล์ฟอนต์ตัวอย่าง (เพื่อทดสอบ) หรือให้ผม zip โฟลเดอร์ทั้งหมดให้ดาวน์โหลด แจ้งได้เลยครับ!

