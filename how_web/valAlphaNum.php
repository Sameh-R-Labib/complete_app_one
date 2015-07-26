<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
VALIDATE ALPHANUMERIC STRING
----------------------------
This script presents a how-to on the subject of validating a
string for alphanumeric.
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


site_header('Validate Alphanumeric String');

$page_str = <<<EOPAGESTR

<p>Here is a code sample which validates a string is all alphanumeric:</p>

<pre>
if (!ctype_alnum(&#36;vehicleTag)) { return false; }
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>