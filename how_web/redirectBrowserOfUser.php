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


site_header('Redirect User\'s Browser');

$page_str = <<<EOPAGESTR

<p>The header command sends an HTTP header. To work properly
this command must be the first output of the script. Even
something as simple as a space in the wrong place
in your script may be construed as output and thus cause this
command to fail.</p>

<p>Example of redirection using a relative path:</p>

<pre>
// Redirect to login page.
form_destroy();
header("Location: login.php");
exit;
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>