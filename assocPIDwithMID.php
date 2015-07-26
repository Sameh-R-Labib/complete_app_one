<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
This script is for associating a single part with a single maintenance
item. It is more enhanced than the first script I wrote to do the same
thing.
*/

session_start();

require_once('includes/login_funcs.php');
require_once('includes/db_vars.php');
include_once('includes/header_footer.php');
require_once('includes/assocPIDwithMID_funcs.php');

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

if (isSet($_SESSION['APWM_mode'])) {
  $mode = $_SESSION['APWM_mode'];
} else {
  $mode = 'stageOne';
}

if ($mode == 'stageOne') {
  /*
  Present form. Selection box.
  Have a select box where the user selects the vehicle.
  */
  $_SESSION['APWM_mode'] = 'stageTwo';
  selectVehicle();
} elseif ($mode == 'stageTwo') {
  /*
  Find out the vehicle id based on the results of the submission of the
  form produced by stageOne. Make sure to validate the form field first.
  Abort if vehicle id was not valid.
  Present a form which displays the maintenance items for the vehicle
  and allows the user to select the one he wants. It will be a selection
  box input field.
  */
  $_SESSION['APWM_mode'] = 'stageThree';
  $_SESSION['APWM_PrevMode'] = 'stageTwo';
  selectMID(); 
} elseif ($mode == 'stageThree') {
  /*
  Present form with a text box. Ask the user to type in the part number.
  Provide guidelines for what the part number consists of and give
  examples.
  
  Also, accept and validate input from submission of the form produced
  by stageTwo. This must yield a maintenance item and its label.
  Now keep in mind what I just told you to do and don't do it if we got
  to stageThree via stageFive or stageSix.
  */
  $_SESSION['APWM_mode'] = 'stageFour';
  inputPNtoFind();
  $_SESSION['APWM_PrevMode'] = 'stageOther';
} elseif ($mode == 'stageFour') {
  /*
  CAUTION:
  Must validate the text field input resulting from the user's submission
  of the stageThree form. If it is a spoof then kill the script.
  ACCOMPLISH:
  1. Look at the part number string the user supplied.
  2. See if string matches a part from the system.
  3. If found then set mode var to stageSeven.
  4. If not found then present a form asking the user which way he
     wants to proceed: A. Try a different spelling for the part number.
                       B. Give up on guessing and just add a new part.
  */
  if ($foundPN = searchForStatedPN()) {
    $_SESSION['APWM_mode'] = 'stageSeven';
    /*
    Present a form which is informational.
    */
    goodJob();
  } else {
    $_SESSION['APWM_mode'] = 'stageFive';
    /*
    Present form. Selection box.
    */
    tryAgainOrAddNew();
  }
} elseif ($mode == 'stageFive') {
  /*
  Picking up where we left off from stageFour where $foundPN == FALSE.
  5. Set the stage var to stageThree if user chose "try again".
     Set the stage var to stageSix if user chose "add new".
  Then reload the page.
  */
  $_SESSION['APWM_mode'] = processTheAnswer();
  $host = $_SERVER['HTTP_HOST'];
  $uri = $_SERVER['PHP_SELF'];
  header("Location: http://$host$uri");
} elseif ($mode == 'stageSix') {
  /*
  Present a form telling the user to open up a new tab in the browser
  add the new part then come back to this script's browser tab. Set
  mode var to stageThree.
  */
  $_SESSION['APWM_mode'] = 'stageThree';
  instructToAddPart();
} elseif ($mode == 'stageSeven') {
  /*
  Match the PID with the MID. In other words add a record which
  correlates our PID and MID. Display confirmation.
  */
  validateSave();
  displayConfirmation();
} else {
  form_destroy();
  die('The script died for reason # 294875654. -Programmer.');
}

?>