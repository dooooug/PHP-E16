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

//Définition des horaires
$schedule = multiexplode(array("\n","-"), $datas[5]['J']);
$scheduleAfternoon = multiexplode(array("\n","-"), $datas[5]['Q']);
for($i=0; $i<count($scheduleAfternoon); $i++)
{
  array_push($schedule, $scheduleAfternoon[$i]);
}

//Suppression des 00 après XXh00 => xxh
for($i=0; $i<count($schedule); $i++){
  $schedule[$i] = substr($schedule[$i], 0, strrpos($schedule[$i], "h")+1);
}

//Définition des salles
$rooms = explode("\n", $datas[5]['H']);

for($i=0; $i<count($rooms); $i++){
  $rooms[$i] = substr($rooms[$i], 1);
}

echo "<pre>";
print_r($rooms);
echo "</pre>";

// PARSE TABLE 
for ($i=5; $i < 10; $i++) { 
  //5 -> 10
	$course = array();
	$courses = array();
	$morning = array();
	$afternoon = array();
  $speaker = array();

	// EXPLODE
	$morning = multiexplode(array("(","/",")"), $datas[$i]['F']);
	$afternoon = multiexplode(array("(","/",")"), $datas[$i]['M']);
  $speakerMorning = explode("\n", $datas[$i]['G']);
  $speakerAfternoon = explode("\n", $datas[$i]['N']);

	// COURS MORNING 
    // M1
  if(isset($morning[4]) || (isset($morning[2]) && strlen($morning[2]) > 1)) {
    if(isset($morning[1]) && strlen($morning[1])>2) {
      $course[substr($morning[1],0,2)][$morning[1][2]]["course"] = trim($morning[0]);
      $course[substr($morning[1],0,2)][$morning[1][2]]["speaker"] = trim($speakerMorning[0]);
      $course[substr($morning[1],0,2)][$morning[1][2]]["start"] = $schedule[0];
      $course[substr($morning[1],0,2)][$morning[1][2]]["end"] = $schedule[1];
    }
    elseif(isset($morning[1])) {
       $course[substr($morning[1],0,2)]['A']["course"] = trim($morning[0]);
       $course[substr($morning[1],0,2)]['B']["course"] = trim($morning[0]);
       $course[substr($morning[1],0,2)]['A']["speaker"] = trim($speakerMorning[0]);
       $course[substr($morning[1],0,2)]['B']["speaker"] = trim($speakerMorning[0]);
       $course[substr($morning[1],0,2)]['A']["start"] = $schedule[0];
       $course[substr($morning[1],0,2)]['B']["start"] = $schedule[0];
       $course[substr($morning[1],0,2)]['A']["end"] = $schedule[1];
       $course[substr($morning[1],0,2)]['B']["end"] = $schedule[1];
    }
    
    if(strrpos($morning[1], "1")){
      $course[substr($morning[1],0,2)]['room'] = $rooms[0];
    }
    else {
      $course[substr($morning[1],0,2)]['room'] = $rooms[1];
    }

    if(isset($morning[4]) && strlen($morning[4])>2) {
      $course[substr($morning[4],0,2)][$morning[4][2]]["course"] = trim($morning[3]);
      $course[substr($morning[4],0,2)][$morning[4][2]]["speaker"] = trim($speakerMorning[1]);
      $course[substr($morning[4],0,2)][$morning[4][2]]["start"] = $schedule[0];
      $course[substr($morning[4],0,2)][$morning[4][2]]["end"] = $schedule[1];
      if(strrpos($morning[4], "1")){
        $course[substr($morning[4],0,2)]['room'] = $rooms[0];
      }
      else {
        $course[substr($morning[4],0,2)]['room'] = $rooms[1];
      }
    }
    elseif(isset($morning[4])) {
       $course[substr($morning[4],0,2)]['A']["course"] = trim($morning[3]);
       $course[substr($morning[4],0,2)]['B']["course"] = trim($morning[3]);
       $course[substr($morning[4],0,2)]['A']["speaker"] = trim($speakerMorning[1]);
       $course[substr($morning[4],0,2)]['B']["speaker"] = trim($speakerMorning[1]);
       $course[substr($morning[4],0,2)]['A']["start"] = $schedule[0];
       $course[substr($morning[4],0,2)]['B']["start"] = $schedule[0];
       $course[substr($morning[4],0,2)]['A']["end"] = $schedule[1];
       $course[substr($morning[4],0,2)]['B']["end"] = $schedule[1];
       if(strrpos($morning[4], "1")){
         $course[substr($morning[4],0,2)]['room'] = $rooms[0];
       }
       else {
         $course[substr($morning[4],0,2)]['room'] = $rooms[1];
       }
    }
  }
  else {
    $course[substr($morning[1],0,2)]['A']["course"] = trim($morning[0]);
    $course[substr($morning[1],0,2)]['B']["course"] = trim($morning[0]);
    $course[substr($morning[1],0,2)]['A']["speaker"] = trim($speakerMorning[0]);
    $course[substr($morning[1],0,2)]['B']["speaker"] = trim($speakerMorning[0]);
    $course[substr($morning[1],0,2)]['A']["start"] = $schedule[0];
    $course[substr($morning[1],0,2)]['B']["start"] = $schedule[0];
    $course[substr($morning[1],0,2)]['A']["end"] = $schedule[1];
    $course[substr($morning[1],0,2)]['B']["end"] = $schedule[1];
    
    if(strrpos($morning[1], "1")){
      $course[substr($morning[1],0,2)]['room'] = $rooms[0];
    }
    else {
      $course[substr($morning[1],0,2)]['room'] = $rooms[1];
    }
  }

  $courses['m1'] = $course;  
  $course = array();
  
    // M2
    
  if(isset($morning[5]) || (isset($morning[2]) && strlen($morning[2]) > 1)) {
    if(isset($morning[2]) && strlen($morning[2])>2) {
      $course[substr($morning[2],0,2)][$morning[2][2]]["course"] = trim($morning[0]);
      $course[substr($morning[2],0,2)][$morning[2][2]]["speaker"] = trim($speakerMorning[0]);
      $course[substr($morning[2],0,2)][$morning[2][2]]["start"] = $schedule[1];
      $course[substr($morning[2],0,2)][$morning[2][2]]["end"] = $schedule[3];
      if(strrpos($morning[2], "1")){
        $course[substr($morning[2],0,2)]['room'] = $rooms[0];
      }
      else {
        $course[substr($morning[2],0,2)]['room'] = $rooms[1];
      }
    }
    elseif(isset($morning[5])) {
       $course[substr($morning[2],0,2)]['A']["course"] = trim($morning[0]);
       $course[substr($morning[2],0,2)]['B']["course"] = trim($morning[0]);
       $course[substr($morning[2],0,2)]['A']["speaker"] = trim($speakerMorning[0]);
       $course[substr($morning[2],0,2)]['B']["speaker"] = trim($speakerMorning[0]);
       $course[substr($morning[2],0,2)]['A']["start"] = $schedule[1];
       $course[substr($morning[2],0,2)]['B']["start"] = $schedule[1];
       $course[substr($morning[2],0,2)]['A']["end"] = $schedule[3];
       $course[substr($morning[2],0,2)]['B']["end"] = $schedule[3];
       if(strrpos($morning[2], "1")){
         $course[substr($morning[2],0,2)]['room'] = $rooms[0];
       }
       else {
         $course[substr($morning[2],0,2)]['room'] = $rooms[1];
       }
    }

    if(isset($morning[5]) && strlen($morning[5])>2) {
      $course[substr($morning[5],0,2)][$morning[5][2]]["course"] = trim($morning[3]);
      $course[substr($morning[5],0,2)][$morning[5][2]]["speaker"] = trim($speakerMorning[1]);
      $course[substr($morning[5],0,2)][$morning[5][2]]["start"] = $schedule[1];
      $course[substr($morning[5],0,2)][$morning[5][2]]["end"] = $schedule[3];
      if(strrpos($morning[5], "1")){
        $course[substr($morning[5],0,2)]['room'] = $rooms[0];
      }
      else {
        $course[substr($morning[5],0,2)]['room'] = $rooms[1];
      }
    }
    elseif(isset($morning[5])) {
       $course[substr($morning[5],0,2)]['A']["course"] = trim($morning[3]);
       $course[substr($morning[5],0,2)]['B']["course"] = trim($morning[3]);
       $course[substr($morning[5],0,2)]['A']["speaker"] = trim($speakerMorning[1]);
       $course[substr($morning[5],0,2)]['B']["speaker"] = trim($speakerMorning[1]);
       $course[substr($morning[5],0,2)]['A']["start"] = $schedule[1];
       $course[substr($morning[5],0,2)]['B']["start"] = $schedule[1];
       $course[substr($morning[5],0,2)]['A']["end"] = $schedule[3];
       $course[substr($morning[5],0,2)]['B']["end"] = $schedule[3];
       if(strrpos($morning[5], "1")){
         $course[substr($morning[5],0,2)]['room'] = $rooms[0];
       }
       else {
         $course[substr($morning[5],0,2)]['room'] = $rooms[1];
       }
    }
  }
  else {
    $course[substr($morning[1],0,2)]['A']["course"] = trim($morning[0]);
    $course[substr($morning[1],0,2)]['B']["course"] = trim($morning[0]);
    $course[substr($morning[1],0,2)]['A']["speaker"] = trim($speakerMorning[0]);
    $course[substr($morning[1],0,2)]['B']["speaker"] = trim($speakerMorning[0]);
    $course[substr($morning[1],0,2)]['A']["start"] = $schedule[1];
    $course[substr($morning[1],0,2)]['B']["start"] = $schedule[1];
    $course[substr($morning[1],0,2)]['A']["end"] = $schedule[3];
    $course[substr($morning[1],0,2)]['B']["end"] = $schedule[3];
    if(strrpos($morning[1], "1")){
      $course[substr($morning[1],0,2)]['room'] = $rooms[0];
    }
    else {
      $course[substr($morning[1],0,2)]['room'] = $rooms[1];
    }
  }
  $courses['m2'] = $course;
  $course = array();
  
  // COURS AFTERNOON 
    // S1
  if(isset($afternoon[4]) || (isset($afternoon[2]) && strlen($afternoon[2]) > 1)) {  
    
    if(isset($afternoon[1]) && strlen($afternoon[1])>2) {
      $course[substr($afternoon[1],0,2)][$afternoon[1][2]]["course"] = trim($afternoon[0]);
      $course[substr($afternoon[1],0,2)][$afternoon[1][2]]["speaker"] = trim($speakerAfternoon[0]);
      $course[substr($afternoon[1],0,2)][$afternoon[1][2]]["start"] = $schedule[4];
      $course[substr($afternoon[1],0,2)][$afternoon[1][2]]["end"] = $schedule[5];
      if(strrpos($afternoon[1], "1")){
        $course[substr($afternoon[1],0,2)]['room'] = $rooms[1];
      }
      else {
        $course[substr($afternoon[1],0,2)]['room'] = $rooms[0];
      }
    }
    elseif(isset($afternoon[1])) {
       $course[substr($afternoon[1],0,2)]['A']["course"] = trim($afternoon[0]);
       $course[substr($afternoon[1],0,2)]['B']["course"] = trim($afternoon[0]);
       $course[substr($afternoon[1],0,2)]['A']["speaker"] = trim($speakerAfternoon[0]);
       $course[substr($afternoon[1],0,2)]['B']["speaker"] = trim($speakerAfternoon[0]);
       $course[substr($afternoon[1],0,2)]['A']["start"] = $schedule[4];
       $course[substr($afternoon[1],0,2)]['B']["start"] = $schedule[4];
       $course[substr($afternoon[1],0,2)]['A']["end"] = $schedule[5];
       $course[substr($afternoon[1],0,2)]['B']["end"] = $schedule[5];
       if(strrpos($afternoon[1], "1")){
         $course[substr($afternoon[1],0,2)]['room'] = $rooms[1];
       }
       else {
         $course[substr($afternoon[1],0,2)]['room'] = $rooms[0];
       }
    }
  
    if(isset($afternoon[4]) && strlen($afternoon[4])>2) {
      $course[substr($afternoon[4],0,2)][$afternoon[4][2]]["course"] = trim($afternoon[3]);
      $course[substr($afternoon[4],0,2)][$afternoon[4][2]]["speaker"] = trim($speakerAfternoon[1]);
      $course[substr($afternoon[4],0,2)][$afternoon[4][2]]["start"] = $schedule[4];
      $course[substr($afternoon[4],0,2)][$afternoon[4][2]]["end"] = $schedule[5];
      if(strrpos($afternoon[4], "1")){
        $course[substr($afternoon[4],0,2)]['room'] = $rooms[1];
      }
      else {
        $course[substr($afternoon[4],0,2)]['room'] = $rooms[0];
      }
    }
    elseif(isset($afternoon[4])) {
       $course[substr($afternoon[4],0,2)]['A']["course"] = trim($afternoon[3]);
       $course[substr($afternoon[4],0,2)]['B']["course"] = trim($afternoon[3]);
       $course[substr($afternoon[4],0,2)]['A']["speaker"] = trim($speakerAfternoon[1]);
       $course[substr($afternoon[4],0,2)]['B']["speaker"] = trim($speakerAfternoon[1]);
       $course[substr($afternoon[4],0,2)]['A']["start"] = $schedule[4];
       $course[substr($afternoon[4],0,2)]['B']["start"] = $schedule[4];
       $course[substr($afternoon[4],0,2)]['A']["end"] = $schedule[5];
       $course[substr($afternoon[4],0,2)]['B']["end"] = $schedule[5];
       if(strrpos($afternoon[4], "1")){
         $course[substr($afternoon[4],0,2)]['room'] = $rooms[1];
       }
       else {
         $course[substr($afternoon[4],0,2)]['room'] = $rooms[0];
       }
    }
  }
  else {
    $course[substr($afternoon[1],0,2)]['A']["course"] = trim($afternoon[0]);
    $course[substr($afternoon[1],0,2)]['B']["course"] = trim($afternoon[0]);
    $course[substr($afternoon[1],0,2)]['A']["speaker"] = trim($speakerAfternoon[0]);
    $course[substr($afternoon[1],0,2)]['B']["speaker"] = trim($speakerAfternoon[0]);
    $course[substr($afternoon[1],0,2)]['A']["start"] = $schedule[4];
    $course[substr($afternoon[1],0,2)]['B']["start"] = $schedule[4];
    $course[substr($afternoon[1],0,2)]['A']["end"] = $schedule[5];
    $course[substr($afternoon[1],0,2)]['B']["end"] = $schedule[5];
    if(strrpos($afternoon[1], "1")){
      $course[substr($afternoon[1],0,2)]['room'] = $rooms[1];
    }
    else {
      $course[substr($afternoon[1],0,2)]['room'] = $rooms[0];
    }
  }
  
  $courses['s1'] = $course;    
  $course = array();
  
  
  // S2
  if(isset($afternoon[5]) || (isset($afternoon[2]) && strlen($afternoon[2]) > 1)){
    if(isset($afternoon[2]) && strlen($afternoon[2])>2) {
      $course[substr($afternoon[2],0,2)][$afternoon[2][2]]["course"] = trim($afternoon[0]);
      $course[substr($afternoon[2],0,2)][$afternoon[2][2]]["speaker"] = trim($speakerAfternoon[0]);
      $course[substr($afternoon[2],0,2)][$afternoon[2][2]]["start"] = $schedule[5];
      $course[substr($afternoon[2],0,2)][$afternoon[2][2]]["end"] = $schedule[7];
      if(strrpos($afternoon[2], "1")){
        $course[substr($afternoon[2],0,2)]['room'] = $rooms[1];
      }
      else {
        $course[substr($afternoon[2],0,2)]['room'] = $rooms[0];
      }
    }
    elseif(isset($afternoon[2])) {
       $course[substr($afternoon[2],0,2)]['A']["course"] = trim($afternoon[0]);
       $course[substr($afternoon[2],0,2)]['B']["course"] = trim($afternoon[0]);
       $course[substr($afternoon[2],0,2)]['A']["speaker"] = trim($speakerAfternoon[0]);
       $course[substr($afternoon[2],0,2)]['B']["speaker"] = trim($speakerAfternoon[0]);
       $course[substr($afternoon[2],0,2)]['A']["start"] = $schedule[5];
       $course[substr($afternoon[2],0,2)]['B']["start"] = $schedule[5];
       $course[substr($afternoon[2],0,2)]['A']["end"] = $schedule[7];
       $course[substr($afternoon[2],0,2)]['B']["end"] = $schedule[7];
       if(strrpos($afternoon[2], "1")){
         $course[substr($afternoon[2],0,2)]['room'] = $rooms[1];
       }
       else {
         $course[substr($afternoon[2],0,2)]['room'] = $rooms[0];
       }
    }
   
    if(isset($afternoon[5]) && strlen($afternoon[5])>2) {
      $course[substr($afternoon[5],0,2)][$afternoon[5][2]]["course"] = trim($afternoon[3]);
      $course[substr($afternoon[5],0,2)][$afternoon[5][2]]["speaker"] = trim($speakerAfternoon[1]);
      $course[substr($afternoon[5],0,2)][$afternoon[5][2]]["start"] = $schedule[5];
      $course[substr($afternoon[5],0,2)][$afternoon[5][2]]["end"] = $schedule[7];
      if(strrpos($afternoon[5], "1")){
        $course[substr($afternoon[5],0,2)]['room'] = $rooms[1];
      }
      else {
        $course[substr($afternoon[5],0,2)]['room'] = $rooms[0];
      }
    }
    elseif(isset($afternoon[5])) {
       $course[substr($afternoon[5],0,2)]['A']["course"] = trim($afternoon[3]);
       $course[substr($afternoon[5],0,2)]['B']["course"] = trim($afternoon[3]);
       $course[substr($afternoon[5],0,2)]['A']["speaker"] = trim($speakerAfternoon[1]);
       $course[substr($afternoon[5],0,2)]['B']["speaker"] = trim($speakerAfternoon[1]);
       $course[substr($afternoon[5],0,2)]['A']["start"] = $schedule[5];
       $course[substr($afternoon[5],0,2)]['B']["start"] = $schedule[5];
       $course[substr($afternoon[5],0,2)]['A']["end"] = $schedule[7];
       $course[substr($afternoon[5],0,2)]['B']["end"] = $schedule[7];
       if(strrpos($afternoon[5], "1")){
         $course[substr($afternoon[5],0,2)]['room'] = $rooms[1];
       }
       else {
         $course[substr($afternoon[5],0,2)]['room'] = $rooms[0];
       }
    }
  }
  else {
    $course[substr($afternoon[1],0,2)]['A']["course"] = trim($afternoon[0]);
    $course[substr($afternoon[1],0,2)]['B']["course"] = trim($afternoon[0]);
    $course[substr($afternoon[1],0,2)]['A']["speaker"] = trim($speakerAfternoon[0]);
    $course[substr($afternoon[1],0,2)]['B']["speaker"] = trim($speakerAfternoon[0]);
    $course[substr($afternoon[1],0,2)]['A']["start"] = $schedule[5];
    $course[substr($afternoon[1],0,2)]['B']["start"] = $schedule[5];
    $course[substr($afternoon[1],0,2)]['A']["end"] = $schedule[7];
    $course[substr($afternoon[1],0,2)]['B']["end"] = $schedule[7];
    if(strrpos($afternoon[1], "1")){
      $course[substr($afternoon[1],0,2)]['room'] = $rooms[1];
    }
    else {
      $course[substr($afternoon[1],0,2)]['room'] = $rooms[0];
    }
  }
  
  
  $courses['s2'] = $course;
  $course = array();
  
  //PUSH IN PROMO AND DAY
  $day[$datas[$i]['A']] = $courses;
  $promo[explode(" ", $datas[1]['O'])[0]] = $day;
}

echo "<pre>";
print_r($promo);
echo "</pre>";


echo json_encode($promo);

?>