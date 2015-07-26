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


site_header('Code a Multi-Stage Form');

$page_str = <<<EOPAGESTR

<p>Template for the script:</p>

<pre>
&lt;?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
TITLE OF THE SCRIPT
-------------------
Description of the script.
*/

session_start();

&#36;docRoot = &#36;_SERVER["DOCUMENT_ROOT"];

require_once("&#36;docRoot/web/includes/login_funcs.php");
require_once("&#36;docRoot/web/includes/db_vars.php");
require_once("&#36;docRoot/web/includes/header_footer.php");

// User must be logged in and be someone special.
if (!user_isloggedin() || !user_type_cookieIsSet()) {
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

if (isSet(&#36;_SESSION['PREPENDFORTHISSCRIPT_mode'])) {
  &#36;mode = &#36;_SESSION['PREPENDFORTHISSCRIPT_mode'];
} else {
  &#36;mode = 'stageOne';
}


if (&#36;mode == 'stageOne') {





  &#36;_SESSION['PREPENDFORTHISSCRIPT_mode'] = 'process the form';
} elseif (&#36;mode == 'process the form') {
  processTheForm();





  &#36;_SESSION['PREPENDFORTHISSCRIPT_mode'] = 'delete or not';
} elseif (&#36;mode == 'delete or not') {





  form_destroy();
} else {
  form_destroy();
  die('The script died for reason # 294875654. -Programmer.');
}




/* **************
FUNCTIONS SECTION:
*/
function form_destroy() {
  &#36;_SESSION['PREPENDFORTHISSCRIPT_mode'] = 'stageOne';
  &#36;_SESSION['PREPENDFORTHISSCRIPT_submitToken'] = "";

  return;
}




function explain_the_script() {


  return;
}




function () {


  return;
}




function () {


  return;
}

?&gt;
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>