<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
This script is for handling the following situation:
I come home after spending money on my bus or buses. I want to document
things about these transactions in the maintenance section of my company's
database. Here is a list of what gets accomplished by this script to
process an invoice:

1. User specifies a vehicle or generic vehicle.
2. User picks out maintenance items that are on the invoice.
3. User is advised to add new maintenace items to the system
   before continuing.
4. User gets to select those items
5. User gets advised about adding new part numbers to the system.
6. User gets to add information to the invoice's service record.
7. Script explains to user how the system will be updated.
8. Script updates the system.
9. Script reports on success or failure of update.
*/



/*
Keep in mind that there needs to be a way to store information
gathered in each stage so that it can be used during later stages.
Note that each stage involves a different instance of execution
of this script. Therefore, simply making the function variables
global will not work. Also, simply passing them as hidden form
variables could be messy since arrays are involved.

This script will use PHP sessions to take care of this problem.
Use $_SESSION array like a suitcase. It is a super global variable so
it does not have to be declared as global to be seen inside the
functions. I will rely on the fact that the session ID will be stored
as a cookie. The actual session data gets stored in a file on the server.
See P. 459 of the PHP/MySQL Bible for more info about sessions.
*/
session_start();


require_once('includes/login_funcs.php');
require_once('includes/db_vars.php');
include_once('includes/header_footer.php');
require_once('includes/processAnInvoice_funcs.php');

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



if (isSet($_SESSION['ProcAnInv_mode'])) {
  $mode = $_SESSION['ProcAnInv_mode'];
} else {
  $mode = 'stageOne';
}



if ($mode == 'stageOne') {
  // Present a form which allows the user to select the vehicle
  // for which this invoice pertains. If this invoice is for general
  // maintenance purchase unrelated to a specific vehicle then the
  // user will select Generic Vehicle.
  selectVehicle();
} elseif ($mode == 'stageTwo') {
  // Present a form which allows the user to select from a list of
  // this vehicle's maintenance items the ones which pertain to
  // this invoice.
  selectFromExistingMIDs();
} elseif ($mode == 'stageThree') {
  // This function will do two things. First it will save (into a session
  // variable) the submitted selected maintnance items. Second, it will
  // present a form which asks the user how many new maintenance items
  // need to be made available.
  // Just for clarity this form will display the maintenance items that
  // have already been added to the online version of this invoice.
  askHowManyNewMIDs();
} elseif ($mode == 'stageFour') {
  if (isset($_POST['qtyMoreMID']) AND !empty($_POST['qtyMoreMID'])
      AND $_POST['qtyMoreMID'] < 15) {
    $_SESSION['PAI_qtyMoreMID'] = $_POST['qtyMoreMID'];
  } else {
    $_SESSION['PAI_qtyMoreMID'] = 0;
  }
  if ($_SESSION['PAI_qtyMoreMID'] >= 1) {
    // Present a form that collects names for new MIDs. Mode becomes
    // stageFive.
    gatherNewMID_labels();
  } else {
    // Present a form that informs the user that the system acknowledges
    // that there is no need to add new maintenance items. Make sure
    // there is a button to continue. Mode becomes stageSeven.
    informNoNewMIDs();
  }
} elseif ($mode == 'stageFive') {
  // If the user didn't fill out anything in the text boxes then the
  // user should be presented with the page which the function
  // informNoNewMIDs() presents.
  // Here we pick up from the TRUE side of stageFour. Present a form
  // which displays the new MID labels and instructs the user to go
  // create them and come back.
  instructToCreateMIDs();
} elseif ($mode == 'stageSix') {
  // Here we pick up after stageFive. Present a form which allows the
  // user to select the new MIDs so they will be added to our collection
  // of MIDs to be included in the proccessing of this invoice. Now mode
  // becomes stageSeven.
  addNewMIDs();
} elseif ($mode == 'stageSeven') {
  /*
  Before doing its main task this function will add the new maintenance
  items submitted in the previous stage (if there are any) to the session
  variable which stores them.
  Present a form that shows all the collected MIDs along with their
  associated parts. Ask the user to note any new parts we learned about,
  take note of them so they can be included in our maintenance system.
  Other than that, ask the user to not proceed any further with this
  script if for some odd reason he/she does not see all the MIDs
  which should be there at this point on the invoice.
  */
  presentMIDandPartInfoGathered();
} elseif ($mode == 'stageEight') {
  /*
  Present a form that allows the user to supply the values for the
  service record fields.
  */
  getServiceRecordVals();
} elseif ($mode == 'stageNine') {
  /*
  Present a form that shows the user all the information collected so
  far about this invoice. Ask him/her to verify its validity and proceed
  only if it is valid.
  */
  presentInfoGathered();
} elseif ($mode == 'stageTen') {
  /*
  Things validateSave() will do:
  1. Save all collected data if $_SESSION['requiremet'] == TRUE and
     no anomalies are found.
  2. Return a boolean to let us know whether it saved the data.
  3. It will set the value for $_SESSION['isanomaly']

  One thing that definetly constitutes an anomaly is if the service record
  already exists. An anomaly of a different type is when data is found to
  be non-valid.
  */
  if (validateSave()) {
    /*
    retrieveAndConfirm() function should remind the user to update the timeNext and
    mileNext for each maintenance item which was done on this invoice. Make sure
    the retrieveAndConfirm() function destroys the session (both its data and id).
    */
    retrieveAndConfirm();
  } else {
    /*
    Make sure presentErrorStatus() destroys the session (both its data and id).
    */
    presentErrorStatus();
  }
} else {
  form_destroy();
  die('The script died for reason # 294875654. -Programmer.');
}

?>