<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once('../includes/login_funcs.php');
require_once('../includes/db_vars.php');
require_once('../includes/header_footer.php');

// User must be logged in and be someone special.
if (!user_isloggedin() || !user_type_cookieIsSet()) {
  $host = $_SERVER['HTTP_HOST'];
  header("Location: http://{$host}/web/login.php");
  exit;
}
if ($_COOKIE['user_type'] != 1) {
  die('Script aborted #3098. -Programmer.');
}


site_header('Is American Date-Time');

$page_str = <<<EOPAGESTR

<p>Include file is found in this directory of the website:</p>

<pre>
/web/includes/generalFormFuncs.php
</pre>

<p>Usage Example:</p>

<pre>
if ( strlen(&#36;timedate) > 0 and !isDateTime(&#36;timedate)) { return false; }
</pre>

<p>Date-Time Format:</p>

<pre>
ccyy-mm-dd hh:mm:ss
</pre>

<p>Code For isDateTime Function:</p>

<pre>
function isDateTime(&#36;dateTime_in) {
/*
Purpose: Return truth about &#36;dateTime_in. Is it a MySQL datetime string formatted
as ccyy-mm-dd hh:mm:ss ... huh?
Author: Sameh Labib
Date: 09/16/2010
*/

  &#36;strIsValid = TRUE;
  
  if ( &#36;dateTime_in == "0000-00-00 00:00:00" ) { return &#36;strIsValid; }

  // check the format first
  if (!ereg("^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}&#36;", &#36;dateTime_in)) {
    &#36;strIsValid = FALSE;
  } else {
    // format is okay, check that years, months, days, hours, minutes , seconds
    // are okay
    &#36;dateTimeAry = explode(" ", &#36;dateTime_in); // break up string by space into date, time
    &#36;dateStr = &#36;dateTimeAry[0];
    &#36;timeStr = &#36;dateTimeAry[1];
    
    &#36;dateAry = explode("-", &#36;dateStr); // break up date string by hyphen
    &#36;yearVal = &#36;dateAry[0];
    &#36;monthVal = &#36;dateAry[1];
    &#36;dayVal = &#36;dateAry[2];
    
    &#36;timeAry = explode(":", &#36;timeStr); // break up time string by colon
    &#36;hourVal = &#36;timeAry[0];
    &#36;minVal = &#36;timeAry[1];
    &#36;secVal = &#36;timeAry[2];
    
    &#36;dateValIsDate = checkdate(&#36;monthVal, &#36;dayVal, &#36;yearVal);
    
    if (&#36;hourVal > -1 && &#36;hourVal < 24 && &#36;minVal > -1 && &#36;minVal < 60
        && &#36;secVal > -1 && &#36;secVal < 60) {
      &#36;timeValIsTime =  TRUE;
    } else {
      &#36;timeValIsTime =  FALSE;
    }

    if(!&#36;dateValIsDate || !&#36;timeValIsTime) {
      &#36;strIsValid = FALSE;
    }
  }

  return (&#36;strIsValid);
}
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>