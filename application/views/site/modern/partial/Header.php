<header class="header_area">
    <div id="header_navbar" class="header_navbar">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <nav class="navbar navbar-expand-lg">
                        <a class="navbar-brand" href="<?php echo base_url(); ?>">
                            <img id="logo" src="<?php echo base_url(); ?>assets/img/logo.png" alt="Logo">
                        </a>
                        <button class="navbar-toggler" type="button" data-toggle="collapse"
                                data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                                aria-expanded="false" aria-label="Toggle navigation">
                            <span class="toggler-icon"></span>
                            <span class="toggler-icon"></span>
                            <span class="toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse sub-menu-bar" id="navbarSupportedContent">
                            <ul id="nav" class="navbar-nav ml-auto">
                                <li class="nav-item">
                                    <a class="page-scroll active" href="#home">
                                        <?php echo $this->lang->line('Home'); ?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="page-scroll" href="#feature">
                                        <?php echo $this->lang->line('Features'); ?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="page-scroll" href="#pricing">
                                        <?php echo $this->lang->line('Pricing'); ?>
                                    </a>
                                </li>
                                <li <?php if ($this->config->item('display_video_block') == '0') echo "class='d-none'"; else echo "class='nav-item'"; ?>>
                                    <a class="page-scroll" href="#tutorial">
                                        <?php echo $this->lang->line('Tutorial'); ?>
                                    </a>
                                </li>
                                <?php if ($this->session->userdata('license_type') == 'double') { ?>
                                    <li class="nav-item">
                                        <a href="<?php echo base_url('blog'); ?>">
                                            <?php echo $this->lang->line('Blog'); ?>
                                        </a>
                                    </li>
                                <?php } ?>
                                <li class="nav-item">
                                    <a class="page-scroll" href="#contact">
                                        <?php echo $this->lang->line('Contact'); ?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?php echo site_url('home/login'); ?>" class="btn btn-outline-secondary rounded-pill px-3 py-2 mx-1">
                                        <?php echo $this->lang->line('Login'); ?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?php echo site_url('home/sign_up'); ?>" class="btn btn-primary rounded-pill px-3 py-2 mx-1">
                                        <?php echo $this->lang->line('Sign Up Free'); ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>
