<section class="content-header">
  <h3 class="box-title">Department <small>Manage your department here!</small></h3>
  <?php $this->load->view('template/action_message'); ?>
</section>

<section class="content">
  
  <div class="box">
    <div class="box-body">
      <div class="table-wrap">
        <div class="table-responsive">
          <button 
              data-toggle="modal" 
              data-target="#actionModal"
              onclick="edit('','')" 
              class="btn btn-info">
              <i class="fa fa-plus"></i> Add Department
          </button>
          <hr>
          <table class="table table-hover table-bordered" id="example1">
            <thead>
              <tr>
                  <th>No</th>
                  <th>Name</th>
                  <th width="140">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php $no=1; foreach ($departments as $department) : ?>
                <tr>
                  <td><?= $no ?></td>
                  <td><?= $department->name ?></td>
                  <td>
                      <!-- <button
                        data-toggle="modal"
                        data-target="#actionModal"
                        onclick="edit(1,<?= $department->id ?>)"
                        class="btn btn-default"><i class="fa fa-edit"></i>
                      </button> -->
                      <a
                        href="<?= base_url('department/'.$department->id.'/section') ?>"
                        class="btn btn-default"><i class="fa fa-plus"></i>
                      </a>
                  </td>
                </tr>
              <?php $no++; endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  
</section>

<div id="actionModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"></h4>
        </div>
        <form action="<?= base_url('department/store') ?>" method="post">
            <div class="modal-body">
              <input type="hidden" name="isUpdate" id="isUpdate" value="">
              <div class="form-group">
                <label for="yearEdit">Name</label>
                <input 
                  type="text" 
                  class="form-control" 
                  id="name"
                  name="name" 
                  required="">
              </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="btnSubmit" class="btn btn-primary"></button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>
  </div>
</div>

<script>
  function edit (isUpdate, id) {
    if (isUpdate === '') {
      $('.modal-title').text('Add Department')
      $('#btnSubmit').text('Save')
      $('#name').val('')
      $('#isUpdate').val('')

    } else {
      $.get('<?= base_url() ?>department/' + id + '/detail', function(response) {
        var responseData = JSON.parse(response)
        $('.modal-title').text('Edit Department')
        $('#btnSubmit').text('Update')
        $('#name').val(responseData.name)
        $('#isUpdate').val(responseData.id)
      })
    }
  }
</script>