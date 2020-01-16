<!-- prevent error when page used on edit mode -->
<?php error_reporting(0); ?>

<section class="content-header">
    <h3 class="box-title">Create Information<small> Create new information here.</small></h3>
</section>

<!-- Main content -->
<section class="content">
  <div class="box ">
    <!-- /.box-header -->
    <div class="box-body ">
      <form method="post" action="<?= base_url('information/store') ?>">
        <div class="form-group">
          <label for="title">Title</label>
          <input type="text" class="form-control" id="title" value="<?= $information->title ?>" placeholder="insert information title" name="title" required="">
        </div>
        <input type="hidden" name="is_update" value="<?= $isUpdate ?>">
        <div class="form-group">
          <label for="type">Type</label>
          <select name="type" class="form-control" id="type" required="">
            <option value=""></option>
            <option <?= $information->type == 'PUBLIC' ? 'selected=""' : NULL ?> value="PUBLIC">Public</option>
            <option <?= $information->type == 'RESTRICTED' ? 'selected=""' : NULL ?> value="RESTRICTED">Restricted</option>
          </select>
        </div>
        <div class="form-group" id="position">
          <label for="positionOpt">Position</label>
          <select name="position" class="form-control" id="positionOpt">
            <option value="" selected="" disabled=""></option>
            <?php foreach ($positions as $position) : ?>
              <option value="<?= $position->id ?>"><?= $position->name ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <textarea id="summernote" name="content"><?= $information->content ?></textarea>

        <?php if ($isUpdate == 1) { ?>
          <a class="btn btn-warning" href="<?= base_url('information/'.$isUpdate.'/detail') ?>"><i class="fa fa-chevron-left"></i> Back</a>
        <?php } else { ?>
          <a class="btn btn-warning" href="<?= base_url('information') ?>"><i class="fa fa-chevron-left"></i> Back</a>
        <?php } ?>

        <button class="btn btn-primary pull-right" type="submit"><i class="fa fa-save"></i> Save</button>
      </form>
    </div>
    <!-- /.box-body -->
  </div>
</section>

<script>
  $('#position').hide();
  /**
  <?php if ($information->type == 'PUBLIC' && $isUpdate == 1) { ?>
    $('#position').hide();
  <?php } elseif ($information->type == 'RESTRICTED' && $isUpdate == 1) { ?>
    $('#position').show();
  <?php } else  { ?>
    $('#position').hide();
  <?php } ?>
  
  $('#type').on('change',function(){
    var type = $(this).val();
    if (type === 'PUBLIC') {
      $('#positionOpt').val('');
      $('#position').hide('fast');
    } else {
      $('#position').show('fast');
    }
  }) */
</script>