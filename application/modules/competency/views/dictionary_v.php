<section class="content-header">
    <h3 class="box-title">Competency Dictionary <small>List of competency dictionary</small></h3>
  
    <!-- flash data for fail data save -->
    <?php if ($this->session->flashdata('fail_remove_data')) { ?>
      <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h5><i class="icon fa fa-ban"></i> <?= $this->session->flashdata('fail_remove_data') ?></h5>
      </div>
    <?php } ?>

    <!-- flash data for successfully data save -->
    <?php if ($this->session->flashdata('success_save_data')) { ?>
      <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h5><i class="icon fa fa-check"></i> <?= $this->session->flashdata('success_save_data') ?></h5>
      </div>
    <?php } ?>

    <!-- flash data for successfully data update -->
    <?php if ($this->session->flashdata('success_update_data')) { ?>
      <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h5><i class="icon fa fa-refresh"></i> <?= $this->session->flashdata('success_update_data') ?></h5>
      </div>
    <?php } ?>

    <!-- flash data for successfully data remove -->
    <?php if ($this->session->flashdata('success_remove_data')) { ?>
      <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h5><i class="icon fa fa-trash"></i> <?= $this->session->flashdata('success_remove_data') ?></h5>
      </div>
    <?php } ?>
</section>

<!-- Main content -->
<section class="content">
  <!-- Custom Tabs -->
  <div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
      <?php $no = 1; foreach ($skillTypes as $skills) { ?>
        <li <?= $skills->id == 1 ? 'class="active"' : '' ?> >
          <a 
            href="#tab_<?= $skills->id ?>" 
            data-toggle="tab" 
            onclick="get_dictionary(<?= $skills->id ?>)">
            <?= $skills->name ?>
          </a>
        </li>
      <?php $no++; } ?>
    </ul>
    <div class="tab-content">
      <?php foreach ($skillTypes as $skill) { ?>
        <div class="tab-pane <?= $skill->id == 1 ? 'active' : '' ?>" id="tab_<?= $skill->id ?>">
          
        </div>
      <?php } ?>
    </div>
  </div>
</section>

<script> 
  window.onload=get_dictionary(1)
  /**
   * Get data of dictionary competency depend skill id
   * @param int skillTypeId 
   * @return void (HTML table)
   */
  function get_dictionary(skillTypeId) {
    $('#tab_' + skillTypeId).load('<?= base_url() ?>dictionary/'+skillTypeId+'/detail');
  }
</script>