<?php
// ปรับปรุงข้อมูลฟีเจอร์ให้ชัดเจนขึ้น

$features = [
    [ 'icon' => 'feature-auto-reply.svg', 'title' => 'ตอบแชทลูกค้า 24 ชม.', 'desc' => 'ตั้งค่าตอบกลับอัตโนมัติ ถาม-ตอบ สินค้า แจ้งโปรโมชั่น ไม่พลาดทุกโอกาสขาย', 'url' => '/features/auto-reply' ],
    [ 'icon' => 'feature-order.svg',     'title' => 'รับออเดอร์ & แจ้งโอน', 'desc' => 'สรุปยอดอัตโนมัติ แจ้งเลขบัญชี ตรวจสอบสลิป ลดขั้นตอน ลดความผิดพลาด', 'url' => '/features/orders' ], // เปลี่ยน icon และ url
    [ 'icon' => 'feature-inventory.svg', 'title' => 'จัดการสต็อก แม่นยำ', 'desc' => 'ตัดสต็อกอัตโนมัติเมื่อขายได้ แจ้งเตือนเมื่อสต็อกใกล้หมด ไม่ต้องกลัวของหมด', 'url' => '/features/inventory' ],
    [ 'icon' => 'feature-crm.svg',      'title' => 'ดูแลลูกค้าสัมพันธ์ (CRM)', 'desc' => 'เก็บข้อมูลลูกค้า แบ่งกลุ่มลูกค้า บรอดแคสต์โปรโมชั่นเฉพาะกลุ่ม เพิ่มยอดซื้อซ้ำ', 'url' => '/features/crm' ], // เปลี่ยน icon และ url
];
?>
<section id="features" class="features-section section-padding">
    <div class="container">
        <h2 class="section-title text-center">ismartai ทำอะไรให้คุณได้บ้าง?</h2>
        <div class="features-grid">
            <?php foreach ($features as $feature): ?>
            <div class="feature-card card" data-aos="fade-up">
                <div class="feature-icon-wrapper bg-primary-light">
                     <img src="assets/icons/<?php echo htmlspecialchars($feature['icon']); ?>" alt="" class="feature-icon icon-primary">
                </div>
                <h3 class="feature-title"><?php echo htmlspecialchars($feature['title']); ?></h3>
                <p class="feature-description text-secondary"><?php echo htmlspecialchars($feature['desc']); ?></p>
                <?php if (!empty($feature['url'])): ?>
                <a href="<?php echo htmlspecialchars($feature['url']); ?>" class="learn-more-link link-primary">ดูรายละเอียด &rarr;</a>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>