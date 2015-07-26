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


site_header('Error Message CSS');

$page_str = <<<EOPAGESTR

<p>Example:</p>

<pre>
  // Define &#36;message_str based on the value of &#36;status_message
  // and how you want HTML to represent it.
  if ( &#36;status_message == "" || !isset(&#36;status_message) ) {
    &#36;message_str = "";
  } else {
    &#36;message_str = "&lt;p class=\"errmsg\">&#36;status_message&lt;/p>";
  }


  // --------------
  // Construct Page
  // --------------

  site_header('Create A New user_type');

  &#36;userform_str = &lt;&lt;&lt;EOUSERFORMSTR

&#36;message_str

&lt;P>If you see a form below then choose a User Type.
</pre>

<p>Example (cont'd):</p>

<pre>
&#36;status_message =  'Error -- you did not complete a required field.';
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>