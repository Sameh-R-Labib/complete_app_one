<?php

/**************************************************
* Script to set user cookie so admins can browse  *
* site from the viewpoint of a particular user.   *
* Potentially VERY unsafe!!! Protect it with      *
* your life!                                      *
**************************************************/

// If this person isn't recognized as the admin, bounce them
if ( IsSet($_COOKIE['user_name']) ) {
  if ($_COOKIE['user_name'] != 'administrator') {
    header("Location: index.php");
    exit;
  }
}

require_once('includes/login_funcs.php');
require_once('includes/db_vars.php');

function user_impersonate() {
  // This function will only work with superglobal arrays,
  // because I'm not passing in any values or declaring globals
  if (!$_POST['user_name'] || !$_POST['password']) {
    $feedback = 'ERROR -- Missing username or password';
    return $feedback;
  } else {
    $user_name = strtolower($_POST['user_name']);
    // Dont need to trim because extra spaces should fail
    // for this
    // Dont need to addslashes because single quotes
    // arent allowed
    $password = $_POST['password'];
    // Dont need to addslashes because we'll be hashing it
    $crypt_pwd = md5($password);
    $query = "SELECT user_name
              FROM user
              WHERE user_name = 'administrator'
              AND password='$crypt_pwd'";
    $result = mysql_query($query);
    if (!$result || mysql_num_rows($result) < 1){
      $feedback = 'ERROR -- User not found or password incorrect';
      return $feedback;
    } else {
      if (mysql_result($result, 0, 0) == 'administrator') {
        user_set_tokens($user_name);
        return 1;
      } else {
        $feedback = 'ERROR -- How did we get here in the code?';
        return $feedback;
      }
    }
  }
}

// Avoid undefined index notice from php processor
if (!IsSet($_POST['submit'])) {
  $submit = "";
} else {
  $submit = $_POST['submit'];
}

if ($submit == 'Login') {
  $feedback = user_impersonate();
  if ($feedback == 1) {
    // On successful login, redirect to homepage
    header("Location: index.php");
    exit;
  } else {
    $feedback_str = "<p>$feedback</p>";
  }
} else {
  $feedback_str = '';
}



// ----------------
// DISPLAY THE FORM
// ----------------
include_once('includes/header_footer.php');
site_header('Impersonate');

// Superglobals don't work with heredoc
$php_self = $_SERVER['PHP_SELF'];

$login_form = <<< EOLOGINFORM

$feedback_str
<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>over here!</legend>
  <div>
    <label for="user_name" class="fixedwidth">Username</label>
    <input type="text" name="user_name" id="user_name" value="" size="10" maxlength="25" />
  </div>
  <div>
    <label for="password" class="fixedwidth">Password</label>
    <input type="password" name="password" id="password" value="" size="10" maxlength="25" />
  </div>
  <div class="buttonarea">    
    <input type="submit" name="submit" value="Login"/>
  </div>
  </fieldset>
</form>

<p>The username is that of the user you want to impersonate. However, the
password is that of the Administrator.</p>

EOLOGINFORM;
echo $login_form;

site_footer();

?>