<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
This file is forinclusion in shopJobList.php.
*/



require_once('table_funcs.php');
require_once('generalFormFuncs.php');
include_once('header_footer_printerFriendly.php');





function form_destroy() {
/*
Reset all $_SESSION variables for this form to their default values.
*/
  $_SESSION['SJL_mode'] = 'stageOne';
  $_SESSION['SJL_vclID'] = "";
  $_SESSION['SJL_vclNumber'] = NULL;
  $_SESSION['SJL_timeDesired_val_array'] = array();
  $_SESSION['SJL_matrix'] = array();
  $_SESSION['SJL_timeDesired'] = "";
  $_SESSION['SJL_dataStr'] = "";
  $_SESSION['SJL_submitToken'] = "";

  return;
}




function selectVehicle() {
/*
Inform the user. Present a vehicle selector.
*/

  /*
  retrieve the array of vehicles
  */
  $tableName = "vehicles";
  $fieldNames = array('id', 'knownAs');
  $whereClause = '';
  $vehicle = getValuesForHTML_Table($fieldNames, $tableName, $whereClause);
  if ($vehicle == FALSE) {
    form_destroy();
    die('Err: 66812444. -Programmer.');
  }


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
  $_SESSION['SJL_submitToken'] = $submitToken;

  /*
  Construct form:
  */
  site_header('Shop Job List');

  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];
  $userform_str = <<<EOUSERFORMSTR

<h2>Step One</h2>

<p>This form is for producing a list to present (along with the vehicle and key) to
a shop when you turn in your vehicle for maintenance. Earlier,
you were able to specify timeDesired for the maintenance items of this vehicle. That
form was called "Set timeDesired."</p>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Specify the Vehicle:</legend>
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




function validateSave() {
/*
Capture and validate the vehicle id.
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
  if ($submitToken != $_SESSION['SJL_submitToken']) {
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
    $_SESSION['SJL_vclID'] = $_POST['theID'];
    $vclID = $_SESSION['SJL_vclID'];
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
  return;
}




function getVehicleNumber() {
/*
Get the vehicle number (what it is knownAs.)
*/
  $vclID = $_SESSION['SJL_vclID'];
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
  $_SESSION['SJL_vclNumber'] = $vclNumber;
  return;
}




function getAllTimeDesiredVals() {
/*
Gather all possibilities of timeDesired values for vehicle.
Note: We are making sure that timeDesired values being shown to the user are maintained
in mm/dd/yyyy format instead of the SQL format of yyyy-mm-dd in which they are stored.
*/

  /*
  Get all timeDesired values for maintenItems records for this vehicle.
  */
  $vclID = $_SESSION['SJL_vclID'];
  $query = "SELECT DATE_FORMAT(timeDesired, '%m/%d/%Y')
            FROM maintenItems
            WHERE vehicleId='$vclID'";
  $result = mysql_query($query);
  if (!$result OR mysql_num_rows($result) < 1) {
    form_destroy();
    die('Query failed. Err: 81252986895. -Programmer.');
  }
  while ($row = mysql_fetch_row($result)) {
    $timeDesired_All[] = $row;
  }


  /*
  So, what do we have now?
  $timeDesired_All
  
  What is $timeDesired_All?
  It is an array of all the timeDesired values for maintenItems records for this vehicle.
  Some array elements may be a string like "" or "" or a NULL character or a duplicate.
  The format of the array is (shown by example):
    $timeDesired_All[0][0] = the first timeDesired
    $timeDesired_All[1][0] = the second timeDesired
    $timeDesired_All[2][0] = the third timeDesired
    And so on.
  */

  /*
  What should we do now?
  We need to strip away all undesired timeDesired elements and feed the resulting
  list of timeDesired values into a one dimensional array. This new array will be
  called $unique. The format of the array is (shown by example):
    $unique[0] = our first unique timeDesired value.
    $unique[1] = our second ...
    $unique[2] = our third ...
    ...
  */
  $unique = array(); // Array of unique timeDesired values for vehicle.
  foreach ($timeDesired_All as $oneElement) {
    if ($oneElement[0] != "00/00/0000" AND !empty($oneElement[0]) AND
                 !alreadyThere($oneElement[0], $unique)) {
      $unique[] = $oneElement[0];
    }
  }

  /*
  If $unique array has no elements there is no reason to continue.
  */
  if (empty($unique)) {
    form_destroy();
    die('No timeDesired values were established for this vehicle from before. -Programmer.');
  }

  $_SESSION['SJL_timeDesired_val_array'] = $unique;
  return;
}




function alreadyThere($x_IN, $y_IN) {
/*
This is a helper for getAllTimeDesiredVals().
It returns a TRUE or FALSE.
The return is TRUE if $x_IN is found in $y_IN.
Otherwise, returns false.
*/

  $returnVal = FALSE;

  foreach ($y_IN as $temp) {
    if ($temp == $x_IN) {
      $returnVal = TRUE;
    }
  }
  return $returnVal;
}




function presentSpecifyTimeDesired() {
/*
Present a form so user can specify a timeDesired value.
*/
  $vclNumber = $_SESSION['SJL_vclNumber'];

  /*
  We need to make a selection box string. We made one for the vehicle id. However,
  this one is different. We have $_SESSION['SJL_timeDesired_val_array'] which is a one
  dimensional array. And, the selectable portion is a single part.
  */
  $tD_Array = $_SESSION['SJL_timeDesired_val_array'];
  $tD_Selector = "";

  if (empty($tD_Array)) {
    form_destroy();
    die('No values for select box err:56079888. -Programmer.');
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

  $tD_Selector .= "\n<div>\n  <label for=\"timeDesired\" class=\"fixedwidth\">Pick one:</label>\n" .
      "  <select name=\"timeDesired\" id=\"timeDesired\">\n";

  /*
  Here is the loop that builds the main body of the select box.
  */
  unset($temp);
  reset($tD_Array);
  while ($array_cell = each($tD_Array))
  {
    $temp = $array_cell['value'];
    $tD_Selector .=
    "    <option value=\"$temp\">$temp</option>\n";
  }

  $tD_Selector .= "  </select>\n</div>\n\n";

  /*
  Manage protection from aborted form since code uses sessions.
  In other words the code which validates the values received from a
  submitted form needs to know that it is not executing after the user
  has come back after a previously abandoned instance of the script
  (another possibility is the user may have attempted to run two
  instance of the script).
  */
  $submitToken = time();
  $_SESSION['SJL_submitToken'] = $submitToken;

  /*
  Construct form:
  */
  site_header('Shop Job List');

  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];
  $userform_str = <<<EOUSERFORMSTR

<h2>Step Two</h2>

<p>[ For vehicle $vclNumber ] Specify the timeDesired value which corresponds
to the group of maintenance items you want worked on.</p>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Specify timeDesird value:</legend>
$tD_Selector
  <div>
    <input type="hidden" name="submitToken" value="$submitToken">
  </div>
  <div class="buttonarea">
    <input type="submit" name="cancel" value="Cancel"/>
    <input type="submit" name="submit" value="Submit"/>
  </div>
  </fieldset>
</form>

EOUSERFORMSTR;
  echo $userform_str;
  site_footer();
  return;
}




function validateSaveTimeDesired() {
/*
Capture and validate the timeDesired value.
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
  if ($submitToken != $_SESSION['SJL_submitToken']) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  /*
  Take the timeDesired from the $_POST variable and put it into a
  $_SESSION variable.
  */
  if ( !isset($_POST['timeDesired']) ) {
    form_destroy();
    die("Err: 3855507. Try again! -Programmer.");
  } else {
    $_SESSION['SJL_timeDesired'] = $_POST['timeDesired'];
    $timeDesired = $_SESSION['SJL_timeDesired'];
    if ( strlen($timeDesired) != 10 ) {
      form_destroy();
      die('Date string is too short or too long (3341330.) -Programmer.');
    }
    if ( strlen($timeDesired) > 0 and !isDate($timeDesired)) {
      form_destroy();
      die('The string is not a date value (54565656221.) -Programmer.');
    }
    /*
    Validate that timeDesired is in our database table.
    */
    $query = "SELECT DATE_FORMAT(timeDesired, '%m/%d/%Y')
              FROM maintenItems
              WHERE timeDesired = STR_TO_DATE('$timeDesired','%m/%d/%Y')";
    $result = mysql_query($query);
    if (!$result) {
      form_destroy();
      die('Query failed. Err: 8695986895. -Programmer.');
    }
    if ( mysql_num_rows($result) < 1) {
      form_destroy();
      die('err: 010104414034532. -Programmer.');
    }
  }
  return;
}




function formMatrixsShopJobList() {
/*
Create a matrix of maintenance items and parts associated with
the timeDesired value.
*/
  /*
  What will the matrix look like?
  Example:
  $matrix[0]          is maintenance item [0].
  $matrix[0][0]       is the array of field values for maintenance item [0].
  $matrix[0][0][0]    is maintenance item [0]'s label.
  $matrix[0][0][1]    is maintenance item [0]'s comment.
  $matrix[0][0][2]    is maintenance item [0]'s id.
  $matrix[0][1]       is an array of part information for maintenance item [0].
  $matrix[0][1][0]    is the first part element.
  $matrix[0][1][2]    is the second part element.
  So on.
  Each part element is an array whose first element is the part number and the second
  is the part's label.
  */

  /*
  Let's build the matrix.
  Start by adding the maintenance items to the matrix.
  Which maintenance items?
  The ones for this vehicle and timeDesired value.
  */
  $vclID = $_SESSION['SJL_vclID'];
  $timeDesired = $_SESSION['SJL_timeDesired'];
  $tableName = "maintenItems";
  $fieldNames = array('label', 'comment', 'id');
  $whereClause = "WHERE vehicleId = '$vclID' AND timeDesired = STR_TO_DATE('$timeDesired','%m/%d/%Y')";
  $mntItem = getValuesForHTML_Table($fieldNames, $tableName, $whereClause);
  if ($mntItem == FALSE) {
    form_destroy();
    die('Error: no records found (0994128). -Programmer.');
  }

  /*
  What is $mntItem ?
  It is an array of maintenance items. Each element contains an array whose first element
  is the label and second element is the comment.
  Example:
  $mntItem[0][0] = label for first maintenance item.
  $mntItem[0][1] = comment for first maintenance item.
  $mntItem[0][2] = id for the first maintenance item.
  $mntItem[1][0] = label for second maintenance item.
  $mntItem[1][1] = comment for second maintenance item.
  $mntItem[1][2] = id for the second maintenance item.
  */

  /*
  Use values stored in $mntItem to build $matrix up from nothing.
  */
  $matrix = array();
  reset($mntItem);
  while ($array_cell = each($mntItem))
  {
    $matrix[][0] = $array_cell['value'];
  }
  
  /*
  So, we have a matrix with the maintenance items. Now, we need to add the parts
  to the matrix. Hold on to your hat because here it goes!
  */

  /*
  $matrix needs a parts array for each $matrix element.
  The $matrix[][1] holds the array of parts.
  To get a defenition of the parts array see the defenition of $matrix above.
  $matrix[][0][2] holds the id of the maintenance item for the part numbers.
  So this is what needs to be done:
  For each first dimension $matrix element I need to assign to its second
  dimension element having index one (1) an array of all the parts
  associated with that maintenance item.
  */
  unset($i);
  reset($matrix);
  while ($array_cell = each($matrix))
  {
    $i = $array_cell['key'];
    $matrix[$i][1] = partsForThis($matrix[$i][0][2]);
  }

  $_SESSION['SJL_matrix'] = $matrix;
  return;
}




function partsForThis($MID_in) {
/*
This function is different from a function by the same name in processAnInvoice.php.
This function takes a maintenance ID and returns an array containing all the
parts associated with it. If there are no parts associated
with it then the function will return an empty array. If the database query
fails then the script will abort. If no maintenance ID was passed to the
function it will abort.

The parts array which is returned has the following characteristics:
  - Each element is a "part".
  - Each "part" is an array containing the partNumber and label field values for that part.
*/
  $partsArr = array();
  if (!isset($MID_in) OR $MID_in == "") {
    form_destroy();
    die('Error 56565606. -Programmer.');
  }
  
  /*
  Query to find all part numbers associated with MID.
  */
  $query = "SELECT maintenVehicleParts.partNumber, maintenVehicleParts.label
            FROM itemToPart INNER JOIN maintenVehicleParts
            ON itemToPart.partId = maintenVehicleParts.id
            WHERE itemToPart.itemId = '$MID_in'";
  $result = mysql_query($query);
  if (!$result) {
    form_destroy();
    die('Query failed. Err: 278862221. May want sleep(). -Programmer.');
  }
  if ( mysql_num_rows($result) < 1) {
    return $partsArr;
  }
  while ($row = mysql_fetch_row($result)) {
    $partsArr[] = $row;
  }
  return $partsArr;
}




function displayMatrixForBrowser() {
/*
Display all the information gathered in session and from database.
The display should include a form having "I'm done" and "Show printer
friendly" submit buttons.
*/
  /*
  Load local variables.
  */
  $matrix = $_SESSION['SJL_matrix'];
  $vclNumber = $_SESSION['SJL_vclNumber'];
  $timeDesired = $_SESSION['SJL_timeDesired'];

  /*
  Now form a string of HTML that constitutes the presentation of the maintenance
  items along with their associated parts. If the $matrix has no elements then
  the string will reflect this.
  */
  $dataStr = "";
  if (empty($matrix)) {
    $dataStr = "<p class=\"errmsg\">There is nothing to present since no" .
               " maintenance items for this timeDesired value and vehicle.</p>";
  } else {
    /*
    Loop builds $dataStr. Basically the string will be made up of a series of
    tables.
    */
    reset($matrix);
    while ($array_cell = each($matrix))
    {

      /*
      Create a temporary string for the maintenance item table. Then, add it to
      $dataStr. Note: the reason we use $maintenItemCurr[0] not $maintenItemCurr
      below is that the table function expects a two dimensional array.
      */
      $maintenItemCurr[0] = $array_cell['value'][0];
      $cap = "Maintenance Item";
      $nOfCols = 3;
      $tblHeader = array('label', 'comment', 'id');
      $mTable = makeTable($cap, $nOfCols, $tblHeader, $maintenItemCurr);
      $dataStr .= $mTable;

      /*
      Create a temporary string for the part number table. Then, add it to $dataStr.
      */
      $partsCurr = $array_cell['value'][1];
      $cap = "Parts";
      $nOfCols = 2;
      $tblHeader = array('p/n', 'label');
      $pTable = makeTable($cap, $nOfCols, $tblHeader, $partsCurr);
      $dataStr .= $pTable;
      
      /*
      Insert a blank line into the string.
      */
      $dataStr .= "\n<p>&nbsp;</p>\n\n";
    }
  }

  /*
  We will need this if user wants a printer friendly version.
  */
  $_SESSION['SJL_dataStr'] = $dataStr;

  /*
  Manage protection from aborted form since code uses sessions.
  In other words the code which validates the values received from a
  submitted form needs to know that it is not executing after the user
  has come back after a previously abandoned instance of the script
  (another possibility is the user may have attempted to run two
  instance of the script).
  */
  $submitToken = time();
  $_SESSION['SJL_submitToken'] = $submitToken;

  /*
  Construct page:
  */
  site_header('Shop Job List');
  
  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];

  $page_str = <<<EOPAGESTR

<h2>Step Three</h2>

<p>vehicle number: $vclNumber <br/>target date: $timeDesired </p>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>To do list:</legend>
$dataStr
  <div>
    <input type="hidden" name="submitToken" value="$submitToken">
  </div>
  <div class="buttonarea">
    <input type="submit" name="cancel" value="done"/>
    <input type="submit" name="submit" value="printer friendly"/>
  </div>
  </fieldset>
</form>

EOPAGESTR;
  echo $page_str;
  site_footer();
  return;
}




function displayPrinterFriendly() {
/*
Display the same information in black and white so as to be printer friendly.
*/
  $vclNumber = $_SESSION['SJL_vclNumber'];
  $timeDesired = $_SESSION['SJL_timeDesired'];
  $dataStr = $_SESSION['SJL_dataStr'];

  /*
  Construct page:
  */
  site_headerPF('Shop Job List');

  $page_str = <<<EOPAGESTR

<h2>SAMEH R LABIB, LLC.<br/>443-538-1746</h2>

<p>vehicle number: $vclNumber <br/>target date: $timeDesired </p>

<h3>Info. for Shop</h3>
$dataStr

EOPAGESTR;
  echo $page_str;
  site_footerPF();
  return;
}

?>