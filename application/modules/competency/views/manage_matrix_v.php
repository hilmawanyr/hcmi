<?php $userSession = $this->session->userdata('login_session'); ?>
<section class="content-header">
    <h3 class="box-title"><?= $jobtitle->jobtitle_name; ?> <small>Competency matrix</small></h3>
    <?php $this->load->view('template/action_message'); ?>
</section>

<!-- Main content -->
<div class="row">
  <div class="col-md-12">
    <section class="content col-md-4">
      <div class="box ">
        <!-- /.box-header -->
        <div class="box-body ">
          <div class="form-group">
            <label for="name">Department</label>
            <input 
              type="text" 
              class="form-control"
              value="<?= $jobtitle->department_name; ?>" 
              readonly="">
          </div>

          <div class="form-group">
            <label for="name">Section</label>
            <input 
              type="text" 
              class="form-control"
              value="<?= $jobtitle->section_name; ?>" 
              readonly="">
          </div>

          <div class="form-group">
            <label for="name">Job Title</label>
            <input 
              type="text" 
              class="form-control"
              value="<?= $jobtitle->jobtitle_name; ?>" 
              readonly="">
          </div>

          <div class="form-group">
            <label for="name">Position</label>
            <input 
              type="text" 
              class="form-control"
              value="<?= $jobtitle->position_name; ?>" 
              readonly="">
          </div>

          <div class="form-group">
            <label for="name">Grade</label>
            <input 
              type="text" 
              class="form-control"
              value="<?= $jobtitle->position_grade; ?>" 
              readonly="">
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
          <?php if ($userSession['group'] != 3 && ($userSession['level'] != 2 || $userSession['level'] != 3)) { ?>
            <button 
              class="btn btn-info" 
              data-toggle="modal" 
              data-target="#actionModal"
              onclick="action('','')">
              <i class="fa fa-plus"></i> Add Data
            </button>
          <?php }  ?>
          <hr>
          <table class="table table-hover table-bordered" id="example1">
            <thead>
                <tr>
                  <th>Level</th>
                  <th>Name</th>
                  <th>Description</th>
                  <?php if ($userSession['group'] != 3 && ($userSession['level'] != 2 || $userSession['level'] != 3)) { ?>
                    <th>Delete</th>
                  <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach ($matrixes as $matrix) : ?>
                    <tr>
                      <td><?= $matrix->level ?></td>
                      <td><?= $matrix->name ?></td>
                      <td><?= $matrix->description ?></td>
                      <?php if ($userSession['group'] != 3 && ($userSession['level'] != 2 || $userSession['level'] != 3)) { ?>
                        <th>
                          <a 
                            class="btn btn-danger" 
                            onclick="return confirm('Are you sure want to delete this data?')"
                            href="<?= base_url('competency_matrix/'.$matrix->matrix_id.'/remove/'.$jobtitle_id.'/jobtitle') ?>">
                            <i class="fa fa-trash"></i>
                          </a>
                        </th>
                      <?php } ?>
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

<div id="actionModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Add Competency</h4>
        </div>
        <form action="<?= base_url('competency_matrix/store_competency') ?>" id="formAction" method="post">
            <div class="modal-body">
              <div class="row">
                <div class="col-md-2">
                  <input type="hidden" name="jobtitle" value="<?= $jobtitle_id ?>">
                  <div class="form-group">
                    <label for="level">Level</label>
                    <select name="level" id="" class="form-control select2" style="width: 100%" required="">
                      <?php for ($i = 1; $i < 6; $i++) : ?>
                        <option value="<?= $i ?>"><?= $i ?></option>
                      <?php endfor; ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-10">
                  <div class="form-group">
                    <label for="competency">Competency</label>
                    <select name="competency" id="" class="form-control select2" style="width: 100%" required="">
                      <?php foreach ($competencies as $competency) : ?>
                        <option value="<?= $competency->id ?>"><?= $competency->name_id ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" id="btnSubmit">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>

  </div>
</div>