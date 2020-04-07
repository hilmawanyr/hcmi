<section class="content-header">
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
          <td><b>Superior</b></td>
          <td>:</td>
          <td><?= $detail['spv'] ?></td>
        </tr>
      </table>
      <hr>
      <table id="example1" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>Employee</th>
            <th>Job Title</th>
            <th>Grade</th>
            <th>Position</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1; foreach ($result as $row) : ?>
            <tr>
              <td><?= $row->name ?></td>
              <td><?= get_jobtitle_name($row->job_title_id) ?></td>
              <td><?= $row->grade ?></td>
              <td><?= get_position_name($row->position_id) ?></td>
            </tr>
          <?php $no++; endforeach; ?>
        </tbody>
      </table>
    </div>
    <!-- /.box-body -->
  </div>
</section>