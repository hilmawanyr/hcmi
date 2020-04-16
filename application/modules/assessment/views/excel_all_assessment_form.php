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
$excel->getActiveSheet()->setCellValue('A1', ' Form Year : '.$activeyear);

//border
$excel->getActiveSheet()->getStyle('A2:M'.(count($assessment)+3).'')->applyFromArray($BStyle);
$excel->getActiveSheet()->getStyle('A1')->applyFromArray($BStyle);

//name the worksheet
$excel->getActiveSheet()->setTitle('Assessment Form');

//header
$excel->getActiveSheet()->setCellValue('A2', 'FORM PENILAIAN TAHUN '.$activeyear);

$cell_body   = 4;
$excel->getActiveSheet()->setCellValue('A3', 'NIK');
$excel->getActiveSheet()->setCellValue('B3', 'NAME');
$excel->getActiveSheet()->setCellValue('C3', 'POSITION');
$excel->getActiveSheet()->setCellValue('D3', 'GRADE');
$excel->getActiveSheet()->setCellValue('E3', 'DEPARTMENT');
$excel->getActiveSheet()->setCellValue('F3', 'SECTION');

foreach ($assessment as $ass) {
    // show competency
    $indexColumn      = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N'];
    $initColumn       = "G";
    $competency       = $this->assessment->get_assessment_matrix($ass->job_title_id);
    $dictionaryNumber = count($competency);

    $total_competency = 1;
    foreach ($competency as $dict) {
        $excel->getActiveSheet()->setCellValue($initColumn.'3', 'K'.$total_competency);
        $initColumn++;
        $total_competency++;
    }
    $excel->getActiveSheet()->setCellValue($indexColumn[$dictionaryNumber+6].'3', 'Nilai Absolut');
    $excel->getActiveSheet()->setCellValue($indexColumn[$dictionaryNumber+7].'3', 'Level');

    $excel->getActiveSheet()->setCellValue('A'.$cell_body, $ass->nik);
    $excel->getActiveSheet()->setCellValue('B'.$cell_body, $ass->name);
    $excel->getActiveSheet()->setCellValue('C'.$cell_body, $ass->position);
    $excel->getActiveSheet()->setCellValue('D'.$cell_body, $ass->grade);
    $excel->getActiveSheet()->setCellValue('E'.$cell_body, $ass->dept_name);
    $excel->getActiveSheet()->setCellValue('F'.$cell_body, $ass->sect_name);

    // show score
    $initColumn2 = "G";
    foreach ($competency as $dicts) {
        // get assessment form to get its ID
        $assessmentForm = $this->db->where('nik', $ass->nik)
                                    ->like('code',$activeyear,'both')
                                    ->get('assessment_forms')
                                    ->row();
        
        // its ID will use to get detail form question
        $form_id = $assessmentForm->id;
        
        // get score per competency
        $detailPoint = $this->db->query("SELECT * from assessment_form_questions ass 
                                        JOIN skill_units un ON ass.skill_unit_id = un.id 
                                        WHERE un.id_dictionary = '$dicts->skill_id' 
                                        AND poin IS NOT NULL 
                                        AND ass.form_id = '$form_id'")->result();
                            
        // count amount of each unit competency
        $const = 0;
        foreach ($detailPoint as $value) {
            $const = $const + (($value->weight * $value->poin) / 5);
        }

        // point of each competency dictionary
        $pointPerCompetency = $const;

        // get poin of all competency for each employee
        $all_point  = $this->db->query("SELECT * from assessment_form_questions 
                                        WHERE poin IS NOT NULL 
                                        AND form_id = '$form_id'")->result();
        $constall = 0;
        foreach ($all_point as $valpoint) {
            $constall = $constall + (($valpoint->poin * $valpoint->weight) / 5);
        }
        
        $excel->getActiveSheet()->setCellValue($initColumn2.$cell_body, $pointPerCompetency);
        $initColumn2++;
    }
    $excel->getActiveSheet()->setCellValue($indexColumn[$dictionaryNumber+6].$cell_body, $constall);
    $excel->getActiveSheet()->setCellValue($indexColumn[$dictionaryNumber+7].$cell_body, get_assessment_grade($constall));
    // end show score
    $cell_body++;
}
$excel->getActiveSheet()->mergeCells('A2:M2');

//align
$style = array(
        'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    )
);

$excel->getActiveSheet()->getStyle("A2:H2")->applyFromArray($style);

$excel->getActiveSheet()->mergeCells('A1:B1');
$excel->getActiveSheet()->getStyle("A1:M3")->getFont()->setBold(true);
// $excel->getActiveSheet()->getStyle("A6:N7")->applyFromArray($style);
// //$excel->getDefaultStyle()->applyFromArray($style);

$filename = "Export_of_Assessment_Form_".str_replace(' ', '_', $department_name)."_Department_".$activeyear.".xls";
header('Content-Type: application/vnd.ms-excel'); //mime type
header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
header('Cache-Control: max-age=0'); //no cache
$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');  
//force user to download the Excel file without writing it to server's HD
$objWriter->save('php://output');
?>