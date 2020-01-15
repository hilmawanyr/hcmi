<section class="content-header">
    <h3 class="box-title">Restricted Information<small> List of all restricted information.</small></h3>
</section>

<!-- Main content -->
<section class="content">
  <div class=" ">
    <!-- /.box-header -->
    <div class="">
      
      <?php foreach ($informations as $information) : ?>
      <div class="col-md-4 col-xs-12 bg-gray callout">
        <h4>
          <?= $information->title ?> 
          <small style="color: gray"><i class="fa fa-clock-o"></i> <?= $information->created_at ?></small>
        </h4>
        <?= substr($information->content, 0, 50) ?> 
        <br>[<a href="<?= base_url('information/'.$information->id.'/detail') ?>" style="color: gray">Read more...</a>]
      </div>
      <?php endforeach; ?>

    </div>
    <!-- /.box-body -->
  </div>
</section>