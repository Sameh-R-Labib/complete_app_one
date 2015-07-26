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


site_header('foreach');

$page_str = <<<EOPAGESTR

<p>Example from Create A KDA:</p>

<pre>
/*
Put all the phrases into one array.
*/
&#36;arr = array_merge(&#36;sp, &#36;tp);

/*
Add slashes to phrases.
*/
reset(&#36;arr);
foreach (&#36;arr as &#36;phraseKey => &#36;phrase) {
  &#36;arr[&#36;phraseKey] = addslashes(&#36;phrase);
}

/*
Make a string called &#36;composite.
*/
&#36;composite = "";

/*
Make sure the appropriate records exist in order for associations to exist.
*/
reset(&#36;arr);
foreach (&#36;arr as &#36;phrase) {
  insertIfNotExist(&#36;composite, &#36;phrase);
  &#36;composite = &#36;composite . ":::" . &#36;phrase;
}
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>