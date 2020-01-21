<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>HRIS | HCMI</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?= base_url() ?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= base_url() ?>assets/bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?= base_url() ?>assets/bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?= base_url() ?>assets/dist/css/AdminLTE.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/iCheck/square/blue.css">

  <!-- Google Font -->
  <link rel="stylesheet" href="<?= base_url('assets/dist/google-font.css') ?>">
</head>
<body class="hold-transition">

  <div class="col-md-8 bg-yellow" style="height: 100%;">
    <div class="col-md-12">
      <h1>
        <b>INFORMATIONS</b>Board 
        <!-- <a class="btn btn-success btn-xs" href="<?= base_url('information/read_all') ?>">Read all</a> -->
      </h1>
      
      <ul class="timeline">
      <?php foreach ($informations as $information)  : ?>
        
          <li>
            <i class="fa fa-bell bg-blue"></i>
            <div class="timeline-item">
              <span class="time"><i class="fa fa-clock-o"></i> <?= $information->created_at ?></span>
              <h3 class="timeline-header"><?= $information->title ?></h3>
              <div class="timeline-body">
                <?= substr($information->content, 0,400) ?> <a href="<?= base_url('information/'.$information->id.'/public') ?>">
                  ... Read more
                </a>
              </div>
            </div>
          </li>
          <!-- END timeline item -->
        
      <?php endforeach; ?>
      </ul>
    </div>
  </div>
  <div class="col-md-4">
    <div class="login-box" style="margin-top: 40%">
      <div class="login-logo">
        <a href="<?= base_url() ?>"><b>HRIS</b>HCMI</a>
      </div>
      <!-- /.login-logo -->

      <!-- show alert if login fail -->
      <?php if ($this->session->flashdata('login_fail')) { ?>
        <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
          <h5><i class="icon fa fa-ban"></i> <?= $this->session->flashdata('login_fail') ?></h5>
        </div>
      <?php } ?>

      <?php $this->load->view('template/action_message'); ?>

      <div class="login-box-body">
        <p class="login-box-msg">Sign in</p>

        <form action="<?= base_url('attemptlogin') ?>" method="post">
          <div class="form-group has-feedback">
            <input type="text" class="form-control" name="nik" placeholder="NIK">
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input type="password" class="form-control" name="password" placeholder="Password">
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>
          <div class="row">
            <div class="col-xs-4">
              <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
            </div>
            <!-- /.col -->
          </div>
        </form>
      </div>
    </div>
  </div>
<!-- /.login-box -->

<!-- jQuery 3 -->
<script src="<?= base_url() ?>assets/bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?= base_url() ?>assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="<?= base_url() ?>assets/plugins/iCheck/icheck.min.js"></script>
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    });
  });
</script>
</body>
</html>
