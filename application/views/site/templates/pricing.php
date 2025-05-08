<?php
// pricing.php (หน้ารวมแพ็กเกจทั้งหมด)

// --- Configuration ---
// ตั้งค่าสกุลเงินและข้อความต่างๆ
$currency_symbol = '฿'; // สัญลักษณ์สกุลเงิน (บาท)
$monthly_text = '/เดือน';
$yearly_text = '/ปี';
$yearly_billing_text_prefix = '(เรียกเก็บรายปี ';
$yearly_billing_text_suffix = ')';
$free_tier_text = 'ฟรี';
$contact_us_text = 'ติดต่อเรา';
$popular_badge_text = 'ยอดนิยม';
$save_badge_text_prefix = 'ประหยัด '; // เว้นวรรคท้าย
$save_badge_text_suffix = '%!'; // เครื่องหมาย % หรือ อื่นๆ

// --- Package Data ---
// ข้อมูลแพ็กเกจทั้งหมด (ควรดึงมาจากฐานข้อมูลในระบบจริง)
$all_packages = [
    [
        'id' => 'free', // ID สำหรับอ้างอิง
        'name' => 'ฟรี',
        'price_monthly' => 0,
        'price_yearly' => 0,
        'description' => 'ทดลองใช้งานฟีเจอร์พื้นฐาน เหมาะสำหรับผู้เริ่มต้น',
        'features' => [
            'chatbot_rules' => '5 Rules',
            'platforms' => '1 ช่องทาง',
            'orders' => '50 ออเดอร์/เดือน',
            'contacts' => '100 ผู้ติดต่อ',
            'users' => '1 ผู้ใช้งาน',
            'stock_management' => false,
            'crm_basic' => false,
            'broadcast' => false,
            'reports' => 'รายงานพื้นฐาน',
            'api_access' => false,
            'support' => 'ช่วยเหลือผ่านชุมชน',
        ],
        'button_text' => 'เริ่มต้นฟรี',
        'button_url' => '/signup?plan=free',
        'highlighted' => false,
    ],
    [
        'id' => 'starter',
        'name' => 'เริ่มต้น',
        'price_monthly' => 499,
        'price_yearly' => 4990, // 499 * 10 (ประหยัด 2 เดือน)
        'yearly_saving_percentage' => 17, // คำนวณ % ส่วนลด
        'description' => 'สำหรับร้านค้าขนาดเล็ก และผู้ที่ต้องการระบบจัดการพื้นฐาน',
        'features' => [
            'chatbot_rules' => '20 Rules',
            'platforms' => '1 ช่องทาง',
            'orders' => 'ไม่จำกัด',
            'contacts' => '1,000 ผู้ติดต่อ',
            'users' => '1 ผู้ใช้งาน',
            'stock_management' => '✓',
            'crm_basic' => '✓ (พื้นฐาน)',
            'broadcast' => '500 ข้อความ/เดือน',
            'reports' => 'รายงานยอดขาย',
            'api_access' => false,
            'support' => 'อีเมล & แชท',
        ],
        'button_text' => 'เลือกแพ็กเกจนี้',
        'button_url' => '/signup?plan=starter',
        'highlighted' => false,
    ],
    [
        'id' => 'business',
        'name' => 'ธุรกิจ',
        'price_monthly' => 999,
        'price_yearly' => 9990, // 999 * 10
        'yearly_saving_percentage' => 17,
        'description' => 'ครบครัน! สำหรับธุรกิจที่ต้องการเติบโต และจัดการอย่างมืออาชีพ',
        'features' => [
            'chatbot_rules' => 'ไม่จำกัด',
            'platforms' => 'สูงสุด 3 ช่องทาง',
            'orders' => 'ไม่จำกัด',
            'contacts' => '5,000 ผู้ติดต่อ',
            'users' => 'สูงสุด 3 ผู้ใช้งาน',
            'stock_management' => '✓ (ขั้นสูง)',
            'crm_basic' => '✓ (พร้อม Automation)',
            'broadcast' => 'ไม่จำกัด',
            'reports' => 'รายงานวิเคราะห์',
            'api_access' => '✓',
            'support' => 'อีเมล, แชท & โทรศัพท์',
        ],
        'button_text' => 'เลือกแพ็กเกจนี้',
        'button_url' => '/signup?plan=business',
        'highlighted' => true, // แพ็กเกจแนะนำ
    ],
    [
        'id' => 'pro',
        'name' => 'โปร',
        'price_monthly' => 1999,
        'price_yearly' => 19990, // 1999 * 10
        'yearly_saving_percentage' => 17,
        'description' => 'ฟีเจอร์ขั้นสูง สำหรับธุรกิจที่ต้องการประสิทธิภาพสูงสุด',
        'features' => [
            'chatbot_rules' => 'ไม่จำกัด',
            'platforms' => 'สูงสุด 5 ช่องทาง',
            'orders' => 'ไม่จำกัด',
            'contacts' => '10,000 ผู้ติดต่อ',
            'users' => 'สูงสุด 5 ผู้ใช้งาน',
            'stock_management' => '✓ (หลายคลัง)',
            'crm_basic' => '✓ (ขั้นสูง + Custom Fields)',
            'broadcast' => 'ไม่จำกัด + A/B Testing',
            'reports' => 'รายงานขั้นสูง + Custom',
            'api_access' => '✓ (Rate Limit สูงขึ้น)',
            'support' => 'Priority Support',
        ],
        'button_text' => 'เลือกแพ็กเกจนี้',
        'button_url' => '/signup?plan=pro',
        'highlighted' => false,
    ],
     [
        'id' => 'enterprise',
        'name' => 'Enterprise',
        'price_monthly' => $contact_us_text, // ใช้ตัวแปร
        'price_yearly' => $contact_us_text,
        'yearly_saving_percentage' => null, // ไม่มีส่วนลด
        'description' => 'โซลูชันปรับแต่งพิเศษ ตอบโจทย์องค์กรขนาดใหญ่',
        'features' => [
            'chatbot_rules' => 'ปรับแต่งได้',
            'platforms' => 'ไม่จำกัด',
            'orders' => 'ปริมาณสูง',
            'contacts' => 'ไม่จำกัด',
            'users' => 'ไม่จำกัด',
            'stock_management' => '✓ (Custom)',
            'crm_basic' => '✓ (Custom + Integration)',
            'broadcast' => '✓ (Dedicated)',
            'reports' => 'Custom Dashboard',
            'api_access' => '✓ (Custom)',
            'support' => 'SLA & Account Manager',
        ],
        'button_text' => 'ติดต่อฝ่ายขาย',
        'button_url' => '/contact-sales',
        'highlighted' => false,
    ]
];

// --- Feature List ---
// รายการฟีเจอร์ทั้งหมดสำหรับแสดงในตารางเปรียบเทียบ (Key ควรตรงกับใน $all_packages['features'])
$feature_list_details = [
    // จัดกลุ่มฟีเจอร์เพื่อให้อ่านง่าย
    'Core Features' => [ // ชื่อกลุ่ม (Optional)
        'chatbot_rules' => 'จำนวน Rules แชทบอท',
        'platforms' => 'จำนวนช่องทางเชื่อมต่อ',
        'orders' => 'การจัดการออเดอร์',
        'contacts' => 'จำนวนผู้ติดต่อ',
        'users' => 'จำนวนผู้ใช้งานระบบ',
    ],
    'Management Tools' => [
        'stock_management' => 'ระบบจัดการสต็อก',
        'crm_basic' => 'ระบบลูกค้าสัมพันธ์ (CRM)',
        'broadcast' => 'บรอดแคสต์ข้อความ',
        'reports' => 'รายงานและวิเคราะห์',
    ],
    'Advanced' => [
        'api_access' => 'การเข้าถึง API',
        'support' => 'การสนับสนุนลูกค้า',
    ]
    // เพิ่มหมวดหมู่และฟีเจอร์อื่นๆ
];

// --- Helper Function ---
// ฟังก์ชันสำหรับแสดงราคา
function display_price($price, $currency_symbol, $free_text) {
    if ($price === 0) {
        return $free_text;
    } elseif (is_numeric($price)) {
        return $currency_symbol . number_format($price);
    } else {
        return htmlspecialchars($price); // สำหรับ "ติดต่อเรา"
    }
}

// ฟังก์ชันสำหรับแสดงราคาต่อเดือน (สำหรับรายปี)
function display_yearly_price_per_month($yearly_price, $currency_symbol, $contact_text) {
     if (is_numeric($yearly_price) && $yearly_price > 0) {
         return $currency_symbol . number_format(round($yearly_price / 12));
     } elseif ($yearly_price === 0) {
         return ''; // ไม่แสดงราคาต่อเดือนสำหรับแพ็กฟรี
     }
     else {
         return ''; // ไม่แสดงสำหรับติดต่อเรา
     }
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ราคาและแพ็กเกจ - ismartai แชทบอทจัดการร้านค้า</title>
    <meta name="description" content="เปรียบเทียบแพ็กเกจและราคา ismartai เลือกแผนที่ใช่สำหรับธุรกิจออนไลน์ของคุณ เริ่มต้นฟรีได้ทันที!">
    <link rel="stylesheet" href="assets/css/style.css"> <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;500;600;700&display=swap" rel="stylesheet">
    </head>
<body>

    <?php include 'templates/header.php'; // Include your standard header ?>

    <main class="pricing-page">
        <section class="pricing-hero-section text-center bg-light">
            <div class="container" data-aos="fade-up">
                <h1 class="page-title">แพ็กเกจที่ยืดหยุ่น ตอบโจทย์ทุกขนาดธุรกิจ</h1>
                <p class="page-subtitle">
                    เริ่มต้นใช้งาน ismartai ได้ง่ายๆ ด้วยแพ็กเกจฟรี หรือเลือกแผนบริการที่เหมาะกับการเติบโตของคุณ พร้อมรับส่วนลดพิเศษเมื่อชำระรายปี
                </p>
                 <div class="billing-toggle-container main-billing-toggle" data-aos="fade-up" data-aos-delay="100">
                    <span class="billing-toggle-label monthly-label active">รายเดือน</span>
                    <label class="billing-switch">
                        <input type="checkbox" id="billingTogglePage">
                        <span class="slider round"></span>
                    </label>
                    <span class="billing-toggle-label yearly-label">
                        รายปี
                        <span class="save-badge">
                            <?php
                            // หา % ส่วนลดสูงสุด (อาจจะคำนวณจาก package แรกที่มีส่วนลด)
                            $max_saving = 0;
                            foreach ($all_packages as $pkg) {
                                if (isset($pkg['yearly_saving_percentage']) && $pkg['yearly_saving_percentage'] > $max_saving) {
                                    $max_saving = $pkg['yearly_saving_percentage'];
                                }
                            }
                            if ($max_saving > 0) {
                                echo htmlspecialchars($save_badge_text_prefix) . round($max_saving) . htmlspecialchars($save_badge_text_suffix);
                            }
                            ?>
                        </span>
                    </span>
                </div>
            </div>
        </section>

        <section class="pricing-table-section section-padding">
            <div class="container">
                <div class="pricing-table-wrapper" data-aos="fade-up" data-aos-delay="200">
                    <table class="pricing-table">
                        <thead>
                            <tr>
                                <th class="feature-header">
                                    <span style="opacity: 0;">ฟีเจอร์</span> </th>
                                <?php foreach ($all_packages as $package): ?>
                                <th class="package-header <?php echo $package['highlighted'] ? 'featured-col' : ''; ?>">
                                    <div class="package-name-full"><?php echo htmlspecialchars($package['name']); ?></div>
                                    <?php if ($package['highlighted']): ?>
                                        <div class="featured-badge-table"><?php echo $popular_badge_text; ?></div>
                                    <?php endif; ?>
                                    <div class="package-price-full">
                                        <span class="price-value monthly-price-full">
                                            <?php echo display_price($package['price_monthly'], $currency_symbol, $free_tier_text); ?>
                                        </span>
                                        <span class="price-value yearly-price-full" style="display:none;">
                                            <?php echo display_yearly_price_per_month($package['price_yearly'], $currency_symbol, $contact_us_text); ?>
                                        </span>
                                        <span class="billing-cycle-full monthly-cycle"><?php echo ($package['price_monthly'] !== 0 && is_numeric($package['price_monthly'])) ? htmlspecialchars($monthly_text) : ''; ?></span>
                                        <span class="billing-cycle-full yearly-cycle" style="display:none;"><?php echo ($package['price_yearly'] !== 0 && is_numeric($package['price_yearly'])) ? htmlspecialchars($monthly_text) : ''; ?></span>
                                    </div>
                                    <div class="yearly-info-full" style="display:none;">
                                        <?php if (is_numeric($package['price_yearly']) && $package['price_yearly'] > 0 && isset($package['yearly_saving_percentage'])): ?>
                                            <small>
                                                <?php echo $yearly_billing_text_prefix . display_price($package['price_yearly'], $currency_symbol, '') . ' ' . htmlspecialchars($yearly_text) . $yearly_billing_text_suffix; ?>
                                                <br>
                                                <span class="yearly-saving-text"><?php echo htmlspecialchars($save_badge_text_prefix) . round($package['yearly_saving_percentage']) . htmlspecialchars($save_badge_text_suffix); ?></span>
                                            </small>
                                        <?php elseif ($package['price_monthly'] === 0): // Free plan ?>
                                            <small><?php echo htmlspecialchars($package['billing_cycle_monthly']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <a href="<?php echo htmlspecialchars($package['button_url']); ?>" class="btn <?php echo $package['highlighted'] ? 'btn-primary' : 'btn-outline-primary'; ?> btn-sm btn-block-table">
                                        <?php echo htmlspecialchars($package['button_text']); ?>
                                    </a>
                                </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($feature_list_details as $group_name => $features_in_group): ?>
                                <?php if (is_string($group_name)): // Check if it's a group header ?>
                                <tr class="feature-group-header">
                                    <th colspan="<?php echo count($all_packages) + 1; ?>">
                                        <?php echo htmlspecialchars($group_name); ?>
                                    </th>
                                </tr>
                                <?php endif; ?>
                                <?php foreach ($features_in_group as $feature_key => $feature_name): ?>
                                <tr>
                                    <td class="feature-name-cell">
                                        <?php echo htmlspecialchars($feature_name); ?>
                                        </td>
                                    <?php foreach ($all_packages as $package): ?>
                                    <td class="feature-value-cell <?php echo $package['highlighted'] ? 'featured-col-bg' : ''; ?>">
                                        <?php
                                        $value = $package['features'][$feature_key] ?? false; // Get feature value or default to false
                                        if ($value === true || $value === '✓' || $value === '✓ (พื้นฐาน)' || $value === '✓ (ขั้นสูง)' || $value === '✓ (Custom)' || $value === '✓ (หลายคลัง)' || $value === '✓ (พร้อม Automation)' || $value === '✓ (ขั้นสูง + Custom Fields)' || $value === '✓ (Dedicated)' || $value === '✓ (Custom Dashboard)' || $value === '✓ (Rate Limit สูงขึ้น)' || $value === '✓ (Custom + Integration)') {
                                            // แสดงเครื่องหมายถูก และอาจมีข้อความเพิ่มเติม
                                            echo '<svg class="check-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 6L9 17L4 12" stroke="var(--primary-color)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
                                            if (strpos($value, '(') !== false) { // ถ้ามีข้อความในวงเล็บ
                                                echo '<span class="feature-detail">' . htmlspecialchars(preg_replace('/^✓\s*\((.*)\)$/', '$1', $value)) . '</span>';
                                            }
                                        } elseif ($value === false) {
                                            echo '<span class="cross-icon">&ndash;</span>';
                                        } else {
                                            // แสดงข้อความตามที่กำหนด (เช่น '5 Rules', '1 ช่องทาง')
                                            echo htmlspecialchars($value);
                                        }
                                        ?>
                                    </td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tbody>
                         <tfoot>
                            <tr>
                                <td></td>
                                <?php foreach ($all_packages as $package): ?>
                                <td class="<?php echo $package['highlighted'] ? 'featured-col-bg' : ''; ?>">
                                    <a href="<?php echo htmlspecialchars($package['button_url']); ?>" class="btn <?php echo $package['highlighted'] ? 'btn-primary' : 'btn-outline-primary'; ?> btn-sm btn-block-table">
                                        <?php echo htmlspecialchars($package['button_text']); ?>
                                    </a>
                                </td>
                                <?php endforeach; ?>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="pricing-faq-link text-center" data-aos="fade-up">
                    <p>มีคำถามเพิ่มเติมเกี่ยวกับแพ็กเกจและการใช้งาน? <a href="/faq" class="link-primary">ดูคำถามที่พบบ่อย</a> หรือ <a href="/contact" class="link-primary">ติดต่อทีมงาน</a></p>
                </div>
            </div>
        </section>

        </main>

    <?php include 'templates/footer.php'; // Include your standard footer ?>

    <script src="assets/js/main.js"></script> <script>
        // Ensure this runs after the main script or combine them
        document.addEventListener('DOMContentLoaded', function () {
            const billingTogglePage = document.getElementById('billingTogglePage');
            const monthlyPricesFull = document.querySelectorAll('.pricing-page .monthly-price-full');
            const yearlyPricesFull = document.querySelectorAll('.pricing-page .yearly-price-full');
            const monthlyCyclesFull = document.querySelectorAll('.pricing-page .monthly-cycle');
            const yearlyCyclesFull = document.querySelectorAll('.pricing-page .yearly-cycle');
            const yearlyInfoFull = document.querySelectorAll('.pricing-page .yearly-info-full');
            const monthlyLabel = document.querySelector('.main-billing-toggle .monthly-label');
            const yearlyLabel = document.querySelector('.main-billing-toggle .yearly-label');

            // Function to update display based on toggle state
            function updatePricingDisplayPage(isYearly) {
                monthlyPricesFull.forEach(price => price.style.display = isYearly ? 'none' : 'block');
                yearlyPricesFull.forEach(price => price.style.display = isYearly ? 'block' : 'none');
                monthlyCyclesFull.forEach(cycle => cycle.style.display = isYearly ? 'none' : 'inline'); // Use inline or block as appropriate
                yearlyCyclesFull.forEach(cycle => cycle.style.display = isYearly ? 'inline' : 'none');
                yearlyInfoFull.forEach(info => info.style.display = isYearly ? 'block' : 'none');

                // Update active label style
                if (monthlyLabel && yearlyLabel) {
                    if (isYearly) {
                        monthlyLabel.classList.remove('active');
                        yearlyLabel.classList.add('active');
                    } else {
                        monthlyLabel.classList.add('active');
                        yearlyLabel.classList.remove('active');
                    }
                }
            }

            if (billingTogglePage) {
                billingTogglePage.addEventListener('change', function()