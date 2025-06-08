<?php
// application/views/payment/payment_status.php
// สมมติว่า $data['status'], $data['message'], $data['transaction_info'] ถูกส่งมา
// $transaction_info จะมี: 'order_id', 'amount', 'currency', 'gateway', 'gateway_transaction_id', 'payment_date'

// กำหนดข้อมูลตามสถานะ
$status_class = '';
$icon_class = '';
$title = '';
$message_default = '';
$redirect_button_text = $this->lang->line("Go to Dashboard");
$redirect_url = base_url('home/dashboard'); // Default redirect

if ($status === 'success') {
    $status_class = 'alert-success';
    $icon_class = 'fas fa-check-circle text-success';
    $title = $this->lang->line("Payment Successful!");
    $message_default = $this->lang->line("Your payment has been successfully processed. Thank you for your purchase!");
    $redirect_button_text = $this->lang->line("Access Your Package");
    // หากต้องการ redirect ไปหน้า package_details หรือ user's current package
    // $redirect_url = base_url('member/my_package');
} elseif ($status === 'failed') {
    $status_class = 'alert-danger';
    $icon_class = 'fas fa-times-circle text-danger';
    $title = $this->lang->line("Payment Failed");
    $message_default = $this->lang->line("Your payment could not be processed. Please try again or contact support.");
    $redirect_button_text = $this->lang->line("Try Again");
    // หากต้องการ redirect ไปหน้าเลือก payment method อีกครั้ง
    // $redirect_url = base_url('payment/choose_payment_method/' . ($transaction_info['package_id'] ?? ''));
} elseif ($status === 'pending' || $status === 'waiting_for_admin_review') {
    $status_class = 'alert-warning';
    $icon_class = 'fas fa-hourglass-half text-warning';
    $title = $this->lang->line("Payment Pending");
    $message_default = $this->lang->line("Your payment is pending verification. We will notify you once it's confirmed.");
    $redirect_button_text = $this->lang->line("Go to Dashboard");
} elseif ($status === 'cancelled') {
    $status_class = 'alert-info';
    $icon_class = 'fas fa-info-circle text-info';
    $title = $this->lang->line("Payment Cancelled");
    $message_default = $this->lang->line("Your payment was cancelled. If this was a mistake, please try again.");
    $redirect_button_text = $this->lang->line("Try Again");
} else {
    // Unknown status
    $status_class = 'alert-secondary';
    $icon_class = 'fas fa-question-circle text-secondary';
    $title = $this->lang->line("Payment Status Unknown");
    $message_default = $this->lang->line("We are unable to determine the status of your payment. Please contact support.");
    $redirect_button_text = $this->lang->line("Go to Dashboard");
}

$display_message = $message ?? $message_default; // ใช้ message ที่ Controller ส่งมา หากมี
?>

<style>
    .status-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        padding: 40px;
        margin-top: 30px;
        text-align: center;
    }
    .status-icon {
        font-size: 80px;
        margin-bottom: 25px;
    }
    .status-title {
        font-size: 2.2em;
        font-weight: bold;
        margin-bottom: 15px;
        color: #333;
    }
    .status-message {
        font-size: 1.1em;
        color: #666;
        margin-bottom: 30px;
        line-height: 1.6;
    }
    .transaction-summary {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        width: 100%;
        max-width: 450px;
        margin-bottom: 30px;
    }
    .transaction-summary p {
        margin-bottom: 8px;
        font-size: 0.95em;
    }
    .transaction-summary strong {
        color: #444;
    }
    .btn-action {
        padding: 12px 30px;
        font-size: 1.1em;
        border-radius: 8px;
    }
</style>

<div class="row">
    <div class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><?php echo $this->lang->line("Payment Result"); ?></h4>
            </div>
            <div class="card-body status-container <?php echo $status_class; ?>">

                <div class="status-icon">
                    <i class="<?php echo $icon_class; ?>"></i>
                </div>

                <h2 class="status-title"><?php echo $title; ?></h2>

                <p class="status-message"><?php echo $display_message; ?></p>

                <?php if (isset($transaction_info) && !empty($transaction_info)): ?>
                    <div class="transaction-summary">
                        <h5 class="mb-3"><?php echo $this->lang->line("Order Summary"); ?></h5>
                        <p><strong><?php echo $this->lang->line("Order ID"); ?>:</strong> <?php echo htmlspecialchars($transaction_info['order_id'] ?? $this->lang->line('N/A')); ?></p>
                        <p><strong><?php echo $this->lang->line("Amount Paid"); ?>:</strong> <span class="text-danger"><?php echo number_format($transaction_info['paid_amount'] ?? $transaction_info['amount'] ?? 0, 2) . ' ' . strtoupper($transaction_info['currency'] ?? 'THB'); ?></span></p>
                        <p><strong><?php echo $this->lang->line("Payment Method"); ?>:</strong> <?php echo htmlspecialchars(ucfirst($transaction_info['gateway'] ?? $this->lang->line('N/A'))); ?></p>
                        <?php if (!empty($transaction_info['gateway_transaction_id'])): ?>
                            <p><strong><?php echo $this->lang->line("Transaction ID"); ?>:</strong> <?php echo htmlspecialchars($transaction_info['gateway_transaction_id']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($transaction_info['payment_date'])): ?>
                            <p><strong><?php echo $this->lang->line("Payment Date"); ?>:</strong> <?php echo date('d M Y H:i:s', strtotime($transaction_info['payment_date'])); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="mt-3">
                    <a href="<?php echo $redirect_url; ?>" class="btn btn-primary btn-lg btn-action">
                        <?php echo $redirect_button_text; ?> <i class="fas fa-arrow-right"></i>
                    </a>
                    <?php if ($status === 'failed' || $status === 'pending' || $status === 'waiting_for_admin_review'): ?>
                        <a href="<?php echo base_url('home/contact_us'); ?>" class="btn btn-outline-info btn-lg btn-action ml-3">
                            <i class="fas fa-headset"></i> <?php echo $this->lang->line("Contact Support"); ?>
                        </a>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</div>
