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


site_header('Discover URL Parts');

$page_str = <<<EOPAGESTR

<p>Example:</p>

<pre>
form_destroy();
&#36;host = &#36;_SERVER['HTTP_HOST'];
&#36;uri = &#36;_SERVER['PHP_SELF'];
header("Location: http://&#36;host&#36;uri");
exit;
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>