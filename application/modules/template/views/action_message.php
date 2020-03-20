<!-- flash data for fail data save -->
<?php if ($this->session->flashdata('fail_remove_data')) { ?>
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
    <h5><i class="icon fa fa-ban"></i> <?= $this->session->flashdata('fail_remove_data') ?></h5>
  </div>
<?php } ?>

<!-- flash data for fail data save -->
<?php if ($this->session->flashdata('fail_save_data')) { ?>
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
    <h5><i class="icon fa fa-ban"></i> <?= $this->session->flashdata('fail_save_data') ?></h5>
  </div>
<?php } ?>

<!-- flash data for successfully data save -->
<?php if ($this->session->flashdata('success_save_data')) { ?>
  <div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
    <h5><i class="icon fa fa-check"></i> <?= $this->session->flashdata('success_save_data') ?></h5>
  </div>
<?php } ?>

<!-- flash data for successfully data update -->
<?php if ($this->session->flashdata('success_update_data')) { ?>
  <div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
    <h5><i class="icon fa fa-refresh"></i> <?= $this->session->flashdata('success_update_data') ?></h5>
  </div>
<?php } ?>

<!-- flash data for successfully data remove -->
<?php if ($this->session->flashdata('success_remove_data')) { ?>
  <div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
    <h5><i class="icon fa fa-trash"></i> <?= $this->session->flashdata('success_remove_data') ?></h5>
  </div>
<?php } ?>

<!-- flash data for successfully data remove -->
<?php if ($this->session->flashdata('success_change_active_year')) { ?>
  <div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
    <h5><i class="icon fa fa-check"></i> <?= $this->session->flashdata('success_change_active_year') ?></h5>
  </div>
<?php } ?>