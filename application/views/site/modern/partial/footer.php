<footer id="footer" class="footer-area bg_cover">
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-3">
                <div class="footer-widget wow fadeInUp" data-wow-delay=".2s">
                    <a href="<?php echo base_url(); ?>" class="mb-4 d-block">
                        <img class="logo" src="<?php echo base_url(); ?>assets/img/logo.png" alt="Logo">
                    </a>
                    <p class="wow fadeInUp" data-wow-delay=".4s">
                        <?php echo $this->lang->line("Revolutionary, world's very first, and complete marketing software for Instagram developed using official APIs."); ?>
                    </p>
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="footer-widget wow fadeInUp" data-wow-delay=".4s">
                    <h4><?php echo $this->lang->line("Features"); ?></h4>
                    <ul>
                        <li><a href="#"><?php echo $this->lang->line("Chatbot System"); ?></a>
                            <ul>
                                <li><a href="#"><?php echo $this->lang->line("Keyword Chatbot"); ?></a></li>
                                <li><a href="#"><?php echo $this->lang->line("AI Chatbot"); ?></a></li>
                                <li><a href="#"><?php echo $this->lang->line("Comment Reply Bot"); ?></a></li>
                                <li><a href="#"><?php echo $this->lang->line("Live Chat"); ?></a></li>
                            </ul>
                        </li>
                        <li><a href="#"><?php echo $this->lang->line("Automation Marketing"); ?></a>
                             <ul>
                                <li><a href="#"><?php echo $this->lang->line("Auto Post"); ?></a></li>
                                <li><a href="#"><?php echo $this->lang->line("Auto Comment"); ?></a></li>
                                <li><a href="#"><?php echo $this->lang->line("Delete/Hide Comment"); ?></a></li>
                                <li><a href="#"><?php echo $this->lang->line("Broadcast"); ?></a></li>
                            </ul>
                        </li>
                        <li><a href="#"><?php echo $this->lang->line("CRM"); ?></a></li>
                        <li><a href="#"><?php echo $this->lang->line("Sell Page"); ?></a></li>
                        <li><a href="#"><?php echo $this->lang->line("Affiliate"); ?></a></li>
                    </ul>
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="footer-widget wow fadeInUp" data-wow-delay=".6s">
                    <h4><?php echo $this->lang->line("About Us"); ?> / <?php echo $this->lang->line("Help"); ?></h4>
                    <ul>
                        <li><a href="#"><?php echo $this->lang->line("About Us"); ?></a></li>
                        <li><a href="#"><?php echo $this->lang->line("Articles"); ?></a></li>
                        <li><a href="#"><?php echo $this->lang->line("Help"); ?></a></li>
                        <li><a href="#"><?php echo $this->lang->line("Customer Success"); ?></a></li>
                    </ul>

                    <h4 class="mt-4"><?php echo $this->lang->line("Learn More"); ?></h4>
                    <ul>
                        <li><a href="#"><?php echo $this->lang->line("How to Use"); ?></a></li>
                        <li><a href="#"><?php echo $this->lang->line("API Docs"); ?></a></li>
                        <li><a href="#pricing" class="page-scroll"><?php echo $this->lang->line("Pricing"); ?></a></li>
                        <li><a href="#"><?php echo $this->lang->line("Changelog"); ?></a></li>
                        <li><a href="#"><?php echo $this->lang->line("Blog"); ?></a></li>
                    </ul>
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="footer-widget wow fadeInUp" data-wow-delay=".8s">
                    <h4><?php echo $this->lang->line("Follow Us"); ?> / <?php echo $this->lang->line("Urgent Help"); ?></h4>
                    <ul class="social-links">
                        <li <?php if($facebook=='') echo "class='d-none'"; ?>>
                            <a target="_BLANK" href="<?php echo $facebook; ?>" class="facebook">
                                <i class="lni lni-facebook-original"></i> <?php echo $this->lang->line("Facebook"); ?>
                            </a>
                        </li>
                        <li <?php if($instagram=='') echo "class='d-none'"; ?>>
                            <a target="_BLANK" href="<?php echo $instagram; ?>" class="instagram">
                                <i class="lni lni-instagram-original"></i> <?php echo $this->lang->line("Instagram"); ?>
                            </a>
                        </li>
                         <li <?php if($tiktok=='') echo "class='d-none'"; ?>>
                            <a target="_BLANK" href="<?php echo $tiktok; ?>" class="tiktok">
                                <i class="lni lni-tiktok"></i> <?php echo $this->lang->line("Tiktok"); ?>
                            </a>
                        </li>
                        <li <?php if($youtube=='') echo "class='d-none'"; ?>>
                            <a target="_BLANK" href="<?php echo $youtube; ?>" class="youtube">
                                <i class="lni lni-youtube"></i> <?php echo $this->lang->line("Youtube"); ?>
                            </a>
                        </li>
                        <li <?php if($x=='') echo "class='d-none'"; ?>>
                            <a target="_BLANK" href="<?php echo $x; ?>" class="twitter">
                                <i class="lni lni-twitter-original"></i> <?php echo $this->lang->line("X"); ?>
                            </a>
                        </li>
                        <li <?php if($line=='') echo "class='d-none'"; ?>>
                            <a target="_BLANK" href="<?php echo $line; ?>" class="line">
                                <i class="lni lni-line"></i> <?php echo $this->lang->line("Line"); ?>
                            </a>
                        </li>
                    </ul>

                    <h4 class="mt-4"><?php echo $this->lang->line("Urgent Help"); ?></h4>
                    <ul>
                        <li><a href="#"><?php echo $this->lang->line("Chat with Us"); ?></a></li>
                        <li><a href="#"><?php echo $this->lang->line("Send Email"); ?></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="footer-cradit">
            <p class="text-center mb-0">
                <?php echo $this->lang->line("Copyright"); ?> &copy;
                <a target="_blank" href="<?php echo site_url(); ?>">
                    <?php echo $this->config->item("institute_address1"); ?>
                </a>
            </p>
        </div>
    </div>
</footer>

<?php if($this->session->userdata('allow_cookie')!='yes') : ?>
    <div class="text-center cookiealert">
        <div class="cookiealert-container py-3">
            <a class="cookie_content_css" href="<?php echo base_url('home/privacy_policy#cookie_policy');?>">
                <?php echo $this->lang->line("This site requires cookies in order for us to provide proper service to you.");?>
            </a>
            <a type="button" href="#" class="btn btn-warning btn-sm acceptcookies black_color" aria-label="Close">
                <?php echo $this->lang->line("Got it !"); ?>
            </a>
        </div>
    </div>
<?php endif; ?>
