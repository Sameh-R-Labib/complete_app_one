<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
USED SCRIPT SESSION PREPENDS
----------------------------
This script is for registering or finding out if registered a string as a
script $_SESSION index prepend.
*/

session_start();

require_once('includes/login_funcs.php');
require_once('includes/db_vars.php');
include_once('includes/header_footer.php');
require_once('includes/usedScriptSESSION_Prepends_funcs.php');

// User must be logged in and be someone special.
if (!user_isloggedin() || !user_type_cookieIsSet()) {
  form_destroy();
  $host = $_SERVER['HTTP_HOST'];
  header("Location: http://{$host}/web/login.php");
  exit;
}
if ($_COOKIE['user_type'] != 1) {
  form_destroy();
  die('Script aborted #3098. -Programmer.');
}

// Cancel if requested.
if (isset($_POST['cancel'])) {
  form_destroy();
  $host = $_SERVER['HTTP_HOST'];
  $uri = $_SERVER['PHP_SELF'];
  header("Location: http://$host$uri");
  exit;
}

if (isSet($_SESSION['USSP_mode'])) {
  $mode = $_SESSION['USSP_mode'];
} else {
  $mode = 'stageOne';
}


if ($mode == 'stageOne') {
  site_header('Used Script SESSION Prepends');
  explainPurposeOfForm();
  explainSyntaxOfPrepend();
  presentInputFormForPrependString();
  site_footer();
  $_SESSION['USSP_mode'] = 'receive prepend string from user';
} elseif ($mode == 'receive prepend string from user') {
  if (alreadyUsed()) {
    site_header('Used Script SESSION Prepends');
    informAlreadyUsed();
    presentForm_NextOrCancel();
    site_footer();
    $_SESSION['USSP_mode'] = 'stageOne';
  } else {
    site_header('Used Script SESSION Prepends');
    informAvailable();
    presentForm_DoYouWantToRegisterIt();
    site_footer();
    $_SESSION['USSP_mode'] = 'rcvAnswer: Do you want to register it?';
  }
} elseif ($mode == 'rcvAnswer: Do you want to register it?') {
  if (wantToRegisterIt()) {
    site_header('Used Script SESSION Prepends');
    insertPrepend();
    presentConfirmation();
    site_footer();
    form_destroy();
  } else {
    form_destroy();
  }
} else {
  form_destroy();
  die('The script died for reason # 294875654. -Programmer.');
}

?>