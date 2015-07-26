<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
This script presents one of two forms per iteration.
It is self submitting -- starts with logic then
presents a form.

Each iteration of this script will correlate to a mode.
Here is a general description of each mode:
- stageOne:   Ask tag/State of vehicle.
- stageTwo:   Insert new vehicle record, present update form
              or repeat stageOne.
- stageThree: Update vehicle record.
*/

// Includes:
require_once('includes/login_funcs.php');
require_once('includes/db_vars.php');
include_once('includes/header_footer.php');
require_once('includes/addCoVeh_funcs.php');

/* Nobody should see this page if they are not logged in
or don't have their user_type cookies set.
*/
if (!user_isloggedin() || !user_type_cookieIsSet()) {
  // Redirect to login page.
  $host = $_SERVER['HTTP_HOST'];
  header("Location: http://{$host}/web/login.php");
  exit;
}

if ($_COOKIE['user_type'] != 1) {
  die('Script aborted #3098. -Programmer.');
}

// Initialize submit counter if it is not set.
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
  presentTagStateForm(); 
} elseif (validStateTagInfo()) {
  if (vehicleIsInTable()) {
    if ($mode == 'stageThree') { validateSave(); }
  } else {
    if ($mode == 'stageTwo') {
      createNewRecord();
    } else {
      die('Can not update a non-existent record. -Programmer.');
    }
  }
  reloadDataVars();
  presentUpdateForm();
} else {
  $status_message = 'You\'re not filling out the fields appropriately.';
  presentTagStateForm();
}

?>