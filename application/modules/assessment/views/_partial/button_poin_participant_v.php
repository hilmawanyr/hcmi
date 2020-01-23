<?php $sess_login = $this->session->userdata('login_session'); ?>
<td style="text-align:center">    
    <!-- if login as asistant manager -->
    <?php if ($sess_login['group'] == 3 && $sess_login['level'] == 1) {
        if ($isSubmited < 1) { ?>
            <button 
                class='btn btn-sm btn-default' 
                type="button" 
                title='input nilai' 
                data-toggle='modal' 
                data-target='#addModal' 
                onclick="loadCompetency('<?= $skill ?>','<?= $nik ?>','<?= $jobtitle ?>')">
                <i class='fa fa-pencil'></i>
            </button>

        <?php } else { ?>
            <button 
                class="btn btn-sm" 
                type="button" 
                data-toggle="modal" 
                data-target="#detailPoin" 
                onclick="loadDetailPoin('<?= $skill ?>','<?=$nik ?>','<?= $jobtitle ?>')" 
                title="Lihat detail nilai">
                <i class="fa fa-eye"></i>
            </button>

        <?php } ?>
    
    <!-- if login as manager -->
    <?php } elseif ($sess_login['group'] == 3 && $sess_login['level'] == 2) {
        if ($submitStatus == 1) { ?>
            <button 
                class='btn btn-sm btn-default' 
                type="button" 
                title='input nilai' 
                data-toggle='modal' 
                data-target='#addModal' 
                onclick="loadCompetency('<?= $skill ?>','<?= $nik ?>','<?= $jobtitle ?>')">
                <i class='fa fa-pencil'></i>
            </button>

        <?php } else { ?>
            <button 
                class="btn btn-sm" 
                type="button" 
                data-toggle="modal" 
                data-target="#detailPoin" 
                onclick="loadDetailPoin('<?= $skill ?>','<?=$nik ?>','<?= $jobtitle ?>')" 
                title="Lihat detail nilai">
                <i class="fa fa-eye"></i>
            </button>

        <?php } ?>
    
    <!-- if login as GM -->
    <?php } elseif ($sess_login['group'] == 3 && $sess_login['level'] == 3) {
        if ($submitStatus == 2) { ?>
            <button 
                class='btn btn-sm btn-default' 
                type="button" 
                title='input nilai' 
                data-toggle='modal' 
                data-target='#addModal' 
                onclick="loadCompetency('<?= $skill ?>','<?= $nik ?>','<?= $jobtitle ?>')">
                <i class='fa fa-pencil'></i>
            </button>

        <?php } else { ?>
            <button 
                class="btn btn-sm" 
                type="button" 
                data-toggle="modal" 
                data-target="#detailPoin" 
                onclick="loadDetailPoin('<?= $skill ?>','<?=$nik ?>','<?= $jobtitle ?>')" 
                title="Lihat detail nilai">
                <i class="fa fa-eye"></i>
            </button>
            
        <?php } ?>
    <?php } ?>
    
    <!-- before v.0.0.1 -->
    <!--
    <?php if ($isSubmited > 0) : ?>
    <button class='btn btn-sm btn-default' type="button" title='input nilai'>
        <i class='fa fa-edit'></i>
    </button>

    <?php else : ?>
    <button class='btn btn-sm btn-default' type="button" title='input nilai' data-toggle='modal' data-target='#addModal' onclick="loadCompetency('<?= $skill ?>','<?= $nik ?>','<?= $jobtitle ?>')">
        <i class='fa fa-pencil'></i>
    </button>
    <?php endif; ?> -->

</td>