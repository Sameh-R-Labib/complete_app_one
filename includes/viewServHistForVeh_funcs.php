<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
This file is for inclusion in viewServHistForVeh.php.
*/


require_once('table_funcs.php');
require_once('generalFormFuncs.php');




function form_destroy() {
/*
Reset all $_SESSION variables for this form to their default values.
*/
  $_SESSION['VSHFV_mode'] = 'stageOne';
  $_SESSION['VSHFV_vclID'] = "";
  $_SESSION['VSHFV_dateFrom'] = "";
  $_SESSION['VSHFV_dateTo'] = "";
  $_SESSION['VSHFV_matrix'] = array();
  $_SESSION['VSHFV_submitToken'] = "";

  return;
}




function selectVehicle() {
/*
Present a form which allows user to specify a vehicle. At the top
instruct user to purpose of this script. The purpose is to show the
service records along with their maintenance items chronologically
for a period in time.
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
  $_SESSION['VSHFV_submitToken'] = $submitToken;

  /*
  Construct page:
  */
  site_header('View Service History for Vehicle');
  
  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];

  $page_str = <<<EOPAGESTR

<h2>Step One</h2>

<p>This script allows you view the service history of a vehicle. If you
want to see general maintenance purchases just specify vehicle 000.</p>

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



function saveVID() {
/*
Take the vehicle id from post to session. Make sure it gets form
validated.
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
  if ($submitToken != $_SESSION['VSHFV_submitToken']) {
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
    $_SESSION['VSHFV_vclID'] = $_POST['theID'];
    $vclID = $_SESSION['VSHFV_vclID'];
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



function formDateRange() {
/*
Have the user specify the FROM and To dates.
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
  $_SESSION['VSHFV_submitToken'] = $submitToken;

  site_header('View Service History for Vehicle');
  $php_self = $_SERVER['PHP_SELF'];
  $page_str = <<<EOPAGESTR

<h2>Step Two</h2>

<p>This script will show the history for a range of time which is
bound by the dates you specify.</p>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Specify Time Range:</legend>
  <div>
    <label for="dateFrom" class="fixedwidth">From (mm/dd/ccyy):</label>
    <input type="text" name="dateFrom" id="dateFrom" value="" size="10" maxlength="10"/>
  </div>
  <div>
    <label for="dateTo" class="fixedwidth">To (mm/dd/ccyy):</label>
    <input type="text" name="dateTo" id="dateTo" value="" size="10" maxlength="10"/>
  </div>
  <div>
    <input type="hidden" name="submitToken" value="$submitToken">
  </div>
  <div class="buttonarea">
    <input type="submit" name="cancel" value="Cancel"/>
    <input type="submit" name="submit" value="Submit Time Range"/>
  </div>
  </fieldset>
</form>

EOPAGESTR;
  echo $page_str;
  site_footer();
  return;
}



function saveDateRange() {
/*
Receive and validate the date range values.
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
  if ($submitToken != $_SESSION['VSHFV_submitToken']) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  if ( isset($_POST['submit']) and $_POST['submit'] == "Submit Time Range" ) {
    // Transfer POSTS to regular vars
    if ( isset($_POST['dateFrom']) ) {
      $dateFrom = $_POST['dateFrom'];
    } else {
      $dateFrom = "";
    }
    if ( isset($_POST['dateTo']) ) {
      $dateTo = $_POST['dateTo'];
    } else {
      $dateTo = "";
    }
    $dateFrom = trim($dateFrom);
    $dateTo = trim($dateTo);
    if ( strlen($dateFrom) != 10 OR strlen($dateTo) != 10 ) {
      form_destroy();
      die('Err: 81828289477. -Programmer.');
    }
    if ( strlen($dateFrom) > 0 and !isDate($dateFrom) ) {
      form_destroy();
      die('Err: 81828289322. -Programmer.');
    }
    if ( strlen($dateTo) > 0 and !isDate($dateTo) ) {
      form_destroy();
      die('Err: 81828289144. -Programmer.');
    }
    $_SESSION['VSHFV_dateFrom'] = $dateFrom;
    $_SESSION['VSHFV_dateTo'] = $dateTo;
  } else {
    form_destroy();
    die('Err: 339546123. -Programmer.');
  }
  return;
}



function buildMatrix() {
/*
Query the database and feed the matrix which holds all the values we
need. NOTE: Chronological order is important here.

Description of Matrix:
Variable name is $matrix. It is a multi-dimensional array. Each top level
$matrix element correlates to a single invoice. So for example:
$matrix[0] is for the first invoice
$matrix[1] is for the second invoice
And so on.
Each invoice is an array. The first element of this array will be an
array that holds the sevice record field values. The second element of
this invoice array holds an array of maintenance items. So for example:
$matrix[0] is the first invoice
$matrix[0][0] is an array that holds the service record field values for it.
$matrix[0][1] is an array (multidimensional) that holds maintenance item labels for it.
*/

  $dateFrom = $_SESSION['VSHFV_dateFrom'];
  $dateTo = $_SESSION['VSHFV_dateTo'];
  $vclID = $_SESSION['VSHFV_vclID'];

  /*
  Get all the service records within the date range for this vehicle.
  When you get them make sure they are ordered chronologically from
  oldest to newest. Store them in the matrix. Each service record will
  feed a $matrix[][0] element. This element (service record) is an array also!
  This service record array will consist of the following:
  $matrix[][0][0] is the service record id
  $matrix[][0][1] is the service record cost
  $matrix[][0][2] is the shops businessName associated with the service record shopId
  $matrix[][0][3] is the service record timedate
  $matrix[][0][4] is the service record mileage
  */

  /*
  TO HAVE AN ARRAY OF THE SERVICE RECORDS WHICH ARE IN THE TIME FRAME AND
  ASSOCIATED WITH OUR VEHICLE:
  1. We collect all the service records in this time frame in an array.
  2. We collect all the maintenance items for this vehicle in an array.
  3. Strip away the service records that do not associate with any of the
     maintenance items we have.
  */

  /*
  We collect all the service records in this time frame in an array.
  */
  $query = "SELECT serviceRecords.id, serviceRecords.cost, shops.businessName,
            serviceRecords.timedate, serviceRecords.mileage
            FROM serviceRecords INNER JOIN shops
            ON serviceRecords.shopId = shops.id
            WHERE serviceRecords.timedate BETWEEN STR_TO_DATE('$dateFrom' , '%m/%d/%Y')
            AND STR_TO_DATE('$dateTo' , '%m/%d/%Y')
            ORDER BY serviceRecords.timedate ASC";
  $result = mysql_query($query);
  if (!$result) {
    form_destroy();
    die('Error 4877789987. -Programmer.');
  } elseif ( mysql_num_rows($result) < 1 ) {
    form_destroy();
    die('No service records found in this time frame. -Programmer.');
  } else {
    /*
    Here I need a loop which reads the query results into an array.
    */
    while ($row = mysql_fetch_row($result)) {
      // businessName may have slashes
      $row[2] = stripslashes($row[2]);
      $servRecInfo[] = $row;
    }
  }
  
  /*
  We collect all the maintenance items for this vehicle in an array.
  */
  $tableName = "maintenItems";
  $fieldNames = array('id', 'label');
  $whereClause = "WHERE vehicleId = '$vclID'";
  $MID_Info = getValuesForHTML_Table($fieldNames, $tableName, $whereClause);
  if ($MID_Info == FALSE) {
    form_destroy();
    die('Err: 2232546879. -Programmer.');
  }

  /*
  Strip away the service records that do not associate with any of the
  maintenance items we have.
  */
  $servRecInfo = stripAway($servRecInfo, $MID_Info);


  /*
  Now we feed $servRecInfo into $matrix.
  Iterate through $servRecInfo putting each of its elements into a new
  $matrix element.
  */
  foreach ($servRecInfo as $sR) {
    $matrix[][0] = $sR;
  }

  /*
  $matrix[][1] is an array that holds the maintenance item labels for the
  matrix element. Now we need to build that portion of our matrix. To do
  this we iterate through $matrix and assign this.
  */
  foreach ($matrix as $key_var => $value_var) {
    $matrix[$key_var][1] = makeMID_Arr($value_var[0][0]);
    // give MySQL a break.
    usleep(30);
  }
  if (empty($matrix)){
    form_destroy();
    die('Error 0110110958. -Programmer.');
  }
  $_SESSION['VSHFV_matrix'] = $matrix;
  return;
}



function makeMID_Arr($SID) {
/*
This function returns an array of maintenance item labels. These labels
are for the maintenance items associated with $SID. If we are unable to
return an array with at least one label then the script dies.

BE AWARE: the array returned must be two dimensional in order for it to be compatible
with the function that displays tables. That is why it's neccessary to have the extra
[0] you see in the while statement below.
*/
  $query = "SELECT maintenItems.label
            FROM servRecToMaintenItem INNER JOIN maintenItems
            ON servRecToMaintenItem.maintenItemId = maintenItems.id
            WHERE servRecToMaintenItem.servRecId = '$SID'";
  $result = mysql_query($query);
  if (!$result) {
    form_destroy();
    die('Query failed. Err: 367790875. May want sleep(). -Programmer.');
  }
  if ( mysql_num_rows($result) < 1) {
    form_destroy();
    die('This should never happen. Err: 765790875. -Programmer.');
  }
  while ($row = mysql_fetch_row($result)) {
    // label may have slashes
    $row[0] = stripslashes($row[0]);
    // see header comment about the extra[0]
    $miArr[][0] = $row[0];
  }
  return $miArr;
}



function stripAway($servRecInfo_IN, $MID_Info_IN) {
/*
This is a helper function for buildMatrix(). It takes $servRecInfo_IN and
removes the elements which have no correlation to the maintenance items in
$MID_Info_IN according to the table servRecToMaintenItem.

If $servRecInfo_IN becomes empty then the script dies. Otherwise, the function
returns the new $servRecInfo_IN.
*/
  /*
  Iterate through $servRecInfo_IN. While doing so build $new_servRecInfo.
  Elements of $servRecInfo_IN get copied into $new_servRecInfo only if
  they meet the criteria.
  */
  foreach ($servRecInfo_IN as $sR) {
    /*
    If the id belonging to the service record $sR correlates to one of the
    maintenance items for this vehicle according to the table servRecToMaintenItem
    then copy add $sR as a member of $new_servRecInfo.
    */
    if (correlatesHmm($sR[0], $MID_Info_IN)) {
      $new_servRecInfo[] = $sR;
    }
  }
  if (empty($new_servRecInfo)) {
    form_destroy();
    die('Again, no service records (224). -Programmer.');
  }
  return $new_servRecInfo;
}



function correlatesHmm($SID_IN, $MID_Array) {
/*
This function takes a service record id $SID_IN and an array of maintenance items
$MID_Array of the type used in stripAway and tells you if there is a correlation
between the SID and any MID found in $MID_Array where the correlation is defined by
the table servRecToMaintenItem.
*/
  $itCorrelates = FALSE;
  /*
  Iterate through $MID_Array. If any member of $MID_Array has an MID which
  correlates to $SID_IN then set $itCorrelates = TRUE.
  */
  foreach ($MID_Array as $member) {
    if (foundOneToOne($member[0], $SID_IN)) {
      $itCorrelates = TRUE;
    }
  }
  return $itCorrelates;
}



function foundOneToOne($MID, $SID) {
/*
Tells you if there is a record in the servRecToMaintenItem table which has the
MID/SID pair given.
*/
  $query = "SELECT *
            FROM servRecToMaintenItem
            WHERE servRecId = '$SID' AND maintenItemId = '$MID'";
  $result = mysql_query($query);
  if (!$result) {
    form_destroy();
    die('Error 9296989594. -Programmer.');
  } elseif (mysql_num_rows($result) < 1) {
    return FALSE;
  } else {
    return TRUE;
  }
}



function presentMatrix() {
/*
NOTE: Chronological order is important here.
Display the information. The information came from a database and therefore
stripslashes may need to be applied (I'm thinking maintenance item label and
businessName.) NOTE: I went back and took care of this outside this function.
*/
  /*
  Access the matrix.
  */
  if (isset($_SESSION['VSHFV_matrix'])) {
    $matrix = $_SESSION['VSHFV_matrix'];
  }
  /*
  Form a string of HTML that constitutes the presentation of the service record
  info along with their associated maintenance item labels. If the $matrix has
  no elements then the string will reflect this.
  */
  $dataStr ="";
  if (empty($matrix)) {
    $dataStr = "<p class=\"errmsg\">There is nothing to present.</p>";
  } else {
    /*
    Loop builds $dataStr. Basically the string will be made up of a series of
    tables.
    */
    /*
    Loop through matrix. Each time add to $dataStr a table for service record
    and a table for maintenance items.
    */
    reset($matrix);
    while ($array_cell = each($matrix))
    {
      $servrTemp = $array_cell['value'][0];  // service record array temp
      $maintTemp = $array_cell['value'][1];  // maintenance array temp
      $dataStr .= makeServiceTable($servrTemp);
      $dataStr .= makeMaintenTable($maintTemp);
      $dataStr .= "<p>&nbsp;</p>\n";
    }
  }
  /*
  Construct page:
  */
  site_header('View Service History for Vehicle');
  
  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];

  $page_str = <<<EOPAGESTR

<h2>Step Three</h2>

<p>Service History:</p>

$dataStr

EOPAGESTR;
  echo $page_str;
  site_footer();
  return;
}



function makeServiceTable($servrTemp_IN) {
/*
This function returns an HTML table string. This table is for displaying service
record related information for the viewServHistForVeh.php script. The variable
$servrTemp_IN is an array:
  $servrTemp_IN[0] is the service record id
  $servrTemp_IN[1] is the service record cost
  $servrTemp_IN[2] is the shops businessName associated with the service record shopId
  $servrTemp_IN[3] is the service record timedate
  $servrTemp_IN[4] is the service record mileage
If $servrTemp_IN is empty then return an empty string.
*/
  $returnStr = "";
  if (empty($servrTemp_IN)) {
    return $returnStr;
  }
  
  /*
  We need to make $servrTemp_IN multidimensional so it will work with the table
  function.
  */
  $data[0] = $servrTemp_IN;
  
  /*
  Use table function to create the desired string.
  */
  $cap = 'Service Record';
  $nOfCols = 5;
  $tblHeader = array('id', 'cost', 'shop', 'date time', 'mileage');
  $returnStr .= makeTable($cap, $nOfCols, $tblHeader, $data);

  return $returnStr;
}



function makeMaintenTable($maintTemp_IN) {
/*
This function returns an HTML table string. This table is for displaying maintenance
item related information for the viewServHistForVeh.php script. The variable
$maintTemp_IN is an array:
  $maintTemp_IN[0][0] is the label for the first maintenance item
  $maintTemp_IN[1][0] is the label for the second maintenance item
  $maintTemp_IN[2][0] is the label for the third maintenance item
  and so on
If $maintTemp_IN is empty then return an empty string.
*/
  $returnStr = "";
  if (empty($maintTemp_IN)) {
    return $returnStr;
  }

  /*
  Use table function to create the desired string.
  */
  $cap = 'Maintenance Items';
  $nOfCols = 1;
  $tblHeader = array('label');
  $returnStr .= makeTable($cap, $nOfCols, $tblHeader, $maintTemp_IN);

  return $returnStr;
}

?>