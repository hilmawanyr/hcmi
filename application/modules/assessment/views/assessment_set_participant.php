<style>
  .ui-autocomplete {
    z-index: 9999 !important;
  }
</style>

<section class="content-header">
  <h3 class="box-title">Assessment Form List</h3>
  <?php $this->load->view('template/action_message'); ?>
</section>

<!-- Main content -->
<section class="content">
  <div class="box">
    <!-- /.box-header -->
    <div class="box-body">
      <button class="btn btn-primary" data-target="#myModal" data-toggle="modal"><i class="fa fa-plus"></i> Add Form</button>
      <hr>
      <table id="example1" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>Form Code</th>
            <th>Department</th>
            <th>Section</th>
            <th>Grade</th>
            <th>Superior</th>
            <th>Total Employees</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recaps as $recap) : ?>
            <tr>
              <td><?= $recap->code ?></td>
              <td><?= $recap->dept_name ?></td>
              <td><?= $recap->section_name ?></td>
              <td><?= convert_to_roman($recap->grade) ?></td>
              <td><?= $recap->head_name ?></td>
              <td><?= $recap->number_of_participant ?></td>
              <td>
                <a 
                  href="<?= base_url('form/'.$recap->code.'/see_detail') ?>" 
                  class="btn btn-primary btn-sm">
                  <i class="fa fa-eye"></i> Detail
                </a>
                <a 
                  onclick="return confirm('Are you sure want to delete this form?')"
                  href="<?= base_url('form/'.$recap->code.'/remove') ?>" 
                  class="btn btn-danger btn-sm">
                  <i class="fa fa-trash"></i> Delete
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <!-- /.box-body -->
  </div>
</section>

<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Create Form</h4>
      </div>
      <form action="<?= base_url('assessment/setup_supervisor') ?>" method="post">
        <div class="modal-body">
          <div class="form-group">
            <label for="department">Department</label>
            <select name="department" class="form-control select2" style="width: 100%" id="department" required="">
              <option value="" disabled="" selected=""></option>
              <?php foreach ($departments as $department) : ?>
                <option value="<?= $department->id ?>"><?= $department->name ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="section">Section</label>
            <select name="section" class="form-control select2" style="width: 100%" id="section" required="">
              <option value="" disabled="" selected=""></option>
            </select>
          </div>

          <div class="form-group">
            <label for="supervisor">Superior</label>
            <input type="text" class="form-control" id="supervisor" value="" name="supervisor" required="">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Submit</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>

  </div>
</div>

<script>
  $('#department').change(function() {
    $.get('<?= base_url('assessment/get_sections/') ?>' + $(this).val(),{}, 
      function(response) {
        $('#section').html(response)
    })
  });

  $('#supervisor').autocomplete({
    source: '<?= base_url('manage/employes/get_employe');?>',
    minLength: 2,
    select: function (evt, ui) {
      this.form.supervisor.value = ui.item.value;
    }
  });
</script>