<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
DOES RECORD EXIST (BY FIELD VALUES)
-----------------------------------
This page is for showing how to write code which will tell you if a table in a
database holds a record which has a particular set of field values.
*/

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


site_header('Does Record Exist (by Field Values)');

$page_str = <<<EOPAGESTR

<p>Here is code that tells you if the database table
holds a record having a particular set of field values.</p>

<pre>
function vehicleIsInTable() {
/* Indicates whether the vehicle whose tag and state are
available in the global &#36;tag and &#36;state are found in the
table. */

  global &#36;tag, &#36;state;
  
  &#36;query = "SELECT id
            FROM vehicles
            WHERE tag = '&#36;tag' AND state = '&#36;state'";
  &#36;result = mysql_query(&#36;query);
  
  if (!&#36;result) {
    die('Query failed. Err: 880037. -Programmer.');
  }

  if ( mysql_num_rows(&#36;result) < 1) {
    return false;
  } else {
    return true;
  }
}
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>