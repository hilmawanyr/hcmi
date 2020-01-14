<section class="content-header">
  <h1>
    Authentication Log
    <small>Detail of authentication login</small>
  </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="box">
        <div class="box-body">
            <a href="<?=  base_url('print_auth_log') ?>" class="btn btn-primary pull-right">
                <i class="fa fa-print"></i> Print all log
            </a>
            <a href="<?= base_url() ?>" class="btn btn-warning"><i class="fa fa-chevron-left"></i> Back</a>
            <hr>
            <table class="table table-bordered table-stripped" id="example1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIK</th>
                        <th>Name</th>
                        <th>Last Login</th>
                        <th>Print</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; foreach ($logs as $log) : ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= $log->nik ?></td>
                            <td><?= user_name($log->nik) ?></td>
                            <td><?= $log->last_login ?></td>
                            <td>
                                <a href="<?= base_url('print_auth_log/').$log->nik ?>" class="btn btn-primary"><i class="fa fa-print"></i></a>
                            </td>
                        </tr>
                    <?php $no++; endforeach ?>
                </tbody>
            </table>    
        </div>
    </div>
    <!-- /.box -->
</section>