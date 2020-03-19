<style>
  .ui-autocomplete {
    z-index: 9999 !important;
  }
</style>

<section class="content-header">
    <h3 class="box-title">Employes <small>Manage employes here.</small></h3>
    <?php $this->load->view('template/action_message'); ?>
</section>

<!-- Main content -->
<div class="row">
  <div class="col-md-12">
    <section class="content">
      <div class="box ">
        <!-- /.box-header -->
        <div class="box-body ">
          <button 
            class="btn btn-info" 
            data-toggle="modal" 
            data-target="#actionModal"
            onclick="action('','')">
            <i class="fa fa-plus"></i> Add Data
          </button>
          <hr>
          <table class="table table-hover table-bordered" id="example1">
            <thead>
                <tr>
                  <th>No</th>
                  <th>NIK</th>
                  <th>Name</th>
                  <th>Section</th>
                  <th>Position</th>
                  <th>Job Title</th>
                  <th>Grade</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
            </thead>
            <tbody>
              <?php $no=1; foreach ($employes as $employe) : ?>
                <tr>
                  <td><?= $no ?></td>
                  <td><?= $employe->nik ?></td>
                  <td><?= $employe->name ?></td>
                  <td><?= $employe->section ?></td>
                  <td><?= $employe->position ?></td>
                  <td><?= $employe->jobtitle ?></td>
                  <td><?= $employe->grade ?></td>
                  <td>
                    <span class="badge <?= is_null($employe->deleted_at) ? "bg-green" : "bg-red"; ?>">
                      <?= is_null($employe->deleted_at) ? "Active" : "Nonactive"; ?>
                    </span>
                  </td>
                  <td>
                    <button
                      class="btn btn-warning"
                      data-toggle="modal"
                      data-target="#actionModal"
                      onclick="action('<?= $employe->nik ?>','1')">
                      <i class="fa fa-pencil"></i>
                    </button>
                    <!-- <a
                      class="btn <?= is_null($employe->deleted_at) ? 'btn-primary' : 'btn-default' ?>"
                      href="<?= base_url('employe/'.$employe->nik.'/set_status') ?>"
                      onclick="return confirm('Are You sure to change this employee status?')">
                      <i class="fa <?= is_null($employe->deleted_at) ? 'fa-toggle-on' : 'fa-toggle-off' ?>"></i>
                    </a> -->
                  </td>
                </tr>
              <?php $no++; endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </div>
</div>

<div id="actionModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"></h4>
      </div>
      <form action="<?= base_url('employe/store') ?>" id="formAction" method="post">
        <div class="modal-body">
          <input type="hidden" id="isUpdate" name="isUpdate" value="">
          <div class="form-group">
            <label for="jobtitle">NIK</label>
            <input type="text" class="form-control" id="nik" value="" name="nik" required="">
            <input type="hidden" name="hidden_nik" value="" id="hidden_nik">
          </div>
          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" value="" name="name" required="">
          </div>
          <div class="form-group">
            <label for="section">Section</label>
            <select name="section" class="form-control select2" style="width: 100%" id="section" required="">
              <option value="" disabled="" selected=""></option>
              <?php foreach ($sections as $section) : ?>
                <option value="<?= $section->id ?>"><?= $section->name ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="position">Position</label>
            <select name="position" class="form-control select2" style="width: 100%" id="position" required="">
              <option value="" disabled="" selected=""></option>
              <?php foreach ($positions as $position) : ?>
                <option value="<?= $position->id ?>"><?= $position->name ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="jobtitle">Job Title</label>
            <select name="jobtitle" class="form-control select2" style="width: 100%" id="jobtitle" required="">
              <option value="" disabled="" selected=""></option>
            </select>
          </div>
          <div class="form-group">
            <label for="jobtitle">Grade</label>
            <select name="grade" class="form-control" style="width: 100%" id="grade" required="">
              <option value="" disabled="" selected=""></option>
              <?php for ($i=1; $i < 8; $i++) : ?>
                <option value="<?= $i ?>"><?= $i ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="jobtitle">Head</label>
            <input type="text" class="form-control" id="head" value="" name="head" required="">
          </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" id="btnSubmit"></button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>

  </div>
</div>

<script>
  function action(id, activity) {
    if (activity === '') {
      $('.modal-title').text('Add Employe');
      $('#btnSubmit').text('Save');
      $('#isUpdate, #nik, #name, #head').val('');
      $('#section, #position, #jobtitle, #grade').val(null).trigger('change');

    } else {
      $('.modal-title').text('Edit Employe');
      $('#btnSubmit').text('Update');
      $.get('<?= base_url() ?>employe/'+id+'/detail', function(response) {
        var employe = JSON.parse(response)
        $('#isUpdate').val(employe.id);
        $('#nik').val(employe.nik);
        $('#hidden_nik').val(employe.nik);
        $('#name').val(employe.name);
        $('#section').val(employe.section).trigger('change');
        $('#position').val(employe.position).trigger('change');
        $('#jobtitle').val(employe.jobtitle).trigger('change');
        $('#grade').val(employe.grade);
        $('#head').val(employe.head);
      })
    }
  }

  $(document).ready(function($) {
    $('#section').change(function() {
      $.get('<?= base_url('manage/employes/get_jobtitle/') ?>' + $(this).val() +'/'+ $('#position').val(),
        {}, 
        function(response) {
          $('#jobtitle').html(response)
      })
    });

    $('#position').change(function() {
      $.get('<?= base_url('manage/employes/get_jobtitle/') ?>' + $('#section').val() +'/'+ $(this).val(),
        {}, 
        function(response) {
          $('#jobtitle').html(response)
      })
    })

    $('#head').autocomplete({
      source: '<?= base_url('manage/employes/get_employe');?>',
      minLength: 3,
      select: function (evt, ui) {
        this.form.head.value = ui.item.value;
      }
    });
  });
</script>