<?php

set_include_path(get_include_path() . PATH_SEPARATOR . 'Classes/');
include 'PHPExcel/IOFactory.php';

$files = array();
// SETTINGS
$server     = '{imap.gmail.com:993/imap/ssl}INBOX';
$username   = 'heticallendar@gmail.com';
$password   = 'hetic2016';
$export_dir = 'export/'; #final slash is required
// END SETTINGS
 
$mbox = imap_open($server, $username, $password) or die('Unable to login');

// Getting all emails
if ($headers = imap_headers($mbox)) {
    $i = 0;
    foreach ($headers as $val) {
        $i ++;
 
        // Will return many infos about current email
        // Use var_dump($info) to check content
        $info   = imap_headerinfo($mbox, $i);
        $msgid  = trim($info->Msgno);
 
        // Gets the current email structure (including parts)
        // Use var_dump($structure) to check it out
        $structure = imap_fetchstructure($mbox, $msgid);
 
        // Getting attachments
        // Will return an array with all included files
        // Also works with inline attachments
        $attachments = get_attachments($structure);
 
        // You are now able to get attachments' raw content
        foreach ($attachments as $k => $at) {
            $filename = $export_dir.$at['filename'];
            echo $filename;
            $content = imap_fetchbody($mbox, $msgid, $at['part']);
 
            if ($content !== false && strlen($content) > 0 && $content != '') {
                switch ($at['encoding']) {
                    case '3':
                        $content = base64_decode($content);
                    break;
 
                    case '4':
                        $content = quoted_printable_decode($content);
                    break;
                }
 
                file_put_contents($filename, $content);
                array_push($files, $filename);
            }
        }
    }
}

imap_close($mbox);
echo "OK<br> <pre>";
print_r($files);
echo "</pre>";

/**
* Gets all attachments
* Including inline images or such
* @author: Axel de Vignon
* @param $content: the email structure
* @param $part: not to be set, used for recursivity
* @return array(type, encoding, part, filename)
*
*/
function get_attachments($content, $part = null, $skip_parts = false) {
    static $results;
 
    // First round, emptying results
    if (is_null($part)) {
        $results = array();
    }
    else {
        // Removing first dot (.)
        if (substr($part, 0, 1) == '.') {
            $part = substr($part, 1);
        }
    }
 
    // Saving the current part
    $actualpart = $part;
    // Split on the "."
    $split = explode('.', $actualpart);
 
    // Skipping parts
    if (is_array($skip_parts)) {
        foreach ($skip_parts as $p) {
            // Removing a row off the array
            array_splice($split, $p, 1);
        }
        // Rebuilding part string
        $actualpart = implode('.', $split);
    }
 
    // Each time we get the RFC822 subtype, we skip
    // this part.
    if (strtolower($content->subtype) == 'rfc822') {
        // Never used before, initializing
        if (!is_array($skip_parts)) {
            $skip_parts = array();
        }
        // Adding this part into the skip list
        array_push($skip_parts, count($split));
    }
 
    // Checking ifdparameters
    if (isset($content->ifdparameters) && $content->ifdparameters == 1 && isset($content->dparameters) && is_array($content->dparameters)) {
        foreach ($content->dparameters as $object) {
            if (isset($object->attribute) && preg_match('~filename~i', $object->attribute)) {
                $results[] = array(
                'type'          => (isset($content->subtype)) ? $content->subtype : '',
                'encoding'      => $content->encoding,
                'part'          => empty($actualpart) ? 1 : $actualpart,
                'filename'      => $object->value
                );
            }
        }
    }
 
    // Checking ifparameters
    else if (isset($content->ifparameters) && $content->ifparameters == 1 && isset($content->parameters) && is_array($content->parameters)) {
        foreach ($content->parameters as $object) {
            if (isset($object->attribute) && preg_match('~name~i', $object->attribute)) {
                $results[] = array(
                'type'          => (isset($content->subtype)) ? $content->subtype : '',
                'encoding'      => $content->encoding,
                'part'          => empty($actualpart) ? 1 : $actualpart,
                'filename'      => $object->value
                );
            }
        }
    }
 
    // Recursivity
    if (isset($content->parts) && count($content->parts) > 0) {
        // Other parts into content
        foreach ($content->parts as $key => $parts) {
            get_attachments($parts, ($part.'.'.($key + 1)), $skip_parts);
        }
    }
    return $results;
}






//Parsing


foreach($files as $file) {

  $inputFileName = $file;

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
  
    // Jour de la semaine
    $courses['day'] = array("name"=>$datas[$i]['A'], "number"=>$datas[$i]['B'], "month"=>$datas[$i]['C'], "year"=>$datas[$i]['D']);

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
  
  $outputFileName = $export_dir.strtolower(substr($inputFileName, strpos($inputFileName, "/")+1, 2)).".json"; 
  
  file_put_contents ($outputFileName, json_encode($promo));
}
