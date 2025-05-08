<?php
// templates/footer.php

?>

<footer class="site-footer bg-dark text-light">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-column footer-about">
                <a href="/" class="footer-logo-link">
                    <img src="assets/images/ismartai_logo_grey.svg" alt="ismartai logo" class="footer-logo">
                    </a>
                <p class="footer-tagline">
                    ismartai: แชทบอทอัจฉริยะ<br>คู่ใจร้านค้าออนไลน์ยุคใหม่
                </p>
                <div class="footer-social-links">
                    <a href="https://facebook.com/your_ismartai_page" target="_blank" rel="noopener noreferrer" aria-label="ismartai Facebook">
                        <img src="assets/icons/social-facebook.svg" alt="Facebook">
                        </a>
                    <a href="https://line.me/R/ti/p/%40your_line_oa" target="_blank" rel="noopener noreferrer" aria-label="ismartai LINE OA">
                         <img src="assets/icons/social-line.svg" alt="LINE">
                    </a>
                    <a href="https://instagram.com/your_ismartai_ig" target="_blank" rel="noopener noreferrer" aria-label="ismartai Instagram">
                         <img src="assets/icons/social-instagram.svg" alt="Instagram">
                    </a>
                    </div>
            </div>

            <div class="footer-column">
                <h4 class="footer-heading">เมนูหลัก</h4>
                <ul class="footer-links">
                    <li><a href="/features">ฟีเจอร์</a></li>
                    <li><a href="/pricing">ราคา</a></li>
                    <li><a href="/case-studies">ลูกค้าของเรา</a></li>
                    <li><a href="/blog">บทความ</a></li>
                    <li><a href="/contact">ติดต่อเรา</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h4 class="footer-heading">แหล่งข้อมูล</h4>
                <ul class="footer-links">
                    <li><a href="/help/guides">คู่มือเริ่มต้น</a></li>
                    <li><a href="/faq">คำถามที่พบบ่อย</a></li>
                    <li><a href="/api-docs">เอกสาร API</a></li>
                    <li><a href="/status">สถานะระบบ</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h4 class="footer-heading">เกี่ยวกับเรา</h4>
                <ul class="footer-links">
                     <li><a href="/about">เกี่ยวกับ ismartai</a></li>
                    <li><a href="/terms-of-service">ข้อตกลงการใช้งาน</a></li>
                    <li><a href="/privacy-policy">นโยบายความเป็นส่วนตัว</a></li>
                     <li><a href="/careers">ร่วมงานกับเรา</a></li>
                </ul>
            </div>

        </div> <div class="footer-bottom">
            <p>&copy; <?php echo date("Y"); ?> ismartai. สงวนลิขสิทธิ์.</p>
            </div>
    </div>
</footer>
```css
/* assets/css/style.css - เพิ่มเติมสำหรับ Footer */

/* ... (สไตล์เดิมของคุณ) ... */

/* 19. Footer */
.site-footer {
    padding-top: var(--spacing-16); /* 64px */
    padding-bottom: var(--spacing-8); /* 32px */
    /* background-color: var(--bg-dark); ถูกกำหนดใน HTML แล้ว */
    color: rgba(255, 255, 255, 0.7); /* สีเทาอ่อนสำหรับข้อความทั่วไป */
    font-size: 0.9rem; /* ลดขนาดฟอนต์เล็กน้อย */
}

.footer-grid {
    display: grid;
    /* กำหนดคอลัมน์: คอลัมน์แรกกว้างกว่า, ที่เหลือเท่ากัน */
    grid-template-columns: 1.5fr 1fr 1fr 1fr;
    gap: var(--spacing-10); /* 40px ระยะห่างระหว่างคอลัมน์ */
    margin-bottom: var(--spacing-12); /* 48px */
}

.footer-column {
    /* สไตล์สำหรับแต่ละคอลัมน์ */
}

.footer-logo-link {
    display: inline-block;
    margin-bottom: var(--spacing-4); /* 16px */
}
.footer-logo {
    height: 35px; /* ปรับขนาดโลโก้ใน Footer */
    opacity: 0.9;
}
.footer-tagline {
    font-size: 0.9rem;
    line-height: 1.6;
    color: rgba(255, 255, 255, 0.8);
    margin-bottom: var(--spacing-6); /* 24px */
}
.footer-social-links a {
    margin-right: var(--spacing-4); /* 16px */
    opacity: 0.8;
    transition: opacity 0.3s ease;
    display: inline-block;
}
.footer-social-links a:hover {
    opacity: 1;
}
.footer-social-links img {
    height: 24px;
    /* filter: brightness(0) invert(1); ถ้า SVG ไม่ใช่สีขาว */
    vertical-align: middle;
}
/* ถ้าใช้ Font Awesome */
/* .footer-social-links i {
    font-size: 1.5rem;
    color: rgba(255, 255, 255, 0.8);
    transition: color 0.3s ease;
}
.footer-social-links a:hover i {
    color: var(--text-light);
} */


.footer-heading {
    font-size: 1rem; /* 16px */
    font-weight: 600; /* SemiBold */
    color: var(--text-light);
    margin-bottom: var(--spacing-5, 1.25rem); /* 20px */
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
}
.footer-links li {
    margin-bottom: var(--spacing-3); /* 12px */
}
.footer-links a {
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    transition: color 0.3s ease;
}
.footer-links a:hover {
    color: var(--text-light);
    text-decoration: underline;
}

.footer-bottom {
    text-align: center;
    padding-top: var(--spacing-8); /* 32px */
    margin-top: var(--spacing-8); /* 32px */
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.5);
}
.footer-bottom p {
    margin-bottom: var(--spacing-1);
}
.footer-bottom a {
    color: rgba(255, 255, 255, 0.6);
}
.footer-bottom a:hover {
    color: var(--text-light);
}

/* Responsive Footer */
@media (max-width: 992px) {
    .footer-grid {
        grid-template-columns: repeat(2, 1fr); /* 2 คอลัมน์บน Tablet */
        gap: var(--spacing-8);
    }
    .footer-column.footer-about {
        grid-column: span 2; /* ให้คอลัมน์แรกเต็มความกว้าง */
        text-align: center; /* จัดกลางเนื้อหา */
    }
     .footer-social-links {
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .footer-grid {
        grid-template-columns: 1fr; /* 1 คอลัมน์บน Mobile */
        text-align: center; /* จัดกลางทั้งหมด */
    }
     .footer-column {
        margin-bottom: var(--spacing-6);
    }
    .footer-links li {
        margin-bottom: var(--spacing-2);
    }
    .footer-heading {
        margin-bottom: var(--spacing-3);
    }
}
```

**การนำไปใช้งาน:**

1.  **สร้างไฟล์ `templates/footer.php`**: นำโค้ด PHP/HTML ด้านบนไปใส่
2.  **เพิ่ม CSS**: คัดลอกส่วน CSS สำหรับ `.site-footer`, `.footer-grid`, และคลาสย่อยอื่นๆ ไปยัง `assets/css/style.css`
3.  **เพิ่มใน `index.php`**: ตรวจสอบว่ามีการ include `templates/footer.php` ในตอนท้ายสุดของไฟล์ `index.php` (ก่อนปิด `</body>`)
    ```php
    // ใน index.php
    // ... (ส่วน main และ section อื่นๆ) ...
    <?php include 'templates/footer.php'; ?>

    <script src="assets/js/main.js"></script>
    </body>
    </html>
    ```
4.  **เตรียม Assets**: ตรวจสอบว่ามีไฟล์โลโก้ (`ismartai_logo_grey.svg` หรือ `_white.svg`) และไอคอนโซเชียล (`social-*.svg`) ในโฟลเดอร์ `assets/` หรือปรับแก้ path และชื่อไฟล์ให้ถูกต้อง
5.  **ปรับแต่งลิงก์**: แก้ไข URL ใน `<a>` tags ทั้งหมดให้ตรงกับโครงสร้างหน้าเว็บของคุณ (เช่น ลิงก์ Facebook Page, LINE OA, หน้า Features, Pricing, etc.)

