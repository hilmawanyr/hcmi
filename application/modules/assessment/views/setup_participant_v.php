<?php error_reporting(0); ?>
<section class="content-header">
  <h3 class="box-title">Set up Relation <small>Matching subordinate with their supervisor</small></h3>
  <?php $this->load->view('template/action_message'); ?>
</section>
<!-- Main content -->
<section class="content">
  <div class="box">
    <!-- /.box-header -->
    <div class="box-body">
      <a class="btn btn-warning pull-right" href="<?= base_url('assessment/form_list') ?>">
        <i class="fa fa-chevron-left"></i> Back
      </a>
      <table>
        <tr>
          <td width="120"><b>Department</b></td>
          <td width="40">:</td>
          <td><?= get_department($detail['dept']) ?></td>
        </tr>
        <tr>
          <td><b>Section</b></td>
          <td>:</td>
          <td><?= get_section($detail['sect'])->name ?></td>
        </tr>
        <tr>
          <td><b>Supervisor</b></td>
          <td>:</td>
          <td><?= $detail['spv'] ?></td>
        </tr>
      </table>
      <hr>
      <form action="<?=  base_url('assessment/create_employee_relation') ?>" method="post">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>No</th>
              <th>Employee</th>
              <th>Job Title</th>
              <th style="text-align: center;">Choose</th>
            </tr>
          </thead>
          <tbody>
            <?php $no = 1; foreach ($employees as $employee) : ?>

              <!-- apakah job title ini memiliki kompetensi -->
              <?php 
              if (is_null($employee->job_title_id)) {
                $sign = '';
                $message = '';
              } else {
                $is_jobtitle_has_competency = $this->assessment->get_competency($employee->job_title_id)->num_rows();
                $sign = $is_jobtitle_has_competency < 1 ? 'style="background:#ffb9a8"' : '';
                $message = $is_jobtitle_has_competency < 1 ? 'data-toggle="tooltip" title="Pegawai dengan job title ini belum memiliki kompetensi penilaian"' : '';
              } ?>

              <tr <?= $message ?> >
                <td <?= $sign ?> ><?= $no ?></td>
                <td <?= $sign ?> ><?= $employee->name ?></td>
                <td <?= $sign ?> ><?= get_jobtitle_name($employee->job_title_id) ?></td>
                <td style="text-align: center;" <?= $sign ?> >
                  <?php $is_employe_has_spv = $this->db->get_where('employee_relations', ['nik' => $employee->nik])->num_rows(); ?>
                  <input 
                    type="checkbox" 
                    name="employes[]" 
                    value="<?= $employee->nik ?>"
                    <?= $is_employe_has_spv > 0 ? 'checked=""' : ''; ?> />
                  <input type="hidden" name="spv" value="<?= explode(' - ', $detail['spv'])[0] ?>" />
                  <input type="hidden" name="jobtitle[<?= $employee->nik ?>]" value="<?= $employee->job_title_id ?>">
                </td>
              </tr>
            <?php $no++; endforeach; ?>
          </tbody>
        </table>
        <hr>
        <button type="submit" class="btn btn-success pull-right">Set up and Create Form</button>
      </form>
    </div>
    <!-- /.box-body -->
  </div>
</section>