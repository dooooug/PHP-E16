<?php

set_include_path(get_include_path() . PATH_SEPARATOR . 'Classes/');
include 'PHPExcel/IOFactory.php';


$inputFileName = './export/id_1_part_2_H3 P2016 Semaine 16.xlsx';

try {
	$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
} 
catch(Exception $e) {
	die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
}

$datas = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);


function multiexplode ($delimiters,$string) {
    $ready = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);
    return  $launch;
}

$promo = array();
$day = array();

// PARSE TABLE 
for ($i=5; $i < 10; $i++) { 
	$course = array();
	$courses = array();
	$morning = array();
	$afternoon = array();

	// EXPLODE
	$morning = multiexplode(array("(","/",")"), $datas[$i]['F']);
	$afternoon = multiexplode(array("(","/",")"), $datas[$i]['M']);

	// COURS MORNING 
		// M1
	$course['cours'] = trim($morning[0]);
	$course['9h - 11h'] = $morning[1];
	$course['11h - 13h'] = $morning[2];
		if (empty($course['11h - 13h'])) {
			$course['9h - 13h'] = $morning[1];
			unset($course['9h - 11h'], $course['11h - 13h']);
		}

	$courses['m1'] = $course;	
	$course = array();

		// M2
	$course['cours'] = trim($morning[3]);
	$course['9h - 11h'] = $morning[4];
	$course['11h - 13h'] = $morning[5];
		if (empty($course['11h - 13h'])) {
			$course['9h - 13h'] = $morning[4];
			unset($course['9h - 11h'], $course['11h - 13h']);
		}

	if (!empty($course['cours'])) {
		$courses['m2'] = $course;
	}
	$course = array();
	
	// COURS AFTERNOON 
		// S1
	$course['cours'] = trim($afternoon[0]);
	$course['14h - 16h'] = $afternoon[1];
	$course['16h - 18h'] = $afternoon[2];
		if (empty($course['16h - 18h'])) {
			$course['14h - 18h'] = $afternoon[1];
			unset($course['14h - 16h'], $course['16h - 18h']);
		}

	$courses['s1'] = $course;		
	$couse = array();

		// S2
	$course['cours'] = trim($afternoon[3]);
	$course['14h - 16h'] = $afternoon[4];
	$course['16h - 18h'] = $afternoon[5];
		if (empty($course['16h - 18h'])) {
			$course['14h - 18h'] = $afternoon[4];
			unset($course['14h - 16h'], $course['16h - 18h']);
		}

	if (!empty($course['cours'])) {
		$courses['s2'] = $course;
	}
	$course = array();

	// PUSH IN PROMO AND DAY
	$day[$datas[$i]['A']] = $courses;
	$promo[$datas[1]['O']] = $day;
}

// $group = multiexplode(array("(","/",")"), $datas[6]['F']);
// $group1 = multiexplode(array("(","/",")"), $datas[6]['M']);

// switch ($group[1]) {
//     case 'G1':
//         echo "G1";
//         break;
//     case 'G2':
//         echo "G2";
//         break;
//     case 'G1A':
//         echo "G1A";
//         break;
//     case 'G2A':
//         echo "G2A";
//         break;
//     case 'G1B':
//         echo "G1B";
//         break;
//     case 'G2B':
//         echo "G2B";
//         break;
// }

// echo "<pre>";
// print_r($group);
// echo "</pre>";

// echo "<pre>";
// print_r($group1);
// echo "</pre>";


echo "<pre>";
print_r($promo);
echo "</pre>";

echo json_encode($promo);

?>