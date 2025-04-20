<section id="home" class="hero-area bg_cover">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-xl-6 col-lg-6">
                <div class="hero-content">
                    <h1 class="wow fadeInUp" data-wow-delay=".2s">
                        <?php echo $this->lang->line("Boost customer communication with"); ?> <?php echo $this->config->item('product_name'); ?>
                    </h1>
                    <p class="wow fadeInUp" data-wow-delay=".4s">
                        <?php echo $this->lang->line("AI-powered bots and seamless agent handoff for higher sales and instant support."); ?>
                    </p>
                    <div class="hero-buttons">
                        <a href="<?php echo site_url('home/sign_up'); ?>"
                           class="btn primary-cta btn-hover wow fadeInUp <?php if ($this->config->item('enable_signup_form') == '0') echo "d-none"; ?>"
                           data-wow-delay=".45s">
                            <?php echo $this->lang->line("Start free"); ?>
                        </a>
                        <a href="#feature" class="btn secondary-cta btn-hover wow fadeInUp page-scroll"
                           data-wow-delay=".5s">
                            <?php echo $this->lang->line("Explore features"); ?>
                        </a>
                    </div>
                    <p class="hero-small-print wow fadeInUp" data-wow-delay=".6s">
                        <?php echo $this->lang->line("Try it free. No credit card. Affordable pricing."); ?>
                    </p>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6">
                <div class="hero-img">
                    <img src="<?php echo base_url(); ?>assets/modern/images/features-image-2.png" alt=""
                         class="wow fadeInRight" data-wow-delay=".2s">
                    <img src="<?php echo base_url(); ?>assets/modern/images/features-image-1.png" alt=""
                         class="img-screen screen-1 wow fadeInUp" data-wow-delay=".25s">
                    <img src="<?php echo base_url(); ?>assets/modern/images/features-image-2.png" alt=""
                         class="img-screen screen-2 wow fadeInUp" data-wow-delay=".3s">
                    <img src="<?php echo base_url(); ?>assets/modern/images/features-image-2.png" alt=""
                         class="img-screen screen-3 wow fadeInUp" data-wow-delay=".35s">
                </div>
            </div>
        </div>
    </div>
</section>
<style>
    .hero-section {
        padding: 80px 0; /* Adjust as needed */
    }

    .hero-content {
        margin-bottom: 40px; /* Spacing between text and image */
    }

    .hero-content h1 {
        font-size: 2.5rem; /* Adjust as needed */
        font-weight: bold;
        line-height: 1.2;
        margin-bottom: 20px;
    }

    .hero-content p {
        font-size: 1.1rem; /* Adjust as needed */
        color: #555;
        line-height: 1.6;
    }

    .hero-buttons {
        margin-top: 30px;
    }

    .btn {
        padding: 12px 30px;
        border-radius: 30px;
        text-decoration: none;
        display: inline-block;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .primary-cta {
        background-color: #007bff; /* Primary color (e.g., blue) */
        color: white;
        border: 2px solid #007bff;
    }

    .primary-cta:hover {
        background-color: white;
        color: #007bff;
    }

    .secondary-cta {
        background-color: transparent;
        color: #007bff; /* Primary color */
        border: 2px solid #007bff;
    }

    .secondary-cta:hover {
        background-color: #007bff;
        color: white;
    }

    .hero-small-print {
        font-size: 0.9rem;
        color: #888;
        margin-top: 20px;
    }

    .hero-img {
        text-align: center; /* Center the image */
    }

    .hero-img img {
        max-width: 100%; /* Make image responsive */
        height: auto;
    }

    @media (max-width: 767px) {
        .hero-section {
            padding: 50px 0;
        }

        .hero-content {
            text-align: center;
        }

        .hero-content h1 {
            font-size: 2rem;
        }

        .hero-image {
            order: -1; /* Move image above text on mobile */
            margin-bottom: 30px;
        }
    }
</style>
