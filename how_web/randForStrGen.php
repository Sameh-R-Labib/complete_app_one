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


site_header('Generate Random String');

$page_str = <<<EOPAGESTR

<p>Example:</p>

<pre>
// Generate a random password
&#36;password = "";
&#36;alphanum =
array('a','b','c','d','e','f','g','h','i','j','k','m','n','o',
  'p','q','r','s','t','u','v','x','y','z','A','B','C','D','E',
  'F','G','H','I','J','K','M','N','P','Q','R','S','T','U',
  'V','W','X','Y','Z','2','3','4','5','6','7','8','9');
&#36;chars = sizeof(&#36;alphanum);
&#36;a = time();
mt_srand(&#36;a);
for (&#36;i=0; &#36;i < 6; &#36;i++) {
  &#36;randnum = intval(mt_rand(0,56));
  &#36;password .= &#36;alphanum[&#36;randnum];
}
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>