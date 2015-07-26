<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
VALIDATE - VARIOUS EXAMPLES
---------------------------
Here are some examples of form input validation which may not be mentioned
elswhere on this website.
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


site_header('Validate - Various Examples');

$page_str = <<<EOPAGESTR

<p>First, get the user submitted strings:</p>

<pre>
// Initialize &#36;tag and &#36;state.
if ( isset(&#36;_POST[submit']) AND ((&#36;_POST['submit'] == "Edit vehicle data")
   OR (&#36;_POST['submit'] == "Retrieve vehicle data")) ) {

  // Initialize &#36;tag and &#36;state.
  if ( isset(&#36;_POST['tag']) ) {
    &#36;tag = &#36;_POST['tag'];
  } else {
    &#36;tag = "";
  }
  if ( isset(&#36;_POST['state']) ) {
    &#36;state = &#36;_POST['state'];
  } else {
    &#36;state = "";
  }
</pre>

<p>Before I validate (user submitted strings) I'll want to make sure
they've had no slashes added.</p>

<pre>
// If magic quotes is on I'll stripslashes.
if ( get_magic_quotes_gpc() ) {
   &#36;tag = stripslashes(&#36;tag);
   &#36;state = stripslashes(&#36;state);
}
</pre>

<p>You'll also want to trim whitespace.</p>

<pre>
// Trim white space.
&#36;tag = trim(&#36;tag);
&#36;state = trim(&#36;state);
</pre>

<p>Verify string is not too long:</p>

<pre>
if ( strlen(&#36;state) > 12 OR strlen(&#36;tag) > 20 ) { return false; }
</pre>

<p>Verify string is not empty:</p>

<pre>
if ( empty(&#36;state) OR empty(&#36;tag) )  { return false; }
</pre>

<p>If the rest of the script assumes field string values are ready
for input into the database then add slashes.</p>

<pre>
// addslashes
&#36;tag = addslashes(&#36;tag);
&#36;state = addslashes(&#36;state);
</pre>

<p>To handle the case where no strings were posted (in other words
an anomaly caused this script to run instead of the proper form
being submitted):</p>

<pre>
} else {
  die('Script aborted #12580. -Programmer.');
}
</pre>

<p>Example of making sure we arrived here properly:</p>

<pre>
// I don't want to rely on registered_globals
if (!IsSet(&#36;_POST['submit']) || &#36;_POST['submit'] != 'Login')
{
  &#36;submit = "";
} else {
  &#36;submit = &#36;_POST['submit'];
}
</pre>

EOPAGESTR;
echo $page_str;


site_footer();
?>