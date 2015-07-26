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


site_header('Protect from Wanderer');

$page_str = <<<EOPAGESTR

<p>In the function form_destroy put:</p>

<pre>
  &#36;_SESSION['PREPEND_submitToken'] = "";
</pre>

<p>In the function that produces the form put:</p>

<pre>
  /*
  Manage protection from aborted form since code uses sessions.
  In other words the code which validates the values received from a
  submitted form needs to know that it is not executing after the user
  has come back after a previously abandoned instance of the script
  (another possibility is the user may have attempted to run two
  instance of the script).
  */
  &#36;submitToken = time();
  &#36;_SESSION['PREPEND_submitToken'] = &#36;submitToken;
</pre>

<p>and (in the form) put:</p>

<pre>
  &lt;div&gt;
    &lt;input type="hidden" name="submitToken" value="&#36;submitToken"&gt;
  &lt;/div&gt;
</pre>

<p>In the function that validates put:</p>

<pre>
  /*
  Handle the situation where we have arrived here as a result of the user
  wandering back to the script after giving up in the middle of running it
  earlier when presented with a form (or running multiple instances).
  */
  if (isset(&#36;_POST['submitToken'])) {
    &#36;submitToken = &#36;_POST['submitToken'];
  } else {
    &#36;submitToken = "";
  }
  if (&#36;submitToken != &#36;_SESSION['PREPEND_submitToken']) {
    form_destroy();
    &#36;host = &#36;_SERVER['HTTP_HOST'];
    &#36;uri = &#36;_SERVER['PHP_SELF'];
    header("Location: http://&#36;host&#36;uri");
    exit;
  }
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>