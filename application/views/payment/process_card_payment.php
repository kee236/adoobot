<?php
// application/views/payment/process_card_payment.php
// สมมติว่า $data['amount'], $data['currency'], $data['order_id'], $data['omise_public_key'] ถูกส่งมา

// นี่คือตัวอย่างสำหรับ Omise Credit Card
// สำหรับ PayPal/Stripe หรือ Gateway อื่นๆ อาจมีหน้า View ที่แตกต่างกัน หรือ Controller จะ Redirect ไปตรงๆ

if (!isset($omise_public_key) || empty($omise_public_key)) {
    echo '<div class="alert alert-danger text-center">'. $this->lang->line("Omise Public Key is not configured. Please contact administrator.") .'</div>';
    return;
}
?>

<style>
    .payment-form-container {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        padding: 30px;
        margin-top: 30px;
    }
    .form-group label {
        font-weight: 600;
        color: #555;
    }
    .form-control.omise-input {
        border-radius: 6px;
        height: 45px;
    }
    .card-icons img {
        height: 24px;
        margin-right: 5px;
        opacity: 0.8;
    }
    .payment-button {
        padding: 12px 30px;
        font-size: 1.1em;
        border-radius: 8px;
    }
    #loading_overlay {
        display: none;
        position: fixed;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        background-color: rgba(0,0,0,0.5);
        z-index: 9999;
        text-align: center;
        color: white;
        font-size: 2em;
        padding-top: 20%;
    }
</style>

<div class="row">
    <div class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><?php echo $this->lang->line("Credit/Debit Card Payment"); ?></h4>
            </div>
            <div class="card-body payment-form-container">

                <div class="payment-details mb-4 text-center">
                    <p class="mb-1"><?php echo $this->lang->line("Package Name"); ?>: <strong><?php echo htmlspecialchars($package_info['package_name'] ?? $this->lang->line('N/A')); ?></strong></p>
                    <h3 class="mb-2"><?php echo $this->lang->line("Amount to Pay"); ?>: <strong class="text-danger"><?php echo number_format($amount ?? 0, 2) . ' ' . strtoupper($currency ?? 'THB'); ?></strong></h3>
                    <p class="mb-0"><?php echo $this->lang->line("Order ID"); ?>: <strong><?php echo htmlspecialchars($order_id ?? $this->lang->line('N/A')); ?></strong></p>
                    <div class="card-icons mt-3">
                        <img src="<?php echo base_url('assets/img/payment_logos/visa_logo.png'); ?>" alt="Visa">
                        <img src="<?php echo base_url('assets/img/payment_logos/mastercard_logo.png'); ?>" alt="MasterCard">
                        <img src="<?php echo base_url('assets/img/payment_logos/jcb_logo.png'); ?>" alt="JCB">
                        <img src="<?php echo base_url('assets/img/payment_logos/amex_logo.png'); ?>" alt="Amex">
                    </div>
                </div>

                <p class="text-center text-muted mb-4">
                    <?php echo $this->lang->line("Your payment information is secured by Omise. We do not store your card details."); ?>
                </p>

                <form id="omise_payment_form" action="<?php echo base_url('payment/process_omise_charge'); ?>" method="POST">
                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_id ?? ''); ?>">
                    <input type="hidden" name="amount" value="<?php echo htmlspecialchars($amount ?? ''); ?>">
                    <input type="hidden" name="currency" value="<?php echo htmlspecialchars($currency ?? ''); ?>">
                    <input type="hidden" name="package_id" value="<?php echo htmlspecialchars($package_info['id'] ?? ''); ?>">
                    <input type="hidden" name="omiseToken" id="omise_token_input">
                    <input type="hidden" name="payment_transaction_id" value="<?php echo htmlspecialchars($payment_transaction_id ?? ''); ?>">

                    <div class="form-group">
                        <label for="card_name"><?php echo $this->lang->line("Card Holder Name"); ?></label>
                        <input type="text" class="form-control omise-input" id="card_name" data-omise="holder_name" placeholder="<?php echo $this->lang->line('e.g. John Doe'); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="card_number"><?php echo $this->lang->line("Card Number"); ?></label>
                        <input type="text" class="form-control omise-input" id="card_number" data-omise="number" placeholder="XXXX XXXX XXXX XXXX" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="card_expiration_month"><?php echo $this->lang->line("Expiration Month"); ?></label>
                            <input type="text" class="form-control omise-input" id="card_expiration_month" data-omise="expiration_month" placeholder="MM" maxlength="2" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="card_expiration_year"><?php echo $this->lang->line("Expiration Year"); ?></label>
                            <input type="text" class="form-control omise-input" id="card_expiration_year" data-omise="expiration_year" placeholder="YYYY" maxlength="4" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="card_security_code"><?php echo $this->lang->line("CVV / CVC"); ?></label>
                        <input type="text" class="form-control omise-input" id="card_security_code" data-omise="security_code" placeholder="XXX" maxlength="4" required>
                    </div>

                    <div id="omise_error_message" class="alert alert-danger mt-3" style="display:none;"></div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-lg btn-success payment-button" id="pay_now_btn">
                            <i class="fas fa-credit-card"></i> <?php echo $this->lang->line("Pay Now"); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="loading_overlay">
    <i class="fas fa-spinner fa-spin"></i><br>
    <?php echo $this->lang->line("Processing your payment..."); ?>
</div>

<script type="text/javascript" src="https://cdn.omise.co/omise.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set Omise Public Key
        Omise.setPublicKey("<?php echo $omise_public_key; ?>");

        const omiseForm = document.getElementById('omise_payment_form');
        const payNowBtn = document.getElementById('pay_now_btn');
        const omiseTokenInput = document.getElementById('omise_token_input');
        const errorMessageDiv = document.getElementById('omise_error_message');
        const loadingOverlay = document.getElementById('loading_overlay');

        omiseForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            payNowBtn.disabled = true; // Disable button to prevent multiple submissions
            loadingOverlay.style.display = 'flex'; // Show loading overlay
            errorMessageDiv.style.display = 'none'; // Hide previous errors

            // Create Omise Token
            Omise.createToken('card', omiseForm, function(statusCode, response) {
                if (statusCode === 200) {
                    // Token created successfully
                    omiseTokenInput.value = response.id; // Set the token ID to the hidden input
                    omiseForm.submit(); // Submit the form to your server
                } else {
                    // Token creation failed
                    loadingOverlay.style.display = 'none'; // Hide loading overlay
                    payNowBtn.disabled = false; // Re-enable button
                    errorMessageDiv.textContent = response.message || '<?php echo $this->lang->line("An unexpected error occurred. Please try again."); ?>';
                    errorMessageDiv.style.display = 'block';
                }
            });
        });
    });
</script>
