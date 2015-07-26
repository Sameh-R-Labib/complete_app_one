<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/* This file is for inclusion in viewMItems4Vehicle.php. */

require_once('table_funcs.php');



function presentM4V_Form() {
/*
Present the form which allows the user to submit a vehicle ID.
$status_message is global. Its message will be displayed.
stageTwo will be passed as a hidden form field. Also,
$submitCounter will be received as a global and passed on as
a hidden form field.

Note: This function presents the form along with the complete
HTML for a page. This will also include an introductory
paragraph.
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

  site_header('View Maintenance Items for Vehicle');

  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];

  if ( !isset($status_message) || $status_message == "" ) {
    $message_str = "";
  } else {
    $message_str = "<p class=\"errmsg\">$status_message</p>";
  }

  $userform_str = <<<EOUSERFORMSTR

$message_str

<p>The purpose of this script is to show a table containing some information about
all the maintenance items associated with the vehicle you specify. Note that the
code has a limit of three hundred records when retrieving from the database.</p>

<p>Note: to view maintenance items which do not correlate with any particular vehicle
use a vehicle ID value of five (5).</p>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Specify a Vehicle/machine: <span class="formcomment">*Required</span></legend>
$selectBox
  <div>
    <input type="hidden" name="mode" value="stageTwo">
    <input type="hidden" name="submitCounter" value="$submitCounter">
  </div>
  <div class="buttonarea">    
    <input type="submit" name="submit" value="Show m. items for vehicle"/>
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



function validV_id() {
/*
Returns a boolean indicating whether the vehicle ID entered is:
1. not an empty string.
2. not too long a string.
3. is numeric.
4. belongs to a record in the vehicles table.

Also, makes $vehicleId available as global.
*/


  global $vehicleId;


  if ( isset($_POST['submit']) AND ($_POST['submit'] == "Show m. items for vehicle") ) {

    if (isset($_POST['theID'])) {
      $vehicleId = $_POST['theID'];
    } else {
      $vehicleId = "";
    }

    // If magic quotes is on I'll stripslashes.
    if ( get_magic_quotes_gpc() ) {
      $vehicleId = stripslashes($vehicleId);
    }

    if ( strlen($vehicleId) > 12 ) { return false; }
    if ( strlen($vehicleId) <  1 ) { return false; }

    // Verify numeric data is numeric.
    if ( strlen($vehicleId) > 0 and !is_numeric($vehicleId)) { return false; }

    // addslashes not necessary or desired for specific reason

    // make sure it is in the system
    $isInSystem = isThere($vehicleId);
    return $isInSystem;
  } else {
    die('Script aborted #12580. -Programmer.');
  }
}



function isThere($vehicleId_in) {
/*
Is the vehicle in our database table?
*/

  $query = "SELECT id
            FROM vehicles
            WHERE id = '$vehicleId_in'";
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



function presentM4V_Table() {
/*
Presents the maintenance items for the vehicle ID specified by the user.
Also, present a message passed to it from the main script.
*/


  global $vehicleId, $status_message;




  /*
  construct the table string
  */


  $tableName = "maintenItems";


  /*
  Ceate an array (numerically indexed) containing the names
  of the database table fields which we want to display in
  our HTML table.
  */
  $fieldNames = array('id', 'label');


  /*
  Create an SQL WHERE clause.
  */
  $whereClause = "WHERE vehicleId = '$vehicleId'";


  /*
  Create a two dimensional array called $mItem which will
  hold all the maintenance item data to be displayed in the table.
  See the header of getValuesForHTML_Table() for a description
  of the two dimensional array $mItem.
  */
  $mItem = getValuesForHTML_Table($fieldNames, $tableName, $whereClause);

  if ($mItem) {


    $cap = "Maintenance Items for Vehicle ID $vehicleId";


    $nOfCols = 2;   // number of columns to be displayed


    /*
    Create an array (numerically indexed) containing the column
    header strings.
    */
    $tblHeader = array('M. item', 'label');


    /*
    Create an HTML string for the table.
    */
    $htmlTable_1 = makeTable($cap, $nOfCols, $tblHeader, $mItem);



    /*
    Construct page:
    */

    if ( !isset($status_message) || $status_message == "" ) {
      $message_str = "";
    } else {
      $message_str = "<p class=\"errmsg\">$status_message</p>";
    }


    site_header('View Maintenance Items for Vehicle');
  
  
    $page_str = <<<EOPAGESTR

$message_str

$htmlTable_1

EOPAGESTR;
    echo $page_str;
    site_footer();

    return;

  } else {
    /*
    Show a page with a message only.
    */

    $status_message = "No maintenance items found for Vehicle ID $vehicleId";

    $message_str = "<p class=\"errmsg\">$status_message</p>";

    site_header('View Maintenance Items for Vehicle');
  
  
    $page_str = <<<EOPAGESTR

$message_str

EOPAGESTR;
    echo $page_str;
    site_footer();

    return;
  }
}


?>