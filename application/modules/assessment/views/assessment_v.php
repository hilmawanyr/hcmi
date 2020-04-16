<?php error_reporting(0); ?>

<section class="content-header">
  <?php if ($this->session->userdata('login_session')['group'] == 3) { ?>

    <!-- if AM or SAM -->
    <?php if ($position_grade > 3 && $position_grade < 7) { ?>

      <h3 class="box-title"></h3>
      <ol class="breadcrumb">
        <h3 class="box-title pull-right"></h3>
      </ol>
    
    <!-- if GM or higher -->
    <?php } elseif ($position_grade > 6) { ?>
      
      <h3 class="box-title">Department : <?= get_department($department); ?></h3>

    <?php } ?>
    
  <?php } else { ?>
    <h3 class="box-title">Job Title List</h3>
  <?php } ?>

  <?php $this->load->view('template/action_message'); ?>
</section>

<!-- Main content -->
<section class="content">
  <div class="box">
    <!-- /.box-header -->
    <div class="box-body">
      <!-- export just for admin -->
      <?php if ($position_grade == 99) { ?>
        <a href="#exportModal" data-toggle="modal" class="btn btn-success">
          <i class="fa fa-download"></i> Export Excel
        </a>
      <?php } ?>
      <hr>
      <table id="example1" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>Grade</th>
            <th>Job Title</th>
            <th>Total Employes</th>
            <th>Section</th>

            <!-- if login as manager and upper -->
            <?php if ($position_grade > 5) : ?>
            <th>Superior</th>
            <th>Department</th>
            <?php endif; ?>
            <!-- end if -->

            <th>Percentage of Filling</th>
            <th>On Process</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php $no=1; foreach ($jobtitleList as $row) { ?>
            <tr>
              <td><?= convert_to_roman($row->grade) ?></td>
              <td><?= $row->jobtitleName ?></td>
              <td><?= $row->numberOfPeople ?></td>
              <td><?= get_section($row->section_id)->name ?></td>

              <!-- if login as manager and upper -->
              <?php if ($position_grade > 5) : ?>
              <td><?= user_name(explode('-',$row->code)[3]) ?></td>
              <td><?= get_department(get_section($row->section_id)->dept_id) ?></td>
              <?php endif; ?>
              <!-- end if -->

              <td><?= is_form_complete($row->job_title_id, $row->head) ?> %</td>

              <?php $code = 'AF-'.$row->job_title_id.'-'.get_active_year().'-'.$row->head; 
              // for admin
              if ($position_grade == 99) { ?>

                <td>
                  <a 
                    onclick="edit_state('<?= $code ?>')" 
                    href="javascript:void(0);" 
                    data-target="#addModal" 
                    data-toggle="modal">
                    <?= get_filling_state($code) ?>
                  </a>
                </td>

              <?php } else { ?>

                <td><?= get_filling_state($code) ?></td>

              <?php } ?>

              <td>
                <a 
                  href="<?= base_url('form/'.'AF-'.$row->job_title_id.'-'.get_active_year().'-'.$row->head) ?>" 
                  class="btn btn-info">
                  <i class="fa fa-file-text-o"></i> Form
                </a>
              </td>
            </tr>
          <?php $no++; } ?>
        </tbody>
      </table>
    </div>
    <!-- /.box-body -->
  </div>
</section>

<script>
  function edit_state(code) {
    $.get('<?= base_url('assessment/get_form/') ?>' + code, function(res) {
      var data = JSON.parse(res);
      document.getElementById('dept').value = data.dept;
      document.getElementById('sect').value = data.sect;
      document.getElementById('job').value = data.job;
      document.getElementById('spv').value = data.spv;
      document.getElementById('code').value = code;
      document.getElementById('form-number').innerHTML = code;
    });
  }
</script>

<div id="addModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
  <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Change Filling State for Form <b id="form-number"></b></h4>
      </div>
      <form action="<?= base_url('assessment/change_state') ?>" method="post">
          <div class="modal-body">
            <div class="form-group">
              <label for="year">Department</label>
              <input type="text" name="dept" id="dept" value="" readonly="" class="form-control">
            </div>
            <div class="form-group">
              <label for="year">Section</label>
              <input type="text" name="sect" id="sect" value="" readonly="" class="form-control">
            </div>
            <div class="form-group">
              <label for="year">Job Title</label>
              <input type="text" name="job" id="job" value="" readonly="" class="form-control">
            </div>
            <div class="form-group">
              <label for="year">Superior</label>
              <input type="text" name="spv" id="spv" value="" readonly="" class="form-control">
            </div>
            <div class="form-group">
              <input type="hidden" value="" name="code" id="code">
              <label for="year">State</label>
              <select name="state" id="" class="form-control">
                <option value="" disabled="" selected=""></option>
                <?php foreach ($state as $value) : ?>
                  <option value="<?= $value->code ?>"><?= $value->code ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="modal-footer">
              <button type="submit" id="btnSubmit" class="btn btn-primary">Update</button>
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
      </form>
    </div>
  </div>
</div>

<div id="exportModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
  <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Select Department to Export</h4>
      </div>
      <form action="<?= base_url('assessment/export_all_form') ?>" method="post">
        <div class="modal-body">
          <div class="form-group">
            <label for="year">Department</label>
            <select name="department" class="form-control" id="" required="">
              <option value="" disabled="" selected=""></option>
              <?php foreach ($dept_list as $dept) : ?>
                <option value="<?= $dept->id ?>"><?= $dept->name ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
            <button type="submit" id="btnSubmit" class="btn btn-primary">Export</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>