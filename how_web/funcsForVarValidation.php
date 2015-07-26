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


site_header('Functions for Variable Validation');

$page_str = <<<EOPAGESTR

<h2>empty()</h2>

<p>Determine whether a variable is considered to be empty.
The following things are considered to be empty:</p>

<ul>
  <li>"" (an empty string)</li>
  <li>0 (0 as an integer)</li>
  <li>"0" (0 as a string)</li>
  <li>NULL</li>
  <li>FALSE</li>
  <li>array() (an empty array)</li>
  <li>var &#36;var; (a variable declared, but without a value in a class)</li>
</ul>

<pre>
if (empty(&#36;sp[0])) {
  form_destroy();
  die('Subject phrases missing 467778777. -Programmer.');
}
</pre>

<h2>!&#36;var</h2>

<p>Determine whether the user supplied a field value:</p>

<pre>
if (!&#36;_POST['user_name'] || !&#36;_POST['password']) {
  &#36;feedback = 'ERROR -- Missing username or password';
  return &#36;feedback;
}
</pre>

<p>A &#36;_POST value is set to "" when a form is submitted and the
field is blank.</p>

EOPAGESTR;
echo $page_str;

site_footer();
?>