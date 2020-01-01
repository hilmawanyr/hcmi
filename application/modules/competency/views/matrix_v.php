<section class="content-header">
  <h3 class="box-title">Competency Matrix <small>Competency grouping by job title</small></h3>
  <?php $this->load->view('template/action_message'); ?>
</section>

<!-- Main content -->
<section class="content">
  <div class="box">
    <!-- /.box-header -->
    <div class="box-body">
      <button 
        class="btn btn-info" 
        data-toggle="modal" 
        data-target="#addModal">
        <i class="fa fa-plus"></i> Add Job Title
      </button>
      <hr>
      <table id="example1" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>Department</th>
            <th>Section</th>
            <th>Job Title</th>
            <th>Position</th>
            <th>Grade</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($jobtitles as $jobtitle) { ?>
            <tr>
              <td><?= $jobtitle->department_name ?></td>
              <td><?= $jobtitle->section_name ?></td>
              <td><?= $jobtitle->jobtitle_name ?></td>
              <td><?= $jobtitle->position_name ?></td>
              <td><?= $jobtitle->position_grade ?></td>
              <td>
                <a href="<?= base_url('competency_matrix/'.$jobtitle->jobtitle_id.'/manage') ?>" class="btn btn-info">
                  <i class="fa fa-plus"></i>
                </a>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
    <!-- /.box-body -->
  </div>
</section>

<div id="addModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Add Job Title</h4>
        </div>
        <form action="<?= base_url('competency_matrix/store') ?>" id="formAction" method="post">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label for="section">Section</label>
                  <select name="section" id="" class="form-control select2" style="width: 100%" required="">
                    <?php foreach ($sections as $section) : ?>
                      <option value="<?= $section->id ?>"><?= $section->name ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="position">Position</label>
                  <select name="position" id="" class="form-control select2" style="width: 100%" required="">
                    <?php foreach ($positions as $position) : ?>
                      <option value="<?= $position->id ?>"><?= $position->name ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="group">Group</label>
                  <select name="group" id="" class="form-control select2" style="width: 100%" required="">
                    <?php for ($i = 1; $i < 6; $i++) : ?>
                      <option value="<?= $i ?>"><?= $i ?></option>
                    <?php endfor; ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="jobtitle">Job Title</label>
                  <input type="text" class="form-control" id="jobtitle" value="" name="jobtitle" required="">
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
              <button type="submit" class="btn btn-primary" id="btnSubmit">Save</button>
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </form>
    </div>

  </div>
</div>