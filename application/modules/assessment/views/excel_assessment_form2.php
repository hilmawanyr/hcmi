<?php 
$excel = new PHPExcel();
$BStyle = array(
  'borders' => array(
    'allborders' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN
    )
  )
);

$excel->setActiveSheetIndex(0);

$excel->getActiveSheet()->setCellValue('A1', ' Form No : AF-'.$jobtitle.'-'.$activeyear);

//border
$excel->getActiveSheet()->getStyle('A2:I'.(count($employee)+3).'')->applyFromArray($BStyle);
$excel->getActiveSheet()->getStyle('A1')->applyFromArray($BStyle);

//name the worksheet
$excel->getActiveSheet()->setTitle('Assessment Form');
//header
$excel->getActiveSheet()->setCellValue('A2', 'FORM PENILAIAN '.'| '.$jobtitlename->name.' | Tahun '.$activeyear);
$excel->getActiveSheet()->setCellValue('A3', 'NIK');
$excel->getActiveSheet()->setCellValue('B3', 'NAMA');

$arrayColumn      = ['A','B','C','D','E','F','G','H','I','J'];
$initColumn       = "C";
$dictionaryNumber = count($dictionary);

foreach ($dictionary as $dict) {
    $excel->getActiveSheet()->setCellValue($initColumn.'3', $dict->name_id);
    $excel->getActiveSheet()->setCellValue($arrayColumn[$dictionaryNumber+2].'3', 'Nilai Absolut');
    $excel->getActiveSheet()->setCellValue($arrayColumn[$dictionaryNumber+3].'3', 'Level');
    $initColumn++;
}

$num = 4;
foreach ($employee as $employe) {
    $excel->getActiveSheet()->setCellValue('A'.$num, $employe->nik);
    $excel->getActiveSheet()->setCellValue('B'.$num, $employe->name);

    $initColumn2 = "C";
    foreach ($dictionary as $dicts) {
        // get assessment form to get its ID
        $assessmentForm = $this->db->where('nik', $employe->nik)->like('code',$activeyear,'both')->get('assessment_forms')->row();
        
        // its ID will use to get detail form question
        $formId = $assessmentForm->id;
        
        // get poin per competency
        $detailPoint = $this->db->query("SELECT * from assessment_form_questions ass 
                                        JOIN skill_units un ON ass.skill_unit_id = un.id 
                                        where un.id_dictionary = '$dicts->skill_id' 
                                        AND poin IS NOT NULL AND ass.form_id = '$formId'")->result();
                            
        // count amount of each unit competency
        $const = 0;
        foreach ($detailPoint as $value) {
            $const = $const + (($value->weight * $value->poin) / 5);
        }

        // point of each competency dictionary
        $pointPerCompetency = $const;

        // get poin of all competency for each employee
        $allPoint = $this->db->query("SELECT * from assessment_form_questions where poin IS NOT NULL AND form_id = '$formId'")->result();
        $constall = 0;
        foreach ($allPoint as $valpoint) {
            $constall = $constall + (($valpoint->poin * $valpoint->weight) / 5);
        }
        
        $excel->getActiveSheet()->setCellValue($initColumn2.$num, $pointPerCompetency);
        $initColumn2++;
    }
    $excel->getActiveSheet()->setCellValue('H'.$num, $constall);
    $excel->getActiveSheet()->setCellValue('I'.$num, get_assessment_grade($constall));
    $num++;
}

$excel->getActiveSheet()->mergeCells('A2:I2');

//align
$style = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    )
);

$excel->getActiveSheet()->getStyle("A2:H2")->applyFromArray($style);

$excel->getActiveSheet()->mergeCells('A1:B1');
$excel->getActiveSheet()->getStyle("A1:B1")->getFont()->setBold(true);
// $excel->getActiveSheet()->getStyle("A6:N7")->applyFromArray($style);
// //$excel->getDefaultStyle()->applyFromArray($style);

$jobtitle = str_replace(" ","_",$jobtitlename->name);
$filename = "Export_of_Assessment_Form_".$jobtitle."_".$activeyear.".xls";
header('Content-Type: application/vnd.ms-excel'); //mime type
header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
header('Cache-Control: max-age=0'); //no cache
$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');  
//force user to download the Excel file without writing it to server's HD
$objWriter->save('php://output');
?>