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


site_header('Is American Date');

$page_str = <<<EOPAGESTR

<p>Include file is found in this directory of the website:</p>

<pre>
/web/includes/generalFormFuncs.php
</pre>

<p>Usage Example:</p>

<pre>
if ( strlen(&#36;dateOfPurchase) > 0 and !isDate(&#36;dateOfPurchase) ) { &#36;isAnomaly = true; }
</pre>

<p>Date Format:</p>

<pre>
mm/dd/yyyy
</pre>

<p>Code For isDate Function:</p>

<pre>
function isDate(&#36;i_sDate) {
/*
function isDate
boolean isDate(string)
Summary: checks if a date is formatted correctly: mm/dd/yyyy (US English)
Author: Laurence Veale (modified by Sameh Labib)
Date: 07/30/2001
*/

  &#36;blnValid = TRUE;
  
  if ( &#36;i_sDate == "00/00/0000" ) { return &#36;blnValid; }
  
  // check the format first (may not be necessary as we use checkdate() below)
  if(!ereg ("^[0-9]{2}/[0-9]{2}/[0-9]{4}&#36;", &#36;i_sDate)) {
    &#36;blnValid = FALSE;
  } else {
    //format is okay, check that days, months, years are okay
    &#36;arrDate = explode("/", &#36;i_sDate); // break up date by slash
    &#36;intMonth = &#36;arrDate[0];
    &#36;intDay = &#36;arrDate[1];
    &#36;intYear = &#36;arrDate[2];
    
    &#36;intIsDate = checkdate(&#36;intMonth, &#36;intDay, &#36;intYear);
    
    if(!&#36;intIsDate) {
      &#36;blnValid = FALSE;
    }
  }//end else
  
  return (&#36;blnValid);
} //end function isDate
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>