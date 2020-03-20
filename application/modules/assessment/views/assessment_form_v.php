<?php $sess_login = $this->session->userdata('login_session'); ?>
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
	<h3 class="box-title">Form Penilaian | <?= $jobTitleName->name ?></h3>
	<!-- <ol class="breadcrumb">
		<h3 class="box-title pull-right">Waktu pengisian form penilaian skill 2 - 15 Maret 2020</h3>
	</ol> -->
</section>

<!-- Main content -->
<section class="content">
	<div class="box">
	<!-- /.box-header -->
		<div class="box-body">
			<form action="" method="">
            <a class="btn btn-default mr-2" href="<?= base_url('export_to_excel/'.$jobTitleName->id) ?>">
                <i class="fa fa-file-excel-o"></i> Export to Excel 
                </a>
                
                <!-- <a style="margin-left:5px;" class="btn btn-success mr-2" 
                        data-toggle="modal" 
                        data-target="#uploadForm" >
	                <i class="fa fa-upload"></i> Upload Form
	            </a> -->

                <!-- submit button just for participant user -->
                <?php if ($sess_login['group'] == 3 || $sess_login['group'] == 2) {
                    $dataButton = [
                        'statementAmount'    => $statementAmount,
                        'completeAssessment' => $completeAssessment,
                        'submitStatus'       => $submitStatus,
                        'jobtitle'           => $jobTitleName->id,
                        'department'         => $department
                    ];
                    $this->load->view('_partial/submit_button_v', $dataButton);
                } ?>

	            <hr>
                <?php $this->load->view('template/action_message'); ?>
                <div class="tableFixHead">
				<table class="table table-hover table-bordered">
					<thead>
                        <tr>
                            <th style="white-space:nowrap; vertical-align: middle;" rowspan="2">NIK</th>
                            <th style="white-space:nowrap; vertical-align: middle;" rowspan="2">Nama Pegawai</th>

                            <?php foreach ($dictionary->result() as $dictlist) : ?>
                                <th 
                                    data-toggle="modal"
                                    data-target="#descriptionCompetency" 
                                    class="header_competency"
                                    colspan="2"
                                    onclick="showCompetencyDescription('<?= $dictlist->id ?>')" 
                                >
                                    <?= strtoupper($dictlist->name_id)  ?>
                                </th>
                            <?php endforeach; ?>

                            <th style="white-space:nowrap; vertical-align: middle;" rowspan="2">Nilai Absolut</th>
                            <th style="white-space:nowrap; vertical-align: middle;" rowspan="2">Level</th>
                        </tr>
                        <div class="tableFicHeadR2">
                        <tr>

                            <?php for ($i = 0; $i < count($dictionary->result()); $i++) : ?>
                                <!-- edit poin just show if user is assessment participant -->
                                <?php if($sess_login['group'] == 3 && ($sess_login['level'] == 2 || $sess_login['level'] == 1)) : ?>
                                    <th style="text-align:center; position: sticky; top: 150px;">Isi Nilai</th>
                                <!-- see detail poin show if user admin/PA -->
                                <?php elseif ($sess_login['group'] == 1 || $sess_login['group'] == 2) : ?>
                                    <th style="text-align:center; position: sticky; top: 150px;">Detail Nilai</th>
                                <?php endif; ?>
                                <th style="text-align:center; position: sticky; top: 150px;">Nilai</th>
                            <?php endfor; ?>

                        </tr>
                        </div>
                    </thead>
                    <tbody>

                        <?php foreach ($employes->result() as $employe) : ?>
                            <tr>
                                <!-- red flag for uncomplete employe assessment -->
                                <?php 
                                    $isFullfilled = is_value_complete(count($dictionary->result()),$employe->nik,$active_year);
                                    if (!$isFullfilled) {
                                        $columnColor = 'background: #fabacf;';
                                        $info = 'data-toggle="tooltip" title="Penilaian untuk karyawan ini belum terisi penuh!"';
                                    } else {
                                        $columnColor = '';
                                        $info = '';
                                    }
                                 ?>

                                <td style="white-space:nowrap; <?= $columnColor ?>">
                                    <span <?=$info?>><?= $employe->nik; ?></span>
                                </td>
                                <td style="white-space:nowrap; <?= $columnColor ?>">
                                    <span <?=$info?>><?= $employe->name; ?></span>
                                </td>

                                <?php foreach ($dictionary->result() as $dicts) :

                                    /*=================================================
                                    =            Condition for Poin Button            =
                                    =================================================*/
                                    
                                    $data = [
                                        'skill'        => $dicts->skill_id,
                                        'nik'          => $employe->nik,
                                        'jobtitle'     => $employe->job_title_id,
                                        'isSubmited'   => $isSubmited,
                                        'submitStatus' => $submitStatus,
                                        'sectionId'    => $sectionId,
                                        'department'   => $department
                                    ];

                                    // if login as participant
                                    if ($sess_login['group'] == 3) :
                                        $this->load->view('_partial/button_poin_participant_v', $data);

                                    // if login as admin
                                    elseif ($sess_login['group'] == 1) :
                                        $this->load->view('_partial/button_poin_admin_v', $data);

                                    // if HR but not in his section
                                    elseif ($sess_login['group'] == 2 && $sess_login['section'] != $sectionId) :
                                        $this->load->view('_partial/button_poin_admin_v', $data);

                                    // if HR and in his section
                                    elseif ($sess_login['group'] == 2 && $sess_login['section'] == $sectionId) :
                                        $this->load->view('_partial/button_poin_hr_v', $data);

                                    // if HR and in his section
                                    elseif ($sess_login['group'] == 2 && $sess_login['level'] == 3 && $sess_login['department'] == $department) :
                                        $this->load->view('_partial/button_poin_hr_v', $data);
                                        
                                    endif;
                                    
                                    /*=====  End of Condition for Poin Button  ======*/
                                    
                                    // get assessment form to get its ID
                                    $assessmentForm = $this->db->where('nik', $employe->nik)
                                    							->like('code',$active_year,'before')
                                    							->get('assessment_forms');

                                    // its ID will use to get detail form question
                                    $formId = $assessmentForm->row()->id;

                                    $detailPoint = $this->db->query("SELECT * from assessment_form_questions ass 
                                    								JOIN skill_units un ON ass.skill_unit_id = un.id 
                                    								where un.id_dictionary = '".$dicts->skill_id."' 
                                    								AND poin IS NOT NULL 
                                    								AND ass.form_id = '".$formId."'");

                                    // count competency unit which have NOT NULL poin (for average point)
                                    $filledAssessment = count($detailPoint->result());

                                    // count amount of each unit competency
                                    $const = 0;
                                    foreach ($detailPoint->result() as $value) {
                                        $const = $const + ($value->weight / 5) * $value->poin;
                                    }

                                    // average point of each competency dictionary
                                    $averagePoint = $const; ?>

                                <td style="text-align:center">
                                    <input 
                                        type="text" 
                                        style='width:4em' 
                                        min='1' 
                                        class="pointof-<?= $formId ?>" 
                                        value="<?= $averagePoint ?>" 
                                        max='100' 
                                        readonly>
                                </td>
                                <?php endforeach; ?>

                                <input type="hidden" name="jobid" value="<?php $employe->job_title_id ?>">
                                <td style="text-align:center">
                                    <?php if ($assessmentForm->num_rows() < 1) : ?>
                                        <input 
                                            type='text' 
                                            name='absolutepoint' 
                                            style='width:4em' 
                                            min='1' 
                                            value="0" 
                                            max='100' 
                                            readonly>

                                    <?php elseif (is_null($assessmentForm->row()->total_poin)) : ?>
                                        <input 
                                            type='text' 
                                            name='absolutepoint' 
                                            id="<?= $employe->nik ?>" 
                                            style='width:4em' 
                                            min='1' 
                                            value="" 
                                            max='100' 
                                            readonly>
                                        <!-- js for count amount of poin of each employee -->
                                        <script>
                                            let inputBox_<?= $formId ?> = document.getElementsByClassName('pointof-<?=$formId?>');
                                            let constanta_<?= $formId ?> = 0;
                                            for (let i = 0; i < inputBox_<?= $formId ?>.length; i++) {
                                                constanta_<?= $formId ?> += parseFloat(inputBox_<?= $formId ?>[i].value);
                                            }
                                            document.getElementById('<?= $employe->nik ?>').value = constanta_<?= $formId ?>;
                                        </script>

                                    <?php else : ?>
                                        <input 
                                            type='text' 
                                            name='absolutepoint' 
                                            style='width:4em' 
                                            min='1' 
                                            value="<?= $assessmentForm->row()->total_poin ?>" 
                                            max='100' 
                                            readonly>

                                    <?php endif; ?>
                                </td>
                                <td style="text-align:center; vertical-align: middle;">
                                    <?= get_assessment_grade($assessmentForm->row()->total_poin) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
				</table>
                </div>
				<a href="<?= base_url('assessment') ?>" class="btn btn-primary pull-right"><i class="fa fa-chevron-left"></i> Back</a>
			</form>
		</div>
	</div>
</section>

<div id="addModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <form id="form-id" method="POST" action="<?= base_url('store_poin') ?>" onsubmit="return checkform(this);">
            <!-- Modal content-->
            <div class="modal-content" id="field-poin">

            </div>
        </form>
    </div>
</div>

<div id="infomodal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="title-mod">Assessment form information</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Rentang nilai untuk pengisian <i>assessment form</i> adalah 1 - 5.</p>
                <p>
                    Dimana:
                    <ul>
                        <li>1 = Sangat tidak baik</li>
                        <li>2 = Tidak baik</li>
                        <li>3 = Cukup</li>
                        <li>4 = Baik</li>
                        <li>5 = Sangat baik</li>
                    </ul>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="detailPoin" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="detailpoin-content">

        </div>
    </div>
</div>

<div id="descriptionCompetency" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="descriptionCompetency-content">

        </div>
    </div>
</div>

<div id="uploadForm" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="title-mod">Import Assessment Form | <?= $jobTitleName->name ?> </h4>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('form/upload') ?>" id="formUpload" enctype="multipart/form-data" method="post">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group">
                            <label for="inputFile">Import File</label>
                            <input type="file" name="userfile" id="inputFile" required>
                            <input type="hidden" name="job_title_id" value="<?= $job_title?>">
                            <input type="hidden" name="form_code" value="<?= $form_code?>">
                            <p class="help-block">Pastikan file yang di upload sesuai dengan form yg sedang aktif (<?= $jobTitleName->name ?>).</p>
                        </div>
                    </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success" id="submit">Upload</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
    </div>
</div>

<script>
    var pathArray = window.location.pathname.split('/');

    function loadCompetency(skill_id,nik,job_id) {
        $('#field-poin').load(location.origin + '/' + pathArray[1] + '/nik/'+nik+'/jobtitle/'+job_id+'/competency/'+skill_id+'/assessment');
    }

    function loadDetailPoin(skill_id,nik,jobid) {
        $('#detailpoin-content').load(location.origin + '/' + pathArray[1] +'/assessment/'+skill_id+'/competency/'+nik+'/nik/'+jobid+'/jobid');
    }

    function showCompetencyDescription($id) {
        $('#descriptionCompetency-content').load(location.origin + '/' + pathArray[1] + '/competency_description/' + $id)
    }
</script>
