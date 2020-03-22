<section class="content-header">
    <h3 class="box-title">Job Title <small>of <?= strtoupper(section_detail($sectionID)->name); ?> section</small></h3>
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
          <form action="<?= base_url('jobtitle/update_section') ?>" method="post">
            <div class="form-group">
              <input type="hidden" name="sectionID" value="<?= section_detail($sectionID)->id; ?>">
              <label for="section">Section</label>
              <input 
                type="text" 
                class="form-control" 
                name="section"
                id="section" 
                value="<?= section_detail($sectionID)->name; ?>" 
                <?= $isDisable ?> 
                required="">
            </div>
            <div class="form-group">
              <label for="name">Department</label>
              <select class="form-control" id="" disabled="">
                <?php foreach ($departments as $department) : ?>
                  <option 
                    value="<?= $department->id ?>"
                    <?= $department->id == section_detail($sectionID)->dept_id ? 'selected=""' : "" ?>>
                    <?= $department->name ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <input type="hidden" name="department" value="<?= section_detail($sectionID)->dept_id ?>">
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
              <?php $no=1; foreach ($jobtitles as $jobtitle) : ?>
                <tr>
                  <td><?= $no ?></td>
                  <td><?= $jobtitle->name ?></td>
                  <?php if ($userSession['group'] != 3 && ($userSession['level'] != 2 || $userSession['level'] != 3)) { ?>
                    <th>
                      <button 
                        class="btn btn-default"
                        data-toggle="modal"
                        data-target="#actionModal"
                        onclick="action('1','<?= $jobtitle->id ?>')"><i class="fa fa-edit"></i></button>
                        <a 
                          href="<?= base_url('jobtitle/'.$jobtitle->id.'-'.section_detail($sectionID)->id.'/remove') ?>" 
                          onclick="return confirm('Are You sure want to delete this job tilte?')"
                          class="btn btn-default">
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
        <form action="<?= base_url('jobtitle/store') ?>" id="formAction" method="post">
            <div class="modal-body">
                <input type="hidden" id="isUpdate" name="isUpdate" value="">
                <input type="hidden" name="section_id" value="<?= $sectionID ?>">
                <div class="form-group">
                    <label for="jobtitle">Name</label>
                    <input type="text" class="form-control" id="jobtitle" value="" name="jobtitle" required="">
                </div>
                <div class="form-group">
                    <label for="position">Position</label>
                    <select name="position" class="form-control" id="position" required="">
                      <option value="" disabled="" selected=""></option>
                      <?php foreach ($positions as $position) : ?>
                        <option value="<?= $position->id ?>"><?= $position->name ?></option>
                      <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="group">Group</label>
                    <select name="group" class="form-control" id="group" required="">
                      <option value="" disabled="" selected=""></option>
                      <?php foreach ($groups as $group) : ?>
                        <option value="<?= $group->id ?>"><?= $group->name ?></option>
                      <?php endforeach; ?>
                    </select>
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
  function action (isUpdate, jobtitleId) {
    if (isUpdate === "") {
      $('.modal-title').text('Add Data');
      $('#isUpdate').val('');
      $('#jobtitle').val('');
      $('#position').val('')
      $('#group').val('')
      $('#btnSubmit').text('Save');
    } else {
      $('.modal-title').text('Edit Data');
      $('#isUpdate').val(jobtitleId);
      $('#btnSubmit').text('Update');
      $.get('<?= base_url() ?>jobtitle/' + jobtitleId + '/detail', function(response) {
        var data = JSON.parse(response)
        console.log(data)
        $('#jobtitle').val(data.name)
        $('#position').val(data.position_id)
        $('#group').val(data.group)
      })
    }
  }
</script>