<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
This file is for inclusion in setTimeDesired.php.
*/


require_once('table_funcs.php');
require_once('generalFormFuncs.php');




function form_destroy() {
/*
Reset all $_SESSION variables for this form to their default values.
*/
  $_SESSION['STD_mode'] = 'stageOne';
  $_SESSION['STD_vclID'] = "";
  $_SESSION['STD_mileCurr'] = "";
  $_SESSION['STD_mileLast'] = "";
  $_SESSION['STD_vclNumber'] = NULL;
  $_SESSION['STD_changed'] = array();
  $_SESSION['STD_submitToken'] = "";

  return;
}




function selectVehicleAndMore() {
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
  /*
  I'll retrieve the array of vehicles which will feed the selection box.
  I'll use the table function as a wrapper for this query.
  */
  $tableName = "vehicles";
  $fieldNames = array('id', 'knownAs');
  $whereClause = '';
  $vehicle = getValuesForHTML_Table($fieldNames, $tableName, $whereClause);
  if ($vehicle == FALSE) {
    form_destroy();
    die('Err: 66812444. -Programmer.');
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
  $_SESSION['STD_submitToken'] = $submitToken;

  /*
  Construct page:
  */
  site_header('Set timeDesired');
  
  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];

  $page_str = <<<EOPAGESTR

<h2>Step One</h2>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Vehicle and Mileages:</legend>
$selectBox
  <div>
    <label for="mileCurr" class="fixedwidth">miles current</label>
    <input type="text" name="mileCurr" id="mileCurr" value="" size="8" maxlength="8"/>
  </div>
  <div>
    <label for="mileLast" class="fixedwidth">miles last maint.</label>
    <input type="text" name="mileLast" id="mileLast" value="" size="8" maxlength="8"/>
  </div>
  <div>
    <input type="hidden" name="submitToken" value="$submitToken">
  </div>
  <div class="buttonarea">
    <input type="submit" name="cancel" value="Cancel"/>
    <input type="submit" name="submit" value="Submit"/>
  </div>
  </fieldset>
</form>

<p>This script allows you to set the timeDesired field value for maintenance items
belonging to a particular
vehicle. A timeDesired is a date value (mm/dd/yyyy). The timeDesired you assign becomes
a tag associated with a group of maintenance items for this vehicle. This tag
allows you to conveniently specify these maintenance items for Shop Job List.
Therefore, do not assign the same timeDesired value to two maintenance items
which will be taken care of by different shops.</p>

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

  $selectB_str = "\n<div>\n  <label for=\"theID\" class=\"fixedwidth\">Which one?</label>\n" .
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



function validateSaveVehMil() {
/*
Transfer the vehicle id and both mileages from post to session. Make
sure form input is validated.
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
  if ($submitToken != $_SESSION['STD_submitToken']) {
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
    $_SESSION['STD_vclID'] = $_POST['theID'];
    $vclID = $_SESSION['STD_vclID'];
    if ( strlen($vclID) > 12 ) {
      form_destroy();
      die('err: 3333330');
    }
    if ( strlen($vclID) <  1 ) {
      form_destroy();
      die('err: 4444440. -Programmer.');
    }
    if ( strlen($vclID) > 0 and !is_numeric($vclID)) {
      form_destroy();
      die('err: 55555550. -Programmer.');
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
      die('err: 010101010332. -Programmer.');
    }
  }
  /*
  Take the mileCurr and mileLast from $_POST variable and put them into
  $_SESSION variables.
  */
  if ( !isset($_POST['mileCurr']) OR !isset($_POST['mileLast']) ) {
    form_destroy();
    die("Err: 69907890. One or more values were not supplied by you. -Programmer.");
  } else {
    $mileCurr = $_POST['mileCurr'];
    $mileLast = $_POST['mileLast'];
    $mileCurr = trim($mileCurr);
    $mileLast = trim($mileLast);
    if ( strlen($mileCurr) > 8  OR strlen($mileLast) > 8 ) {
      form_destroy();
      die('err: 6932200014. -Programmer.');
    }
    if ( strlen($mileCurr) < 1  OR strlen($mileLast) < 1 ) {
      form_destroy();
      die('err: 8126437776. One or more values were not supplied by you. -Programmer.');
    }
    if ( strlen($mileCurr) > 0 and !is_numeric($mileCurr)) {
      form_destroy();
      die('err: 24545163321. Wrong data type value supplied by you. -Programmer.');
    }
    if ( strlen($mileLast) > 0 and !is_numeric($mileLast)) {
      form_destroy();
      die('err: 87878955887. Wrong data type value supplied by you. -Programmer.');
    }
    // Transfer to session.
    $_SESSION['STD_mileCurr'] = $mileCurr;
    $_SESSION['STD_mileLast'] = $mileLast;
  }
  return;
}



function presentation() {
/*
Present the main form. It will re-iterate the vehicle number and two
mileages plus a form table which allows for a date entry on each row.
The table is described as follows. Last column is form field. Each row
is for a maintenance item. The table columns are:
label, t-int, m-int, m-next, t-next, t-desired, in-t-desired
*/
  /*
  What other script makes a large presentation based on the contents of
  a matrix array? viewServiceHistForVeh.php. It shows a service record table
  and a maintenance item table for each invoice between two dates for a vehicle.
  */

  /*
  String which holds the vehicle number, two mileages and form/table.
  */
  $dataStr ="";
  
  $vclID = $_SESSION['STD_vclID'];
  $mileCurr = $_SESSION['STD_mileCurr'];
  $mileLast = $_SESSION['STD_mileLast'];
  
  /*
  We need the vehicle number for this $vclID.
  */

  $tableName = "vehicles";
  $fieldNames = array('knownAs');
  $whereClause = "WHERE id='$vclID'";
  $vehicle = getValuesForHTML_Table($fieldNames, $tableName, $whereClause);
  if ($vehicle == FALSE) {
    form_destroy();
    die('Err: 254815640. -Programmer.');
  }
  $vclNumber = $vehicle[0][0];
  
  // We'll need this later.
  $_SESSION['STD_vclNumber'] = $vclNumber;

  $dataStr .= "<p>Here is what you entered so far:</p>\n\n";
  $dataStr .= "<p>Vehicle: $vclNumber<br/>\nMiles Current: $mileCurr<br/>\n";
  $dataStr .= "Miles Last: $mileLast</p>\n\n";

  /*
  Before we even think about adding the table to $dataStr we need to
  build the matrix.
  
  $matrix will be an array of maintenance item strings which will become the
  table data cell content. For example:
  
  $matrix[0] is an array of content for one table row for one maintenance item.
  $matrix[0][0] is a string which shows the label.
  $matrix[0][1] is a string which shows the mile next
  $matrix[0][2] is a string which shows the time next
  $matrix[0][3] is a string which shows the original time desired
  $matrix[0][4] is a string for a text input form field for a new time desired
  */

  /*
  We want to get all the maintenance item information for this vehicle from the
  database.
  */
  $tableName = "maintenItems";
  $fieldNames = array('id', 'label', 'mileNext', 'timeNext', 'timeDesired');
  $whereClause = "WHERE vehicleId = '$vclID'";
  $mntItem = getValuesForHTML_Table($fieldNames, $tableName, $whereClause);
  if ($mntItem == FALSE) {
    form_destroy();
    die('Error: no records found (0994128). -Programmer.');
  }

  /*
  Iterate through the $mntItem array and build the matrix. Each top level $mntItem element
  correlates to a top level $matrix element.
  */
  foreach ($mntItem as $mR) {
    $id = $mR[0];
    $label = $mR[1];
    $mileNext = $mR[2];
    $timeNext = $mR[3];
    $timeDesired = $mR[4];
    $txtInput = '<label for="nDT[$id]">timeDesired: </label>';
    $txtInput .= "<input type=\"text\" name=\"nDT[$id]\" id=\"nDT[$id]\" value=\"\" " .
                 "size=\"10\" maxlength=\"10\"/>";
    $matrix[] = array($label, $mileNext, $timeNext, $timeDesired, $txtInput);
  }

  /*
  Okay, now we can think about adding the table to $dataStr. To do this first we will
  create the table string using our library table building function.
  */
  $cap = 'Form Table';
  $nOfCols = 5;
  $tblHeader = array('label', 'm-next', 't-next', 't-desired', 'n/t-desired');
  $formTable= makeTable($cap, $nOfCols, $tblHeader, $matrix);

  /*
  Manage protection from aborted form since code uses sessions.
  In other words the code which validates the values received from a
  submitted form needs to know that it is not executing after the user
  has come back after a previously abandoned instance of the script
  (another possibility is the user may have attempted to run two
  instance of the script).
  */
  $submitToken = time();
  $_SESSION['STD_submitToken'] = $submitToken;

  $php_self = $_SERVER['PHP_SELF'];
  $dataStr .= "<form action=\"$php_self\" method=\"post\" class=\"loginform\">\n";
  $dataStr .= "  <fieldset>\n";
  $dataStr .= "<legend>Form</legend>\n";
  $dataStr .= $formTable;
  $dataStr .= "  <div>\n";
  $dataStr .= "    <input type=\"hidden\" name=\"submitToken\" value=\"$submitToken\">\n";
  $dataStr .= "  </div>\n";
  $dataStr .= "  <div class=\"buttonarea\">\n";
  $dataStr .= "    <input type=\"submit\" name=\"cancel\" value=\"Cancel\"/>\n";
  $dataStr .= "    <input type=\"submit\" name=\"submit\" value=\"Submit\"/>\n";
  $dataStr .= "  </div>\n";
  $dataStr .= "  </fieldset>\n";
  $dataStr .= "</form>\n\n";

  /*
  Construct page:
  */
  site_header('Set timeDesired');
  $page_str = <<<EOPAGESTR

<h2>Step Two</h2>

$dataStr

EOPAGESTR;
  echo $page_str;
  site_footer();
  return;
}



function validateSaveTD() {
/*
Update the timeDesired database table fields using only the values
which the user changed. Validate form data. Also, store the maintenance
id values of the maintenance items whose timeDesired got updated so
that we'll be able to pull the data back out of the database for a
confirmation later. So, put those in a session variable.
*/
  $_SESSION['STD_changed'] = array();  // Holds on to the maintenance ids of items
                                       // the user specified a new timeDesired for.

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
  if ($submitToken != $_SESSION['STD_submitToken']) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  /*
  Get the new timeDesired values from the $_POST array. They should be found in an array
  called $_POST['nDT']. The key for each array element is the maintenance id and its value
  is the new timeDesired date in mm/dd/ccyy format. NOTE: if submit does what I think it
  does then the array will only have elements for form fields in which the user made an
  entry.
  
  After we get the array of submitted new timeDesired values and keys we need to update
  the database with the valid values.
  */
  if (isset($_POST['submit']) AND isset($_POST['nDT']) AND
      is_array($_POST['nDT']) AND !empty($_POST['nDT'])) {
    $newTimeD = $_POST['nDT'];

    /*
    Remove elements associated with empty input fields.
    */
    $temp = array();
    foreach ($newTimeD as $MID => $newDate) {
      $newDate = trim($newDate);
      if (!empty($newDate)) {
        $temp[$MID] = $newDate;
      }
    }
    $newTimeD = $temp;

    /*
    We need to iterate through array $newTimeD and do something with each one of its
    elements.
    */
    foreach ($newTimeD as $MID => $newDate) {
      // Redundant now!
      // $newDate = trim($newDate);

      if (strlen($newDate) != 10) {
        form_destroy();
        die('Err: 43321962203. Date string not 10 characters. -Programmer.');
      }
      if ( strlen($newDate) > 0 and !isDate($newDate) ) {
        form_destroy();
        die('Err: 71949795826. Date string is not a date. -Programmer.');
      }
      /*
      Update the database with the new timeDesired for this maintenance id.
      */
      $query = "UPDATE maintenItems
                SET timeDesired = STR_TO_DATE('$newDate','%m/%d/%Y')
                WHERE id = '$MID'";
      $result = mysql_query($query);
      if (!$result || mysql_affected_rows() < 1) {
        form_destroy();
        die('Err: 37594996. Query either failed or did not change anything. -Programmer.');
      }
      $_SESSION['STD_changed'][] = $MID;
      usleep(40);
    }
  } else {
    form_destroy();
    die('Error: 9784632771. The POST var was not found. -Programmer.');
  }
  return;
}



function confirmation() {
/*
Present a confirmation page which includes fresh values from the database and the vehicle
number. This time just show the label and timeDesired for each maintenance item.
*/

  $vehicleNum = $_SESSION['STD_vclNumber'];

  /*
  Build a $confMatrix which holds the values we will display in the table. This
  $confMatrix will be an array of maintenance item elements. Each maintenance item
  element will be an array that holds the label and the new timeDate.
  */
  $midOfChanged = $_SESSION['STD_changed'];
  $confMatrix = array();
  foreach ($midOfChanged as $changeMID) {
    $query = "SELECT label, DATE_FORMAT(timeDesired, '%m/%d/%Y')
              FROM maintenItems
              WHERE id = '$changeMID'";
    $result = mysql_query($query);
    if (!$result || mysql_num_rows($result) < 1) {
      form_destroy();
      die('Error 5648041. -Programmer.');
    } else {
      $user_array = mysql_fetch_array($result);
      $label = $user_array['label'];
      $timeDesired = $user_array['DATE_FORMAT(timeDesired, \'%m/%d/%Y\')'];
      $label = stripslashes($label);
      $confMatrix[] = array($label, $timeDesired);
    }
  }

  /*
  Here we will prepare the string which gets displayed.
  */
  if (empty($midOfChanged)) {
    $ourMessage = "<p>For the vehicle $vehicleNum there were no changes.</p>";
  } else {
    $ourMessage = "<p>For the vehicle $vehicleNum here are the changes:</p>\n\n";

    /*
    Build the table.
    */
    $cap = 'Changed Maintenance Items';
    $nOfCols = 2;
    $tblHeader = array('label', 't-desired');
    $confTable= makeTable($cap, $nOfCols, $tblHeader, $confMatrix);
    $ourMessage .= $confTable;
  }

  /*
  Construct page:
  */
  site_header('Set timeDesired');
  $page_str = <<<EOPAGESTR

<h2>Step Three</h2>

$ourMessage

EOPAGESTR;
  echo $page_str;
  site_footer();
  return;
}

?>