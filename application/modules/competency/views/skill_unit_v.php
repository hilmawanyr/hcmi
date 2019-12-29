<section class="content-header">
    <h3 class="box-title">Competency Dictionary <small>of <?= $dictionary->name_id ?></small></h3>
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
        <h5><i class="icon fa fa-check"></i> <?= $this->session->flashdata('success_update_data') ?></h5>
      </div>
    <?php } ?>
    <!-- flash data for successfully data remove -->
    <?php if ($this->session->flashdata('success_remove_data')) { ?>
      <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h5><i class="icon fa fa-check"></i> <?= $this->session->flashdata('success_remove_data') ?></h5>
      </div>
    <?php } ?>
</section>

<!-- condition for each user group inflence the access right -->
<?php 
  $userSession = $this->session->userdata('login_session');
  if ($userSession['group'] != 3 && ($userSession['level'] != 2 || $userSession['level'] != 3)) {
    $isDisable = '';
  } else {
    $isDisable = 'disabled=""';
  }
 ?>

<!-- Main content -->
<div class="row">
  <div class="col-md-12">
    <section class="content col-md-4">
      <div class="box ">
        <!-- /.box-header -->
        <div class="box-body ">
          <form action="<?= base_url('dictionary/store') ?>" method="post">
            <input type="hidden" name="isUpdate" value="<?= $dictionary->id ?>">
            <input type="hidden" name="updateFromDetailMode" value="1">
            <div class="form-group">
              <label for="name_id">Indonesian Name</label>
              <input 
                type="text" 
                class="form-control" 
                name="name_id"
                id="name_id" 
                value="<?= $dictionary->name_id ?>" 
                <?= $isDisable ?> 
                required="">
            </div>
            <div class="form-group">
              <label for="name_en">English Name</label>
              <input 
                type="text" 
                class="form-control" 
                name="name_en"
                id="name_en" 
                value="<?= $dictionary->name_en ?>" 
                <?= $isDisable ?> 
                required="">
            </div>
            <div class="form-group">
              <label for="description">Description</label>
              <textarea 
                name="description" 
                id="description" 
                class="form-control" 
                rows="6" 
                <?= $isDisable ?> 
                required="">
                <?= $dictionary->description ?>
              </textarea>
            </div>

            <?php if ($userSession['group'] != 3 && ($userSession['level'] != 2 || $userSession['level'] != 3)) { ?>
              
              <div class="form-group">
                <label for="description">Competency Type</label>
                <?php foreach ($skillTypes as $type) { ?>
                  <div class="radio">
                    <label>
                      <input 
                        type="radio" 
                        name="skill_type"
                        value="<?= $type->id ?>" 
                        <?= $dictionary->skill_group == $type->id ? 'checked=""' : '' ?>>
                      <?= $type->name ?>
                    </label>
                  </div>
                <?php } ?>
              </div>
              <button type="submit" class="btn btn-info pull-right">Update Data</button>
            <?php } else { ?>
                
              <div class="form-group">
                <label for="type">Competency Type</label>
                <input type="text" class="form-control" id="type" value="<?= get_skill_type_name($dictionary->skill_group) ?>" disabled />
              </div>

            <?php } ?>

            <button class="btn btn-primary" type="button" onclick="history.go(-1)">Back</button>
          </form>
        </div>
        <!-- /.box-body -->
      </div>
    </section>

    <section class="content col-md-8">
      <div class="box ">
        <!-- /.box-header -->
        <div class="box-body ">
          <?php if ($userSession['group'] != 3 && ($userSession['level'] != 2 || $userSession['level'] != 3)) { ?>
            <button 
              class="btn btn-info" 
              data-toggle="modal" 
              data-target="#actionModal"
              onclick="action('',<?= $dictionary->id ?>)">
              <i class="fa fa-plus"></i> Add Data
            </button>
          <?php }  ?>
          <a href="<?= base_url('skill_unit/'.$dictionary->id.'/print') ?>" class="btn btn-primary"><i class="fa fa-print"></i> Print</a>
          <hr>
          <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Level</th>
                    <th>Description</th>
                    <?php if ($userSession['group'] != 3 && ($userSession['level'] != 2 || $userSession['level'] != 3)) { ?>
                      <th>Action</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach ($skillUnit as $unit) : ?>
                    <tr>
                        <td><?= $unit->level ?></td>
                        <td><?= $unit->description ?></td>
                        <?php if ($userSession['group'] != 3 && ($userSession['level'] != 2 || $userSession['level'] != 3)) { ?>
                          <th>
                            <button 
                              class="btn btn-default"
                              data-toggle="modal"
                              data-target="#actionModal"
                              onclick="action('1','<?= $unit->id ?>')"><i class="fa fa-edit"></i></button>
                            <a 
                              class="btn btn-default" 
                              href="<?= base_url('skill_unit/'.$unit->id.'/remove/'.$dictionary->id.'/dictionary') ?>"
                              onclick="return confirm('Are You sure to remove this data?')">
                              <i class="fa fa-trash"></i>
                            </a>
                          </th>
                        <?php } ?>
                    </tr>
                <?php $no++; endforeach; ?>
            </tbody>
          </table>
        </div>
        <!-- /.box-body -->
      </div>
    </section>
  </div>
</div>

<div id="actionModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"></h4>
        </div>
        <form action="<?= base_url('skill_unit/store') ?>" id="formAction" method="post">
            <div class="modal-body">
                <input type="hidden" id="isUpdate" name="isUpdate" value="">
                <input type="hidden" name="dictionary_id" value="<?= $dictionary->id ?>">
                <div class="form-group">
                    <label for="level_des">Level description of competency unit</label>
                    <input type="text" class="form-control" id="level_des" value="" name="level_des" required="">
                </div>
                <div class="form-group">
                    <label for="level">Level of competency unit</label>
                    <input type="number" class="form-control" id="level" value="" name="level" required="">
                </div>
                <div class="form-group">
                    <label for="name_id">Description</label>
                    <textarea name="description" class="form-control" id="description" rows="5" required=""></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" id="btnSubmit"></button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>

  </div>
</div>

<script>
  function action (isUpdate, skillId) {
    if (isUpdate === "") {
      $('.modal-title').text('Add Data');
      $('#isUpdate').val('');
      $('#level_des').val('');
      $('#level').val('');
      $('#description').val('');
      $('#btnSubmit').text('Save');
    } else {
      $('.modal-title').text('Edit Data');
      $('#isUpdate').val(skillId);
      $('#btnSubmit').text('Update');
      $.get('<?= base_url() ?>skill_unit/' + skillId + '/detail', function(response) {
        var skillUnitData = JSON.parse(response)
        $('#level_des').val(skillUnitData.level_des);
        $('#level').val(skillUnitData.level);
        $('textarea#description').val(skillUnitData.description);
      })
    }
  }
</script>