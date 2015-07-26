<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
This script is for viewing service history for a vehicle.
*/

session_start();

require_once('includes/login_funcs.php');
require_once('includes/db_vars.php');
include_once('includes/header_footer.php');
require_once('includes/viewServHistForVeh_funcs.php');

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

if (isSet($_SESSION['VSHFV_mode'])) {
  $mode = $_SESSION['VSHFV_mode'];
} else {
  $mode = 'stageOne';
}


if ($mode == 'stageOne') {
  /*
  Present a form which allows user to specify a vehicle. At the top
  instruct user to purpose of this script. The purpose is to show the
  service records along with their maintenance items chronologically
  for a period in time.
  */
  $_SESSION['VSHFV_mode'] = 'stageTwo';
  selectVehicle();
} elseif ($mode == 'stageTwo') {
  /*
  Take the vehicle id from post to session. Make sure it gets form
  validated. Have the user specify the FROM and To dates.
  */
  $_SESSION['VSHFV_mode'] = 'stageThree';
  saveVID();
  formDateRange();
} elseif ($mode == 'stageThree') {
  /*
  Receive and validate the date range values. Query the database
  and feed the matrix which holds all the values we need.
  NOTE: Chronological order is important here.
  Display the information.
  */
  saveDateRange();
  buildMatrix();
  presentMatrix();
  form_destroy();
  
} else {
  form_destroy();
  die('The script died for reason # 294875654. -Programmer.');
}

?>