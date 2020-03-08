<?php error_reporting(0); ?>

<section class="content-header">
  <?php if ($this->session->userdata('login_session')['group'] == 3) { ?>

    <!-- if AM or SAM -->
    <?php if ($position == 7 || $position == 8) { ?>

      <h3 class="box-title">Department : <?= get_department_by_section($section)->name; ?></h3>
      <ol class="breadcrumb">
        <h3 class="box-title pull-right">Section : <?= get_section($section)->name ?></h3>
      </ol>
    
    <!-- if GM or higher -->
    <?php } elseif ($position > 8) { ?>
      
      <h3 class="box-title">Department : <?= get_department($department); ?></h3>

    <?php } ?>
    
  <?php } else { ?>
    <h3 class="box-title">Job Title List</h3>
  <?php } ?>
</section>

<!-- Main content -->
<section class="content">
  <div class="box">
    <!-- /.box-header -->
    <div class="box-body">
      <table id="example1" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>Grade</th>
            <th>Job Title</th>
            <th>Employes</th>

            <!-- if user is not a participant -->
            <?php if ($this->session->userdata('login_session')['group'] != 3) { ?>
            <th>Section</th>
            <th>Department</th>
            <?php } ?>
            <!-- end if -->

            <th>Percentage of Filling</th>
            <th>Filled By</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($jobtitleList as $row) { ?>
            <tr>
              <td><?= convert_to_roman($row->grade) ?></td>
              <td><?= $row->jobtitleName ?></td>
              <td><?= $row->numberOfPeople ?></td>

              <!-- if user is not a participant -->
              <?php if ($this->session->userdata('login_session')['group'] != 3) { ?>
              <td><?= get_section($row->section_id)->name ?></td>
              <td><?= get_department(get_section($row->section_id)->dept_id) ?></td>
              <?php } ?>
              <!-- end if -->

              <td><?= is_form_complete($row->job_title_id) ?> %</td>
              <td></td>
              <td>
                <a 
                  href="<?= base_url('form/'.$row->job_title_id.'/jobtitle/'.$row->grade.'/grade') ?>" 
                  class="btn btn-info">
                  <i class="fa fa-file-text-o"></i> Form
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