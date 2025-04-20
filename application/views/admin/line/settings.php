<style>
    .blue {
        color: #2C9BB3 !important;
    }
</style>

<section class="section">
    <div class="section-header">
        <h1><i class="fab fa-line"></i> <?php echo $page_title; ?></h1>
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
                    <b><?php echo $this->lang->line("Callback URL") . " :</b> <br><span class='blue'>" . base_url('line_callback'); ?></span><br/>
                    <small class="text-muted"><?php echo $this->lang->line("Please use this callback URL in your LINE Developers Console."); ?></small>
                </div>
            </div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <form action="<?php echo base_url("linesettings/update_settings"); ?>" method="POST">
                    <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"><i class="fas fa-info-circle"></i> <?php echo $this->lang->line("LINE API Details"); ?></h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for=""><i class="fas fa-hashtag"></i> <?php echo $this->lang->line("Channel ID"); ?></label>
                                <input name="channel_id" value="<?php echo isset($settings['channel_id']) ? $settings['channel_id'] : set_value('channel_id'); ?>" class="form-control" type="text" required>
                                <span class="red"><?php echo form_error('channel_id'); ?></span>
                            </div>

                            <div class="form-group">
                                <label for=""><i class="fas fa-key"></i> <?php echo $this->lang->line("Channel Secret"); ?></label>
                                <input name="channel_secret" value="<?php echo isset($settings['channel_secret']) ? $settings['channel_secret'] : set_value('channel_secret'); ?>" class="form-control" type="text" required>
                                <span class="red"><?php echo form_error('channel_secret'); ?></span>
                            </div>

                            <div class="form-group">
                                <label for=""><i class="fas fa-link"></i> <?php echo $this->lang->line("Callback URL"); ?></label>
                                <input name="callback_url" value="<?php echo isset($settings['callback_url']) ? $settings['callback_url'] : base_url('line_callback'); ?>" class="form-control" type="text" required>
                                <span class="red"><?php echo form_error('callback_url'); ?></span>
                                <small class="text-muted"><?php echo $this->lang->line("This should match the Callback URL you configured in your LINE Developers Console."); ?></small>
                            </div>

                            <div class="form-group">
                                <label for=""><i class="fas fa-robot"></i> <?php echo $this->lang->line("Bot Basic ID"); ?></label>
                                <input name="bot_basic_id" value="<?php echo isset($settings['bot_basic_id']) ? $settings['bot_basic_id'] : set_value('bot_basic_id'); ?>" class="form-control" type="text">
                                <span class="red"><?php echo form_error('bot_basic_id'); ?></span>
                                <small class="text-muted"><?php echo $this->lang->line("Optional: Your LINE Bot's Basic ID."); ?></small>
                            </div>

                            <div class="form-group">
                                <label for=""><i class="fas fa-mobile-alt"></i> <?php echo $this->lang->line("LIFF ID"); ?></label>
                                <input name="liff_id" value="<?php echo isset($settings['liff_id']) ? $settings['liff_id'] : set_value('liff_id'); ?>" class="form-control" type="text">
                                <span class="red"><?php echo form_error('liff_id'); ?></span>
                                <small class="text-muted"><?php echo $this->lang->line("Optional: LIFF ID for LINE Login in WebView."); ?></small>
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
