<?php

/***************************
* Change email form page. *
***************************/

require_once('includes/login_funcs.php');
require_once('includes/emailpass_funcs.php');
if (!user_isloggedin()) {
  header("Location: index.php");
}


// Avoid undefined index notice from php processor
if (!IsSet($_POST['submit'])) {
  $submit = "";
} else {
  $submit = $_POST['submit'];
}

// Initializing $feedback_str
$feedback_str = "";

if ($submit == "Send my confirmation") {
  $worked = user_change_email();
  if ($worked == 1) {
    $feedback_str = "<p class=\"errmsg\">A confirmation " .
       "email has been sent to you.</p>";
  } else {
    $feedback_str = "<p class=\"errmsg\">$worked</p>";
  }
}



// ------------
// DISPLAY FORM
// ------------

include_once('includes/header_footer.php');
site_header('Change Email');

// Superglobals dont work with heredoc
$php_self = $_SERVER['PHP_SELF'];

$form_str = <<<EOFORMSTR

$feedback_str
<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Change your email address</legend>
  <p>A confirmation email will be sent to you.</p>
  <div>
    <label for="password1" class="fixedwidth">Password</label>
    <input type="password" name="password1" id="password1" value="" size="10" maxlength="25"/>
  </div>
  <div>
    <label for="email" class="fixedwidth">New email (a real one)</label>
    <input type="text" name="email" id="email" value="" size="30" maxlength="50"/>
  </div>
  <div class="buttonarea">
    <input type="submit" name="submit" value="Send my confirmation"/>
  </div>
  </fieldset>
</form>

EOFORMSTR;

echo $form_str;

site_footer();

?>