<section class="content-header">
    <h3 class="box-title">Section <small>of <?= strtoupper(department_detail($departmentID)->name); ?></small></h3>
    <?php $this->load->view('template/action_message'); ?>
</section>

<!-- condition for each user group inflence the access right -->
<?php $userSession = $this->session->userdata('login_session');
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
          <form action="<?= base_url('department/store') ?>" method="post">
            <input type="hidden" name="isUpdate" value="<?= $departmentID ?>">
            <input type="hidden" name="updateFromSection" value="1">
            <div class="form-group">
              <label for="name">Department</label>
              <input 
                type="text" 
                class="form-control" 
                name="name"
                id="name" 
                value="<?= department_detail($departmentID)->name; ?>" 
                <?= $isDisable ?> 
                required="">
            </div>

            <?php if ($userSession['group'] != 3 && ($userSession['level'] != 2 || $userSession['level'] != 3)) { ?>
              <button type="submit" class="btn btn-info pull-right">Update Data</button>
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
              onclick="action('','')">
              <i class="fa fa-plus"></i> Add Data
            </button>
          <?php }  ?>
          <hr>
          <table class="table table-hover table-bordered" id="example1">
            <thead>
                <tr>
                  <th>No</th>
                  <th>Name</th>
                  <?php if ($userSession['group'] != 3 && ($userSession['level'] != 2 || $userSession['level'] != 3)) { ?>
                    <th>Action</th>
                  <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach ($sections as $section) : ?>
                    <tr>
                        <td><?= $no ?></td>
                        <td><?= $section->name ?></td>
                        <?php if ($userSession['group'] != 3 && ($userSession['level'] != 2 || $userSession['level'] != 3)) { ?>
                          <th>
                            <!-- <button 
                              class="btn btn-default"
                              data-toggle="modal"
                              data-target="#actionModal"
                              onclick="action('1','<?= $section->id ?>')"><i class="fa fa-edit"></i></button> -->
                            <a 
                              class="btn btn-default" 
                              href="<?= base_url('section/'.$section->id.'/jobtitle') ?>">
                              <i class="fa fa-plus"></i>
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
        <form action="<?= base_url('section/store') ?>" id="formAction" method="post">
            <div class="modal-body">
                <input type="hidden" id="isUpdate" name="isUpdate" value="">
                <input type="hidden" name="department_id" value="<?= $departmentID ?>">
                <div class="form-group">
                    <label for="section">Name</label>
                    <input type="text" class="form-control" id="section" value="" name="section" required="">
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
  function action (isUpdate, sectionId) {
    if (isUpdate === "") {
      $('.modal-title').text('Add Data');
      $('#isUpdate').val('');
      $('#department_name').val('');
      $('#btnSubmit').text('Save');
    } else {
      $('.modal-title').text('Edit Data');
      $('#isUpdate').val(sectionId);
      $('#btnSubmit').text('Update');
      $.get('<?= base_url() ?>section/' + sectionId + '/detail', function(response) {
        var section = JSON.parse(response)
        $('#section').val(section.name);
      })
    }
  }
</script>