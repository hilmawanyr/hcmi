<?php
$dictionaryName = str_replace(' ', '_', get_skill_type_name($skillTypeId));
header("Content-Type: application/xls");    
header("Content-Disposition: attachment; filename=DICTIONARY_OF_".strtoupper($dictionaryName).".xls");  
header("Pragma: no-cache"); 
header("Expires: 0");
?>

<table border="2">
    <thead>
    	<tr>
    		<th colspan="5"><?= get_skill_type_name($skillTypeId) ?></th>
    	</tr>
        <tr>
            <th>No</th>
            <th>Indonesian Name</th>
            <th>English Name</th>
            <th>Description</th>
            <th>Competency Type</th>
        </tr>
    </thead>
    <tbody>
        <?php $no=1; foreach ($dictionaries as $dictionary) : ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= $dictionary->name_id ?></td>
                <td><?= $dictionary->name_en ?></td>
                <td><?= $dictionary->description ?></td>
                <td><?= get_skill_type_name($dictionary->skill_group) ?></td>
            </tr>
        <?php $no++; endforeach; ?>
    </tbody>
</table>