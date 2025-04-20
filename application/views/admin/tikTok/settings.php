<style>
    .blue {
        color: #2C9BB3 !important;
    }
</style>

<section class="section">
    <div class="section-header">
        <h1><i class="fab fa-tiktok"></i> <?php echo $page_title; ?></h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="<?php echo base_url('integration'); ?>"><?php echo $this->lang->line("Integration"); ?></a></div>
            <div class="breadcrumb-item"><?php echo $page_title; ?></div>
        </div>
    </div>

    <?php $this->load->view('admin/theme/message'); ?>

    <div class="row">
        <div class="col-12 col-md-6 col-lg-6">
            <div class="card">
                <div class="card-body ltr">
                    <b><?php echo $this->lang->line("App Domain") . "</b> : <span class='blue'>" . get_domain_only(base_url()); ?></span><br/>
                    <b><?php echo $this->lang->line("Site URL") . " :</b> <span class='blue'>" . base_url(); ?></span><br/><br>
                    <b><?php echo $this->lang->line("Redirect URI") . " :</b> <br><span class='blue'>" . base_url('tiktok_callback'); ?></span><br/>
                    <small class="text-muted"><?php echo $this->lang->line("Please use this redirect URI in your TikTok Developer Portal."); ?></small>
                </div>
            </div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <form action="<?php echo base_url("tiktoksettings/update_settings"); ?>" method="POST">
                    <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"><i class="fas fa-info-circle"></i> <?php echo $this->lang->line("App Details"); ?></h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for=""><i class="fas fa-file-signature"></i> <?php echo $this->lang->line("App Name"); ?></label>
                                <input name="app_name" value="<?php echo isset($settings['app_name']) ? $settings['app_name'] : set_value('app_name'); ?>" class="form-control" type="text">
                                <span class="red"><?php echo form_error('app_name'); ?></span>
                            </div>

                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for=""><i class="far fa-id-card"></i> <?php echo $this->lang->line("Client Key"); ?></label>
                                        <input name="client_key" value="<?php echo isset($settings['client_key']) ? $settings['client_key'] : set_value('client_key'); ?>" class="form-control" type="text" required>
                                        <span class="red"><?php echo form_error('client_key'); ?></span>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for=""><i class="fas fa-key"></i> <?php echo $this->lang->line("Client Secret"); ?></label>
                                        <input name="client_secret" value="<?php echo isset($settings['client_secret']) ? $settings['client_secret'] : set_value('client_secret'); ?>" class="form-control" type="text" required>
                                        <span class="red"><?php echo form_error('client_secret'); ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for=""><i class="fas fa-link"></i> <?php echo $this->lang->line("Redirect URI"); ?></label>
                                <input name="redirect_uri" value="<?php echo isset($settings['redirect_uri']) ? $settings['redirect_uri'] : base_url('tiktok_callback'); ?>" class="form-control" type="text" required>
                                <span class="red"><?php echo form_error('redirect_uri'); ?></span>
                                <small class="text-muted"><?php echo $this->lang->line("This should match the Redirect URI you configured in your TikTok Developer Portal."); ?></small>
                            </div>

                            <div class="form-group">
                                <?php
                                $status = isset($settings['status']) ? $settings['status'] : "1";
                                ?>
                                <label class="custom-switch mt-2">
                                    <input type="checkbox" name="status" value="1" class="custom-switch-input" <?php if ($status == '1') echo 'checked'; ?>>
                                    <span class="custom-switch-indicator"></span>
                                    <span class="custom-switch-description"><?php echo $this->lang->line('Active'); ?></span>
                                    <span class="red"><?php echo form_error('status'); ?></span>
                                </label>
                            </div>
                        </div>

                        <div class="card-footer bg-whitesmoke">
                            <button class="btn btn-primary btn-lg" id="save-btn" type="submit"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save"); ?></button>
                            <button class="btn btn-secondary btn-lg float-right" onclick='goBack("integration")' type="button"><i class="fa fa-remove"></i> <?php echo $this->lang->line("Cancel"); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
    function goBack(url) {
        window.location.href = '<?php echo base_url(); ?>' + url;
    }
</script>
