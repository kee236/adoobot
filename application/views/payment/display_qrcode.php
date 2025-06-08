<?php
// application/views/payment/display_qrcode.php
// สมมติว่า $data['qr_data'], $data['payment_transaction_id'], $data['gateway_transaction_id'] ถูกส่งมา
// $qr_data ควรมี: 'promptpay_id' (หรือ qr_image_svg), 'amount', 'transaction_id' (ของ gateway), 'expiry_time'

// ตัวอย่างโครงสร้างของ $data['qr_data'] ที่ส่งมาจาก Controller:
/*
$data['qr_data'] = [
    'promptpay_id' => '08xxxxxxxx', // เบอร์โทรศัพท์สำหรับ PromptPay
    'amount' => 123.45,
    'transaction_id' => 'pp_tx_123456789', // ID ของ PromptPay transaction (ถ้ามี)
    'expiry_time' => 1730000000 // Unix timestamp เมื่อ QR หมดอายุ
];

// หรือถ้าเป็น Omise QR (SVG data)
$data['qr_data'] = [
    'qr_image_svg' => '<svg>...</svg>', // SVG data ของ QR Code
    'amount' => 123.45,
    'transaction_id' => 'chrg_xxxxxxxxxxxxxxx', // Omise Charge ID
    'expiry_time' => 1730000000
];
*/

// Function สำหรับแปลง QR Data ให้เป็น URL สำหรับ Google Charts API (ถ้าใช้)
// ถ้าเป็น Omise QR SVG จะใช้ $qr_data['qr_image_svg'] โดยตรง
if (!function_exists('get_qr_code_url')) {
    function get_qr_code_url($promptpay_id, $amount) {
        // ใช้ PromptPay QR Code Standard String (EMVCo)
        // นี่คือตัวอย่างพื้นฐาน คุณอาจต้องใช้ library ที่ซับซ้อนกว่านี้เพื่อสร้าง EMVCo compliant string
        // หรือใช้ API ของธนาคาร/facilitator เช่น https://promptpay.io/
        // สำหรับ Demo/ง่ายๆ: สแกนเพื่อโอนเงินเข้าเบอร์/เลข
        $data_string = "00020101021229370016A000000677010111021308xxxxxxxx" .
                       ($promptpay_id ? "01".sprintf("%02d", strlen($promptpay_id)).$promptpay_id : "") .
                       "5802TH54".sprintf("%02d", strlen(sprintf("%.2f", $amount))).sprintf("%.2f", $amount).
                       "5303764630400000"; // Placeholder for PromptPay data
        // For actual PromptPay, you would generate a proper EMVCo QR string,
        // which includes merchant data, transaction amount, etc.
        // A simple approach for POC/testing: use Google Charts API for plain text QR
        // but for actual PromptPay, it must follow specific EMVCo standard.
        // For this example, we'll assume promptpay_id & amount are all you have.
        // You MUST use a proper PromptPay QR generation library or service.
        return 'https://chart.googleapis.com/chart?cht=qr&chs=300x300&chl=' . urlencode("PromptPayID: {$promptpay_id} Amount: {$amount}");
    }
}
?>

<style>
    .qrcode-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        padding: 30px;
        margin-top: 30px;
        text-align: center;
    }
    .qrcode-container img, .qrcode-container svg {
        max-width: 280px;
        height: auto;
        border: 1px solid #eee;
        padding: 5px;
        border-radius: 5px;
        margin-bottom: 25px;
    }
    .payment-info {
        font-size: 1.1em;
        margin-bottom: 20px;
        color: #555;
    }
    .payment-info strong {
        color: #333;
        font-size: 1.2em;
    }
    .countdown {
        font-size: 1.2em;
        font-weight: bold;
        color: #d9534f; /* Bootstrap danger color */
        margin-top: 15px;
    }
    .instruction-list {
        text-align: left;
        margin-top: 25px;
        padding-left: 20px;
        color: #666;
    }
    .instruction-list li {
        margin-bottom: 8px;
    }
    .alert-info-custom {
        background-color: #e6f7ff;
        border-color: #91d5ff;
        color: #08979c;
        padding: 15px;
        border-radius: 8px;
        margin-top: 25px;
    }
</style>

<div class="row">
    <div class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><?php echo $this->lang->line("Scan to Pay"); ?></h4>
            </div>
            <div class="card-body">

                <div class="qrcode-container">
                    <?php if (isset($qr_data['qr_image_svg']) && !empty($qr_data['qr_image_svg'])): ?>
                        <?php echo $qr_data['qr_image_svg']; ?>
                    <?php elseif (isset($qr_data['promptpay_id']) && isset($qr_data['amount'])): ?>
                        <img src="<?php echo get_qr_code_url($qr_data['promptpay_id'], $qr_data['amount']); ?>" alt="QR Code" class="img-fluid">
                        <p class="text-danger mt-3"><?php echo $this->lang->line("Important: For PromptPay, please use a proper library to generate EMVCo compliant QR code for real transactions."); ?></p>
                    <?php else: ?>
                        <div class="alert alert-danger w-100"><?php echo $this->lang->line("QR Code data not available."); ?></div>
                    <?php endif; ?>

                    <div class="payment-info">
                        <p class="mb-1"><?php echo $this->lang->line("Amount to Pay"); ?>: <strong class="text-danger"><?php echo number_format($qr_data['amount'] ?? 0, 2) . ' ' . strtoupper($currency ?? 'THB'); ?></strong></p>
                        <p class="mb-1"><?php echo $this->lang->line("Order ID"); ?>: <strong><?php echo htmlspecialchars($order_id ?? $this->lang->line('N/A')); ?></strong></p>
                        <?php if (isset($qr_data['transaction_id'])): ?>
                            <p class="mb-0"><?php echo $this->lang->line("Reference No."); ?>: <strong><?php echo htmlspecialchars($qr_data['transaction_id']); ?></strong></p>
                        <?php endif; ?>
                    </div>

                    <?php if (isset($qr_data['expiry_time'])): ?>
                        <div class="countdown">
                            <?php echo $this->lang->line("QR Code will expire in"); ?>: <span id="countdown_timer"></span>
                        </div>
                    <?php endif; ?>

                    <div class="alert alert-info-custom">
                        <p class="font-weight-bold mb-2"><i class="fas fa-info-circle"></i> <?php echo $this->lang->line("How to pay using QR Code"); ?>:</p>
                        <ol class="instruction-list">
                            <li><?php echo $this->lang->line("Open your mobile banking application."); ?></li>
                            <li><?php echo $this->lang->line("Look for the 'Scan QR' or 'QR Payment' option."); ?></li>
                            <li><?php echo $this->lang->line("Scan the QR Code displayed above."); ?></li>
                            <li><?php echo $this->lang->line("Verify the amount and recipient name (if shown)."); ?></li>
                            <li><?php echo $this->lang->line("Confirm the payment."); ?></li>
                        </ol>
                    </div>

                    <div class="mt-4">
                        <p class="text-muted small"><?php echo $this->lang->line("After successful payment, the system will automatically verify your transaction. Please wait a moment."); ?></p>
                        <form action="<?php echo base_url('payment/check_manual_payment_status'); ?>" method="POST" id="manual_check_form">
                            <input type="hidden" name="payment_transaction_id" value="<?php echo htmlspecialchars($payment_transaction_id ?? ''); ?>">
                            <input type="hidden" name="gateway_transaction_id" value="<?php echo htmlspecialchars($gateway_transaction_id ?? ''); ?>">
                            <button type="submit" class="btn btn-primary btn-lg mt-3" id="i_have_paid_btn">
                                <i class="fas fa-check-circle"></i> <?php echo $this->lang->line("I have paid"); ?>
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php if (isset($qr_data['expiry_time'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const expiryTime = <?php echo (int)($qr_data['expiry_time'] ?? 0); ?>;
        const countdownTimer = document.getElementById('countdown_timer');
        const iHavePaidBtn = document.getElementById('i_have_paid_btn');

        function updateCountdown() {
            const now = Math.floor(Date.now() / 1000); // Current Unix timestamp in seconds
            const timeLeft = expiryTime - now;

            if (timeLeft <= 0) {
                countdownTimer.textContent = '<?php echo $this->lang->line("Expired!"); ?>';
                iHavePaidBtn.disabled = true; // Disable button if QR expired
                clearInterval(timerInterval);
                // อาจจะแสดงข้อความว่า QR Code หมดอายุแล้ว หรือ redirect ไปหน้าแจ้งเตือน
                // เช่น alert('QR Code has expired. Please initiate a new payment.');
                // window.location.href = '<?php echo base_url("payment/choose_payment_method/{$package_id}"); ?>'; // ตัวอย่าง
                return;
            }

            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;

            countdownTimer.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }

        updateCountdown(); // Initial call
        const timerInterval = setInterval(updateCountdown, 1000);
    });
</script>
<?php endif; ?>
