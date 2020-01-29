<?php $sess_login = $this->session->userdata('login_session'); ?>

<!-- as asistant manager and has submited -->
<?php if ($sess_login['level'] == 1 && $isSubmited > 0) { ?>
    <button type="button" class="btn btn-success pull-right" onclick="alert('Form has submitted!')">
        <i class="fa fa-check"></i> Form has submitted
    </button>
    
<!-- as asistant manager and not submited yet -->
<?php } elseif ($sess_login['level'] == 1 && $isSubmited < 1) { ?>
    <?php if ($statementAmount == 0 && $completeAssessment == 0) : ?>
        <button type="button" class="btn btn-warning pull-right" onclick="alert('There\'s no competency for this job title!')">
            <i class="fa fa-check"></i> Submit Form
        </button>
    <?php elseif ($statementAmount != $completeAssessment) : ?>
        <button type="button" class="btn btn-warning pull-right" onclick="alert('Please complete the form before submit!')">
            <i class="fa fa-check"></i> Submit Form
        </button>
    <?php else : ?>
        <a 
            href="<?= base_url('submit_form/'.$jobtitle) ?>" 
            class="btn btn-warning pull-right" 
            onclick="return confirm('Dengan ini anda menyatakan bahwa penilaian yang dilakukan adalah benar. Anda Yakin?')">
            <i class="fa fa-check"></i> Submit Form
        </a>
    <?php endif; ?>

<!-- as manager and not submited yet-->
<?php } elseif ($sess_login['level'] == 2 && $submitStatus == 1) { ?>
    <!-- for non HR -->
    <?php if ($sess_login['group'] != 2) { ?>
        <a 
            href="<?= base_url('submit_form/'.$jobtitle) ?>" 
            class="btn btn-warning pull-right" 
            onclick="return confirm('Dengan ini anda menyatakan bahwa penilaian yang dilakukan adalah benar. Anda Yakin?')">
            <i class="fa fa-check"></i> Submit Form
        </a>
    <!-- for HR and in his section -->
    <?php } elseif ($sess_login['group'] == 2 && $sess_login['section'] == $sectionId) { ?>
        <a 
            href="<?= base_url('submit_form/'.$jobtitle) ?>" 
            class="btn btn-warning pull-right" 
            onclick="return confirm('Dengan ini anda menyatakan bahwa penilaian yang dilakukan adalah benar. Anda Yakin?')">
            <i class="fa fa-check"></i> Submit Form
        </a>
    <?php } ?>

<!-- as manager and have submited -->
<?php } elseif ($sess_login['level'] == 2 && $submitStatus == 2) { ?>
    <!-- for non HR -->
    <?php if ($sess_login['group'] != 2) { ?>
        <button type="button" class="btn btn-success pull-right" onclick="alert('Form has submitted!')">
            <i class="fa fa-check"></i> Form has submitted
        </button>
    <!-- for HR and in his section -->
    <?php } elseif ($sess_login['group'] == 2 && $sess_login['section'] == $sectionId) { ?>
        <button type="button" class="btn btn-success pull-right" onclick="alert('Form has submitted!')">
            <i class="fa fa-check"></i> Form has submitted
        </button> 
    <?php } ?>
    
<!-- as GM and not submitted yet -->
<?php } elseif ($sess_login['level'] == 3 && $submitStatus == 2) { ?>
    <!-- for non HR -->
    <?php if ($sess_login['group'] != 2) { ?>
        <a 
            href="<?= base_url('submit_form/'.$jobtitle) ?>" 
            class="btn btn-warning pull-right" 
            onclick="return confirm('Dengan ini anda menyatakan bahwa penilaian yang dilakukan adalah benar. Anda Yakin?')">
            <i class="fa fa-check"></i> Submit Form
        </a>
    <!-- for HR and in his section -->
    <?php } elseif ($sess_login['group'] == 2 && $sess_login['department'] == $department) { ?>
        <a 
            href="<?= base_url('submit_form/'.$jobtitle) ?>" 
            class="btn btn-warning pull-right" 
            onclick="return confirm('Dengan ini anda menyatakan bahwa penilaian yang dilakukan adalah benar. Anda Yakin?')">
            <i class="fa fa-check"></i> Submit Form
        </a>
    <?php } ?>

<!-- as GM and have submitted yet -->
<?php } elseif ($sess_login['level'] == 3 && $submitStatus > 2) { ?>
    <!-- for non HR -->
    <?php if ($sess_login['group'] != 2) { ?>
        <button type="button" class="btn btn-success pull-right" onclick="alert('Form has submitted!')">
            <i class="fa fa-check"></i> Form has submitted
        </button>
    <!-- for HR and in his section -->
    <?php } elseif ($sess_login['group'] == 2 && $sess_login['department'] == $department) { ?>
        <button type="button" class="btn btn-success pull-right" onclick="alert('Form has submitted!')">
            <i class="fa fa-check"></i> Form has submitted
        </button>
    <?php } ?>
<?php } ?>