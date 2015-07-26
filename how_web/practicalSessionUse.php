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


site_header('Practical Session Use');

$page_str = <<<EOPAGESTR

<p>From login.php:</p>

<pre>
// If they're logged in, log them out
// They shouldnt be able to see this page logged-in
// This allows the same page to be used as a logout script
if (&#36;LOGGED_IN = user_isloggedin()) {
  user_logout();
  session_start();
  &#36;_COOKIE['user_name'] = '';
  &#36;_COOKIE['id_hash'] = '';
  &#36;_COOKIE['user_type'] = '';
  &#36;_COOKIE['user_type_hash'] = '';
  session_destroy();
  session_unset();
  unset(&#36;LOGGED_IN);
}
</pre>

<p>In each script on my site that uses sessions have:</p>

<pre>
session_start();

// User must be logged in and be someone special.
if (!user_isloggedin() || !user_type_cookieIsSet()) {
  // Redirect to login page.
  form_destroy();
  &#36;host = &#36;_SERVER['HTTP_HOST'];
  header("Location: http://{&#36;host}/web/login.php");
  exit;
}
if (&#36;_COOKIE['user_type'] != 1) {
  form_destroy();
  die('Script aborted #3098. -Programmer.');
}

// Cancel if requested.
if (isset(&#36;_POST['cancel'])) {
  form_destroy();
  &#36;host = &#36;_SERVER['HTTP_HOST'];
  &#36;uri = &#36;_SERVER['PHP_SELF'];
  header("Location: http://&#36;host&#36;uri");
  exit;
}

if (isSet(&#36;_SESSION['STD_mode'])) {
  &#36;mode = &#36;_SESSION['STD_mode'];
} else {
  &#36;mode = 'stageOne';
}

function form_destroy() {
/*
Reset all &#36;_SESSION variables for this form to their default values.
*/
  &#36;_SESSION['STD_mode'] = 'stageOne';
  &#36;_SESSION['STD_vclID'] = "";
  &#36;_SESSION['STD_mileCurr'] = "";
  &#36;_SESSION['STD_mileLast'] = "";
  &#36;_SESSION['STD_vclNumber'] = NULL;
  &#36;_SESSION['STD_changed'] = array();
  &#36;_SESSION['STD_submitToken'] = "";

  return;
}
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>