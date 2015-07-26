<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/**************************************************
* Login page. There are links to this page from   *
* the header on every page for logged-out         *
* users only.                                     *
**************************************************/

require_once('includes/login_funcs.php');

// I don't want to rely on registered_globals
if (!IsSet($_POST['submit']) || $_POST['submit'] != 'Login')
{
  $submit = "";
} else {
  $submit = $_POST['submit'];
}


// If they're logged in, log them out
// They shouldnt be able to see this page logged-in
// This allows the same page to be used as a logout script
if ($LOGGED_IN = user_isloggedin()) {
  user_logout();
  session_start();
  $_COOKIE['user_name'] = '';
  $_COOKIE['id_hash'] = '';
  $_COOKIE['user_type'] = '';
  $_COOKIE['user_type_hash'] = '';
  session_destroy();
  session_unset();
  unset($LOGGED_IN);
}

if ($submit == 'Login') {
  if (strlen($_POST['user_name']) <= 25 && strlen($_POST['password']) <=25) {
    $feedback = user_login();
  } else {
    $feedback = 'ERROR -- Username and password are too long';
  }
  if ($feedback == 1) {
    $user_name = strtolower($_POST['user_name']);
    
    // Script will abort if the following fails. Otherwise,
    // it will return a value for a user_type id which will
    // be an integer or NULL.
    $user_type_default = user_type_default($user_name);
  
	// If the user's user_type_default == NULL then redirect to
	// Create A New user_type script.
	if ( $user_type_default == NULL ) {
	  header("Location: createANewUserType.php");
	  exit;
	} else {
	
	  // Set user_type cookies and redirect to index.php.
      user_type_set_tokens($user_type_default);
      header("Location: kds_navigate.php");
      exit;
    }
  } else {
    $feedback_str = "<p class=\"errmsg\">$feedback</p>";
  }
} else {
  $feedback_str = '';
}



// ----------------
// DISPLAY THE FORM
// ----------------
include_once('includes/header_footer.php');
site_header('Login');

// Superglobals dont work with heredoc

$php_self = $_SERVER['PHP_SELF'];

$login_form = <<< EOLOGINFORM

$feedback_str
<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>over here!</legend>
  <div>
    <label for="user_name" class="fixedwidth">Username</label>
    <input type="text" name="user_name" id="user_name" value="" size="10" maxlength="25"/>
  </div>
  <div>
    <label for="password" class="fixedwidth">Password</label>
    <input type="password" name="password" id="password" value="" size="10" maxlength="25"/>
  </div>
  <div class="buttonarea">    
    <input type="submit" name="submit" value="Login"/>
  </div>
  </fieldset>
</form>

EOLOGINFORM;
echo $login_form;

site_footer();

?>