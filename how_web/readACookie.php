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


site_header('Read a Cookie');

$page_str = <<<EOPAGESTR

<p>Example:</p>

<pre>
&#36;user_name = &#36;_COOKIE['user_name'];
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>