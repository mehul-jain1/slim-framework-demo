<?php
/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Asia/Kolkata');

// define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** Include PHPExcel */
require_once  './Classes/PHPExcel.php';

// Create new PHPExcel object
echo date('H:i:s') , " Create new PHPExcel object" , EOL;
$objPHPExcel = new PHPExcel();
// Create a new worksheet
$myWorkSheet = new PHPExcel_Worksheet($objPHPExcel, 'Subscriptions');
// Attach the worksheet as the first worksheet in the PHPExcel object
$objPHPExcel->addSheet($myWorkSheet, 0);

// Add some data
echo date('H:i:s') , " Add some data" , EOL;
$objPHPExcel->setActiveSheetIndex(0);

// Set cell A1 with a string value
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'PHPExcel');

// Set cell A2 with a numeric value
$objPHPExcel->getActiveSheet()->setCellValue('A2', 12345.6789);

// Set cell A3 with a boolean value
$objPHPExcel->getActiveSheet()->setCellValue('A3', TRUE);
// Saving  file

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(str_replace('.php', '.xlsx', __FILE__));

?>
