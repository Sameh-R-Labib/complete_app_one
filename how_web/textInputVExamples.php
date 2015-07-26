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


site_header('Text Input Field');

$page_str = <<<EOPAGESTR

<p>Standard Example:</p>

<pre>
&lt;div>
  &lt;label for="lContractor" class="fixedwidth">Most recent contractor&lt;/label>
  &lt;input type="text" name="lContractor"id="lContractor" value="&#36;lContractor" size="21" maxlength="30"/>
&lt;/div>
</pre>

<p>Phone Number Example:</p>

<pre>
&lt;div>
  &lt;label for="phone1_ac" class="fixedwidth">Phone 1&lt;/label>
  &lt;input type="text" name="phone1_ac" id="phone1_ac" value="&#36;phone1_ac" size="3" maxlength="3"/> -
  &lt;input type="text" name="phone1_3d" id="phone1_3d" value="&#36;phone1_3d" size="3" maxlength="3"/> -
  &lt;input type="text" name="phone1_4d" id="phone1_4d" value="&#36;phone1_4d" size="4" maxlength="4"/>
&lt;/div>
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>