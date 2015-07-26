<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
SHOP JOB LIST
-------------
This script will make a handout which lists the maintenance items along
with their associated parts for a particular timeDesired value and
vehicle.
*/

session_start();

require_once('includes/login_funcs.php');
require_once('includes/db_vars.php');
include_once('includes/header_footer.php');
require_once('includes/shopJobList_funcs.php');

// User must be logged in and be someone special.
if (!user_isloggedin() || !user_type_cookieIsSet()) {
  // Redirect to login page.
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


if (isSet($_SESSION['SJL_mode'])) {
  $mode = $_SESSION['SJL_mode'];
} else {
  $mode = 'stageOne';
}


if ($mode == 'stageOne') {
  /*
  Inform the user. Present a vehicle selector.
  */
  $_SESSION['SJL_mode'] = 'stageTwo';
  selectVehicle();
} elseif ($mode == 'stageTwo') {
  /*
  Capture and validate the vehicle id.
  Find out the vehicle number.
  Gather all possibilities of timeDesired values for vehicle.
  Present a form so user can specify one timeDesired value.
  */
  $_SESSION['SJL_mode'] = 'stageThree';
  validateSave();
  getVehicleNumber();
  getAllTimeDesiredVals();
  presentSpecifyTimeDesired();
} elseif ($mode == 'stageThree') {
  /*
  Capture and validate the timeDesired value.
  Create a matrix of maintenance items and parts associated with
  the timeDesired value.
  Display all the information gathered in session and from database.
  The display should include a form having "I'm done" and "Show printer
  friendly" submit buttons.
  */
  $_SESSION['SJL_mode'] = 'stageFour';
  validateSaveTimeDesired();
  formMatrixsShopJobList();
  displayMatrixForBrowser();
} elseif ($mode == 'stageFour') {
  /*
  Display the same information in black and white so as to be printer
  friendly.
  */
  displayPrinterFriendly();
  form_destroy();
} else {
  form_destroy();
  die('The script died for reason # 294875654. -Programmer.');
}

?>