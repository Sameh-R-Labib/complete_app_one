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


site_header('Send Email Using PHP');

$page_str = <<<EOPAGESTR

<p>Example:</p>

<pre>
// Send the email
&#36;to      = &#36;_POST['email'];
&#36;from    = "forgot@gxsam11.net";
&#36;subject = "New password";

&#36;msg     = &lt;&lt;&lt; EOMSG
You recently requested that we send you a new password for
gxsam11.net. Your new password is:

            &#36;password

Please log in at this URL:

             http://gxsam11.net/web/login.php

Then go to this address to change your password:

             http://gxsam11.net/web/changepass.php
EOMSG;

&#36;mailsend = mail("&#36;to","&#36;subject","&#36;msg","From:
  &#36;from&#92;r&#92;nReply-To:gxsam11@gxsam11.net");
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