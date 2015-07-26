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


site_header('Validate Numeric Variables');

$page_str = <<<EOPAGESTR

<p>Finds whether a variable is a number (or a numeric string). This string
would result in a TRUE.</p>

<pre>
+0123.45e6
</pre>

<p>Example of usage:</p>

<pre>
if ( strlen(&#36;cost) > 0 and !is_numeric(&#36;cost)) {&#36;isAnomaly = true; }
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>