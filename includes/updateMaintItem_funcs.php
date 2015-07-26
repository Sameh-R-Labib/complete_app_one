<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
This file is for inclusion in updateMaintItem.php.
*/



require_once('table_funcs.php'); // remove if no table functions
require_once('generalFormFuncs.php');




function form_destroy() {
/*
Reset all $_SESSION variables for this form to their default values.
*/
  $_SESSION['UMI_mode'] = 'stageOne';
  $_SESSION['UMI_PrevMode'] = "";
  $_SESSION['UMI_vclID'] = "";
  $_SESSION['UMI_MID'] = "";
  $_SESSION['UMI_timeInterval'] = "";
  $_SESSION['UMI_mileInterval'] = "";
  $_SESSION['UMI_mileNext'] = "";
  $_SESSION['UMI_timeNext'] = "";
  $_SESSION['UMI_timeDesired'] = "";
  $_SESSION['UMI_comment'] = "";
  $_SESSION['UMI_submitToken'] = "";

  return;
}




function selectVehicle() {
/*
Present a form which has a selection box that allows the user to specify
a vehicle. Instruct the user as to the general purpose of this script
and to the specific purpose of this step in the process.
*/
  /*
  I'll retrieve the array of vehicles which will feed the selection box.
  I'll use the table function as a wrapper for this query.
  */
  $tableName = "vehicles";
  $fieldNames = array('id', 'knownAs');
  $whereClause = '';
  $vehicle = getValuesForHTML_Table($fieldNames, $tableName, $whereClause);
  if ($vehicle == FALSE) {
    die('Error: no records found (66812444). -Programmer.');
  }

  /*
  Now, I'll make a selection box.
  */
  $selectBox = selectBox($vehicle);

  /*
  Manage protection from aborted form since code uses sessions.
  In other words the code which validates the values received from a
  submitted form needs to know that it is not executing after the user
  has come back after a previously abandoned instance of the script
  (another possibility is the user may have attempted to run two
  instance of the script).
  */
  $submitToken = time();
  $_SESSION['UMI_submitToken'] = $submitToken;

  /*
  Construct page:
  */
  site_header('Update Maintenance Item');
  
  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];

  $page_str = <<<EOPAGESTR

<h2>Step One</h2>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Specify the Vehicle:</legend>
$selectBox
  <div>
    <input type="hidden" name="submitToken" value="$submitToken">
  </div>
  <div class="buttonarea">
    <input type="submit" name="cancel" value="Cancel"/>
    <input type="submit" name="submit" value="It is this vehicle!"/>
  </div>
  </fieldset>
</form>

<p>This script allows you to update some of the field values of
a maintenance item which relates to a particular vehicle.</p>

EOPAGESTR;
  echo $page_str;
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
    form_destroy();
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

  $selectB_str = "\n<div>\n  <label for=\"theID\" class=\"fixedwidth\">Make a selection</label>\n" .
      "  <select name=\"theID\" id=\"theID\">\n";

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



function saveVID() {
/*
This function validates and saves the vehicle id received from a POST.
If the received id is not valid the script will die. The id will be saved
in a session variable.
*/
  /*
  Handle the situation where we have arrived here as a result of the user
  wandering back to the script after giving up in the middle of running it
  earlier when presented with a form (or running multiple instances).
  */
  if (isset($_POST['submitToken'])) {
    $submitToken = $_POST['submitToken'];
  } else {
    $submitToken = "";
  }
  if ($submitToken != $_SESSION['UMI_submitToken']) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  /*
  Take the vehicle ID from the $_POST variable and put it into a
  $_SESSION variable.
  */
  if ( !isset($_POST['theID']) ) {
    form_destroy();
    die("Err: 3855507. Try again! -Programmer.");
  } else {
    $_SESSION['UMI_vclID'] = $_POST['theID'];
    $vclID = $_SESSION['UMI_vclID'];
    if ( strlen($vclID) > 12 ) {
      form_destroy();
      die('err: 3333330');
    }
    if ( strlen($vclID) <  1 ) {
      form_destroy();
      die('err: 4444440');
    }
    if ( strlen($vclID) > 0 and !is_numeric($vclID)) {
      form_destroy();
      die('err: 55555550');
    }
    /*
    Validate that vehicle is in our database table.
    */
    $query = "SELECT id
              FROM vehicles
              WHERE id = '$vclID'";
    $result = mysql_query($query);
    if (!$result) {
      form_destroy();
      die('Query failed. Err: 880037. -Programmer.');
    }
    if ( mysql_num_rows($result) < 1) {
      form_destroy();
      die('err: 010101010332.');;
    }
  }
  return;
}



function selectMID() {
/*
This function presents a selection box of the maintenance items for the
vehicle.
*/
  $vclID = $_SESSION['UMI_vclID'];

  /*
  First we need to gather the information which will be presented in the
  form. This includes the id and label of each maintenance item for this
  vehicle. We will gather this information in a two-dimensional array. We are
  creating an array of arrays. The top level is an array of so called maintenance
  items. Each element (aka maintenance item) is a two element array which holds
  the id and label of the maintenance item.
  This array will be called: mntItem.
  For example, the first maintenance item is going to be mntItem[0].
  mntItem[0][0] will contain the id value of mntItem[0].
  mntItem[0][1] will contain the label value of mntItem[0]
  */
  /*
  To load mntItem I'll use the same functions I used to acquire data to
  populate an HTML table.
  */
  $tableName = "maintenItems";
  $fieldNames = array('id', 'label');
  $whereClause = "WHERE vehicleId = '$vclID'";
  $mntItem = getValuesForHTML_Table($fieldNames, $tableName, $whereClause);
  if ($mntItem == FALSE) {
    form_destroy();
    die('Error: no records found (0994128). -Programmer.');
  }
  /*
  Now, I'll make the selection box.
  */
  $selectBox = selectBox($mntItem);

  /*
  Manage protection from aborted form since code uses sessions.
  In other words the code which validates the values received from a
  submitted form needs to know that it is not executing after the user
  has come back after a previously abandoned instance of the script
  (another possibility is the user may have attempted to run two
  instance of the script).
  */
  $submitToken = time();
  $_SESSION['UMI_submitToken'] = $submitToken;

  /*
  Construct page:
  */
  site_header('Update Maintenance Item');
  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];
  $page_str = <<<EOPAGESTR

<h2>Step Two</h2>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Specify the Maintenance Item:</legend>
$selectBox
  <div>
    <input type="hidden" name="submitToken" value="$submitToken">
  </div>
  <div class="buttonarea">
    <input type="submit" name="cancel" value="Cancel"/>
    <input type="submit" name="submit" value="Submit"/>
  </div>
  </fieldset>
</form>

EOPAGESTR;
  echo $page_str;
  site_footer();
  return;
}



function saveMID() {
/*
Save the maintenance id value retrieved from POST. Place the id value
in a SESSION variable. Validate the id and kill the script if id is
not valid.
*/
  /*
  Handle the situation where we have arrived here as a result of the user
  wandering back to the script after giving up in the middle of running it
  earlier when presented with a form (or running multiple instances).
  */
  if (isset($_POST['submitToken'])) {
    $submitToken = $_POST['submitToken'];
  } else {
    $submitToken = "";
  }
  if ($submitToken != $_SESSION['UMI_submitToken']) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  /*
  Take the maintenance item ID from the $_POST variable and put it into a
  $_SESSION variable.
  */
  if ( isset($_POST['theID']) ) {
    $_SESSION['UMI_MID'] = $_POST['theID'];
    $mID = $_SESSION['UMI_MID'];
    if ( strlen($mID) > 12 ) {
      form_destroy();
      die('err: 4433330');
    }
    if ( strlen($mID) <  1 ) {
      form_destroy();
      die('err: 2244440');
    }
    if ( strlen($mID) > 0 and !is_numeric($mID)) {
      form_destroy();
      die('err: 22555550');
    }
    /*
    Validate that maintenance id is in our database table.
    */
    $query = "SELECT id
              FROM maintenItems
              WHERE id = '$mID'";
    $result = mysql_query($query);
    if (!$result) {
      form_destroy();
      die('Query failed. Err: 150037. -Programmer.');
    }

    if ( mysql_num_rows($result) < 1) {
      form_destroy();
      die('err: 202001010332.');;
    }
  }
  return;
}



function maintItemUpdateForm() {
/*
This function does:
1. Presents a prefilled form.
2. Form fields are the fields to edit.
3. Prefill values come from SESSION or database.
4. Prefill values come from database only the first time.
*/
  /*
  Get prefill values.
  */
  $MID = $_SESSION['UMI_MID'];
  if ( $_SESSION['UMI_PrevMode'] == 'stageTwo' ) {
    /*
    Get prefill values from the database.
    */
    $query = "SELECT DATE_FORMAT(timeInterval, '%m/%d/%Y'), mileInterval, mileNext,
DATE_FORMAT(timeNext, '%m/%d/%Y'), DATE_FORMAT(timeDesired, '%m/%d/%Y'), comment
              FROM maintenItems
              WHERE id = '$MID'";
    $result = mysql_query($query);
    if (!$result || mysql_num_rows($result) < 1) {
      form_destroy();
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
      
      $_SESSION['UMI_timeInterval'] = $timeInterval;
      $_SESSION['UMI_mileInterval'] = $mileInterval;
      $_SESSION['UMI_mileNext'] = $mileNext;
      $_SESSION['UMI_timeNext'] = $timeNext;
      $_SESSION['UMI_timeDesired'] = $timeDesired;
      $_SESSION['UMI_comment'] = $comment;
    }
  } elseif ( $_SESSION['UMI_PrevMode'] == 'stageFour' ) {
    $timeInterval = $_SESSION['UMI_timeInterval'];
    $mileInterval = $_SESSION['UMI_mileInterval'];
    $mileNext = $_SESSION['UMI_mileNext'];
    $timeNext = $_SESSION['UMI_timeNext'];
    $timeDesired = $_SESSION['UMI_timeDesired'];
    $comment = $_SESSION['UMI_comment'];
  } else {
    form_destroy();
    die('Err: 13422265. -Programmer.');
  }

  /*
  Manage protection from aborted form since code uses sessions.
  In other words the code which validates the values received from a
  submitted form needs to know that it is not executing after the user
  has come back after a previously abandoned instance of the script
  (another possibility is the user may have attempted to run two
  instance of the script).
  */
  $submitToken = time();
  $_SESSION['UMI_submitToken'] = $submitToken;

  /*
  Construct form:
  */
  site_header('Update Maintenance Item');
  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];
  $userform_str = <<<EOUSERFORMSTR

<h2>Step Three</h2>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Field Values You Can Edit:</legend>
  <div>
    <label for="mileInterval" class="fixedwidth">mile interval (integer)</label>
    <input type="text" name="mileInterval" id="mileInterval" value="$mileInterval" size="8" maxlength="8"/>
  </div>
  <div>
    <label for="mileNext" class="fixedwidth">mile next (integer)</label>
    <input type="text" name="mileNext" id="mileNext" value="$mileNext" size="8" maxlength="8"/>
  </div>
  <div>
    <label for="timeInterval" class="fixedwidth">time interval (mm/dd/ccyy)</label>
    <input type="text" name="timeInterval" id="timeInterval" value="$timeInterval" size="10" maxlength="10"/>
  </div>
  <div>
    <p>Some buses are on schedule A; Others are on schedule B.<br/>
    <em class="highlight">ALL MAINTENANCE MUST ONLY BE ON THESE DATES</em><br/>
    Schedule A: 10/15/CCYY and 04/01/CCYY<br/>
    Schedule B: 12/25/CCYY and 06/22/CCYY</p>
    <label for="timeNext" class="fixedwidth">time next (mm/dd/ccyy)</label>
    <input type="text" name="timeNext" id="timeNext" value="$timeNext" size="10" maxlength="10"/>
  </div>
  <div>
    <label for="timeDesired" class="fixedwidth">time desired (mm/dd/ccyy)</label>
    <input type="text" name="timeDesired" id="timeDesired" value="$timeDesired" size="10" maxlength="10"/>
  </div>
  <div>
    <label for="comment" class="fixedwidth">comment</label>
    <input type="text" name="comment" id="comment" value="$comment" size="41" maxlength="85"/>
  </div>
  <div>
    <input type="hidden" name="submitToken" value="$submitToken">
  </div>
  <div class="buttonarea">
    <input type="submit" name="cancel" value="Cancel"/>
    <input type="submit" name="submit" value="Edit maintenance item data"/>
  </div>
  </fieldset>
</form>

EOUSERFORMSTR;
  echo $userform_str;
  site_footer();
  return;
}



function validateSave() {
/*
Make sure not to copy the invalid data to the SESSION.
Make sure SESSION values do not have slashes since values are for display not for SQL.
Make sure values have slashes before saving.

This function does:
1. Validate field values submitted.
2. Save the data to the database and SESSION if it is all valid.
3. Otherwise update the SESSION values to the good ones only.
4. Return TRUE if data was saved to the database.
5. Return FALSE if not saved to database.
*/
  /*
  Handle the situation where we have arrived here as a result of the user
  wandering back to the script after giving up in the middle of running it
  earlier when presented with a form (or running multiple instances).
  */
  if (isset($_POST['submitToken'])) {
    $submitToken = $_POST['submitToken'];
  } else {
    $submitToken = "";
  }
  if ($submitToken != $_SESSION['UMI_submitToken']) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  $isAnomaly = false;
  $timeIntervalIsValid = true;
  $mileIntervalIsValid = true;
  $mileNextIsValid = true;
  $timeNextIsValid = true;
  $timeDesiredIsValid = true;
  $commentIsValid = true;
  $MID = $_SESSION['UMI_MID'];

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
    if ( isset($_POST['mileNext']) ) {
      $mileNext = $_POST['mileNext'];
    } else {
      $mileNext = "";
    }
    if ( isset($_POST['timeNext']) ) {
      $timeNext = $_POST['timeNext'];
    } else {
      $timeNext = "";
    }
    if ( isset($_POST['timeDesired']) ) {
      $timeDesired = $_POST['timeDesired'];
    } else {
      $timeDesired = "";
    }
    if ( isset($_POST['comment']) ) {
      $comment = $_POST['comment'];
    } else {
      $comment = "";
    }

    if ( get_magic_quotes_gpc() ) {
      $mileInterval = stripslashes($mileInterval);
      $mileNext = stripslashes($mileNext);
      $comment = stripslashes($comment);
    }

    // Trim white space.
    $timeInterval = trim($timeInterval);
    $mileInterval = trim($mileInterval);
    $mileNext = trim($mileNext);
    $timeNext = trim($timeNext);
    $timeDesired = trim($timeDesired);
    $comment = trim($comment);

    // The string length should not be longer than the
    // MAXLENGTH of the FORM field unless slashes were added.
    // Therefore make allowance for this for strings which may
    // have slashes added.
    if ( strlen($timeInterval) > 10 ) {
      $isAnomaly = true;
      $timeIntervalIsValid = false;
    }
    if ( strlen($mileInterval) > 8 ) {
      $isAnomaly = true;
      $mileIntervalIsValid = false;
    }
    if ( strlen($mileNext) > 8 ) {
      $isAnomaly = true;
      $mileNextIsValid = false;
    }
    if ( strlen($timeNext) > 10 ) {
      $isAnomaly = true;
      $timeNextIsValid = false;
    }
    if ( strlen($timeDesired) > 10 ) {
      $isAnomaly = true;
      $timeDesiredIsValid = false;
    }
    if ( strlen($comment) > 92 ) {
      $isAnomaly = true;
      $commentIsValid = false;
    }

    // I need to validate the $dateOfPurchase and $dateOfRemoval
    // strings and set $isAnomaly appropriately.
    if ( strlen($timeInterval) > 0 and !isDateInterval($timeInterval) ) {
      $isAnomaly = true;
      $timeIntervalIsValid = false;
    }
    if ( strlen($timeNext) > 0 and !isDate($timeNext) ) {
      $isAnomaly = true;
      $timeNextIsValid = false;
    }
    if ( strlen($timeDesired) > 0 and !isDate($timeDesired) ) {
      $isAnomaly = true;
      $timeDesiredIsValid = false;
    }

    // Verify numeric data is numeric.
    if ( strlen($mileInterval) > 0 and !is_numeric($mileInterval)) {
      $isAnomaly = true;
      $mileIntervalIsValid = false;
    }
    if ( strlen($mileNext) > 0 and !is_numeric($mileNext)) {
      $isAnomaly = true;
      $mileNextIsValid = false;
    }

    // Verify range for numeric data.
    if ( strlen($mileInterval) > 0 and !($mileInterval < 600000)) {
      $isAnomaly = true;
      $mileIntervalIsValid = false;
    }
    if ( strlen($mileNext) > 0 and !($mileNext < 1800000)) {
      $isAnomaly = true;
      $mileNextIsValid = false;
    }

    /*
    Update the SESSION for the values that were not found invalid.
    */
    if ( $timeIntervalIsValid ) {
      $_SESSION['UMI_timeInterval'] = $timeInterval;
    }
    if ( $mileIntervalIsValid ) {
      $_SESSION['UMI_mileInterval'] = $mileInterval;
    }
    if ( $mileNextIsValid ) {
      $_SESSION['UMI_mileNext'] = $mileNext;
    }
    if ( $timeNextIsValid ) {
      $_SESSION['UMI_timeNext'] = $timeNext;
    }
    if ( $timeDesiredIsValid ) {
      $_SESSION['UMI_timeDesired'] = $timeDesired;
    }
    if ( $commentIsValid ) {
      $_SESSION['UMI_comment'] = $comment;
    }
  } else {
    form_destroy();
    die('Script aborted #7700. -Programmer.');
  }

  if ( $isAnomaly == false ) {
    /*
    Save data to database and return TRUE.
    */
    $comment = addslashes($comment);
    $query = "UPDATE maintenItems
              SET timeInterval = STR_TO_DATE('$timeInterval','%m/%d/%Y'),
                  mileInterval = '$mileInterval',
                  mileNext = '$mileNext',
                  timeNext = STR_TO_DATE('$timeNext','%m/%d/%Y'),
                  timeDesired = STR_TO_DATE('$timeDesired','%m/%d/%Y'),
                  comment = '$comment'
              WHERE id = '$MID'";
    $result = mysql_query($query);
    if (!$result || mysql_affected_rows() < 1) {
      form_destroy();
      die('Err: 227890436. -Programmer.');
    }
    return TRUE;
  } else {
    return FALSE;
  }
}



function retrieveAndConfirm() {
/*
Present a declaration of success which includes the actual values retrieved
from the database.
*/
  $MID = $_SESSION['UMI_MID'];
  $query = "SELECT label, DATE_FORMAT(timeInterval, '%m/%d/%Y'), mileInterval, mileNext,
DATE_FORMAT(timeNext, '%m/%d/%Y'), DATE_FORMAT(timeDesired, '%m/%d/%Y'), comment
            FROM maintenItems
            WHERE id = '$MID'";
  $result = mysql_query($query);
  if (!$result || mysql_num_rows($result) < 1) {
    form_destroy();
    die('Error 56801. -Programmer.');
  } else {
    $user_array = mysql_fetch_array($result);
  
    $label = $user_array['label'];
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
    $comment = stripslashes($comment);
  }

  /*
  Construct page:
  */
  site_header('Update Maintenance Item');
  $page_str = <<<EOPAGESTR

<h2>Step Four</h2>

<p>Congratulations! The data values have been saved. Here is a listing
of their values retrieved directly from the database:</p>

<p>label: $label<br/>
time interval: $timeInterval<br/>
mile interval: $mileInterval<br/>
mile next: $mileNext<br/>
time next: $timeNext<br/>
time desired: $timeDesired<br/>
comment: $comment</p>

EOPAGESTR;
  echo $page_str;
  site_footer();
  form_destroy();
  return;
}



function presentErrorStatus() {
/*
Present a form explaining that one or two data fields were invalid and
that the user will have the opportunity to edit again.
*/
  /*
  Manage protection from aborted form since code uses sessions.
  In other words the code which validates the values received from a
  submitted form needs to know that it is not executing after the user
  has come back after a previously abandoned instance of the script
  (another possibility is the user may have attempted to run two
  instance of the script).
  */
  $submitToken = time();
  $_SESSION['UMI_submitToken'] = $submitToken;

  /*
  Construct page:
  */
  site_header('Update Maintenance Item');
  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];
  $page_str = <<<EOPAGESTR

<h2>Step Four</h2>

<p>One or more values you entered was invalid. Now you will be
presented with the form again. NOTE: the invalid fields will show up
prefilled the same way as before they were edited invalidly.</p>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Continue?</legend>
  <div>
    <input type="hidden" name="submitToken" value="$submitToken">
  </div>
  <div class="buttonarea">
    <input type="submit" name="cancel" value="Cancel"/>
    <input type="submit" name="submit" value="Submit"/>
  </div>
  </fieldset>
</form>

EOPAGESTR;
  echo $page_str;
  site_footer();
  return;
}

?>