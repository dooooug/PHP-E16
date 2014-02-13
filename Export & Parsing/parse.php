<?php

set_include_path(get_include_path() . PATH_SEPARATOR . 'Classes/');
include 'PHPExcel/IOFactory.php';

$schedule = new array();
$inputFileName = './export/id_1_part_2_H3 P2016 Semaine 16.xlsx';

try {
	$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
} 
catch(Exception $e) {
	die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
}

$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);

$promo = explode(" ", $sheetData[1]['O'])[0];

$lundi = $sheetData[5];
$course = split("\(",$lundi['F']);
$field = $course[0];
$groupsdata = split("\/",substr($course[1], 0, -1));
$subgroup = [];
$groups = [];

foreach($groupsdata as $groupdata) {
    array_push($subgroup, $groupdata[2]);
    array_push($groups,$groupdata[1]);
}

$teacher = $lundi['G'];

$room = explode("\n", $lundi['H']);
$hour = explode("\n", $lundi['J']);

for($i = 0; $i < sizeof($hour); $i++){
    $hour[$i] = $hour[$i][0].$hour[$i][1];
}

for($i = 0; $i < sizeof($room); $i++){
    $room[$i] = substr($room[$i], 1);
}

$i = 0;
foreach($groups as $group) {
    print_r('La promo ' . $promo . ' groupe ' . $group . ' sous-groupe '.$subgroup[$i].' aura cours de ' . $field . ' avec ' . $teacher . ' dans la salle ' . $room[$i] . ' a ' . $hour[$i] . 'h</br>');
    $i++;
}


/*mardi mercredi etc.*/

$schedule["lundi"]["m1"]["g1"]["a"]["cours"] = $cours;
$schedule["lundi"]["m1"]["g1"]["a"]["salle"] = $hour;
$schedule["lundi"]["m1"]["g1"]["a"]["prof"] = $teacher;


$newSchedule = json_encode($schedule);


?>