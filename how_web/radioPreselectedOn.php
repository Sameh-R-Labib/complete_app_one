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


site_header('Radio Button Preselected');

$page_str = <<<EOPAGESTR

<p>Example:</p>

<pre>
// Construct availability radio control
if ( &#36;availability == "S" ) {
  &#36;availability_button =
    '&lt;div>&lt;input type="radio" name="availability" id="availSub" value="S" checked="checked"/>' .
    ' &lt;label for="availSub">Substitute&lt;/label>&lt;br/>' .
    '&lt;input type="radio" name="availability" id="availReg" value="R"/>' .
    ' &lt;label for="availReg">Regular&lt;/label>&lt;br/>' .
    '&lt;input type="radio" name="availability" id="availAssist" value="A"/>' .
    ' &lt;label for="availAssist">Assistant&lt;/label>&lt;br/>' .
    '&lt;input type="radio" name="availability" id="availNone" value="N"/>' .
    ' &lt;label for="availNone">None&lt;/label>&lt;/div>';
} elseif ( &#36;availability == "R" ) {
  &#36;availability_button =
    '&lt;div>&lt;input type="radio" name="availability" id="availSub" value="S"/>' .
    ' &lt;label for="availSub">Substitute&lt;/label>&lt;br/>' .
    '&lt;input type="radio" name="availability" id="availReg" value="R" checked="checked"/>' .
    ' &lt;label for="availReg">Regular&lt;/label>&lt;br/>' .
    '&lt;input type="radio" name="availability" id="availAssist" value="A"/>' .
    ' &lt;label for="availAssist">Assistant&lt;/label>&lt;br/>' .
    '&lt;input type="radio" name="availability" id="availNone" value="N"/>' .
    ' &lt;label for="availNone">None&lt;/label>&lt;/div>';
} elseif ( &#36;availability == "A" ) {
  &#36;availability_button =
    '&lt;div>&lt;input type="radio" name="availability" id="availSub" value="S"/>' .
    ' &lt;label for="availSub">Substitute&lt;/label>&lt;br/>' .
    '&lt;input type="radio" name="availability" id="availReg" value="R"/>' .
    ' &lt;label for="availReg">Regular&lt;/label>&lt;br/>' .
    '&lt;input type="radio" name="availability" id="availAssist" value="A" checked="checked"/>' .
    ' &lt;label for="availAssist">Assistant&lt;/label>&lt;br/>' .
    '&lt;input type="radio" name="availability" id="availNone" value="N"/>' .
    ' &lt;label for="availNone">None&lt;/label>&lt;/div>';
} elseif ( &#36;availability == "N" ) {
  &#36;availability_button =
    '&lt;div>&lt;input type="radio" name="availability" id="availSub" value="S"/>' .
    ' &lt;label for="availSub">Substitute&lt;/label>&lt;br/>' .
    '&lt;input type="radio" name="availability" id="availReg" value="R"/>' .
    ' &lt;label for="availReg">Regular&lt;/label>&lt;br/>' .
    '&lt;input type="radio" name="availability" id="availAssist" value="A"/>' .
    ' &lt;label for="availAssist">Assistant&lt;/label>&lt;br/>' .
    '&lt;input type="radio" name="availability" id="availNone" value="N" checked="checked"/>' .
    ' &lt;label for="availNone">None&lt;/label>&lt;/div>';
} else {
  &#36;availability_button =
    '&lt;div>&lt;input type="radio" name="availability" id="availSub" value="S"/>' .
    ' &lt;label for="availSub">Substitute&lt;/label>&lt;br/>' .
    '&lt;input type="radio" name="availability" id="availReg" value="R"/>' .
    ' &lt;label for="availReg">Regular&lt;/label>&lt;br/>' .
    '&lt;input type="radio" name="availability" id="availAssist" value="A"/>' .
    ' &lt;label for="availAssist">Assistant&lt;/label>&lt;br/>' .
    '&lt;input type="radio" name="availability" id="availNone" value="N"/>' .
    ' &lt;label for="availNone">None&lt;/label>&lt;/div>';
}
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>