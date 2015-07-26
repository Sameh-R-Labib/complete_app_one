<?php

require_once('includes/register_funcs.php');

if (!IsSet($_POST['submit']) || $_POST['submit'] != 'Mail confirmation')
{
  $submit = "";
  $first_name = "";
  $last_name = "";
  $user_name = "";
  $email = "";
} else {
  $first_name = $_POST['first_name'];
  $last_name = $_POST['last_name'];
  $user_name = $_POST['user_name'];
  $email = $_POST['email'];
}

if ($submit == 'Mail confirmation') {
  $feedback = user_register();
  // In every case, successful or not, there will be feedback
  $feedback_str = "<P><FONT COLOR=\"red\">$feedback</FONT></P>";
} else {
  // Show form for the first time
  $feedback_str = '';
}

// ----------------
// DISPLAY THE FORM
// ----------------
include_once('includes/header_footer.php');
site_header('Registration');

// Superglobals don't work with heredoc
$php_self = $_SERVER['PHP_SELF'];

$reg_str = <<<EOREGSTR

$feedback_str
<p>
Fill out this form and a confirmation email will be sent to you.
Once you click on the link in the email your account will be
confirmed and you can begin to contribute to the community.</p>
<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Form: <span class="formcomment">*Required</span></legend>
  <div>
    <label for="first_name" class="fixedwidth">First Name</label>
    <input type="text" name="first_name" id="first_name" value="$first_name" size="20" maxlength="25" />
  </div>
  <div>
    <label for="last_name" class="fixedwidth">Last Name</label>
    <input type="text" name="last_name" id="last_name" value="$last_name" size="20" maxlength="25" />
  </div>
  <div>
    <label for="user_name" class="fixedwidth">Username</label>
    <input type="text" name="user_name" id="user_name" value="$user_name" size="10" maxlength="25" />
  </div>
  <div>
    <label for="password1" class="fixedwidth">Password</label>
    <input type="password" name="password1" id="password1" value="" size="10" maxlength="25" />
  </div>
  <div>
    <label for="password2" class="fixedwidth">Password <span class="formcomment">(again)</span></label>
    <input type="password" name="password2" id="password2" value="" size="10" maxlength="25" />
  </div>
  <div>
    <label for="email" class="fixedwidth">Email <span class="formcomment">(required for confirmation)</span></label>
    <input type="text" name="email" id="email" value="$email" size="30" maxlength="50" />
  </div>
  <div class="buttonarea">
    <input type="submit" name="submit" value="Mail confirmation">
  </div>
  </fieldset>
</form>

EOREGSTR;
echo $reg_str;

site_footer();

?>