<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/* This file is for inclusion in addMaintenanceItem.php. */

require_once('generalFormFuncs.php');
require_once('table_funcs.php');



function presentLabelVehicleIdForm() {
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
   4. Present input fields for label and vehicleId
*/

  global $status_message, $submitCounter;

  /*
  I'll retrieve the array of vehicles which will feed the selection box.
  I'll use the table function as a wrapper for this query.
  */
  $tableName = "vehicles";
  $fieldNames = array('id', 'knownAs');
  $whereClause = '';
  $vehicle = getValuesForHTML_Table($fieldNames, $tableName, $whereClause);
  if ($vehicle == FALSE) {
    die('Err: 66812444. -Programmer.');
  }

  /*
  Now, I'll make a selection box.
  */
  $selectBox = selectBox($vehicle);

  /*
  Construct form:
  */

  site_header('Add Maintenance Item');

  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];

  if ( !isset($status_message) || $status_message == "" ) {
    $message_str = "";
  } else {
    $message_str = "<p class=\"errmsg\">$status_message</p>";
  }

  $userform_str = <<<EOUSERFORMSTR

$message_str

<p>This page allows you to add a maintenance.</p>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Specify a Maintenance Item: <span class="formcomment">*All fields required</span></legend>
  <div>
    <label for="label" class="fixedwidth">label</label>
    <input type="text" name="label" id="label" value="" size="41" maxlength="65"/>
  </div>
$selectBox
  <div>
    <input type="hidden" name="mode" value="stageTwo">
    <input type="hidden" name="submitCounter" value="$submitCounter">
  </div>
  <div class="buttonarea">    
    <input type="submit" name="submit" value="Retrieve maintenance item data"/>
  </div>
  </fieldset>
</form>

EOUSERFORMSTR;
  echo $userform_str;
  site_footer();
  return;
}



function selectBox($vehicle_in) {
/*
This function takes a two dimensional array of vehicle information and
returns a selection box for a form. Each selection will return a vehicle
id value. The corresponding thing which the user will click on will be a
string containing both the id and the knownAs. The structure and content
of $vehicle_in array is described in function selectVehicle() above.

If $vehicle_in array is empty or unavailable the script will die.
*/

  if (!isset($vehicle_in) OR !is_array($vehicle_in) OR sizeof($vehicle_in) < 1) {
    die("Function failed to create select box because no array was passed.");
  }


  /*
  Here is a sample code for a select box on my site.

  <div>
    <label for="user_type">Which best describes you?</label>
    <select name="user_type" id="user_type">
      <option value ="1">Owner Administarator of this Website</option>
      <option value ="2">Driver or Assistant for MD HC Public Schools</option>
      <option value ="3">Driver for SAMEH R LABIB, LLC</option>
    </select>
  </div>
  */

  $selectB_str = "\n<div>\n  <label for=\"vehicleId\" class=\"fixedwidth\">Which one?</label>\n" .
      "  <select name=\"vehicleId\" id=\"vehicleId\">\n";

  /*
  Here is the loop that builds the main body of the select box.
  */
  unset($temp_1);
  unset($temp_2);
  reset($vehicle_in);
  while ($array_cell = each($vehicle_in))
  {
    $temp_1 = $array_cell['value'][0];
    $temp_2 = $array_cell['value'][1];
    $selectB_str .=
    "    <option value=\"$temp_1\">$temp_1 $temp_2</option>\n";
  }

  $selectB_str .= "  </select>\n</div>\n\n";
  return $selectB_str;
}



function validLabelVehicleId() {
/* Verifies that the user supplied both a label and
vehicle id. Also, verifies that the supplied string values
are of appropriate length and content. Also, this function
will assign appropriately the global values for $label and
$vehicleId.

This function needs to have the $label, $vehicleId values backslashed
appropriately for possible insertion into database before return to
main program.
*/

  global $label, $vehicleId;

  if ( isset($_POST['submit']) AND (($_POST['submit'] == "Edit maintenance item data")
      OR ($_POST['submit'] == "Retrieve maintenance item data")) ) {
      
    // Initialize $label and $vehicleId.
    if ( isset($_POST['label']) ) {
      $label = $_POST['label'];
    } else {
      $label = "";
    }
    if ( isset($_POST['vehicleId']) ) {
      $vehicleId = $_POST['vehicleId'];
    } else {
      $vehicleId = "";
    }

    // If magic quotes is on I'll stripslashes.
    if ( get_magic_quotes_gpc() ) {
      $label = stripslashes($label);
      $vehicleId = stripslashes($vehicleId);
    }

    // Trim white space.
    $label = trim($label);
    $vehicleId = trim($vehicleId);
    
    // Test the values and return truth.
    if ( strlen($label) > 65 || strlen($vehicleId) > 22 ) {
      return false;
    }
    if ( strlen($label) < 1 || strlen($vehicleId) < 1 ) {
      return false;
    }
    if ( !is_numeric($vehicleId) ) {
      return false;
    }

    // addslashes
    $label = addslashes($label);

  } else {
    die('Script aborted #12580. -Programmer.');
  }

  return true;
}



function labelVehicleIdInTable() {
/* Indicates whether the maintenance item whose label and vehicleId are
available in the global $label and $vehicleId are found in the table. */

  global $label, $vehicleId;

  $query = "SELECT id
            FROM maintenItems
            WHERE label = '$label' AND vehicleId = '$vehicleId'";
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
Assumption: A record containing the label and vehicleId for this
vehicle already exists. Assumption: $label and $vehicleId are
global variables which contain valid values. */

  // $isAnomaly - whether any form string is too long
  $isAnomaly = false;

  // Have required fields been filled out?
  $requireMet = true;

  global $status_message;

  // All form variables are global.
  global $label, $timeInterval, $mileInterval, $vehicleId, $mileNext;
  global $timeNext, $timeDesired, $comment;

  if ( isset($_POST['submit']) and $_POST['submit'] == "Edit maintenance item data" ) {
    // Transfer POSTS to regular vars
    if ( isset($_POST['timeInterval']) ) {
      $timeInterval = $_POST['timeInterval'];
    } else {
      $timeInterval = "";
    }
    if ( isset($_POST['mileInterval']) ) {
      $mileInterval = $_POST['mileInterval'];
    } else {
      $mileInterval = "";
    }
    $mileNext = "";
    $timeNext = "";
    $timeDesired = "";
    if ( isset($_POST['comment']) ) {
      $comment = $_POST['comment'];
    } else {
      $comment = "";
    }

    if ( !get_magic_quotes_gpc() ) {
      $label = addslashes($label);
      $timeInterval = addslashes($timeInterval);
      $mileInterval = addslashes($mileInterval);
      $vehicleId = addslashes($vehicleId);
      $mileNext = addslashes($mileNext);
      $timeNext = addslashes($timeNext);
      $timeDesired = addslashes($timeDesired);
      $comment = addslashes($comment);
    }

    // Trim white space.
    $label = trim($label);
    $vehicleId = trim($vehicleId);
    $timeInterval = trim($timeInterval);
    $mileInterval = trim($mileInterval);
    $mileNext = trim($mileNext);
    $timeNext = trim($timeNext);
    $timeDesired = trim($timeDesired);
    $comment = trim($comment);


    // Verify string length and deal with anomalies

    // The string length should not be longer than the
    // MAXLENGTH of the FORM field unless slashes were added.
    // Therefore make allowance for this for strings which may
    // have slashes added.
    if ( strlen($label) > 65 ) { $isAnomaly = true; }
    if ( strlen($vehicleId) > 22 ) { $isAnomaly = true; }
    if ( strlen($timeInterval) > 10 ) { $isAnomaly = true; }
    if ( strlen($mileInterval) > 8 ) { $isAnomaly = true; }
    if ( strlen($mileNext) > 8 ) { $isAnomaly = true; }
    if ( strlen($timeNext) > 10 ) { $isAnomaly = true; }
    if ( strlen($timeDesired) > 10 ) { $isAnomaly = true; }
    if ( strlen($comment) > 92 ) { $isAnomaly = true; }

    // I need to validate the $dateOfPurchase and $dateOfRemoval
    // strings and set $isAnomaly appropriately.
    if ( strlen($timeInterval) > 0 and !isDateInterval($timeInterval) ) { $isAnomaly = true; }
    if ( strlen($timeNext) > 0 and !isDate($timeNext) ) { $isAnomaly = true; }
    if ( strlen($timeDesired) > 0 and !isDate($timeDesired) ) { $isAnomaly = true; }

    // Verify numeric data is numeric.
    if ( strlen($vehicleId) > 0 and !is_numeric($vehicleId)) {$isAnomaly = true; }
    if ( strlen($mileInterval) > 0 and !is_numeric($mileInterval)) {$isAnomaly = true; }
    if ( strlen($mileNext) > 0 and !is_numeric($mileNext)) {$isAnomaly = true; }

    // Verify range for numeric data.
    if ( strlen($mileInterval) > 0 and !($mileInterval < 600000)) {$isAnomaly = true; }
    if ( strlen($mileNext) > 0 and !($mileNext < 1800000)) {$isAnomaly = true; }

    // Find out if required fields were filled out
    if ( strlen($label) < 1 || strlen($vehicleId) < 1 ) {
      $requireMet = false;
    }

    // If required fields were inputed then update database.
    if ( $requireMet == true and $isAnomaly == false ) {

      // Send data to db
      $query = "UPDATE maintenItems
                SET timeInterval = STR_TO_DATE('$timeInterval','%m/%d/%Y'),
                    mileInterval = '$mileInterval',
                    mileNext = '$mileNext',
                    timeNext = STR_TO_DATE('$timeNext','%m/%d/%Y'),
                    timeDesired = STR_TO_DATE('$timeDesired','%m/%d/%Y'),
                    comment = '$comment'
                WHERE label = '$label' AND vehicleId = '$vehicleId'";
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
/* Puts the data from an existent maintenance item record into the
variables which will populate an update form. It is assumed
that $label and $vehicleId correspond to a maintenance item record which
exists already. Also, this function makes sure that all form
variables get initialized with values and defined as global.
Otherwise, it aborts.
*/

  // All form variables are global.
  global $label, $timeInterval, $mileInterval, $vehicleId, $mileNext;
  global $timeNext, $timeDesired, $comment;

  $query = "SELECT DATE_FORMAT(timeInterval, '%m/%d/%Y'), mileInterval, mileNext,
DATE_FORMAT(timeNext, '%m/%d/%Y'), DATE_FORMAT(timeDesired, '%m/%d/%Y'), comment
            FROM maintenItems
            WHERE label = '$label' AND vehicleId = '$vehicleId'";
  $result = mysql_query($query);
  if (!$result || mysql_num_rows($result) < 1) {
    die('Error 56801. -Programmer.');
  } else {
    $user_array = mysql_fetch_array($result);
    
    $timeInterval = $user_array['DATE_FORMAT(timeInterval, \'%m/%d/%Y\')'];
    $mileInterval = $user_array['mileInterval'];
    $mileNext = $user_array['mileNext'];
    $timeNext = $user_array['DATE_FORMAT(timeNext, \'%m/%d/%Y\')'];
    $timeDesired = $user_array['DATE_FORMAT(timeDesired, \'%m/%d/%Y\')'];
    $comment = $user_array['comment'];
    
    // Text and Textarea fields have had backslashes
    // added to escape single quotes ('), double
    // quotes ("), backslashes (\) and NULL before
    // insertion into the database. Therefore, we must
    // undo this before displaying these strings.
    $mileInterval = stripslashes($mileInterval);
    $mileNext = stripslashes($mileNext);
    $comment = stripslashes($comment);
    // Even though $label was not
    // retrieved
    $label = stripslashes($label);
  }

  return;
}



function createNewRecord() {
/* Inserts a new maintenance item record populating it with the
valid label and vehicleId strings supplied by the user. */

  global $label, $vehicleId;

  $query = "INSERT INTO maintenItems (label, vehicleId)
            VALUES ('$label', '$vehicleId')";
  $result = mysql_query($query);
  if (!$result || mysql_affected_rows() < 1) {
    die('Error adding new record. 00099. -Programmer.');
  } else {
    return;
  }
}



function presentUpdateForm() {
/* Presents the form for supplying (or changing) field
values for an existing maintenance item record. Also, presents
the $status_message. The $mode, $label and $vehicleId
global variables must contain valid values (which will
further more be passed along when the form is submitted.)
The $label and $vehicleId values can't be changed here (but
are displayed.)
*/

  global $mode, $status_message, $submitCounter;

  // All form variables are global.
  global $label, $timeInterval, $mileInterval, $vehicleId, $mileNext;
  global $timeNext, $timeDesired, $comment;

  // mode must be valid
  $modeIsNotValid = true;
  if ( $mode == 'stageOne') { $modeIsNotValid = false; }
  if ( $mode == 'stageTwo') { $modeIsNotValid = false; }
  if ( $mode == 'stageThree') { $modeIsNotValid = false; }
  if ($modeIsNotValid) { die('Error 11102. -Programmer.'); }

  // The main program has a more rigorous validation test.
  if ((strlen($label) < 1) OR (strlen($vehicleId) < 1)) {
    die('Error 22001. -Programmer.');
  }


  /*
  Construct form:
  */

  site_header('Add Maintenance Item');

  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];

  if ( !isset($status_message) || $status_message == "" ) {
    $message_str = "";
  } else {
    $message_str = "<p class=\"errmsg\">$status_message</p>";
  }

  $userform_str = <<<EOUSERFORMSTR

$message_str

<p>Do not create maintenance items that are complex. For
example an oil and filter change should not be one item. It needs to be split up
into 1. Oil Change 2. Main Oil Filter Change 3. Secondary Oil Filter Change. You
should also create maintenance items for parts that are purchased but which will not be used
immediately.</p>
<p>As soon as you are done here, use the Update Maintenance Item script to
<em class="highlight">give timeNext and mileNext their initial values.</em></p>

<p>label: $label<br/>
vehicle id: $vehicleId</p>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Form:</legend>
  <div>
    <label for="timeInterval" class="fixedwidth">time interval (mm/dd/ccyy)</label>
    <input type="text" name="timeInterval" id="timeInterval" value="$timeInterval" size="10" maxlength="10"/>
  </div>
  <div>
    <label for="mileInterval" class="fixedwidth">mile interval (integer)</label>
    <input type="text" name="mileInterval" id="mileInterval" value="$mileInterval" size="8" maxlength="8"/>
  </div>
  <div>
    <label for="comment" class="fixedwidth">comment</label>
    <input type="text" name="comment" id="comment" value="$comment" size="41" maxlength="85"/>
  </div>
  <div>
    <input type="hidden" name="mode" value="stageThree">
    <input type="hidden" name="submitCounter" value="$submitCounter">
    <input type="hidden" name="label" value="$label">
    <input type="hidden" name="vehicleId" value="$vehicleId">
  </div>
  <div class="buttonarea">    
    <input type="submit" name="submit" value="Edit maintenance item data"/>
  </div>
  </fieldset>
</form>

EOUSERFORMSTR;
  echo $userform_str;

  site_footer();
  
  return;

}

?>