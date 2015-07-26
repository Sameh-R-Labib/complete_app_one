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


site_header('Is American Date Interval');

$page_str = <<<EOPAGESTR

<p>Include file is found in this directory of the website:</p>

<pre>
/web/includes/generalFormFuncs.php
</pre>

<p>Usage Example:</p>

<pre>
if ( strlen(&#36;timeInterval) > 0 and !isDateInterval(&#36;timeInterval) ) { &#36;isAnomaly = true; }
</pre>

<p>Date Interval Format (amount of time - NOT a date):</p>

<pre>
mm/dd/yyyy
</pre>

<p>Code For isDateInterval Function:</p>

<pre>
function isDateInterval(&#36;i_sDate) {
/*
function isDateInterval
boolean isDateInterval(string)
Summary: checks if a date interval is formatted correctly: mm/dd/yyyy (US English)
Author: Sameh Labib
Date: 09/09/2010
*/

  &#36;blnValid = TRUE;
  
  if ( &#36;i_sDate == "00/00/0000" ) { return &#36;blnValid; }

  if(!ereg ("^[0-9]{2}/[0-9]{2}/[0-9]{4}&#36;", &#36;i_sDate)) {
    &#36;blnValid = FALSE;
  }

  return (&#36;blnValid);
} //end function isDateInterval
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>