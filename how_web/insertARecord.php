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


site_header('Insert a Record');

$page_str = <<<EOPAGESTR

<p>Code Sample</p>

<pre>
function createNewRecord() {
/* Inserts a new vehicle record populating it with the
valid tag and state strings supplied by the user. */

  global &#36;tag, &#36;state;
  
  &#36;query = "INSERT INTO vehicles (tag, state)
            VALUES ('&#36;tag', '&#36;state')";
  &#36;result = mysql_query(&#36;query);
  if (!&#36;result || mysql_affected_rows() < 1) {
    die('Error adding new record. 00099. -Programmer.');
  } else {
    return;
  }
}
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>