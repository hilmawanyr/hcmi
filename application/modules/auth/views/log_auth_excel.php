<?php
error_reporting(0);

if (!is_null($nik)) {
    $userName = str_replace(' ', '_', user_name($nik));
}

header("Content-Type: application/xls");

if (!is_null($nik)) {
    header("Content-Disposition: attachment; filename=AUTHENTICATION_LOG_OF_".strtoupper($userName).".xls");  
} else {
    header("Content-Disposition: attachment; filename=AUTHENTICATION_LOG.xls");  
}

header("Pragma: no-cache"); 
header("Expires: 0");
?>

<table border="2">
    <thead>
        <tr>
            <th>No</th>
            <th>NIK</th>
            <th>Name</th>
            <th>Last Login</th>
        </tr>
    </thead>
    <tbody>
        <?php $no=1; foreach ($logs as $log) : ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= $log->nik ?></td>
                <td><?= user_name($log->nik) ?></td>
                <td><?= $log->last_login ?></td>
            </tr>
        <?php $no++; endforeach ?>
    </tbody>
</table>