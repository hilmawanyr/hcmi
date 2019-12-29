<?php
$dictionaryName = str_replace(' ', '_', get_dictionary_detail($dictionary)->name_id);
header("Content-Type: application/xls");    
header("Content-Disposition: attachment; filename=SKILL_UNIT_OF_".strtoupper($dictionaryName).".xls");  
header("Pragma: no-cache"); 
header("Expires: 0");
?>

<table border="2">
    <thead>
    	<tr>
    		<th colspan="2"><?= strtoupper(get_dictionary_detail($dictionary)->name_id) ?></th>    		
    	</tr>
    	<tr>
    		<th colspan="2"><?= strtoupper(get_dictionary_detail($dictionary)->name_en) ?></th>
    	</tr>
    	<tr><th colspan="2"></th></tr>
    	<tr>
    		<th>Definisi</th>
    		<th><?= get_dictionary_detail($dictionary)->description ?></th>
    	</tr>
    	<tr><th colspan="2"></th></tr>
        <tr>
            <th>Level</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        <?php $no=1; foreach ($skillUnit as $unit) : ?>
            <tr>
                <td><?= $unit->level ?></td>
                <td><?= $unit->description ?></td>
            </tr>
        <?php $no++; endforeach; ?>
    </tbody>
</table>