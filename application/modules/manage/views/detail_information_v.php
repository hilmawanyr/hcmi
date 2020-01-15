<?php $userSession = $this->session->userdata('login_session'); ?>
<section class="content-header">
  
  <h3 class="box-title">
    <a href="javascript:history.go(-1)" class="btn btn-warning"><i class="fa fa-chevron-left"></i> Back</a>
    <?php if ($userSession['group'] == 1 || $userSession['group'] == 2) : ?>
      <a 
        href="<?= base_url('information/'.$information->id.'/edit') ?>" 
        class="btn btn-primary pull-right" 
        data-toggle="tooltip" 
        data-placement="left" 
        title="edit information">
        <i class="fa fa-pencil"></i>
      </a>
       <a 
        href="<?= base_url('information/'.$information->id.'/delete') ?>" 
        class="btn btn-danger pull-right" 
        style="margin-right: 5px"
        onclick="return confirm('Are You sure want to delete this data?')"
        data-toggle="tooltip" 
        data-placement="left" 
        title="delete information">
        <i class="fa fa-trash"></i>
      </a>
    <?php endif; ?>
  </h3>
  <?php $this->load->view('template/action_message'); ?>
</section>

<!-- Main content -->
<section class="content">
  <div class="box ">
    <!-- /.box-header -->
    <div class="box-header">
      <h3><?= $information->title ?>
        <br>
        <small>
          created at <span class="badge bg-yellow"><?= $information->created_at ?></span>, 
          type <span class="badge bg-yellow"><?= $information->type ?></span>
        </small>
      </h3>
    </div>
    <div class="box-body ">
      <?= $information->content ?>
    </div>
    <!-- /.box-body -->
  </div>
</section>