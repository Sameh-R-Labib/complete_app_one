<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
This script will show the user all the vehicle's maintenance items and allow
him/her to set the timeDesired.
*/

session_start();

require_once('includes/login_funcs.php');
require_once('includes/db_vars.php');
include_once('includes/header_footer.php');
require_once('includes/setTimeDesired_funcs.php');

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

if (isSet($_SESSION['STD_mode'])) {
  $mode = $_SESSION['STD_mode'];
} else {
  $mode = 'stageOne';
}


if ($mode == 'stageOne') {
  /*
  Present a form which allows user to specify a vehicle, its current
  mileage, and the mileage at the last oil change. At the top
  instruct user to purpose of this script. Inform the user that this
  script should be run any time he has a concern that a vehicle will
  be needing maintenance. Also, mention that timeDesired is used by
  the other script which prints out a service request checklist to
  present to the shop when a vehicle is given to it. Tell user to
  therefore not specify the same timeDesired for items which won't be
  serviced by the same shop.
  */
  $_SESSION['STD_mode'] = 'stageTwo';
  selectVehicleAndMore();
} elseif ($mode == 'stageTwo') {
  /*
  Transfer the vehicle id and both mileages from post to session. Make
  sure form input is validated.
  Present the main form. It will re-iterate the vehicle number and two
  mileages plus a form table which allows for a date entry on each row.
  The table is described as follows. Last column is form field. Each row
  is for a maintenance item. The table columns are:
  label, t-int, m-int, m-next, t-next, t-desired, in-t-desired
  */
  $_SESSION['STD_mode'] = 'stageThree';
  validateSaveVehMil();
  presentation();
} elseif ($mode == 'stageThree') {
  /*
  Update the timeDesired database table fields using only the values
  which the user changed. Present a confirmation page which includes
  fresh values from the database and the vehicle number. This time just
  show the label and timeDesired for each maintenance item.
  */
  validateSaveTD();
  confirmation();
  form_destroy();
} else {
  form_destroy();
  die('The script died for reason # 294875654. -Programmer.');
}

?>