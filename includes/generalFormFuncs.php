<?php

function isDate($i_sDate) {
/*
function isDate
boolean isDate(string)
Summary: checks if a date is formatted correctly: mm/dd/yyyy (US English)
Author: Laurence Veale (modified by Sameh Labib)
Date: 07/30/2001
*/

  $blnValid = TRUE;
  
  if ( $i_sDate == "00/00/0000" ) { return $blnValid; }
  
  // check the format first (may not be necessary as we use checkdate() below)
  if(!ereg ("^[0-9]{2}/[0-9]{2}/[0-9]{4}$", $i_sDate)) {
    $blnValid = FALSE;
  } else {
    //format is okay, check that days, months, years are okay
    $arrDate = explode("/", $i_sDate); // break up date by slash
    $intMonth = $arrDate[0];
    $intDay = $arrDate[1];
    $intYear = $arrDate[2];
    
    $intIsDate = checkdate($intMonth, $intDay, $intYear);
    
    if(!$intIsDate) {
      $blnValid = FALSE;
    }
  }//end else
  
  return ($blnValid);
} //end function isDate




function isDateTime($dateTime_in) {
/*
Purpose: Return truth about $dateTime_in. Is it a MySQL datetime string formatted
as ccyy-mm-dd hh:mm:ss ... huh?
Author: Sameh Labib
Date: 09/16/2010
*/

  $strIsValid = TRUE;
  
  if ( $dateTime_in == "0000-00-00 00:00:00" ) { return $strIsValid; }

  // check the format first
  if (!ereg("^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$", $dateTime_in)) {
    $strIsValid = FALSE;
  } else {
    // format is okay, check that years, months, days, hours, minutes , seconds
    // are okay
    $dateTimeAry = explode(" ", $dateTime_in); // break up string by space into date, time
    $dateStr = $dateTimeAry[0];
    $timeStr = $dateTimeAry[1];
    
    $dateAry = explode("-", $dateStr); // break up date string by hyphen
    $yearVal = $dateAry[0];
    $monthVal = $dateAry[1];
    $dayVal = $dateAry[2];
    
    $timeAry = explode(":", $timeStr); // break up time string by colon
    $hourVal = $timeAry[0];
    $minVal = $timeAry[1];
    $secVal = $timeAry[2];
    
    $dateValIsDate = checkdate($monthVal, $dayVal, $yearVal);
    
    if ($hourVal > -1 && $hourVal < 24 && $minVal > -1 && $minVal < 60
        && $secVal > -1 && $secVal < 60) {
      $timeValIsTime =  TRUE;
    } else {
      $timeValIsTime =  FALSE;
    }

    if(!$dateValIsDate || !$timeValIsTime) {
      $strIsValid = FALSE;
    }
  }

  return ($strIsValid);
}




function isDateInterval($i_sDate) {
/*
function isDateInterval
boolean isDateInterval(string)
Summary: checks if a date interval is formatted correctly: mm/dd/yyyy (US English)
Author: Sameh Labib
Date: 09/09/2010
*/

  $blnValid = TRUE;
  
  if ( $i_sDate == "00/00/0000" ) { return $blnValid; }

  if(!ereg ("^[0-9]{2}/[0-9]{2}/[0-9]{4}$", $i_sDate)) {
    $blnValid = FALSE;
  }

  return ($blnValid);
} //end function isDateInterval

?>