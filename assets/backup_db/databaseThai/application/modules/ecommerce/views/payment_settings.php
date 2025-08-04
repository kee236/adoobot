<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-credit-card"></i> การตั้งค่าระบบชำระเงิน</h4>
            </div>
            <div class="card-body">
                <form action="<?php echo base_url('ecommerce/save_payment_settings'); ?>" method="post">
                    
                    <div class="form-group row">
                        <label for="manual_payment_enabled" class="col-sm-3 col-form-label">
                            การโอนเงิน / PromptPay
                        </label>
                        <div class="col-sm-9">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="manual_payment_enabled" name="manual_payment_enabled" value="1" <?php echo ($payment_config['manual_enabled'] ?? '0' == '1') ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="manual_payment_enabled">เปิดใช้งาน</label>
                            </div>
                            <small class="form-text text-muted">สำหรับลูกค้าที่ต้องการโอนเงินผ่านบัญชีธนาคาร หรือ PromptPay และแนบสลิป</small>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="omise_enabled" class="col-sm-3 col-form-label">
                            Omise
                        </label>
                        <div class="col-sm-9">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="omise_enabled" name="omise_enabled" value="1" <?php echo ($payment_config['omise_enabled'] ?? '0' == '1') ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="omise_enabled">เปิดใช้งาน</label>
                            </div>
                        </div>
                    </div>
                    <div id="omise_settings" class="pl-5" style="display: <?php echo ($payment_config['omise_enabled'] ?? '0' == '1') ? 'block' : 'none'; ?>;">
                        <div class="form-group row">
                            <label for="omise_pkey" class="col-sm-3 col-form-label">Public Key</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="omise_pkey" name="omise_pkey" value="<?php echo $payment_config['omise_pkey'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="omise_skey" class="col-sm-3 col-form-label">Secret Key</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="omise_skey" name="omise_skey" value="<?php echo $payment_config['omise_skey'] ?? ''; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="stripe_enabled" class="col-sm-3 col-form-label">
                            Stripe
                        </label>
                        <div class="col-sm-9">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="stripe_enabled" name="stripe_enabled" value="1" <?php echo ($payment_config['stripe_enabled'] ?? '0' == '1') ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="stripe_enabled">เปิดใช้งาน</label>
                            </div>
                        </div>
                    </div>
                    <div id="stripe_settings" class="pl-5" style="display: <?php echo ($payment_config['stripe_enabled'] ?? '0' == '1') ? 'block' : 'none'; ?>;">
                        <div class="form-group row">
                            <label for="stripe_pkey" class="col-sm-3 col-form-label">Publishable Key</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="stripe_pkey" name="stripe_pkey" value="<?php echo $payment_config['stripe_pkey'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="stripe_skey" class="col-sm-3 col-form-label">Secret Key</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="stripe_skey" name="stripe_skey" value="<?php echo $payment_config['stripe_skey'] ?? ''; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group row mt-4">
                        <div class="col-sm-12 text-right">
                            <button type="submit" class="btn btn-primary">บันทึกการตั้งค่า</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $('#omise_enabled').on('change', function() {
        if ($(this).is(':checked')) {
            $('#omise_settings').slideDown();
        } else {
            $('#omise_settings').slideUp();
        }
    });
    $('#stripe_enabled').on('change', function() {
        if ($(this).is(':checked')) {
            $('#stripe_settings').slideDown();
        } else {
            $('#stripe_settings').slideUp();
        }
    });
</script>
