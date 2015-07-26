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


site_header('Read Record Into Variables');

$page_str = <<<EOPAGESTR

<p>Example:</p>

<pre>
function reloadDataVars() {
/* Puts the data from an existent part record into the
variables which will populate an update form. It is assumed
that &#36;label and &#36;partNumber correspond to a part record which
exists already. Also, this function makes sure that all form
variables get initialized with values and defined as global.
Otherwise, it aborts.
*/

  // All form variables are global.
  global &#36;label, &#36;manufacturer, &#36;retailer, &#36;partNumber, &#36;price, &#36;quantityHave;

  &#36;query = "SELECT manufacturer, retailer, price, quantityHave
            FROM maintenVehicleParts
            WHERE label = '&#36;label' AND partNumber = '&#36;partNumber'";
  &#36;result = mysql_query(&#36;query);
  if (!&#36;result || mysql_num_rows(&#36;result) < 1) {
    die('Error 56801. -Programmer.');
  } else {
    &#36;user_array = mysql_fetch_array(&#36;result);
    
    &#36;manufacturer = &#36;user_array['manufacturer'];
    &#36;retailer = &#36;user_array['retailer'];
    &#36;price = &#36;user_array['price'];
    &#36;quantityHave = &#36;user_array['quantityHave'];
    
    // Text and Textarea fields have had backslashes
    // added to escape single quotes ('), double
    // quotes ("), backslashes (\) and NULL before
    // insertion into the database. Therefore, we must
    // undo this before displaying these strings.
    &#36;manufacturer = stripslashes(&#36;manufacturer);
    &#36;retailer = stripslashes(&#36;retailer);
    
    // Even though &#36;label and &#36;partNumber were
    // not retrieved
    &#36;label = stripslashes(&#36;label);
    &#36;partNumber = stripslashes(&#36;partNumber);

  }

  return;
}
</pre>

<p>Better Example:</p>

<pre>
  &#36;query = "SELECT scriptFileName, scriptFileDir, shortTitle
            FROM kds_kda
            WHERE id='&#36;kdaId'";
  &#36;result = mysql_query(&#36;query);
  if (!&#36;result) {
    die('Query failed. Err: 1215895096. -Programmer.');
  }
  if (mysql_num_rows(&#36;result) != 1) {
    die('Query failed. Err: 7805485985. -Programmer.');
  }
  &#36;row = mysql_fetch_array(&#36;result, MYSQL_ASSOC);
  &#36;scriptFileName = &#36;row['scriptFileName'];
  &#36;scriptFileDir = &#36;row['scriptFileDir'];
  &#36;shortTitle = &#36;row['shortTitle'];
  &#36;shortTitle = stripslashes(&#36;shortTitle);
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>