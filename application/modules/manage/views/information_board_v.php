<section class="content-header">
    <h3 class="box-title">Information Board<small> Manage information board</small></h3>
    <?php $this->load->view('template/action_message'); ?>
</section>

<!-- Main content -->
<section class="content">
  <div class="box ">
    <!-- /.box-header -->
    <div class="box-body ">
      <a class="btn btn-success" href="<?= base_url('infomation/create') ?>"><i class="fa fa-plus"></i> Create Information</a>
      <hr>
      <table class="table table-hover table-bordered" id="example1">
        <thead>
          <tr>
            <th>No</th>
            <th>Title</th>
            <th>Type</th>
            <th>Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php $no=1; foreach ($informations as $information) : ?>
            <tr>
              <td><?= $no ?></td>
              <td>
                <span data-toogle="tooltip" title="<?= $information->title ?>">
                  <?= $information->title ?></span>
                </td>
              <td><?= $information->type ?></td>
              <td><?= $information->created_at ?></td>
              <td>
                <a 
                  href="<?= base_url('information/'.$information->id.'/detail') ?>" 
                  data-toggle="tooltip" 
                  title="see detail" 
                  class="btn btn-primary btn-sm">
                  <i class="fa fa-eye"></i>
                </a>
                <a 
                  href="<?= base_url('information/'.$information->id.'/delete') ?>" 
                  onclick="return confirm('Are You sure want to delete this data?')"
                  data-toggle="tooltip" 
                  title="delete information" 
                  class="btn btn-danger btn-sm">
                  <i class="fa fa-trash"></i>
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