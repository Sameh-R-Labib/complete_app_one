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


site_header('Form Comment CSS Class');

$page_str = <<<EOPAGESTR

<p>Example:</p>

<pre>
&lt;form action="&#36;php_self" method="post" class="loginform">
  &lt;fieldset>
  &lt;legend>User Types: &lt;span class="formcomment">*Required&lt;/span>&lt;/legend>
  &lt;div>
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>