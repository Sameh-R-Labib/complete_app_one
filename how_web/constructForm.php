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


site_header('Construct Form');

$page_str = <<<EOPAGESTR

<p>Example:</p>

<pre>
/*
Construct form:
*/
site_header('Add Company Vehicle');

// Superglobals don't work with heredoc
&#36;php_self = &#36;_SERVER['PHP_SELF'];

if ( !isset(&#36;status_message) || &#36;status_message == "" ) {
  &#36;message_str = "";
} else {
  &#36;message_str = "&lt;p class=\"errmsg\"&gt;&#36;status_message&lt;/p&gt;";
}

&#36;userform_str = &lt;&lt;&lt;EOUSERFORMSTR

&#36;message_str

&lt;p&gt;This page allows you to update a company vehicle record in the database table
for company vehicles. This table is used by other scripts (including
the ones for maintenance.)&lt;/p&gt;

&lt;p&gt;Tag: &#36;tag&lt;br/&gt;
State: &#36;state&lt;/p&gt;

&lt;form action="&#36;php_self" method="post" class="loginform"&gt;
  &lt;fieldset&gt;
  &lt;legend&gt;Form:&lt;/legend&gt;
  &lt;div&gt;
    &lt;label for="title" class="fixedwidth"&gt;title&lt;/label&gt;
    &lt;input type="text" name="title" id="title" value="&#36;title" size="9" maxlength="14"/&gt;
  &lt;/div&gt;
&#36;isInFleetBox
  &lt;div class="buttonarea"&gt;    
    &lt;input type="submit" name="submit" value="Edit vehicle data"/&gt;
  &lt;/div&gt;
  &lt;/fieldset&gt;
&lt;/form&gt;

EOUSERFORMSTR;
echo &#36;userform_str;
site_footer();
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>