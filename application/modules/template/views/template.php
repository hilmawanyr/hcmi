<!-- set user data -->
<?php $userSession = $this->session->userdata('login_session'); ?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>HRIS - HCMI | Dashboard</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?= base_url() ?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= base_url() ?>assets/bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?= base_url() ?>assets/dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="<?= base_url() ?>assets/dist/css/skins/_all-skins.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="<?= base_url('assets/') ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">

  <!-- Google Font -->
  <link rel="stylesheet" href="<?= base_url('assets/dist/google-font.css') ?>">

  <!-- jQuery 3 -->
  <script src="<?= base_url() ?>assets/bower_components/jquery/dist/jquery.min.js"></script>

  <!-- jQuery UI 1.11.4 -->
  <script src="<?= base_url() ?>assets/bower_components/jquery-ui/jquery-ui.min.js"></script>

  <!-- HighChart -->
  <script src="<?= base_url() ?>assets/dist/js/highcharts/highcharts.js"></script>
  <script src="<?= base_url() ?>assets/dist/js/highcharts/modules/exporting.js"></script>
  <script src="<?= base_url() ?>assets/dist/js/highcharts/modules/export-data.js"></script>
</head>
<body class="hold-transition skin-yellow sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <!-- Logo -->
    <a href="<?= base_url() ?>" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>HRIS</b></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>HRIS-</b>HCMI</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">

          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle">
              <span class="hidden-xs"><?= $userSession['name'] ?></span>
            </a>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <!-- <li class="header">MAIN NAVIGATION</li> -->
        <li>
          <a href="<?= base_url() ?>">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
          </a>
        </li>
        <li>
          <a href="<?= base_url('assessment') ?>">
            <i class="fa fa-file-text-o"></i> <span>Assessment Form</span>
          </a>
        </li>
<<<<<<< HEAD
        <hr>
        <li>
          <a href="<?= base_url('changepassword') ?>">
            <i class="fa fa-key"></i> <span>Change Password</span>
=======
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-gear"></i>
            <span>Manage</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
>>>>>>> 6d5d5301db670f3d86141afd7df6be5ab03cee70
          </a>
          <ul class="treeview-menu">
            <li><a href="<?= base_url('assessment_year') ?>"><i class="fa fa-circle-o"></i> Assessment Year</a></li>
            <li><a href="<?= base_url('department') ?>"><i class="fa fa-circle-o"></i> Department</a></li>
          </ul>
        </li>

        <li>
<<<<<<< HEAD
          <a href="<?= base_url('logout') ?>">
            <i class="fa fa-sign-out"></i> <span>Sign Out</span>
          </a>
        </li>
=======
          <a href="<?= base_url('dictionary') ?>">
            <i class="fa fa-book"></i> <span>Competency Dictionary</span>
          </a>
        </li>

>>>>>>> 6d5d5301db670f3d86141afd7df6be5ab03cee70
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
      
      <!-- page show here -->
      <?php $this->load->view($page); ?>
      
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1.0.0
    </div>
    <strong>Copyright &copy; 2018 HCMI.</strong> All rights
    reserved.
  </footer>

</div>
<!-- ./wrapper -->

<<<<<<< HEAD
=======

<!-- jQuery UI 1.11.4 -->
<script src="<?= base_url() ?>assets/bower_components/jquery-ui/jquery-ui.min.js"></script>
>>>>>>> 6d5d5301db670f3d86141afd7df6be5ab03cee70
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.7 -->
<script src="<?= base_url() ?>assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

<!-- AdminLTE App -->
<script src="<?= base_url() ?>assets/dist/js/adminlte.min.js"></script>

<!-- DataTables -->
<script src="<?= base_url('assets/') ?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?= base_url('assets/') ?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

<script>
  $(function () {
    $('#example1').DataTable()
  })
</script>
</body>
</html>