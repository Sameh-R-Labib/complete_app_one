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


site_header('Create a Checkbox');

$page_str = <<<EOPAGESTR

<p>In the following code sample a string constituting a checkbox is being assigned to
a string variable. Based on whether &#36;isInFleet == 1, a string constituting
a pre-checked checkbox is assigned or not assigned.</p>

<pre>
// Construct isInFleet checkbox control
if ( &#36;isInFleet == "1" ) {
  &#36;isInFleetBox =
    '  &lt;div&gt;&lt;input type="checkbox" name="isInFleet" id="isInFleet" value="1" checked="checked"/&gt;' .
    '   &lt;label for="isInFleet"&gt;Yes, this vehicle is in our company fleet.&lt;/label&gt;&lt;/div&gt;';
} else {
  &#36;isInFleetBox =
    '  &lt;div&gt;&lt;input type="checkbox" name="isInFleet" id="isInFleet" value="1"/&gt;' .
    '   &lt;label for="isInFleet"&gt;Yes, this vehicle is in our company fleet.&lt;/label&gt;&lt;/div&gt;';
}
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>