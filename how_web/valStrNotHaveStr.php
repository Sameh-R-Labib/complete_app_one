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


site_header('Validate String Not Have This String');

$page_str = <<<EOPAGESTR

<p>Example:</p>

<pre>
  /*
  Make sure TP strings do no contain ':::'.
  */
  if (strpos(&#36;tp_1, ':::') !== FALSE OR strpos(&#36;tp_2, ':::') !== FALSE
  OR strpos(&#36;tp_3, ':::') !== FALSE OR strpos(&#36;tp_4, ':::') !== FALSE
  OR strpos(&#36;tp_5, ':::') !== FALSE OR strpos(&#36;tp_6, ':::') !== FALSE) {
    form_destroy();
    die("You are not allowed to have ':::' in your phrase string (518). -Programmer.");
  }
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>