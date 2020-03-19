<style>
  .ui-autocomplete {
    z-index: 9999 !important;
  }
</style>

<section class="content-header">
    <h3 class="box-title">Users <small>Manage users here.</small></h3>
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
                  <th>LDAP ID</th>
                  <th>e-Mail</th>
                  <th>Group</th>
                  <th>level</th>
                  <th width="70">Action</th>
                </tr>
            </thead>
            <tbody>
              <?php $no=1; foreach ($users as $user) : ?>
                <tr>
                  <td><?= $no ?></td>
                  <td><?= $user->nik ?></td>
                  <td><?= $user->name ?></td>
                  <td><?= $user->ldap_uid ?></td>
                  <td><?= $user->email ?></td>
                  <td><?= $user->group_name ?></td>
                  <td><?= $user->level ?></td>
                  <td>
                    <button
                      class="btn btn-warning"
                      data-toggle="modal"
                      data-target="#actionModal"
                      onclick="action('<?= $user->nik ?>','1')">
                      <i class="fa fa-pencil"></i>
                    </button>
                    <a
                      href="<?= base_url('users/'.$user->nik.'/remove') ?>"
                      class="btn btn-danger"
                      onclick="return confirm('Are you sure want to remove this user?')">
                      <i class="fa fa-trash"></i>
                    </a>
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
      <form action="<?= base_url('users/store') ?>" id="formAction" method="post">
        <div class="modal-body">
          <input type="hidden" id="isUpdate" name="isUpdate" value="">
          <div class="form-group">
            <label for="nik">NIK</label>
            <input type="text" class="form-control" id="nik" value="" name="nik" required="">
          </div>
          <div class="form-group">
            <label for="ldap">LDAP ID</label>
            <input 
              type="text" 
              class="form-control" 
              id="ldap" 
              value="" 
              name="ldap" 
              onkeypress="return isNumber(event)" 
              required="">
          </div>
          <div class="form-group">
            <label for="group">Group</label>
            <select name="group" class="form-control" style="width: 100%" id="group" required="">
              <option value="" disabled="" selected=""></option>
              <?php foreach ($groups as $group) : ?>
                <option value="<?= $group->id ?>"><?= $group->name ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="level">Level</label>
            <select name="level" class="form-control" style="width: 100%" id="level" required="">
              <option value="" disabled="" selected=""></option>
              <?php for ($level = 1; $level < 6; $level++) : ?>
                <option value="<?= $level ?>"><?= $level ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="jobtitle">e-Mail</label>
            <input type="email" class="form-control" id="email" value="" name="email" required="">
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
      $('.modal-title').text('Add User');
      $('#btnSubmit').text('Save');
      $('#isUpdate, #nik, #ldap, #email, #group, #level').val('');
      // $('#section, #position, #jobtitle, #grade').val(null).trigger('change');

    } else {
      $('.modal-title').text('Edit User');
      $('#btnSubmit').text('Update');
      $.get('<?= base_url() ?>users/'+id+'/detail', function(response) {
        var user = JSON.parse(response)
        $('#nik').prop({'readonly': true, 'value':user.nik + ' - ' + user.name});
        $('#isUpdate').val(user.nik);
        $('#ldap').val(user.ldap);
        $('#group').val(user.group);
        $('#level').val(user.level);
        $('#email').val(user.email);
      })
    }
  }

  $('#nik').autocomplete({
    source: '<?= base_url('manage/users/get_employe');?>',
    minLength: 3,
    select: function (evt, ui) {
      this.form.nik.value = ui.item.value;
    }
  });
  
  function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
  }
</script>