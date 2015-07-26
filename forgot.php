<?php

/******************************************
* This file displays the forgot-password  *
* form. It submits to itself, mails a     *
* temporary password, and then redirects  *
* to login.                               *
******************************************/

// A file with the database host, user, password, and
// selected database
$jeffWiggleLabib = "Wake up Jeff 59990!";
require_once('includes/db_vars.php');

// Avoid undefined index notice from php processor
if (!IsSet($_POST['command'])) {
  $command = "";
} else {
  $command = $_POST['command'];
}

// Skip to displaying the page if form not submitted or email too long
if ($command == 'forgot' &&
  strlen($_POST['email'] <= 50)) {
  // Handle submission. This is a one-time only form
  // so there will be no problems with handling errors.
  if ( !get_magic_quotes_gpc() ) {
    $as_email = addslashes($_POST['email']);
  } else {
    $as_email = $_POST['email'];
  }
  $query = "select id from user where email = '$as_email'";
  $result = mysql_query($query);
  $is_user = mysql_num_rows($result);

  if ($is_user == 1) {
    // Generate a random password
    $password = "";
    $alphanum =
array('a','b','c','d','e','f','g','h','i','j','k','m','n','o',
  'p','q','r','s','t','u','v','x','y','z','A','B','C','D','E',
  'F','G','H','I','J','K','M','N','P','Q','R','S','T','U',
  'V','W','X','Y','Z','2','3','4','5','6','7','8','9');
    $chars = sizeof($alphanum);
    $a = time();
    mt_srand($a);
    for ($i=0; $i < 6; $i++) {
      $randnum = intval(mt_rand(0,56));
      $password .= $alphanum[$randnum];
    }
    // One-way encrypt it
    $crypt_pass = md5($password);

    // Put the temp password in the db
    $query = "update user set password = '$crypt_pass'
      where email = '$as_email'";
    $result = mysql_query($query) or die('Cannot complete update');

    // Send the email
    $to      = $_POST['email'];
    $from    = "forgot@gxsam11.net";
    $subject = "New password";
    $msg     = <<< EOMSG
You recently requested that we send you a new password for
gxsam11.net. Your new password is:

            $password

Please log in at this URL:

             http://gxsam11.net/web/login.php

Then go to this address to change your password:

             http://gxsam11.net/web/changepass.php
EOMSG;

    $mailsend = mail("$to","$subject","$msg","From:
      $from\r\nReply-To:gxsam11@gxsam11.net");

    // Redirect to login
    header("Location: login.php");
  } else {
    // The email address isn't good, they lose.
  }

}



// ----------------
// DISPLAY THE FORM
// ----------------
include_once('includes/header_footer.php');
site_header('Forgot');

// Superglobal arrays dont work in heredoc
$php_self = $_SERVER['PHP_SELF'];

$form_str = <<< EOFORMSTR

<p><b>Forgot your password?</b> Dont worry -- simply enter your
email address below, and we will email you a new password.<br>
<i>Please use the email address you provided when you
registered. If you have forgotten, you can always
<a href="register.php">register again</a>.</i></p>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Form</legend>
  <div>
    <label for="email" class="fixedwidth">Email</label>
    <input type="text" name="email" id="email" value="" size="30" maxlength="50"/>
  </div>
  <div>
    <input type="hidden" name="command" value="forgot"/>
  </div>
  <div class="buttonarea">
    <input type="submit" name="submit" value="Send password"/>
  </div>
  </fieldset>
</form>

EOFORMSTR;
echo $form_str;

site_footer();

?>