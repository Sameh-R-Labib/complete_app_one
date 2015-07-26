<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/* This file is for inclusion in addServiceRecord.php. */


require_once('generalFormFuncs.php');



function presentShopIdTimeDateForm() {
/*
Assumptions:
   1. Valid user.
   2. Purpose:
           A. First presentation.
           B. stageOne re-presentation.

Expected Result:
   1. Possible presentation of $status_message.
   2. Pass the stageTwo value for mode as a hidden form field.
   3. Pass the global $submitCounter value through as a hidden field in
      the form.
   4. Present input fields for shopId and timedate
*/

  global $status_message, $submitCounter;


  /*
  Construct form:
  */

  site_header('Add Service Record');

  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];

  if ( !isset($status_message) || $status_message == "" ) {
    $message_str = "";
  } else {
    $message_str = "<p class=\"errmsg\">$status_message</p>";
  }

  $userform_str = <<<EOUSERFORMSTR

$message_str

<p>This page allows you to insert or update a service record in the database table
for service to equipment. This table is used by other scripts (including the ones
for maintenance.)
Please, if you are updating a service record, make sure the shopId and date-time you
supply on this form are the same as the ones in the record. Otherwise you will be
inserting or updating a record other than the intended one.</p>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Specify a Service Record: <span class="formcomment">*All fields required</span></legend>
  <div>
    <label for="shopId" class="fixedwidth">shop ID (number)</label>
    <input type="text" name="shopId" id="shopId" value="" size="12" maxlength="12"/>
  </div>
  <div>
    <label for="timedate" class="fixedwidth">when? (ccyy-mm-dd hh:mm:ss)</label>
    <input type="text" name="timedate" id="timedate" value="" size="19" maxlength="19"/>
  </div>
  <div>
    <input type="hidden" name="mode" value="stageTwo">
    <input type="hidden" name="submitCounter" value="$submitCounter">
  </div>
  <div class="buttonarea">    
    <input type="submit" name="submit" value="Retrieve service record data"/>
  </div>
  </fieldset>
</form>

EOUSERFORMSTR;
  echo $userform_str;
  site_footer();
  return;
}



function validShopIdTimeDate() {
/* Verifies that the user supplied both a shopId and
timedate. Also, verifies that the supplied string values
are of appropriate length and content. Also, this function
will assign appropriately the global values for $shopId and
$timedate.

This function needs to have the $shopId, $timedate values backslashed
appropriately for possible insertion into database before return to
main program.
*/

  global $shopId, $timedate;

  if ( isset($_POST['submit']) AND (($_POST['submit'] == "Edit service record data")
      OR ($_POST['submit'] == "Retrieve service record data")) ) {

    // Initialize $label and $partNumber.
    if ( isset($_POST['shopId']) ) {
      $shopId = $_POST['shopId'];
    } else {
      $shopId = "";
    }
    if ( isset($_POST['timedate']) ) {
      $timedate = $_POST['timedate'];
    } else {
      $timedate = "";
    }

    // If magic quotes is on I'll stripslashes.
    if ( get_magic_quotes_gpc() ) {
      $shopId = stripslashes($shopId);
      $timedate = stripslashes($timedate);
    }

    // Trim white space.
    $shopId = trim($shopId);
    $timedate = trim($timedate);

    // Test the values and return truth.
    if ( strlen($shopId) > 12 || strlen($timedate) > 19 ) { return false; }
    if ( strlen($shopId) < 1 || strlen($timedate) < 1 ) { return false; }

    // Verify numeric data is numeric.
    if ( strlen($shopId) > 0 and !is_numeric($shopId)) { return false; }

    // Verify datetime data is datetime string.
    // I WILL CODE THIS IN LATER
    if ( strlen($timedate) > 0 and !isDateTime($timedate)) { return false; }

    // addslashes
    $shopId = addslashes($shopId);
    $timedate = addslashes($timedate);

  } else {
    die('Script aborted #12580. -Programmer.');
  }

  return true;
}



function shopIdTimeDateInTable() {
/* Indicates whether the service record whose shopId and timedate are
available in the global $shopId and $timedate are found in the table. */

  global $shopId, $timedate;

  $query = "SELECT id
            FROM serviceRecords
            WHERE shopId = '$shopId' AND timedate = '$timedate'";
  $result = mysql_query($query);

  if (!$result) {
    die('Query failed. Err: 880037. -Programmer.');
  }

  if ( mysql_num_rows($result) < 1) {
    return false;
  } else {
    return true;
  }
}



function validateSave() {
/* This function takes the data which was collected by
the update form, validates it and saves it. Also,
it will set $status_message.
Assumption: A record containing the shopId and timedate for this
vehicle already exists. Assumption: $shopId and $timedate are
global variables which contain valid values. */

  // $isAnomaly - whether any form string is too long
  $isAnomaly = false;

  // Have required fields been filled out?
  $requireMet = true;

  global $status_message;

  // All form variables are global.
  global $cost, $shopId, $timedate, $mileage;

  if ( isset($_POST['submit']) and $_POST['submit'] == "Edit service record data" ) {

    // Transfer POSTS to regular vars
    if ( isset($_POST['cost']) ) {
      $cost = $_POST['cost'];
    } else {
      $cost = "";
    }
    if ( isset($_POST['mileage']) ) {
      $mileage = $_POST['mileage'];
    } else {
      $mileage = "";
    }

    if ( !get_magic_quotes_gpc() ) {
      $cost = addslashes($cost);
      $shopId = addslashes($shopId);
      $timedate = addslashes($timedate);
      $mileage = addslashes($mileage);
    }

    // Trim white space.
    $cost = trim($cost);
    $shopId = trim($shopId);
    $timedate = trim($timedate);
    $mileage = trim($mileage);

    // Verify string length and deal with anomalies

    // The string length should not be longer than the
    // MAXLENGTH of the FORM field unless slashes were added.
    // Therefore make allowance for this for strings which may
    // have slashes added.
    if ( strlen($cost) > 12 ) { $isAnomaly = true; }
    if ( strlen($shopId) > 12 ) { $isAnomaly = true; }
    if ( strlen($timedate) > 19 ) { $isAnomaly = true; }
    if ( strlen($mileage) > 8 ) { $isAnomaly = true; }

    // Verify numeric data is numeric.
    if ( strlen($cost) > 0 and !is_numeric($cost)) { $isAnomaly = true; }
    if ( strlen($shopId) > 0 and !is_numeric($shopId)) { $isAnomaly = true; }
    if ( strlen($mileage) > 0 and !is_numeric($mileage)) { $isAnomaly = true; }
    
    // Verify datetime data is datetime string.
    // I WILL CODE THIS IN LATER
    if ( strlen($timedate) > 0 and !isDateTime($timedate)) { $isAnomaly = true; }

    // Find out if required fields were supplied
    if ( strlen($shopId) < 1 || strlen($timedate) < 1 ) {
      $requireMet = false;
    }

    // If required fields were inputed then update database.
    if ( $requireMet == true and $isAnomaly == false ) {

      // Send data to db
      $query = "UPDATE serviceRecords
                SET cost = '$cost',
                    mileage = '$mileage'
                WHERE shopId = '$shopId' AND timedate = '$timedate'";
      $result = mysql_query($query);
      if (!$result || mysql_affected_rows() < 1) {
        $status_message = 'Problem with user data entry';
      } else {
        $status_message = 'Successfully edited user data';
      }
    } elseif ( $requireMet == false ) {
      $status_message =  'Error -- you did not complete a required field.';
      if ( $isAnomaly == true ) {
        $status_message .= ' Also, one or more fields was ' .
          'supplied with non-conforming data. No data was saved.';
      }
    } elseif ( $isAnomaly == true ) {
      $status_message = 'One or more fields was ' .
        'supplied with non-conforming data. No data was saved.';
    }
  } else {
    die('Script aborted #7700. -Programmer.');
  }
  
  return;
}



function reloadDataVars() {
/* Puts the data from an existent service record into the
variables which will populate an update form. It is assumed
that $shopId and $timedate correspond to a service record which
exists already. Also, this function makes sure that all form
variables get initialized with values and defined as global.
Otherwise, it aborts.
*/

  // All form variables are global.
  global $cost, $shopId, $timedate, $mileage;

  $query = "SELECT cost, mileage
            FROM serviceRecords
            WHERE shopId = '$shopId' AND timedate = '$timedate'";
  $result = mysql_query($query);
  if (!$result || mysql_num_rows($result) < 1) {
    die('Error 56801. -Programmer.');
  } else {
    $user_array = mysql_fetch_array($result);
    
    $cost = $user_array['cost'];
    $mileage = $user_array['mileage'];
    
    // Text and Textarea fields have had backslashes
    // added to escape single quotes ('), double
    // quotes ("), backslashes (\) and NULL before
    // insertion into the database. Therefore, we must
    // undo this before displaying these strings.
    
    
    // Ordinarily we would perform a stripslashes
    // here also for $shopId' and $timedate but
    // this won't be neccessary.
  }

  return;
}



function createNewRecord() {
/* Inserts a new service record populating it with the
valid shopId and timedate strings supplied by the user. */

  global $shopId, $timedate;

  $query = "INSERT INTO serviceRecords (shopId, timedate)
            VALUES ('$shopId', '$timedate')";
  $result = mysql_query($query);
  if (!$result || mysql_affected_rows() < 1) {
    die('Error adding new record. 00099. -Programmer.');
  } else {
    return;
  }
}



function presentUpdateForm() {
/* Presents the form for supplying (or changing) field
values for an existing service record. Also, presents
the $status_message. The $mode, $shopId and $timedate
global variables must contain valid values (which will
further more be passed along when the form is submitted.)
The $shopId and $timedate values can't be changed here (but
are displayed.)
*/

  global $mode, $status_message, $submitCounter;

  // All form variables are global.
  global $cost, $shopId, $timedate, $mileage;

  // mode must be valid
  $modeIsNotValid = true;
  if ( $mode == 'stageOne') { $modeIsNotValid = false; }
  if ( $mode == 'stageTwo') { $modeIsNotValid = false; }
  if ( $mode == 'stageThree') { $modeIsNotValid = false; }
  if ($modeIsNotValid) { die('Error 11102. -Programmer.'); }

  // The main program has a more rigorous validation test.
  if ((strlen($shopId) < 1) OR (strlen($timedate) < 1)) {
    die('Error 22001. -Programmer.');
  }

  /*
  Construct form:
  */

  site_header('Add Service Record');

  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];

  if ( !isset($status_message) || $status_message == "" ) {
    $message_str = "";
  } else {
    $message_str = "<p class=\"errmsg\">$status_message</p>";
  }

  $userform_str = <<<EOUSERFORMSTR

$message_str

<p>This page allows you to update a service record in the database table
for service records. This table is used by other scripts (including
the ones for maintenance.)</p>

<p>shop ID: $shopId<br/>
date-time: $timedate</p>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>service record data:</legend>
  <div>
    <label for="cost" class="fixedwidth">cost (decimal)</label>
    <input type="text" name="cost" id="cost" value="$cost" size="12" maxlength="12"/>
  </div>
  <div>
    <label for="mileage" class="fixedwidth">mileage (integer)</label>
    <input type="text" name="mileage" id="mileage" value="$mileage" size="8" maxlength="8"/>
  </div>
  <div>
    <input type="hidden" name="mode" value="stageThree">
    <input type="hidden" name="submitCounter" value="$submitCounter">
    <input type="hidden" name="shopId" value="$shopId">
    <input type="hidden" name="timedate" value="$timedate">
  </div>
  <div class="buttonarea">    
    <input type="submit" name="submit" value="Edit service record data"/>
  </div>
  </fieldset>
</form>
EOUSERFORMSTR;
  echo $userform_str;

  site_footer();
  
  return;

}

?>