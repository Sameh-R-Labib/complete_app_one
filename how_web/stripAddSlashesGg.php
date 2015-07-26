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


site_header('Stripslashes and Addslashes');

$page_str = <<<EOPAGESTR

<p>Values retrieved from the database using PHP are having the slashes automatically
striped off. This may be happening as a result of magic quotes GPC being ON.</p>

<p>To know whether magic quotes GPC is ON or OFF use:</p>

<pre>
get_magic_quotes_gpc()
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>