<div class="col-md-12 col-xs-12">
  <div class="box box-default">
    <div class="box-header with-border">
      <i class="fa fa-bullhorn"></i>
      <h3 class="box-title">Informations</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      <?php foreach ($informations as $information) : ?>
      <div class="col-md-3 col-xs-12 bg-disabled callout">
        <h4>
          <?= $information->title ?> 
          <small style="color: gray"><i class="fa fa-clock-o"></i> <?= $information->created_at ?></small>
        </h4>
        <?= substr($information->content, 0, 50) ?> [<a href="<?= base_url('information/'.$information->id.'/detail') ?>" style="color: gray">Read more...</a>]
      </div>
      <?php endforeach; ?> 
    </div>
    <!-- /.box-body -->
    <div class="box-footer text-center">
      <a href="<?=  base_url('information/all') ?>" class="uppercase">View All Information</a>
    </div>
    <!-- /.box-footer -->
  </div>
</div>