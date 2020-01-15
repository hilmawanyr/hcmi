<section class="content-header">
	<a href="<?= base_url() ?>" class="btn btn-warning pull-right"><i class="fa fa-chevron-left"></i> Back</a>
	<h1>
		Public Information
		<small><i class="fa fa-globe"></i> list of all informations</small>
	</h1>
</section>
<br>
<!-- Main content -->
<section class="content ">
	<div class="row">
	<?php foreach ($informations as $information) { ?>
		<div class="col-md-4 col-sm-2 col-xs-12">
			<div class="box">
				<div class="box-header with-border">
				  <h3 class="box-title"><?= $information->title ?> 
				  <small><i class="fa fa-clock-o"></i> <?= $information->created_at ?></small></h3>
				</div>
				<div class="box-body">
				  <?= substr($information->content, 0, 50) ?> [...]
				</div>
				<div class="box-footer">
				  <a href="<?= base_url('information/'.$information->id.'/public') ?>">Read more...</a>
				</div>
			</div>
		</div>
		
	<?php } ?>
	</div>
</section>
