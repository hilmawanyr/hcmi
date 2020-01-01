
<section class="content-header">
  <h3 class="box-title"><?= $pageTitle ?></h3>
</section>

<!-- Main content -->
<section class="content">
  <div class="box">
    <!-- /.box-header -->
    <div class="box-body">
      <a href="<?= base_url('dashboard') ?>" class="btn btn-info">Back</a>
      <hr>
      <table id="example1" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>No</th>
            <th>Name</th>
            <th>Job Title</th>
          </tr>
        </thead>
        <tbody>
          <?php $no=1; foreach ($employes as $employe) { ?>
            <tr>
              <td><?= $no ?></td>
              <td><?= $employe->name ?></td>
              <td><?= get_jobtitle_name($employe->job_title_id) ?></td>
            </tr>
          <?php $no++; } ?>
        </tbody>
      </table>
    </div>
    <!-- /.box-body -->
  </div>
</section>