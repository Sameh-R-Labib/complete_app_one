<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
This script is for updating any field of an existing maintenance item
record other than its id, label or vehicleId.
*/

session_start();

require_once('includes/login_funcs.php');
require_once('includes/db_vars.php');
include_once('includes/header_footer.php');
require_once('includes/updateMaintItem_funcs.php');

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

if (isset($_POST['cancel'])) {
  form_destroy();
  $host = $_SERVER['HTTP_HOST'];
  $uri = $_SERVER['PHP_SELF'];
  header("Location: http://$host$uri");
  exit;
}

if (isSet($_SESSION['UMI_mode'])) {
  $mode = $_SESSION['UMI_mode'];
} else {
  $mode = 'stageOne';
}

if ($mode == 'stageOne') {
  /*
  Present a form so user can specify a vehicle.
  */
  $_SESSION['UMI_mode'] = 'stageTwo';
  selectVehicle();  // Copy this from assocPIDwithMID.
} elseif ($mode == 'stageTwo') {
  /*
  Present a form which has a select box that contains the maintenance
  items for this vehicle. Make sure to save the value of the vehicle id
  from previous stage. Handle/validate vehicle id.
  */
  $_SESSION['UMI_mode'] = 'stageThree';
  $_SESSION['UMI_PrevMode'] = 'stageTwo';
  saveVID();
  selectMID();   // Copy most of this from assocPIDwithMID.
} elseif ($mode == 'stageThree') {
  /*
  Present form (having fields prefilled) so user can modify the data
  for the maintenance item. Of course we'll have to save the id of the
  maintenance item submitted in stageTwo. Keep in mind we may get here
  from a previous mode other than stageTwo. Make sure the validation
  /handling of the maintenance id received from POST is done properly.
  We will be saving the field values of the maintenance item's record
  in session variables so that they can be maintained and used during
  script execution time. NOTE: when arriving here from stageTwo the data
  prefilling the form will be from the database. However, when we
  arrive here from stageFour the data prefilling the form will be from
  the session variable.
  */
  $_SESSION['UMI_mode'] = 'stageFour';
  saveMID();
  maintItemUpdateForm();
  $_SESSION['UMI_PrevMode'] = 'stageThree';
} elseif ($mode == 'stageFour') {
  /*
  Validate the data submitted by the user of stageThree form. If the
  data is all valid then save it in the database. Otherwise, save the
  valid data fields in their session variables and prepare an
  error message to be presented. Of course that would lead us back to
  stageThree. Since every mode has to result in a form, if the data was
  stored then present a success form showing what was saved. Otherwise,
  present a form explaining that one or two data fields were invalid and
  that the user will have the opportunity to edit again.
  */
  $_SESSION['UMI_PrevMode'] = 'stageFour';
  if (validateSave()) {
    retrieveAndConfirm();
  } else {
    presentErrorStatus();
    $_SESSION['UMI_mode'] = 'stageThree';
  }
} else {
  form_destroy();
  die('The script died for reason # 294875654. -Programmer.');
}

?>