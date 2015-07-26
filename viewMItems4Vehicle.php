<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
This script presents one form or one table per iteration.

Each iteration of this script will correlate to a mode.
Here is a general description of each mode:
- stageOne:   Present form asking for a vehicle id.
- stageTwo:   If vehicle id is good
                 Then present table.
                 Include congratulatory message.
              Else
                 Present form asking for a vehicle id.
                 Include error message.
*/


require_once('includes/login_funcs.php');
require_once('includes/db_vars.php');
include_once('includes/header_footer.php');
require_once('includes/viewMItems4Vcl_funcs.php');


// User must be logged in and be someone special.
if (!user_isloggedin() || !user_type_cookieIsSet()) {
  // Redirect to login page.
  $host = $_SERVER['HTTP_HOST'];
  header("Location: http://{$host}/web/login.php");
  exit;
}
if ($_COOKIE['user_type'] != 1) {
  die('Script aborted #3098. -Programmer.');
}


// Manage the submit counter.
if (!isSet($_POST['submitCounter'])) {
  $submitCounter = 1;
} else {
  // Increment the counter
  $submitCounter = $_POST['submitCounter'] + 1;
  if ( $submitCounter == 6 ) {
    die('Script aborted for undisclosed reason. -Programmer.');
  }
}


$status_message = "";


if (isSet($_POST['mode'])) {
  $mode = $_POST['mode'];
} else {
  $mode = 'stageOne';
}


if ($mode == 'stageOne') {
  presentM4V_Form();
} else {
  if (validV_id()) {
    $status_message = "Data successfully retrieved.";
    presentM4V_Table();
  } else {
    $status_message = "You made a mistake filling out the form.";
    presentM4V_Form();
  }
}


?>