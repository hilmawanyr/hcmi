<?php $sess_login = $this->session->userdata('login_session'); ?>

<?php
// var_dump($sections);die();
?>

<style>
    .header_competency {
        text-align:center; 
        vertical-align: middle !important; 
        cursor: pointer; 
        width: 300px; 
        height: 150px !important;
    }

    .header_competency:hover {
        background-color: #ecf0f5;
    }
</style>

<section class="content-header">
	<h3 class="box-title"> | Reset Assessment Form</h3>
</section>

<!-- Main content -->
<section class="content">
	<div class="box">
	<!-- /.box-header -->
		<div class="box-body">
        <?php $this->load->view('template/action_message'); ?>
            <form method="post" action="<?= base_url('assessment/reset/submit') ?>">
                <div class="form-group">
                    <label for="section">Section</label>
                    <select name="section" class="form-control" id="section" required="required">
                        <?php
                            foreach($sections as $data){
                                echo "<option value='".$data->id."'>".$data->name."</option>";
                            }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="job">Job Title</label>
                    <select name="job" class="form-control" id="job" required="required">
                    
                    </select>
                </div>
                <div class="form-group" id="position">
                <!-- <label for="positionOpt">Position</label>
                <select name="position" class="form-control" id="positionOpt">
                    <option value="" selected="" disabled=""></option>
                    <?php foreach ($positions as $position) : ?>
                    <option value="<?= $position->id ?>"><?= $position->name ?></option>
                    <?php endforeach; ?>
                </select> -->
                </div>
                <button class="btn btn-danger pull-right" type="submit"><i class="fa fa-save"></i> Assessment Reset</button>
            </form>
		</div>
	</div>
</section>


<script>
  $(document).ready(function(){ // Ketika halaman sudah siap (sudah selesai di load)
    
    $("#section").change(function(){ // Ketika user mengganti atau memilih data provinsi
      $("#job").hide(); // Sembunyikan dulu combobox kota nya
    
      $.ajax({
        type: "POST", // Method pengiriman data bisa dengan GET atau POST
        url: "<?php echo base_url("assessment/reset/list/job"); ?>", // Isi dengan url/path file php yang dituju
        data: {id_section : $("#section").val()}, // data yang akan dikirim ke file yang dituju
        dataType: "json",
        beforeSend: function(e) {
          if(e && e.overrideMimeType) {
            e.overrideMimeType("application/json;charset=UTF-8");
          }
        },
        success: function(response){ // Ketika proses pengiriman berhasil
          // lalu munculkan kembali combobox job
          $("#job").html(response.list_job).show();
        },
        error: function (xhr, ajaxOptions, thrownError) { // Ketika ada error
          alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError); // Munculkan alert error
        }
      });
    });
  });
  </script>