<?php $sess_login = $this->session->userdata('login_session');
if ($sess_login['level'] == 1) {
    if ($submitStatus < 1) { ?>
        <td style="text-align: center">
            <button 
                class='btn btn-sm btn-default' 
                type="button" 
                title='input nilai' 
                data-toggle='modal' 
                data-target='#addModal' 
                onclick="loadCompetency('<?= $skill ?>','<?= $nik ?>','<?= $jobtitle ?>')">
                <i class='fa fa-pencil'></i>
            </button>
        </td>

    <?php } else { ?>
        <td style="text-align: center">
            <button 
                class="btn btn-sm" 
                type="button" 
                data-toggle="modal" 
                data-target="#detailPoin" 
                onclick="loadDetailPoin('<?= $skill ?>','<?=$nik ?>','<?= $jobtitle ?>')" 
                title="Lihat detail nilai">
                <i class="fa fa-eye"></i>
            </button>
        </td>
        
    <?php } ?>
<?php } ?>

<!-- manager -->
<?php if ($sess_login['level'] == 2) {
    if ($submitStatus == 1) { ?>
        <td style="text-align: center">
            <button 
                class='btn btn-sm btn-default' 
                type="button" 
                title='input nilai' 
                data-toggle='modal' 
                data-target='#addModal' 
                onclick="loadCompetency('<?= $skill ?>','<?= $nik ?>','<?= $jobtitle ?>')">
                <i class='fa fa-pencil'></i>
            </button>
        </td>

    <?php } else { ?>
        <td style="text-align: center">
            <button 
                class="btn btn-sm" 
                type="button" 
                data-toggle="modal" 
                data-target="#detailPoin" 
                onclick="loadDetailPoin('<?= $skill ?>','<?=$nik ?>','<?= $jobtitle ?>')" 
                title="Lihat detail nilai">
                <i class="fa fa-eye"></i>
            </button>
        </td>
        
    <?php } ?>
<?php } ?>

<!-- GM -->
<?php if ($sess_login['level'] == 3) {
    if ($submitStatus == 2 && $department == $sess_login['department']) { ?>
        <td style="text-align: center">
            <button 
                class='btn btn-sm btn-default' 
                type="button" 
                title='input nilai' 
                data-toggle='modal' 
                data-target='#addModal' 
                onclick="loadCompetency('<?= $skill ?>','<?= $nik ?>','<?= $jobtitle ?>')">
                <i class='fa fa-pencil'></i>
            </button>
        </td>

    <?php } elseif ($submitStatus > 2 && $department == $sess_login['department']) { ?>
        <td style="text-align: center">
            <button 
                class="btn btn-sm" 
                type="button" 
                data-toggle="modal" 
                data-target="#detailPoin" 
                onclick="loadDetailPoin('<?= $skill ?>','<?=$nik ?>','<?= $jobtitle ?>')" 
                title="Lihat detail nilai">
                <i class="fa fa-eye"></i>
            </button>
        </td>
        
    <?php } else { ?>
        <td style="text-align: center">
            <button 
                class="btn btn-sm" 
                type="button" 
                data-toggle="modal" 
                data-target="#detailPoin" 
                onclick="loadDetailPoin('<?= $skill ?>','<?=$nik ?>','<?= $jobtitle ?>')" 
                title="Lihat detail nilai">
                <i class="fa fa-eye"></i>
            </button>
        </td>
    <?php } ?>
<?php } ?>

<!-- admin -->
<?php if ($sess_login['level'] == 99) { ?>
    <td style="text-align: center">
        <button 
            class="btn btn-sm" 
            type="button" 
            data-toggle="modal" 
            data-target="#detailPoin" 
            onclick="loadDetailPoin('<?= $skill ?>','<?=$nik ?>','<?= $jobtitle ?>')" 
            title="Lihat detail nilai">
            <i class="fa fa-eye"></i>
        </button>
    </td>
<?php } ?>