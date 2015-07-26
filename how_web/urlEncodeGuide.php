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


site_header('Urlencode');

$page_str = <<<EOPAGESTR

<p>The urlencode function transforms a string into something which is safe
to have in a browser's URL request window. The web server returns the
string to its original state.</p>

<pre>
&#36;new_composite = urlencode(&#36;new_composite);
&#36;string = "&lt;a href=&#92;"&#36;php_self?composite=&#36;new_composite&#92;">&#36;nextPhrase_IN&lt;/a>";
</pre>

<p>Example:</p>

<pre>
          // Send the confirmation email
          &#36;encoded_email = urlencode(&#36;_POST['email']);
          &#36;mail_body = &lt;&lt;&lt;EOMAILBODY
Thank you for registering at www.gxsam11.net. Click this link
to confirm your registration:

http://www.gxsam11.net/web/confirm.php?hash=&#36;hash&email=&#36;encoded_email

Once you see a confirmation message, you will be logged into
www.gxsam11.net
EOMAILBODY;
          mail(&#36;email, 'www.gxsam11.net registration confirmation',
            &#36;mail_body, 'From: noreply@gxsam11.net');
          // Give a successful registration message
          &#36;feedback = 'YOU HAVE SUCCESSFULLY REGISTERED.
            You will receive a confirmation email soon';
          return &#36;feedback;
</pre>

EOPAGESTR;
echo $page_str;

site_footer();
?>