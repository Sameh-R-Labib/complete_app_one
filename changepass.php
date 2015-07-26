<?php

/******************************
* Change password form page. *
******************************/

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

if ($submit  == "Change my password") {
  $worked = user_change_password();
  if ($worked == 1) {
    $feedback_str = "<p class=\"errmsg\">Password changed</p>";
  } else {
    $feedback_str = "<p class=\"errmsg\">$worked</p>";
  }
}



// ------------
// DISPLAY FORM
// ------------

include_once('includes/header_footer.php');
site_header('Change Password');

// Superglobals don't work with heredoc
$php_self = $_SERVER['PHP_SELF'];

$form_str = <<<EOFORMSTR

$feedback_str
<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Change your password</legend>
  <div>
    <label for="old_password" class="fixedwidth">Old password</label>
    <input type="password" name="old_password" id="old_password" value="" size="10" maxlength="25"/>
  </div>
  <div>
    <label for="new_password1" class="fixedwidth">New Password</label>
    <input type="password" name="new_password1" id="new_password1" value="" size="10" maxlength="25"/>
  </div>
  <div>
    <label for="new_password2" class="fixedwidth">New password (again)</label>
    <input type="password" name="new_password2" id="new_password2" value="" size="10" maxlength="25"/>
  </div>
  <div class="buttonarea">
    <input type="submit" name="submit" value="Change my password"/>
  </div>
  </fieldset>
</form>

EOFORMSTR;

echo $form_str;

site_footer();

?>