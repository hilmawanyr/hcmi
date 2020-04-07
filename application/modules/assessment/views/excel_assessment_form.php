<?php
$jobtitle = str_replace(" ","_",$jobtitlename->name);
header("Content-Type: application/xls");    
header("Content-Disposition: attachment; filename=Export_of_Assessment_Form_".$jobtitle."_".$activeyear.".xls");  
header("Pragma: no-cache"); 
header("Expires: 0");
?>
<?php error_reporting(0); ?>

<style>
    th {
        text-align: center;
    }
</style>

<table border="2">
    <tr style="text-align:center">
        <th colspan="<?= $numberofcolumn ?>">
            <b>Form Penilaian | <?= $jobtitlename->name ?> | Tahun <?= $activeyear ?></b>
        </th>
    </tr>
    <tr>
        <th>NIK</th>
        <th>Nama Karyawan</th>
        <?php foreach ($dictionary as $dict) : ?>
            <th><?= $dict->name_id ?>}</th>
        <?php endforeach; ?>
        <th>Nilai Absolut</th>
    </tr>
    <?php foreach ($employee as $emp) : ?>
        <tr>
            <td><?= $emp->nik ?></td>
            <td><?= $emp->name ?></td>
            <?php foreach ($dictionary as $itemdict) : 

                    // get assessment form to get its ID
                    $assessmentForm = $this->db->where('nik', $emp->nik)->like('code',$activeyear,'before')->get('assessment_forms')->row();
                    
                    // its ID will use to get detail form question
                    $formId = $assessmentForm->id;
                    
                    // get poin per competency
                    $detailPoint = $this->db->query("SELECT * from assessment_form_questions ass 
                                                    JOIN skill_units un ON ass.skill_unit_id = un.id 
                                                    where un.id_dictionary = '$itemdict->skill_id' 
                                                    AND poin IS NOT NULL AND ass.form_id = '$formId'")->result();
                                        
                    // count amount of each unit competency
                    $const = 0;
                    foreach ($detailPoint as $value) {
                        $const = $const + ($value->weight * $value->poin);
                    }

                    // point of each competency dictionary
                    $pointPerCompetency = $const;

                    // get poin of all competency for each employee
                    $allPoint = $this->db->query("SELECT * from assessment_form_questions where poin IS NOT NULL AND form_id = '$formId'")->result();
                    $constall = 0;
                    foreach ($allPoint as $valpoint) {
                        $constall = $constall + ($valpoint->poin * $valpoint->weight);
                    }

                ?>
                <td><?= $pointPerCompetency ?></td>
            <?php endforeach; ?>
            <td><?= $constall ?></td>
        </tr>
    <?php endforeach; ?>
</table>