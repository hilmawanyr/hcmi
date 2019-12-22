<section class="content-header">
    <h3 class="box-title">Competency Dictionary <small>of <?= $dictionary->name_id ?></small></h3>
</section>

<!-- Main content -->
<div class="row">
  <div class="col-md-12">
    <section class="content col-md-4">
      <div class="box ">
        <!-- /.box-header -->
        <div class="box-body ">
          <div class="form-group">
            <label for="name_id">Indonesian Name</label>
            <input type="text" class="form-control" id="name_id" value="<?= $dictionary->name_id ?>" disabled/>
          </div>
          <div class="form-group">
            <label for="name_en">English Name</label>
            <input type="text" class="form-control" id="name_en" value="<?= $dictionary->name_en ?>" disabled/>
          </div>
          <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control" rows="6" disabled=""><?= $dictionary->description ?></textarea>
          </div>
          <div class="form-group">
            <label for="type">Competency Type</label>
            <input type="text" class="form-control" id="type" value="<?= get_skill_type_name($dictionary->skill_group) ?>" disabled />
          </div>
          <button class="btn btn-primary" type="button" onclick="history.go(-1)">Back</button>
        </div>
        <!-- /.box-body -->
      </div>
    </section>

    <section class="content col-md-8">
      <div class="box ">
        <!-- /.box-header -->
        <div class="box-body ">
          <a href="<?= base_url('skill_unit/'.$dictionary->id.'/print') ?>" class="btn btn-primary"><i class="fa fa-print"></i> Print</a>
          <hr>
          <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Level</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach ($skillUnit as $unit) : ?>
                    <tr>
                        <td><?= $unit->level ?></td>
                        <td><?= $unit->description ?></td>
                    </tr>
                <?php $no++; endforeach; ?>
            </tbody>
          </table>
        </div>
        <!-- /.box-body -->
      </div>
    </section>
  </div>
</div>