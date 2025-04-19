<div class="container mt-5">
  <div class="row">
    <div class="col-12 col-sm-8 offset-sm-2 col-md-8 offset-md-2 col-lg-6 offset-lg-3 col-xl-6 offset-xl-3">
      <div class="login-brand">
        <a href="<?php echo base_url();?>"><img src="<?php echo base_url(); ?>assets/img/logo.png" alt="<?php echo $this->config->item('product_name');?>" width="200"></a>
      </div>

      <div class="card card-primary">
        <div class="card-header"><h4><i class="fas fa-sign-in-alt"></i> <?php echo ($is_exist_team_member_addon && $is_team_login=='1') ? 'เข้าสู่ระบบสำหรับทีม' : 'เข้าสู่ระบบ'; ?></h4></div>
        <?php
          if($this->session->userdata('login_msg')!='')
          {
              echo "<div class='alert alert-danger text-center'>";
                  echo $this->session->userdata('login_msg'); // แสดงข้อความ error (ควร escape HTML เพื่อความปลอดภัย)
              echo "</div>";
              $this->session->unset_userdata('login_msg');
          }
          if($this->session->flashdata('reset_success')!='')
          {
              echo "<div class='alert alert-success text-center'>".$this->session->flashdata('reset_success')."</div>"; // แสดงข้อความสำเร็จ (ควร escape HTML เพื่อความปลอดภัย)
          }
          if($this->session->userdata('reg_success') != ''){
            echo '<div class="alert alert-success text-center">'.$this->session->userdata("reg_success").'</div>'; // แสดงข้อความสำเร็จ (ควร escape HTML เพื่อความปลอดภัย)
            $this->session->unset_userdata('reg_success');
          }
          if(form_error('username') != '' || form_error('password')!="" )
          {
            $form_error="";
            if(form_error('username') != '') $form_error.=form_error('username');
            if(form_error('password') != '') $form_error.=form_error('password');
            echo "<div class='alert alert-danger text-center'>".$form_error."</div>"; // แสดงข้อความ validation error
          }

          $default_user = $default_pass ="";
          if($this->is_demo=='1'){
            $default_user = "admin@xerochat.com";
            $default_pass="123456";
          }
        ?>
        <div class="card-body">
          <form method="POST" action="<?php echo ($is_exist_team_member_addon && $is_team_login=='1') ? base_url('home/login/1') : base_url('home/login'); ?>" class="needs-validation" novalidate="">
            <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">
            <div class="form-group">
              <label for="email"><?php echo ($is_exist_team_member_addon && $is_team_login=='1') ? 'อีเมล' : 'อีเมล หรือ FB ID'; ?></label>
              <input id="email" type="text" class="form-control" value="<?php echo $default_user;?>" name="username" tabindex="1" required autofocus>
            </div>

            <div class="form-group">
              <div class="d-block">
                      <label for="password" class="control-label">รหัสผ่าน</label>
                <?php if(!$is_exist_team_member_addon || $is_team_login=='0'):?>
                  <div class="float-right">
                    <a href="<?php echo site_url();?>home/forgot_password" class="text-small">
                      ลืมรหัสผ่าน?
                    </a>
                  </div>
                <?php endif;?>
              </div>
              <input id="password" type="password" class="form-control" value="<?php echo $default_pass;?>" name="password" tabindex="2" required>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary btn-lg btn-block login_btn" tabindex="4">
                <i class="fa fa-sign-in-alt"></i> <?php echo ($is_exist_team_member_addon && $is_team_login=='1') ? 'เข้าสู่ระบบสำหรับทีม' : 'เข้าสู่ระบบ'; ?>
              </button>
            </div>
          </form>

          <?php if($this->config->item('enable_signup_form')!='0' && ($is_team_login=='0'|| !$is_exist_team_member_addon)) : ?>
          <div class="row sm-gutters mb-4">
            <div class="col-12 col-sm-12 col-md-12 col-lg-6 text-center margin_top_5px">
              <?php echo str_replace("ThisIsTheLoginButtonForGoogle","ลงชื่อเข้าใช้ด้วย Google", $google_login_button); ?>
             </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-6 text-center margin_top_5px">
              <?php echo $fb_login_button2=str_replace("ThisIsTheLoginButtonForFacebook","ลงชื่อเข้าใช้ด้วย Facebook", $fb_login_button); ?>
            </div>
          </div>
          <?php endif;?>

          <div class="row sm-gutters">
            <div class="col-12">

              <?php if($is_team_login=='0' || !$is_exist_team_member_addon):?>

                <?php if($this->config->item('enable_signup_form')!='0'):?>
                  <div class="text-muted text-center">
                    ยังไม่มีบัญชี? <a href="<?php echo base_url('home/sign_up'); ?>">สร้างบัญชี</a>
                  </div>
                <?php endif;?>

                <?php if($is_team_login=='0' && $is_exist_team_member_addon):?>
                  <div class="text-muted text-center">
                   <a href="<?php echo base_url('home/login/1'); ?>">เข้าสู่ระบบในฐานะทีม</a>
                  </div>
                <?php endif;?>

              <?php endif;?>

              <?php if($is_team_login=='1' &&$is_exist_team_member_addon):?>
              <div class="text-muted text-center">
                <a href="<?php echo base_url('home/login'); ?>">เข้าสู่ระบบในฐานะผู้ใช้</a>
              </div>
              <?php endif;?>

            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<?php
$current_theme = $this->config->item('current_theme');
if($current_theme == '') $current_theme = 'modern';
$style_url = "application/views/site/".$current_theme."/login_style.php";
include($style_url);
?>
