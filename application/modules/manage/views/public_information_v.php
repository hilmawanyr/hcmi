<section class="content-header">
  <a href="<?= base_url() ?>" class="btn btn-warning pull-right"><i class="fa fa-chevron-left"></i> Back</a>
  <h1>
    <?= $information->title ?>
    <small><i class="fa fa-clock-o"></i> published date <?= $information->created_at ?></small>
  </h1>
</section>
<br>
<!-- Main content -->
<section class="content">
  <div class="box">
    <div class="box-body"><?= $information->content ?></div>
  </div>
</section>
