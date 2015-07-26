<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
SHOW PHP PARSE ERRORS
---------------------
This script is presents a how-to on the subject of showing php parse/
runtime errors.
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


site_header('Show PHP Parse Errors');

$page_str = <<<EOPAGESTR

<p>If you want the PHP engine on the web server to spit out errors
back to the browser then include the following code right after
the php shebang:</p>

<pre>
error_reporting(E_ALL);
ini_set("display_errors", 1);
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>