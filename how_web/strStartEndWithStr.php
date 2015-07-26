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


site_header('Validate String Start & End Sub-String');

$page_str = <<<EOPAGESTR

<p>Example:</p>

<pre>
/*
Validate the scriptFileDir by making sure:
  1. It's not too long or short.
  2. It starts with a '/' and ends with a '/'.
*/
&#36;length = strlen(&#36;scriptFileDir);
if (&#36;length > 90 OR &#36;length < 1) {
  form_destroy();
  die('Problem with string length. Err 3643348226. -Programmer.');
}

&#36;beginning = strpos(&#36;scriptFileDir, '/');
&#36;temp = strrpos(&#36;scriptFileDir, '/');
&#36;length = strlen(&#36;scriptFileDir);
&#36;end =  &#36;temp - (&#36;length - 1);

if (&#36;beginning !== 0 OR &#36;end !== 0) {
  form_destroy();
  die("The path string does not start and end with a '/' (2087). -Programmer.");
}
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>