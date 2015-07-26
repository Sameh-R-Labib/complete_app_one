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


site_header('Select from 2 Merged Tables');

$page_str = <<<EOPAGESTR5

<p>Excerpted from /web/includes/processAnInvoice_funcs.php:</p>

<pre>
function midsForThis(&#36;srId_in) {
/*
This function takes a service record ID and returns an array containing all the
maintenance item labels associated with it.

BE AWARE: the array returned must be two dimensional in order for it to be compatible
with the function that displays tables. That is why it's neccessary to have the extra
[0] you see in the while statement below.

If there are no maintenance item labels
associated with it then the function will return an empty array. If the database query
fails then the script will abort. If no service record ID was passed to the
function it will abort.
*/
  &#36;miArr = array();
  if (!isset(&#36;srId_in) OR &#36;srId_in == "") {
    form_destroy();
    die('Error 47821346879. -Programmer.');
  }
  /*
  Query to find all maintenance item labels associated with service record ID.
  */
  &#36;query = "SELECT maintenItems.label
            FROM servRecToMaintenItem INNER JOIN maintenItems
            ON servRecToMaintenItem.maintenItemId = maintenItems.id
            WHERE servRecToMaintenItem.servRecId = '&#36;srId_in'";
  &#36;result = mysql_query(&#36;query);
  if (!&#36;result) {
    form_destroy();
    die('Query failed. Err: 367790875. May want sleep(). -Programmer.');
  }
  if ( mysql_num_rows(&#36;result) < 1) {
    return &#36;miArr;
  }
  while (&#36;row = mysql_fetch_row(&#36;result)) {
    &#36;miArr[][0] = &#36;row[0];
  }
  return &#36;miArr;
}
</pre>

EOPAGESTR5;
echo $page_str;

site_footer();
?>