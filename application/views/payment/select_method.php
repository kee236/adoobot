<?php
// application/views/payment/select_method.php
// สมมติว่า $data['package_info'], $data['amount'], $data['currency'], $data['order_id'] ถูกส่งมา
// และ $data['payment_gateways'] เป็น array ของ gateway ที่เปิดใช้งานและตั้งค่าแล้ว

// ตัวอย่างโครงสร้างของ $data['payment_gateways'] ที่ส่งมาจาก Controller:
/*
$data['payment_gateways'] = [
    'promptpay' => [
        'name' => 'พร้อมเพย์ (PromptPay)',
        'description' => 'ชำระเงินผ่านแอปพลิเคชันธนาคารด้วย QR Code',
        'logo' => 'promptpay_logo.png', // ชื่อไฟล์รูปภาพ
        'enabled' => true
    ],
    'omise_card' => [
        'name' => 'บัตรเครดิต/เดบิต',
        'description' => 'ชำระด้วยบัตร Visa, MasterCard, JCB ผ่าน Omise',
        'logo' => 'omise_card_logo.png',
        'enabled' => true
    ],
    'omise_mobile_banking' => [ // อาจรวมเป็น Omise QR Payment หรือแยกย่อย
        'name' => 'โมบายแบงก์กิ้ง (QR Payment)',
        'description' => 'สแกน QR Code จากแอปธนาคารชั้นนำ',
        'logo' => 'omise_qr_logo.png',
        'enabled' => true // Omise รองรับหลายธนาคารใน QR เดียว
    ],
    'paypal' => [
        'name' => 'PayPal',
        'description' => 'ชำระด้วยบัญชี PayPal',
        'logo' => 'paypal_logo.png',
        'enabled' => true
    ],
    // ... Gateway อื่นๆ
];
*/
?>

<style>
    .payment-method-card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        background-color: #fff;
        display: flex;
        align-items: center;
    }
    .payment-method-card:hover, .payment-method-card.selected {
        border-color: #007bff;
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.1);
    }
    .payment-method-card img {
        max-width: 60px;
        max-height: 40px;
        margin-right: 20px;
        object-fit: contain;
    }
    .payment-method-card h5 {
        margin-bottom: 5px;
        color: #333;
        font-weight: 600;
    }
    .payment-method-card p {
        font-size: 0.9em;
        color: #666;
        margin-bottom: 0;
    }
    .payment-details {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
        border: 1px solid #dee2e6;
    }
    .payment-details strong {
        color: #007bff;
    }
    .form-group {
        margin-bottom: 1rem;
    }
</style>

<div class="row">
    <div class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><?php echo $this->lang->line("Choose Payment Method"); ?></h4>
            </div>
            <div class="card-body">

                <div class="payment-details">
                    <p class="mb-1"><strong><?php echo $this->lang->line("Package Name"); ?>:</strong> <?php echo htmlspecialchars($package_info['package_name'] ?? $this->lang->line('N/A')); ?></p>
                    <p class="mb-1"><strong><?php echo $this->lang->line("Amount Due"); ?>:</strong> <span class="text-danger"><?php echo number_format($amount ?? 0, 2) . ' ' . strtoupper($currency ?? 'THB'); ?></span></p>
                    <p class="mb-0"><strong><?php echo $this->lang->line("Order ID"); ?>:</strong> <?php echo htmlspecialchars($order_id ?? $this->lang->line('N/A')); ?></p>
                </div>

                <p class="lead text-center mb-4"><?php echo $this->lang->line("Please select your preferred payment method below."); ?></p>

                <form id="payment_method_form" action="<?php echo base_url('payment/init_payment'); ?>" method="POST">
                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_id ?? ''); ?>">
                    <input type="hidden" name="amount" value="<?php echo htmlspecialchars($amount ?? ''); ?>">
                    <input type="hidden" name="currency" value="<?php echo htmlspecialchars($currency ?? ''); ?>">
                    <input type="hidden" name="package_id" value="<?php echo htmlspecialchars($package_info['id'] ?? ''); ?>">
                    <input type="hidden" name="selected_gateway" id="selected_gateway" value="">

                    <?php if (isset($payment_gateways) && !empty($payment_gateways)): ?>
                        <?php foreach ($payment_gateways as $key => $gateway): ?>
                            <?php if ($gateway['enabled']): ?>
                                <div class="payment-method-card" data-gateway-key="<?php echo htmlspecialchars($key); ?>">
                                    <?php if (!empty($gateway['logo'])): ?>
                                        <img src="<?php echo base_url('assets/img/payment_logos/' . htmlspecialchars($gateway['logo'])); ?>" alt="<?php echo htmlspecialchars($gateway['name']); ?>">
                                    <?php endif; ?>
                                    <div>
                                        <h5><?php echo htmlspecialchars($gateway['name']); ?></h5>
                                        <p><?php echo htmlspecialchars($gateway['description']); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-warning text-center">
                            <?php echo $this->lang->line("No payment methods are currently available. Please contact support."); ?>
                        </div>
                    <?php endif; ?>

                    <div class="text-center mt-4">
                        <button type="submit" id="proceed_to_payment_btn" class="btn btn-lg btn-success" disabled>
                            <i class="fas fa-money-check-alt"></i> <?php echo $this->lang->line("Proceed to Payment"); ?>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentCards = document.querySelectorAll('.payment-method-card');
        const selectedGatewayInput = document.getElementById('selected_gateway');
        const proceedButton = document.getElementById('proceed_to_payment_btn');

        paymentCards.forEach(card => {
            card.addEventListener('click', function() {
                // Remove 'selected' class from all cards
                paymentCards.forEach(c => c.classList.remove('selected'));

                // Add 'selected' class to the clicked card
                this.classList.add('selected');

                // Set the value of the hidden input
                selectedGatewayInput.value = this.dataset.gatewayKey;

                // Enable the button
                proceedButton.disabled = false;
            });
        });
    });
</script>

