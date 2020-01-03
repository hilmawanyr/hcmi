<section class="content-header">
  <h1>
    Account
    <small>Change Password</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Change Password</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
    <?php $this->load->view('template/action_message'); ?>
    
    <!-- Horizontal Form -->
    <div class="box box-info col-md-6">
    <div class="box-header with-border">
        <!-- <h3 class="box-title">Horizontal Form</h3> -->
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <form class="form-horizontal" action="<?= base_url("storenewpass")?>" method="POST">
        <div class="box-body">
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">Old Password</label>
            <div class="col-sm-5">
                <input type="hidden" name="nik" id="nik1" value="<?= $this->session->userdata('login_session')['nik']?>">
                <input 
                    type="password" 
                    name="current_pass" 
                    class="form-control" 
                    id="inputPassword1" 
                    placeholder="Old Password" 
                    required />
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">New Password</label>
            <div class="col-sm-5">
                <input 
                    type="password" 
                    name="new_pass" 
                    class="form-control" 
                    id="inputPassword2" 
                    placeholder="New Password" 
                    required />
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">Repeat New Password</label>
            <div class="col-sm-5">
                <input 
                    type="password" 
                    name="repeat_pass" 
                    class="form-control" 
                    id="inputPassword3" 
                    placeholder="Repeat New Password" 
                    required />
            </div>
        </div>
        
        </div>
        <!-- /.box-body -->
        <div class="box-footer pull-left">
            <a href="<?= base_url() ?>" class="btn btn-default" style="margin-left:16em">Cancel</a> 
            <button type="submit" class="btn btn-info pull-right" style="margin-left:2.5em">Submit</button>
        </div>
        <!-- /.box-footer -->
    </form>
    </div>
    <!-- /.box -->
</section>