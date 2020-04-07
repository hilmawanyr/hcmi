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
          <?php foreach ($jobtitleList as $row) { ?>
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
              <td><?= get_filling_state('AF-'.$row->job_title_id.'-'.get_active_year().'-'.$row->head) ?></td>
              <td>
                <a 
                  href="<?= base_url('form/'.'AF-'.$row->job_title_id.'-'.get_active_year().'-'.$row->head) ?>" 
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