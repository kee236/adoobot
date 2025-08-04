<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-truck"></i> การตั้งค่าระบบขนส่ง</h4>
            </div>
            <div class="card-body">
                <form action="<?php echo base_url('ecommerce/save_shipping_settings'); ?>" method="post">
                    
                    <div class="form-group row">
                        <label for="kerry_enabled" class="col-sm-3 col-form-label">
                            Kerry Express
                        </label>
                        <div class="col-sm-9">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="kerry_enabled" name="kerry_enabled" value="1" <?php echo ($shipping_config['kerry_enabled'] ?? '0' == '1') ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="kerry_enabled">เปิดใช้งาน</label>
                            </div>
                        </div>
                    </div>
                    <div id="kerry_settings" class="pl-5" style="display: <?php echo ($shipping_config['kerry_enabled'] ?? '0' == '1') ? 'block' : 'none'; ?>;">
                        <div class="form-group row">
                            <label for="kerry_shop_id" class="col-sm-3 col-form-label">Shop ID</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="kerry_shop_id" name="kerry_shop_id" value="<?php echo $shipping_config['kerry_shop_id'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kerry_api_key" class="col-sm-3 col-form-label">API Key</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="kerry_api_key" name="kerry_api_key" value="<?php echo $shipping_config['kerry_api_key'] ?? ''; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group row mt-3">
                        <label for="flash_enabled" class="col-sm-3 col-form-label">
                            Flash Express
                        </label>
                        <div class="col-sm-9">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="flash_enabled" name="flash_enabled" value="1" <?php echo ($shipping_config['flash_enabled'] ?? '0' == '1') ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="flash_enabled">เปิดใช้งาน</label>
                            </div>
                        </div>
                    </div>
                    <div id="flash_settings" class="pl-5" style="display: <?php echo ($shipping_config['flash_enabled'] ?? '0' == '1') ? 'block' : 'none'; ?>;">
                        <div class="form-group row">
                            <label for="flash_partner_id" class="col-sm-3 col-form-label">Partner ID</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="flash_partner_id" name="flash_partner_id" value="<?php echo $shipping_config['flash_partner_id'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="flash_app_id" class="col-sm-3 col-form-label">App ID</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="flash_app_id" name="flash_app_id" value="<?php echo $shipping_config['flash_app_id'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="flash_app_secret" class="col-sm-3 col-form-label">App Secret</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="flash_app_secret" name="flash_app_secret" value="<?php echo $shipping_config['flash_app_secret'] ?? ''; ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group row mt-3">
                        <label for="jandt_enabled" class="col-sm-3 col-form-label">
                            J&T Express
                        </label>
                        <div class="col-sm-9">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="jandt_enabled" name="jandt_enabled" value="1" <?php echo ($shipping_config['jandt_enabled'] ?? '0' == '1') ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="jandt_enabled">เปิดใช้งาน</label>
                            </div>
                        </div>
                    </div>
                    <div id="jandt_settings" class="pl-5" style="display: <?php echo ($shipping_config['jandt_enabled'] ?? '0' == '1') ? 'block' : 'none'; ?>;">
                        <div class="form-group row">
                            <label for="jandt_customer_id" class="col-sm-3 col-form-label">Customer ID</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="jandt_customer_id" name="jandt_customer_id" value="<?php echo $shipping_config['jandt_customer_id'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="jandt_password" class="col-sm-3 col-form-label">Password</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" id="jandt_password" name="jandt_password" value="<?php echo $shipping_config['jandt_password'] ?? ''; ?>">
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
    $('#kerry_enabled').on('change', function() {
        if ($(this).is(':checked')) {
            $('#kerry_settings').slideDown();
        } else {
            $('#kerry_settings').slideUp();
        }
    });
    $('#flash_enabled').on('change', function() {
        if ($(this).is(':checked')) {
            $('#flash_settings').slideDown();
        } else {
            $('#flash_settings').slideUp();
        }
    });
    $('#jandt_enabled').on('change', function() {
        if ($(this).is(':checked')) {
            $('#jandt_settings').slideDown();
        } else {
            $('#jandt_settings').slideUp();
        }
    });
</script>
