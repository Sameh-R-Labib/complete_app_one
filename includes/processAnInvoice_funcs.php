<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);


/*
This file is for inclusion in processAnInvoice.php.

The functions will pass their values around using the $_SESSION super
global variable array.

Each function must set $_SESSION['ProcAnInv_mode'] appropriately.
*/


require_once('table_funcs.php');
require_once('generalFormFuncs.php');




function form_destroy() {
/*
Reset all $_SESSION variables for this form to their default values.
*/
  $_SESSION['ProcAnInv_mode'] = 'stageOne';
  $_SESSION['PAI_vclID'] = "";
  $_SESSION['originalChoices'] = array();
  $_SESSION['PAI_mIds'] = array();
  $_SESSION['PAI_qtyMoreMID'] = "";
  $_SESSION['PAI_shopID'] = "";
  $_SESSION['PAI_timedate'] = "";
  $_SESSION['PAI_cost'] = "";
  $_SESSION['PAI_mileage'] = "";
  $_SESSION['requiremet'] = FALSE;
  $_SESSION['isanomaly'] = TRUE;
  $_SESSION['srId'] = NULL;
  $_SESSION['PAI_submitToken'] = "";

  return;
}




function selectVehicle() {
/*
This function presents a form which allows the user to select one of
the vehicles in our database table. This vehicle will later be used to
determine which maintenance items to present as choices. There is a
particular vehicle which is used when the user wants to process an
invoice that involves general maintenance items which are not directly
related to any particular vehicle.
*/

  /*
  First we need to gather the information which will be presented in the
  form. This includes the id and knownAs of each vehicle. We
  will gather this information in a two-dimensional array. The first
  dimension, or top level if you want to call it that, corresponds to
  the vehicles. The second dimension corresponds to the two field values
  of the particular vehicle.
  This array will be called: vehicle.
  For example, the first vehicle is going to be vehicle[0].
  vehicle[0][0] will contain the id value of vehicle[0].
  vehicle[0][1] will contain the knownAs value of vehicle[0]
  */

  /*
  To do this first I have to get these values from the database.
  I'll use the same functions I used before to acquire data to
  populate an HTML table.
  */
  $tableName = "vehicles";
  $fieldNames = array('id', 'knownAs');
  $whereClause = '';
  $vehicle = getValuesForHTML_Table($fieldNames, $tableName, $whereClause);
  if ($vehicle == FALSE) {
    form_destroy();
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
  has come back after a previously abandoned instance of the script.
  */
  $submitToken = time();
  $_SESSION['PAI_submitToken'] = $submitToken;

  /*
  Construct page:
  */
  site_header('Process An Invoice');
  
  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];
  
  $page_str = <<<EOPAGESTR

<h2>Step One</h2>

<p>If this invoice is for a general purchase of maintenance supplies choose vehicle
00000005 000.</p>

<p>WARNING: Do NOT use this script to do modifications on an existing
invoice.

<p>WARNING: Make sure the shop is in the system before proceeding.</p>

<p>WARNING: You must specify at least one maintenance item.</p>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Specify a Vehicle:</legend>
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
  $_SESSION['ProcAnInv_mode'] = 'stageTwo';
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

  $selectB_str = "\n<div>\n  <label for=\"vehicleID\" class=\"fixedwidth\">Which vehicle?</label>\n" .
      "  <select name=\"vehicleID\" id=\"vehicleID\">\n";

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



function selectFromExistingMIDs() {
/*
Before doing its main task this function will store (in a session
variable) the vehicle ID which was submitted by the form submission
of the previous stage.

The main task of this function is to present a form which shows the
user the maintenance items which are already available in the system
for the vehicle specified in the prior stage. Then, the user is asked
to specify which amongst these maintenance items should be considered
part of the invoice.

Technically speaking this is how it will be done:
The user will be presented with a checkbox for each of the maintenance
items mentioned above. 
*/

  if (isset($_POST['cancel'])) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  /*
  Handle the situation where we have arrived here as a result of the user
  wandering back to the script after giving up in the middle of running it
  earlier when presented with a form.
  */
  if (isset($_POST['submitToken'])) {
    $submitToken = $_POST['submitToken'];
  } else {
    $submitToken = "";
  }
  if ($submitToken != $_SESSION['PAI_submitToken']) {
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
  if ( !isset($_POST['vehicleID']) ) {
    form_destroy();
    die("Err: 3855507. Try again! -Programmer.");
  } else {
    $_SESSION['PAI_vclID'] = $_POST['vehicleID'];
    $vclID = $_SESSION['PAI_vclID'];
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
      die('Err: 010101010332.');;
    }
  }


  /*
  First we need to gather the information which will be presented in the
  form. This includes the id and label of each maintenance item for this
  vehicle. We will gather this information in a two-dimensional array. The first
  dimension, or top level if you want to call it that, corresponds to
  the maintenance items. The second dimension corresponds to the two field values
  of the particular maintenance item.
  This array will be called: mntItem.
  For example, the first maintenance item is going to be mntItem[0].
  mntItem[0][0] will contain the id value of mntItem[0].
  mntItem[0][1] will contain the label value of mntItem[0]
  */

  /*
  To do this first I have to get these values from the database.
  I'll use the same functions I used before to acquire data to
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
  The array of maintenance items we have here will be useful later. So,
  I'll store it in a session variable.
  */
  $_SESSION['originalChoices'] = array();
  $_SESSION['originalChoices'] = $mntItem;
  
  /*
  Now, I'll make the check boxes.
  */
  $chkBoxes = chkBoxes($mntItem);

  /*
  Manage protection from aborted form since code uses sessions.
  In other words the code which validates the values received from a
  submitted form needs to know that it is not executing after the user
  has come back after a previously abandoned instance of the script.
  */
  $submitToken = time();
  $_SESSION['PAI_submitToken'] = $submitToken;

  /*
  Construct page:
  */
  site_header('Process An Invoice');
  
  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];
  
  $page_str = <<<EOPAGESTR

<h2>Step Two</h2>

<p>Below you will find all the maintenance items in the system for this vehicle.
Please, select all the ones which apply to this invoice. If you do not find
all the maintenace items which belong on this invoice do not worry. You will
be given the opportunity to add those later.</p>


<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Specify all maintenance items:</legend>
$chkBoxes
  <div>
    <input type="hidden" name="submitToken" value="$submitToken">
  </div>
  <div class="buttonarea">
    <input type="submit" name="cancel" value="Cancel"/>
    <input type="submit" name="submit" value="Submit existing MIDs!"/>
  </div>
  </fieldset>
</form>

EOPAGESTR;
  echo $page_str;
  site_footer();
  $_SESSION['ProcAnInv_mode'] = 'stageThree';
  return;
}



function chkBoxes($mntItem_in) {
/*
Input: $mntItem_in is a two-dimensional array. The first
  dimension corresponds to the maintenance items. The second dimension
  corresponds to the two field values of the particular maintenance item.
  For example, the first maintenance item is going to be mntItem[0].
  mntItem_in[0][0] will contain the id value of mntItem[0].
  mntItem_in[0][1] will contain the label value of mntItem[0]
Action: The function chkBoxes() takes this input array and produces a
  string containing the HTML for all the checkbox form fields corresponding
  to the first dimension of the array.
Return: If $mntItem_in array is empty or unavailable the script will die.
  Otherwise, the string will be returned.
*/

  if (!isset($mntItem_in) OR !is_array($mntItem_in) OR sizeof($mntItem_in) < 1) {
    form_destroy();
    die("Function failed to create check boxes because no array was passed.");
  }

  /*
  Here is sample code for a check boxes div on my site.

  <div>
     <p><b>MVA CDL Status</b></p>
     <div>
        <input type="checkbox" name="cdlAB" id="cdlAB" value="1" checked="checked"/>
        <label for="cdlAB">CDL B (or better)</label>
     </div>
     <div>
        <input type="checkbox" name="cdlP" id="cdlP" value="1" checked="checked"/>
        <label for="cdlP">P</label>
     </div>
     <div>
        <input type="checkbox" name="cdlS" id="cdlS" value="1" checked="checked"/>
        <label for="cdlS">S</label>
     </div>
     <div>
        <input type="checkbox" name="aBrakes" id="aBrakes" value="1" checked="checked"/>
        <label for="aBrakes">Air Brakes</label>
     </div>
  </div>
  */
  
  /*
  We make sure we are starting with a fresh slate.
  */
  unset($_POST['mId']);

  $chkB_str = "\n<div>\n   <p><b>Which maintenance items?</b></p>\n";

  /*
  Here is the loop that builds the main body of the check boxes.
  */
  unset($temp_1);
  unset($temp_2);
  reset($mntItem_in);
  $i = 0;
  while ($array_cell = each($mntItem_in))
  {
    $temp_1 = $array_cell['value'][0];
    $temp_2 = $array_cell['value'][1];
    $chkB_str .=
    "   <div>\n" .
    "      <input type=\"checkbox\" name=\"mId[$i][0]\" id=\"$i\" value=\"$temp_1\"/>\n" .
    "      <label for=\"$i\">$temp_2</label>\n" .
    "   </div>\n" .
    "   <div>\n" .
    "      <input type=\"hidden\" name=\"mId[$i][1]\" value=\"$temp_2\">" .
    "   </div>\n";
    $i += 1;
  }

  $chkB_str .= "</div>\n\n";
  return $chkB_str;
}



function askHowManyNewMIDs() {
/*
Before doing its main task this function will store (in a session
variable) the maintenance items selected and submitted in the prior
stage.

The main task of this function is to present a form which asks the
user how many new maintenance items need to be made available.
The form will display the maintenance items that have already been
added to the online version of this invoice as a reminder to the
user. If no maintenance items were indicated to have been added already
then a message will display instead.
*/

  if (isset($_POST['cancel'])) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  /*
  Handle the situation where we have arrived here as a result of the user
  wandering back to the script after giving up in the middle of running it
  earlier when presented with a form.
  */
  if (isset($_POST['submitToken'])) {
    $submitToken = $_POST['submitToken'];
  } else {
    $submitToken = "";
  }
  if ($submitToken != $_SESSION['PAI_submitToken']) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  /*
  Store the maintenance items which the user has indicated belong on
  the invoice. The name of the variable will be: $_SESSION['PAI_mIds'].
  At this point in time they can be found in the array $_POST['mId'].
  If there are no
  values in this $_POST['mId'] array then assign a message string
  to the variable which would later be used to show the maintenance
  items added so far.
  */
  if (!isset($_POST['mId']) OR !is_array($_POST['mId']) OR sizeof($_POST['mId']) < 1) {
    $infoMsg = "<p>So far no maintenance items have been added to this invoice.</p>";
  } else {
    /*
    The list of maintenance items needs to be sanitized because of the way that
    the form created it. In the state which this array is in now there is a first
    dimension element for all the original elements before the formation of the
    check box array since the hidden portions were included no matter what.
    */
    $_SESSION['PAI_mIds'] = array();
    if (isset($_POST['mId'])) {
      $_SESSION['PAI_mIds'] = sanitize($_POST['mId']);
    }
    $M_items = $_SESSION['PAI_mIds'];
    /*
    Create an HTML table string for displaying all the maintenance items in
    $_SESSION['PAI_mIds']. Store this string in $infoMsg.
    */
    $cap = 'Maintenance Items You Have Already Selected:';

    $nOfCols = 2;   // number of columns to be displayed

    /*
    Create an array (numerically indexed) containing the column
    header strings.
    */
    $tblHeader = array('MID', 'Label');

    /*
    Create an HTML string for the table.
    */
    $infoMsg = makeTable($cap, $nOfCols, $tblHeader, $M_items);
  }

  /*
  Manage protection from aborted form since code uses sessions.
  In other words the code which validates the values received from a
  submitted form needs to know that it is not executing after the user
  has come back after a previously abandoned instance of the script.
  */
  $submitToken = time();
  $_SESSION['PAI_submitToken'] = $submitToken;

  /*
  Construct page:
  */
  site_header('Process An Invoice');
  
  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];

  $page_str = <<<EOPAGESTR

<h2>Step Three</h2>

<p>Below you will find two sections. The first is a form asking you
how many more maintenance items you think should be on this invoice. The second
is a presentation of maintenance items added already.</p>


<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>How many more?</legend>
  <div>
    <label for="qtyMoreMID" class="fixedwidth">How many more:</label>
    <input type="text" name="qtyMoreMID" id="qtyMoreMID" value="" size="2" maxlength="2"/>
  </div>
  <div>
    <input type="hidden" name="submitToken" value="$submitToken">
  </div>
  <div class="buttonarea">
    <input type="submit" name="cancel" value="Cancel"/>
    <input type="submit" name="submit" value="Indicate how many more!"/>
  </div>
  </fieldset>
</form>

$infoMsg

EOPAGESTR;
  echo $page_str;
  site_footer();
  $_SESSION['ProcAnInv_mode'] = 'stageFour';
  return;
}



function sanitize($array_in) {
/*
This is a helper function for the function askHowManyNewMIDs(). It takes the
post array submitted in the prior stage and strips away elements of the submitted
maintenance item array which are an aberation of the process of using check boxes
with a multidimensional array. Basically what we want to accomplish is as follows.
We want to unset all first dimension elements which do not contain a second
dimensional element which contains a valid value for its index zero element.
*/

  if (!isset($array_in) OR !is_array($array_in) OR sizeof($array_in) < 1) {
    form_destroy();
    die("Script died at sanitize (990877999). -Programmer.");
  } else {
    /*
    Here is a loop that does the job.
    I can't use the while/each iteration because it does not allow me to
    have proper access to the array I'm iterating through in order to be
    able to unset the current array element.
    NEW APPROACH: I'll just copy the elements I want into a new array.
    Then return the new array.
    */
    unset($temp_1);
    unset($temp_2);
    reset($array_in);
    $new_array = array();
    $count = 0;
    while ($array_cell = each($array_in))
    {
      if (isset($array_cell['value'][0])) {
        $temp_1 = $array_cell['value'][0];
        $temp_2 = $array_cell['value'][1];
        $new_array[$count][0] = $temp_1;
        $new_array[$count][1] = $temp_2;
        $count++;
      }
    }
  }

  return $new_array;
}



function gatherNewMID_labels() {
/*
The main purose of this function is two things:
1. Present a form that collects the labels of maintenance items which the
   user would like to be able to add to this invoice.
2. Set the mode to stageFive.
*/

  if (isset($_POST['cancel'])) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  /*
  Handle the situation where we have arrived here as a result of the user
  wandering back to the script after giving up in the middle of running it
  earlier when presented with a form.
  */
  if (isset($_POST['submitToken'])) {
    $submitToken = $_POST['submitToken'];
  } else {
    $submitToken = "";
  }
  if ($submitToken != $_SESSION['PAI_submitToken']) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  /*
  First let us store the string which will be the text input fields for
  the labels. The quantity of text boxes will be $_SESSION['PAI_qtyMoreMID']
  */
  
  /*
  What does a text input box on our site look like?
  <div>
    <label for="qtyMoreMID" class="fixedwidth">How many more:</label>
    <input type="text" name="qtyMoreMID" id="qtyMoreMID" value="" size="2" maxlength="2"/>
  </div>
  */

  /*
  Here we build this string:
  */
  $txtBoxes = "";
  /*
  Now we need a loop which iterates $_SESSION['PAI_qtyMoreMID'] times.
  */
  $max = $_SESSION['PAI_qtyMoreMID'];
  for ($i=0; $i<$max; $i+=1) {
    $txtBoxes .=
    "<div>\n" .
    "  <label for=\"mLabel[$i]\" class=\"fixedwidth\">maintenance item $i</label>\n" .
    "  <input type=\"text\" name=\"mLabel[$i]\" id=\"mLabel[$i]\" value=\"\" size=\"41\" maxlength=\"65\"/>\n" .
    "</div>\n";
  }

  /*
  Manage protection from aborted form since code uses sessions.
  In other words the code which validates the values received from a
  submitted form needs to know that it is not executing after the user
  has come back after a previously abandoned instance of the script.
  */
  $submitToken = time();
  $_SESSION['PAI_submitToken'] = $submitToken;

  /*
  Construct page:
  */
  site_header('Process An Invoice');
  
  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];
  
  $page_str = <<<EOPAGESTR

<h2>Step Four</h2>

<p>Specify labels for maintenance items which you wish you could have added to
this invoice during an earlier stage.</p>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Specify and submit:</legend>
$txtBoxes
  <div>
    <input type="hidden" name="submitToken" value="$submitToken">
  </div>
  <div class="buttonarea">
    <input type="submit" name="cancel" value="Cancel"/>
    <input type="submit" name="submit" value="Wish to add these!"/>
  </div>
  </fieldset>
</form>

EOPAGESTR;
  echo $page_str;
  site_footer();
  $_SESSION['ProcAnInv_mode'] = 'stageFive';
  return;
}


function informNoNewMIDs() {
/*
This function presents a form which informs the user that the system
knows he/she does not intend to add any non-in-system-already maintenance
items to this invoice. This function will set the mode to stageSeven.
*/

  if (isset($_POST['cancel'])) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  /*
  Handle the situation where we have arrived here as a result of the user
  wandering back to the script after giving up in the middle of running it
  earlier when presented with a form.
  */
  if (isset($_POST['submitToken'])) {
    $submitToken = $_POST['submitToken'];
  } else {
    $submitToken = "";
  }
  if ($submitToken != $_SESSION['PAI_submitToken']) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  /*
  Manage protection from aborted form since code uses sessions.
  In other words the code which validates the values received from a
  submitted form needs to know that it is not executing after the user
  has come back after a previously abandoned instance of the script.
  */
  $submitToken = time();
  $_SESSION['PAI_submitToken'] = $submitToken;

  /*
  Construct page:
  */
  site_header('Process An Invoice');
  
  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];

  $page_str = <<<EOPAGESTR

<h2>Step Five</h2>

<p>So, you don't want to add any more maintenance items to this invoice.</p>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Move along then!</legend>
  <div>
    <input type="hidden" name="submitToken" value="$submitToken">
  </div>
  <div class="buttonarea">
    <input type="submit" name="cancel" value="Cancel"/>
    <input type="submit" name="submit" value="Continue"/>
  </div>
  </fieldset>
</form>

EOPAGESTR;
  echo $page_str;
  site_footer();
  $_SESSION['ProcAnInv_mode'] = 'stageSeven';
  return;
}


function instructToCreateMIDs() {
/*
The main purpose of this function is to do one of two things:
1. If the user submitted labels of new maintenance items then
   display them back to the user and ask him/her to add those
   to the system before continuing.
2. If the user didn't submit any new maintenance item labels
   then present a form with an informative message and change
   the mode to stageFour and make sure the variable
   $_POST['qtyMoreMID'] in the next stage will equal zero (0).
*/

  if (isset($_POST['cancel'])) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  /*
  Handle the situation where we have arrived here as a result of the user
  wandering back to the script after giving up in the middle of running it
  earlier when presented with a form.
  */
  if (isset($_POST['submitToken'])) {
    $submitToken = $_POST['submitToken'];
  } else {
    $submitToken = "";
  }
  if ($submitToken != $_SESSION['PAI_submitToken']) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  /*
  find out if we should be adding any new maintenance item labels.
  */

  // Start off with assumption.
  $shouldAddMore = TRUE;

  /*
  If the variable $_SESSION['PAI_qtyMoreMID'] is not set or has
  a value of zero then $shouldAddMore = FALSE.
  */
  if ( !isset($_SESSION['PAI_qtyMoreMID']) OR
              $_SESSION['PAI_qtyMoreMID'] == 0 ) {
    $shouldAddMore = FALSE;
  } else {
    /*
    If all the text fields submitted by the user for the gatherNewMID_labels()
    form have no string values $shouldAddMore = FALSE.
    */
    /*
    Loop through all those strings. If all have no string values then set
    $shouldAddMore = FALSE.
    */
    $allHaveNoString = TRUE;
    $numberOfStrings = $_SESSION['PAI_qtyMoreMID'];
    for ($i=0; $i<$numberOfStrings; $i+=1) {
      if (isset($_POST['mLabel'][$i])
          AND is_string($_POST['mLabel'][$i])
          AND STRLEN($_POST['mLabel'][$i]) >= 1) {
        $allHaveNoString = FALSE;
      }
    }
    if ($allHaveNoString) {
      $shouldAddMore = FALSE;
    }
  }

  if ($shouldAddMore) {
    /*
    Present a paragraph, table and form. Basically, we are showing the user
    the maintenance items he/she wanted to add to the invoice, telling them
    to open up a new browser tab to create those maintenance items then come
    back and presenting a form button so they can continue. We also need to
    set the mode to stageSix.
    */
    
    $bad_items = $_POST['mLabel'];
    
    /*
    I need to transform the array $bad_items into some well formed array
    which the makeTable() can use. The good array will be called $M_items.
    */
    unset($temp_1);
    reset($bad_items);
    $M_items = array();
    while ($array_cell = each($bad_items))
    {
      $temp_1 = $array_cell['value'];
      $M_items[][0] = $temp_1;
    }
    
    // Form the table string.
    $cap = 'Maintenance Item Labels';

    $nOfCols = 1;   // number of columns to be displayed

    /*
    Create an array (numerically indexed) containing the column
    header strings.
    */
    $tblHeader = array('maintenance item label');

    /*
    Create an HTML string for the table.
    */
    $infoTable = makeTable($cap, $nOfCols, $tblHeader, $M_items);

    /*
    Manage protection from aborted form since code uses sessions.
    In other words the code which validates the values received from a
    submitted form needs to know that it is not executing after the user
    has come back after a previously abandoned instance of the script.
    */
    $submitToken = time();
    $_SESSION['PAI_submitToken'] = $submitToken;

    /*
    Construct page:
    */
    site_header('Process An Invoice');
  
    // Superglobals don't work with heredoc
    $php_self = $_SERVER['PHP_SELF'];
    
    $page_str = <<<EOPAGESTR

<h2>Step Five</h2>

<p>Below you will find names of the additional maintenance items. Please,
open up a new browser tab and add them before continuing.</p>

$infoTable

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Only if you are ready!</legend>
  <div>
    <input type="hidden" name="submitToken" value="$submitToken">
  </div>
  <div class="buttonarea">
    <input type="submit" name="cancel" value="Cancel"/>
    <input type="submit" name="submit" value="Continue"/>
  </div>
  </fieldset>
</form>

EOPAGESTR;
    echo $page_str;
    site_footer();
    $_SESSION['ProcAnInv_mode'] = 'stageSix';
  } else {
    /*
    Present a paragraph and form. Also, set mode to stageFour and a hidden post
    variable named qtyMoreMID to zero. The paragraph will let the user know that
    even though he/she said they wanted to add more maintenance items to this
    invoice their further actions indicated otherwise. So, the script will
    assume they changed their mind.
    */

    /*
    Manage protection from aborted form since code uses sessions.
    In other words the code which validates the values received from a
    submitted form needs to know that it is not executing after the user
    has come back after a previously abandoned instance of the script.
    */
    $submitToken = time();
    $_SESSION['PAI_submitToken'] = $submitToken;

    /*
    Construct page:
    */
    site_header('Process An Invoice');
  
    // Superglobals don't work with heredoc
    $php_self = $_SERVER['PHP_SELF'];
    
    $page_str = <<<EOPAGESTR

<h2>Step Six</h2>

<p>Even though at one point you stated the number of additional maintenance
items you want to add to the invoice, your actions later on indicated that
you had changed your mind. So, that will be our assumption.</p>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Okay, move along!</legend>
  <div>
    <input type="hidden" name="qtyMoreMID" value="0">
  </div>
  <div>
    <input type="hidden" name="submitToken" value="$submitToken">
  </div>
  <div class="buttonarea">
    <input type="submit" name="cancel" value="Cancel"/>
    <input type="submit" name="submit" value="Continue"/>
  </div>
  </fieldset>
</form>

EOPAGESTR;
    echo $page_str;
    site_footer();
    $_SESSION['ProcAnInv_mode'] = 'stageFour';
  }
  return;
}



function addNewMIDs() {
/*
This function will set $_SESSION['ProcAnInv_mode'] = 'stageSeven'.
Also, it will present the user with any maintenance items that would have been
presented before had they been in the system at the time. They will be
presented as check boxes just as before.
*/

  if (isset($_POST['cancel'])) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  /*
  Handle the situation where we have arrived here as a result of the user
  wandering back to the script after giving up in the middle of running it
  earlier when presented with a form.
  */
  if (isset($_POST['submitToken'])) {
    $submitToken = $_POST['submitToken'];
  } else {
    $submitToken = "";
  }
  if ($submitToken != $_SESSION['PAI_submitToken']) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  /*
  At this point I need to have the vehicle id which was collected from the user
  earlier. I found it stored in $_SESSION['PAI_vclID'].
  */
  $vclID = $_SESSION['PAI_vclID'];

  /*
  At this point I need to have all ids of maintenance items which the user was
  originally presented with. That is why I'm going to go back and put code that
  will make this available. Done deal. Now they are in $_SESSION['originalChoices'].
  */
  $mntItemsOld = $_SESSION['originalChoices'];

  /*
  I need all the maintenance items for this vehicle which are available in the system
  at this point in time.
  */
  $tableName = "maintenItems";
  $fieldNames = array('id', 'label');
  $whereClause = "WHERE vehicleId = '$vclID'";
  $mntItem = getValuesForHTML_Table($fieldNames, $tableName, $whereClause);
  if ($mntItem == FALSE) {
    form_destroy();
    die('Error: no records found (881788987). -Programmer.');
  }

  $MIDsToPresent = myArrayDiff($mntItem, $mntItemsOld);
  
  /*
  Are there new maintenance items to present?
  */
  if (isset($MIDsToPresent) AND count($MIDsToPresent) > 0) {
    $presentItems = TRUE;
  } else {
    $presentItems = FALSE;
  }

  if ($presentItems) {
    $chkBoxes = chkBoxes($MIDsToPresent);

    /*
    Manage protection from aborted form since code uses sessions.
    In other words the code which validates the values received from a
    submitted form needs to know that it is not executing after the user
    has come back after a previously abandoned instance of the script.
    */
    $submitToken = time();
    $_SESSION['PAI_submitToken'] = $submitToken;

    /*
    Construct page:
    */
    site_header('Process An Invoice');
  
    // Superglobals don't work with heredoc
    $php_self = $_SERVER['PHP_SELF'];
  
    $page_str = <<<EOPAGESTR

<h2>Step Six</h2>

<p>Below you will find the maintenance items which were just added. Select
the ones you want to add to this invoice.</p>


<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Make selection:</legend>
$chkBoxes
  <div>
    <input type="hidden" name="submitToken" value="$submitToken">
  </div>
  <div class="buttonarea">
    <input type="submit" name="cancel" value="Cancel"/>
    <input type="submit" name="submit" value="Submit recently created MIDs"/>
  </div>
  </fieldset>
</form>

EOPAGESTR;
    echo $page_str;
    site_footer();

  } else {

    /*
    Manage protection from aborted form since code uses sessions.
    In other words the code which validates the values received from a
    submitted form needs to know that it is not executing after the user
    has come back after a previously abandoned instance of the script.
    */
    $submitToken = time();
    $_SESSION['PAI_submitToken'] = $submitToken;

    /*
    Construct page:
    */
    site_header('Process An Invoice');
  
    // Superglobals don't work with heredoc
    $php_self = $_SERVER['PHP_SELF'];
  
    $page_str = <<<EOPAGESTR

<h2>Step Six</h2>

<p>It appears that no new maintenance items were added since the first time you were
presented with the opportunity to make a selection.</p>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Make selection:</legend>
  <div>
    <input type="hidden" name="submitToken" value="$submitToken">
  </div>
  <div class="buttonarea">
    <input type="submit" name="cancel" value="Cancel"/>
    <input type="submit" name="submit" value="Continue"/>
  </div>
  </fieldset>
</form>

EOPAGESTR;
    echo $page_str;
    site_footer();

  }
  $_SESSION['ProcAnInv_mode'] = 'stageSeven';
  return;
}


function myArrayDiff($larger, $smaller) {
/*
This function returns an array which contains all the elements
of the first array $larger which are not matched by elements found
in the second array. The two arrays $larger and $smaller both have
elements which have two members only. If all the elements match then
the function returns an empty array.
*/
  $returnArr = array();
  foreach ($larger as $z) {
    if (isNotFoundIn($smaller, $z)) {
      $returnArr[] = $z;
    }
  }
  return $returnArr;
}


function isNotFoundIn($arr_in, $element_in) {
/*
Returns boolean. Answers the question: Is element in the array?
This function is a comanion to myArrayDiff($larger, $smaller).
*/
  $isNotFound = TRUE;
  foreach ($arr_in as $y) {
    if ($element_in[0] == $y[0] AND $element_in[1] == $y[1]) {
      $isNotFound = FALSE;
    }
  }
  return $isNotFound;
}



function presentMIDandPartInfoGathered() {
/*
First, this function will check to see if any new/additional maintenace
items were submitted so that they may be
added to the invoice. If yes then it will make them part of
the variable which holds the original collection.
Second, this function will present a form whose main intent is to have
the user recognize any new parts which he/she should assimilate into
the system. It will do this by presenting the maintenance items which
have been chosen by the user along with parts known to be associated with
them. The form will ask the user not to continue if he/she feels that
there are any missing maintenance items. This function will set mode to
stageEight.
*/

  if (isset($_POST['cancel'])) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  /*
  Handle the situation where we have arrived here as a result of the user
  wandering back to the script after giving up in the middle of running it
  earlier when presented with a form.
  */
  if (isset($_POST['submitToken'])) {
    $submitToken = $_POST['submitToken'];
  } else {
    $submitToken = "";
  }
  if ($submitToken != $_SESSION['PAI_submitToken']) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  /*
  Were any maintenance items added by the user as part of the form
  displayed by addNewMIDs()?
  */
  if (isset($_POST['mId'])) {
    $newestMItems = sanitize($_POST['mId']);
  }
  if (!isset($newestMItems) OR !is_array($newestMItems) OR empty($newestMItems)) {
    $noNewMIDs = TRUE;
  } else {
    $noNewMIDs = FALSE;
  }

  $oldOnes = $_SESSION['PAI_mIds'];

  if (!$noNewMIDs) {
    /*
    Add the new ones to the old ones.
    */
    reset($newestMItems);
    while ($array_cell = each($newestMItems))
    {
      $oldOnes[] = $array_cell['value'];
    }
    $_SESSION['PAI_mIds'] = $oldOnes;
  }
  
  /*
  The script must die if there are no maintenance items for this invoice.
  */
  if (empty($oldOnes)) {
    form_destroy();
    die("You must have at least one maintenance item. -Programmer.");
  }


  /*
  We will present a page and form. The page will list all the maintenance items
  the user has specified along with their associated parts. See the function
  header comment for more detail.
  */

  /*
  First I will gather the information into an array variable. Of course this will
  be a multi-dimensional array. The first dimension will be the maintenance items.
  The second dimension will be the properties of a maintenance item. Property [0]
  will be an array of field values (id, label) for the maintenance item. Property
  [1] will be an array of all the part numbers associated with the maintenance item.
  We will name the array $matrix.
  
  Example:
  $matrix[0]          is maintenance item [0].
  $matrix[0][0]       is the array of field values for maintenance item [0].
  $matrix[0][0][0]    is field [0] of maintenance item [0] (id).
  $matrix[0][0][1]    is field [1] of maintenance item [0] (label).
  $matrix[0][1]       is the array of part numbers associated with maintenance item [0].
  $matrix[0][1][0]    is part number [0] associated with maintenance item [0]
  $matrix[0][1][1]    is part number [1] associated with maintenance item [0]
  $matrix[0][1][2]    is part number [2] associated with maintenance item [0]
  */

  /*
  The first step in building $matrix will be to copy all the data from $oldOnes.
  Iterate through $oldOnes and feed $matrix.
  */
  $matrix = array();
  reset($oldOnes);
  while ($array_cell = each($oldOnes))
  {
    $matrix[][0] = $array_cell['value'];
  }

  /*
  Now $matrix needs a part numbers array for each maintenance item.
  Now $matrix needs a part numbers array for each $matrix[][1].
  So this is what needs to be done:
  For each first dimension $matrix element I need to assign to its second
  dimension element having index one (1) an array of all the part numbers
  associated with that maintenance item.
  */
  unset($i);
  reset($matrix);
  while ($array_cell = each($matrix))
  {
    $i = $array_cell['key'];
    $matrix[$i][1] = partsForThis($matrix[$i][0][0]);
  }

  /*
  Now form a string of HTML that constitutes the presentation of the maintenance
  items along with their associated parts. If the $matrix has no elements then
  the string will reflect this.
  */
  $dataStr = "";
  if (empty($matrix)) {
    $dataStr = "<p class=\"errmsg\">There is nothing to present since no" .
               " maintenance items were added to this invoice.</p>";
  } else {
    /*
    Loop builds $dataStr. Basically the string will be made up of a series of
    tables.
    */
    unset($maintId);
    unset($maLabel);
    unset($maParts);
    reset($matrix);
    while ($array_cell = each($matrix))
    {
      $maintId = $array_cell['value'][0][0];
      $maLabel = $array_cell['value'][0][1];
      $maParts = $array_cell['value'][1];
    
      /*
      At this point for a single MID we have all the information to build
      its table.
      */
      $temp_table = mAndPtable($maintId, $maLabel, $maParts);
      $dataStr .= $temp_table;
    }
  }

  /*
  Manage protection from aborted form since code uses sessions.
  In other words the code which validates the values received from a
  submitted form needs to know that it is not executing after the user
  has come back after a previously abandoned instance of the script.
  */
  $submitToken = time();
  $_SESSION['PAI_submitToken'] = $submitToken;

  /*
  Construct page:
  */
  site_header('Process An Invoice');
  
  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];

  $page_str = <<<EOPAGESTR

<h2>Step Seven</h2>

<p>Below you will find the maintenance items which you are adding to this
invoice along with their associated parts (in the system). If you learn of new
parts from your paper invoice please take note and add them for future use.
If you don't see all the maintenance items you've added then consider aborting
this script.</p>

$dataStr

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Verify, take note, continue!</legend>
  <div>
    <input type="hidden" name="submitToken" value="$submitToken">
  </div>
  <div class="buttonarea">
    <input type="submit" name="cancel" value="Cancel"/>
    <input type="submit" name="submit" value="Continue"/>
  </div>
  </fieldset>
</form>

EOPAGESTR;
  echo $page_str;
  site_footer();
  $_SESSION['ProcAnInv_mode'] = 'stageEight';
  return;
}



function partsForThis($MID_in) {
/*
This function takes a maintenance ID and returns an array containing all the
part numbers associated with it. If there are no part numbers associated
with it then the function will return an empty array. If the database query
fails then the script will abort. If no maintenance ID was passed to the
function it will abort.
*/
  $partsArr = array();
  if (!isset($MID_in) OR $MID_in == "") {
    form_destroy();
    die('Error 56565606. -Programmer.');
  }
  
  /*
  Query to find all part numbers associated with MID.
  */
  $query = "SELECT maintenVehicleParts.partNumber
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
    $partsArr[] = $row[0];
  }
  return $partsArr;
}



function mAndPtable($maintId_in, $maLabel_in, $maParts_in) {
/*
Generally speaking this is a function for showing a list of simple variable
values in a tabular form. In this particular case we have a maintenance item
and its associated parts. We put a string that specifies the maintenance item
in the table caption. And then put the part numbers in a table matrix which
is three columns wide and as many rows as needed.


This function returns an HTML table string for displaying a three column
table. The caption will be a MID followed by its label. The table data
fields will be filled with part numbers. If the MID is missing the function
will return an empty string. Part number cells which are empty will have the
string '&nbsp;'.
*/
  $retStr = "";
  if (!isset($maintId_in) OR $maintId_in == "") {
    return $retStr;
  }
  $cap = "$maintId_in $maLabel_in";
  $nOfCols = 3;
  $tblHeader = array('part number', 'part number', 'part number');

  /*
  I need to transform the array $maParts_in into the type of array
  which is the fourth argument of the function makeTable(). Currently,
  $maParts_in is an enumerated array of part numbers. I need to make it
  into an enumerated array of three element enumerated array. The three
  element enumerated array will hold the part numbers. To understand what
  it looks like is easier on both you and me to just look at the code.
  */
  $filler = '&nbsp;';
  $pNs = array();     // the modified array we are building
  $k = 0;             // an index into the three element array
  $j = 0;             // an index into $pNs array
  reset($maParts_in);
  while ($array_cell = each($maParts_in))
  {
    $pNs[$j][$k] = $array_cell['value'];
    $k += 1;
    if ($k==3) {
      $k = 0;
      $j += 1;
    }
  }
  if ($k==1) {
    $pNs[$j][$k] = $filler;
    $k += 1;
    $pNs[$j][$k] = $filler;
  } elseif ($k==2) {
    $pNs[$j][$k] = $filler;
  }
  $retStr = makeTable($cap, $nOfCols, $tblHeader, $pNs);
  return $retStr;
}



function getServiceRecordVals() {
/*
This function will set mode to stageNine. Also, it will present a form which
allows the user to enter all the field values for the service record for this
invoice.
*/

  if (isset($_POST['cancel'])) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  /*
  Handle the situation where we have arrived here as a result of the user
  wandering back to the script after giving up in the middle of running it
  earlier when presented with a form.
  */
  if (isset($_POST['submitToken'])) {
    $submitToken = $_POST['submitToken'];
  } else {
    $submitToken = "";
  }
  if ($submitToken != $_SESSION['PAI_submitToken']) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  /*
  In the form there is a select box so that the user can pick the shop. So,
  let's get develop the string for this box. It will be $shopSelector.
  */
  /*
  First we need to gather the information which will be presented in the
  select box. This includes the id and businessName of each shop. We
  will gather this information in a two-dimensional array. The first
  dimension, or top level if you want to call it that, corresponds to
  the shop. The second dimension corresponds to the two field values
  of the particular shop.
  This array will be called: shop.
  For example, the first shop is going to be shop[0].
  shop[0][0] will contain the id value of shop[0].
  shop[0][1] will contain the businessName value of shop[0]
  */
  /*
  To do this first I have to get these values from the database.
  I'll use the same functions I used before to acquire data to
  populate an HTML table.
  */
  $tableName = "shops";
  $fieldNames = array('id', 'businessName');
  $whereClause = '';
  $shop = getValuesForHTML_Table($fieldNames, $tableName, $whereClause);
  if ($shop == FALSE) {
    form_destroy();
    die('Error: no records found (79754557). -Programmer.');
  }
  $shopSelector = selectBoxShop($shop);

  /*
  Manage protection from aborted form since code uses sessions.
  In other words the code which validates the values received from a
  submitted form needs to know that it is not executing after the user
  has come back after a previously abandoned instance of the script.
  */
  $submitToken = time();
  $_SESSION['PAI_submitToken'] = $submitToken;

  site_header('Process An Invoice');
  $php_self = $_SERVER['PHP_SELF'];
  $userform_str = <<<EOUSERFORMSTR

<h2>Step Eight</h2>

<p>Enter field values for this service record. Do NOT specify the same shop and
date-time values for this invoice as any that have been used before.</p>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Service Record:</legend>
  <div>
    <span class="formcomment">* required</span>
  </div>
$shopSelector
  <div>
    <span class="formcomment">* required</span>
  </div>
  <div>
    <label for="timedate" class="fixedwidth">when? (ccyy-mm-dd hh:mm:ss)</label>
    <input type="text" name="timedate" id="timedate" value="" size="19" maxlength="19"/>
  </div>
  <div>
    <label for="cost" class="fixedwidth">cost (decimal)</label>
    <input type="text" name="cost" id="cost" value="" size="12" maxlength="12"/>
  </div>
  <div>
    <label for="mileage" class="fixedwidth">mileage (integer)</label>
    <input type="text" name="mileage" id="mileage" value="" size="8" maxlength="8"/>
  </div>
  <div>
    <input type="hidden" name="submitToken" value="$submitToken">
  </div>
  <div class="buttonarea">
    <input type="submit" name="cancel" value="Cancel"/>
    <input type="submit" name="submit" value="Submit service record data"/>
  </div>
  </fieldset>
</form>

EOUSERFORMSTR;
  echo $userform_str;
  site_footer();
  $_SESSION['ProcAnInv_mode'] = 'stageNine';
  return;
}



function selectBoxShop($shop_in) {
/*
This function takes a two dimensional array of shop information and
returns a selection box for a form. Each selection will return a shop
id value. The corresponding thing which the user will click on will be a
string containing both the id and the businessName. The structure and content
of $shop_in array is described in function getServiceRecordVals() above.

If $shop_in array is empty or unavailable the script will die.
*/
  if (!isset($shop_in) OR !is_array($shop_in) OR sizeof($shop_in) < 1) {
    form_destroy();
    die("Function failed to create select box because no array was passed.");
  }
  $selectB_str = "\n<div>\n  <label for=\"shopID\">Which shop?</label>\n" .
      "  <select name=\"shopID\" id=\"shopID\">\n";
  unset($temp_1);
  unset($temp_2);
  reset($shop_in);
  while ($array_cell = each($shop_in))
  {
    $temp_1 = $array_cell['value'][0];
    $temp_2 = $array_cell['value'][1];
    $selectB_str .=
    "    <option value=\"$temp_1\">$temp_1 $temp_2</option>\n";
  }
  $selectB_str .= "  </select>\n</div>\n\n";
  return $selectB_str;
}



function presentInfoGathered() {
/*
This function will set mode to stageTen. Also, it will present a form which
shows the user all the information collected so far about this invoice. Ask
him/her to verify its validity and proceed only if it is valid. Also, it
needs to transfer the post values from the previous form submission to session
variables. Also, it needs to prepare the values appropriately for display
(I'm talking about slashes and trimming here!). Also, this function has to set
the session variable $_SESSION['requiremet'] appropriately.
*/

  if (isset($_POST['cancel'])) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  /*
  Handle the situation where we have arrived here as a result of the user
  wandering back to the script after giving up in the middle of running it
  earlier when presented with a form.
  */
  if (isset($_POST['submitToken'])) {
    $submitToken = $_POST['submitToken'];
  } else {
    $submitToken = "";
  }
  if ($submitToken != $_SESSION['PAI_submitToken']) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  /*
  First things first! ... Transfer the post values from the previous form
  submission to session variables.
  */
  if ( isset($_POST['submit']) and $_POST['submit'] == "Submit service record data" ) {
    if (isset($_POST['shopID'])) {
      $_SESSION['PAI_shopID'] = $_POST['shopID'];
    } else {
      $_SESSION['PAI_shopID'] = "";
    }
    if (isset($_POST['timedate'])) {
      $_SESSION['PAI_timedate'] = $_POST['timedate'];
    } else {
      $_SESSION['PAI_timedate'] = "";
    }
    if (isset($_POST['cost'])) {
      $_SESSION['PAI_cost'] = $_POST['cost'];
    } else {
      $_SESSION['PAI_cost'] = "";
    }
    if (isset($_POST['mileage'])) {
      $_SESSION['PAI_mileage'] = $_POST['mileage'];
    } else {
      $_SESSION['PAI_mileage'] = "";
    }
  }

  // stripslashes
  if ( get_magic_quotes_gpc() ) {
   $_SESSION['PAI_timedate']  = stripslashes($_SESSION['PAI_timedate']);
   $_SESSION['PAI_cost']  = stripslashes($_SESSION['PAI_cost']);
   $_SESSION['PAI_mileage']  = stripslashes($_SESSION['PAI_mileage']);
  }

  // trim
  $_SESSION['PAI_timedate'] = trim($_SESSION['PAI_timedate']);
  $_SESSION['PAI_cost'] = trim($_SESSION['PAI_cost']);
  $_SESSION['PAI_mileage'] = trim($_SESSION['PAI_mileage']);

  // requiremet
  $_SESSION['requiremet'] = TRUE;
  if ( strlen($_SESSION['PAI_shopID']) < 1|| strlen($_SESSION['PAI_timedate']) < 1 ) {
    $_SESSION['requiremet'] = FALSE;
  }

  /*
  Here is a listing of all the other things to display:
  $_SESSION['PAI_mIds']
  $_SESSION['PAI_vclID']

  Now lets get to the business of displaying stuff.

  Let's look at the vehicle specifier. $_SESSION['PAI_vclID'] should have an id
  from a selection box submission. I think we should just try to match it up
  with a vehicle and retrieve the knownAs field. Then compose a string with
  the id followed by the knownAs. This will be what we will display. However,
  keep in mind that any information we present to the user should be in a table.
  */
  $temp = $_SESSION['PAI_vclID'];
  $query = "SELECT knownAs
            FROM vehicles
            WHERE id = '$temp'";
  $result = mysql_query($query);
  if (!$result OR mysql_num_rows($result) < 1) {
    form_destroy();
    die('Query failed. Err: 159871411. May want sleep(). -Programmer.');
  }
  $vKA = mysql_result($result, 0, 0);
  $temp_1 = "$temp $vKA";
  
  /*
  Now I need to come up with a table that shows $temp_1 very nicely. Lets compose
  an array that fieds the table function real quick and dirty.
  */
  $temp_2[0][0] = $temp_1;
  $cap = 'Vehicle';
  $nOfCols = 1;
  $tblHeader = array('vehicle info');
  $display_vehicle = makeTable($cap, $nOfCols, $tblHeader, $temp_2);

  /*
  Let's look at the maintenance items. $_SESSION['PAI_mIds'] has an array
  of maintenance item elements. Each one of these elements is an array which
  holds the id and label of the maintenance item. So what we will want to display
  is a table which lists all the maintenance items on this invoice.
  */
  $temp_3 = $_SESSION['PAI_mIds'];
  $cap = 'Maintenance Items';
  $nOfCols = 2;
  $tblHeader = array('id', 'label');
  $display_mItems = makeTable($cap, $nOfCols, $tblHeader, $temp_3);

  /*
  Let's look at the fields of the service record. How will we present them?
  In one table quick and dirty again. Don't forget we'll want the shop to
  display as a business name not an id.
  */
  $temp_BID = $_SESSION['PAI_shopID'];
  $query = "SELECT businessName
            FROM shops
            WHERE id = '$temp_BID'";
  $result = mysql_query($query);
  if (!$result OR mysql_num_rows($result) < 1) {
    form_destroy();
    die('Query failed. Err: 36833443. May want sleep(). -Programmer.');
  }
  $bName = mysql_result($result, 0, 0);

  $temp_4[0][0] = $bName;
  $temp_4[0][1] = $_SESSION['PAI_timedate'];
  $temp_4[0][2] = $_SESSION['PAI_cost'];
  $temp_4[0][3] = $_SESSION['PAI_mileage'];
  $cap = 'Service Record';
  $nOfCols = 4;
  $tblHeader = array('shop', 'date time', 'cost', 'mileage');
  $display_SR = makeTable($cap, $nOfCols, $tblHeader, $temp_4);

  /*
  Manage protection from aborted form since code uses sessions.
  In other words the code which validates the values received from a
  submitted form needs to know that it is not executing after the user
  has come back after a previously abandoned instance of the script.
  */
  $submitToken = time();
  $_SESSION['PAI_submitToken'] = $submitToken;

  site_header('Process An Invoice');
  $php_self = $_SERVER['PHP_SELF'];
  $userform_str = <<<EOUSERFORMSTR

<h2>Step Nine</h2>

<p>Review the data and proceed if it is valid and complete.</p>

$display_vehicle
$display_mItems
$display_SR

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>submit if you approve</legend>
  <div>
    <input type="hidden" name="submitToken" value="$submitToken">
  </div>
  <div class="buttonarea">
    <input type="submit" name="cancel" value="Cancel"/>
    <input type="submit" name="submit" value="Submit this info"/>
  </div>
  </fieldset>
</form>

EOUSERFORMSTR;
  echo $userform_str;
  site_footer();
  // don't forget to add the slashes if I stripped them
  if ( get_magic_quotes_gpc() ) {
   $_SESSION['PAI_timedate']  = addslashes($_SESSION['PAI_timedate']);
   $_SESSION['PAI_cost']  = addslashes($_SESSION['PAI_cost']);
   $_SESSION['PAI_mileage']  = addslashes($_SESSION['PAI_mileage']);
  }
  $_SESSION['ProcAnInv_mode'] = 'stageTen';
  return;
}



function validateSave() {
/*
Things this function will do:
1. Save all collected data if $_SESSION['requiremet'] == TRUE and
   no anomalies are found.
2. Return a boolean to let us know whether it saved the data.
3. It will set the value for $_SESSION['isanomaly']

Make sure that the value of $_SESSION['isanomaly'] gets set before return
gets called.

One thing that definetly constitutes an anomaly is if the service record
already exists. An anomaly of a different type is when data is found to
be non-valid.
*/

  if (isset($_POST['cancel'])) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  /*
  Handle the situation where we have arrived here as a result of the user
  wandering back to the script after giving up in the middle of running it
  earlier when presented with a form.
  */
  if (isset($_POST['submitToken'])) {
    $submitToken = $_POST['submitToken'];
  } else {
    $submitToken = "";
  }
  if ($submitToken != $_SESSION['PAI_submitToken']) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  /*
  First, find out if there are any anomalies.
  */
  $_SESSION['isanomaly'] = FALSE;
  /*
  Validating the vehicle id. We won't be storing this in the database. So, it doesn't
  matter that much. However, this value should have been verified as being non-empty,
  not too long, is-numeric and an id in the vehicles table at a much earlier stage
  of the user's session. The session should have been made to end at that point if
  the vehicle id was not valid.
  */
  /*
  Seeing if the service record field values have an anomaly. Yes we should do that
  here because we want the assignment of a value for $_SESSION['isanomaly'] to be
  exclusive to this function. The values we are looking for are found in:
  $_SESSION['PAI_shopID'], $_SESSION['PAI_timedate'], $_SESSION['PAI_cost'],
  $_SESSION['PAI_mileage'].
  If get_magic_quotes_gpc() is a true value then PHP ran the addslashes on them.
  (Generally speaking) we don't want these slashes to affect our validations. However,
  keep in mind that (generally speaking) at some point addslashes should be done before
  insertion into a database.
  
  All in all we are not generally speaking and I know that the fact that any slashes
  would have been added to our specific data means that it was anomolous to start with.
  So, I will validate the service record values without worrying about slashes.
  */
  if ( strlen($_SESSION['PAI_cost']) > 12 ) { $_SESSION['isanomaly'] = TRUE; }
  if ( strlen($_SESSION['PAI_shopID']) > 12 ) { $_SESSION['isanomaly'] = TRUE; }
  if ( strlen($_SESSION['PAI_timedate']) > 19 ) { $_SESSION['isanomaly'] = TRUE; }
  if ( strlen($_SESSION['PAI_mileage']) > 8 ) { $_SESSION['isanomaly'] = TRUE; }
  if ( strlen($_SESSION['PAI_cost']) > 0 and !is_numeric($_SESSION['PAI_cost'])) {
    $_SESSION['isanomaly'] = TRUE;
  }
  if ( strlen($_SESSION['PAI_shopID']) > 0 and !is_numeric($_SESSION['PAI_shopID'])) {
    $_SESSION['isanomaly'] = TRUE;
  }
  if ( strlen($_SESSION['PAI_mileage']) > 0 and !is_numeric($_SESSION['PAI_mileage'])) {
    $_SESSION['isanomaly'] = TRUE;
  }
  if ( strlen($_SESSION['PAI_timedate']) > 0 and !isDateTime($_SESSION['PAI_timedate'])) {
    $_SESSION['isanomaly'] = TRUE;
  }
  /*
  There is one more significant thing which should cause $_SESSION['isanomaly'] = TRUE:
  if the service record already exists.
  */
  if (shopIdTimeDateInTblV2()) { $_SESSION['isanomaly'] = TRUE; }
  /*
  Verify that a hacker didn't overload the maintenance item array. There should be
  no more than twenty five maintenance items on any particular invoice.
  */
  if (count($_SESSION['PAI_mIds']) > 25) {
    form_destroy();
    die('err: 433098788');
  }
  /*
  Seeing if the maintenance id values of $_SESSION['PAI_mIds'] have an anomaly.
  If the mid value is too long, not there, or not numeric then the script should die
  before the database query.
  */
  unset($temp_ID);
  reset($_SESSION['PAI_mIds']);
  while ($array_cell = each($_SESSION['PAI_mIds']))
  {
    $temp_ID = $array_cell['value'][0];
    if ( strlen($temp_ID) > 12 ) {
      form_destroy();
      die('err: 5228286');
    }
    if ( strlen($temp_ID) < 1 ) {
      form_destroy();
      die('err: 2427252');
    }
    if ( strlen($temp_ID) > 0 and !is_numeric($temp_ID)) {
      form_destroy();
      die('err: 3521242');
    }
    if (midNotInTable($temp_ID)) { $_SESSION['isanomaly'] = TRUE; }
  }
  /*
  Save all data that needs to be saved if there are no anomalies and all required
  field values have been supplied. For certain types of data you would want to take
  care of backslashing before saving. However this won't be necessary here.
  */
  if ($_SESSION['isanomaly'] == FALSE AND $_SESSION['requiremet'] == TRUE) {
    // insert the service record
    $cost = $_SESSION['PAI_cost'];
    $shopId = $_SESSION['PAI_shopID'];
    $timedate = $_SESSION['PAI_timedate'];
    $mileage = $_SESSION['PAI_mileage'];
    $query = "INSERT INTO serviceRecords (cost, shopId, timedate, mileage)
              VALUES ('$cost', '$shopId', '$timedate', '$mileage')";
    $result = mysql_query($query);
    if (!$result || mysql_affected_rows() < 1) {
      form_destroy();
      die('Error adding new record. 11188. -Programmer.');
    }
    // associate maintenance ids with the service record id. However, first I must
    // find out what the service record id is.
    $query = "SELECT id
              FROM serviceRecords
              WHERE shopId = '$shopId' AND timedate = '$timedate'";
    $result = mysql_query($query);
    if (!$result OR mysql_num_rows($result) < 1) {
      form_destroy();
      die('Query failed. Err: 5833399871. May want sleep function. -Programmer.');
    }
    $srId = mysql_result($result, 0, 0);
    $_SESSION['srId'] = $srId;
    unset($tempMID);
    reset($_SESSION['PAI_mIds']);
    while ($array_cell = each($_SESSION['PAI_mIds']))
    {
      $tempMID = $array_cell['value'][0];
      $query = "INSERT INTO servRecToMaintenItem (servRecId, maintenItemId)
                VALUES ('$srId', '$tempMID')";
      $result = mysql_query($query);
      if (!$result OR mysql_affected_rows() < 1) {
        form_destroy();
        die('Query failed. Err: 739146825. May want to add sleep function. -Programmer.');
      }
    }
    return TRUE;
  } else {
    return FALSE;
  }
}



function midNotInTable($mid_in) {
/*
Tells you if the maintenance id is in the table.
*/
  $query = "SELECT id
            FROM maintenItems
            WHERE id = '$mid_in'";
  $result = mysql_query($query);
  if (!$result) {
    form_destroy();
    die('Query failed. Err: 49633278. May want sleep function in loop. -Programmer.');
  }
  if ( mysql_num_rows($result) < 1) {
    return TRUE;
  } else {
    return FALSE;
  }

}



function shopIdTimeDateInTblV2() {
/*
Indicates whether the service record is in the table based on $_SESSION['PAI_shopID']
and $_SESSION['PAI_timedate'].
*/
  $shopId = $_SESSION['PAI_shopID'];
  $timedate = $_SESSION['PAI_timedate'];
  $query = "SELECT id
            FROM serviceRecords
            WHERE shopId = '$shopId' AND timedate = '$timedate'";
  $result = mysql_query($query);
  if (!$result) {
    form_destroy();
    die('Query failed. Err: 8869237. May want sleep function. -Programmer.');
  }
  if ( mysql_num_rows($result) < 1) {
    return FALSE;
  } else {
    return TRUE;
  }
}



function retrieveAndConfirm() {
/*
Generally speaking: this function presents a form/page to make the user feel re-assured
that everything went well and to give him/her some advice. Also, it takes care of the
session stuff to make sure the session is ended completely.

This function will retrieve all the data from the database and re-present it as proof
that all is well.

It will remind the user to update timeNext and mileNext for the maintenance items which
were taken care of by the shop. This function will destroy both the session id and the
session variables.
*/

  if (isset($_POST['cancel'])) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  /*
  Handle the situation where we have arrived here as a result of the user
  wandering back to the script after giving up in the middle of running it
  earlier when presented with a form.
  */
  if (isset($_POST['submitToken'])) {
    $submitToken = $_POST['submitToken'];
  } else {
    $submitToken = "";
  }
  if ($submitToken != $_SESSION['PAI_submitToken']) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  /*
  retrieve service record data
  */
  $srId = $_SESSION['srId'];
  $query = "SELECT cost, shopId, timedate, mileage
            FROM serviceRecords
            WHERE id = '$srId'";
  $result = mysql_query($query);
  if (!$result || mysql_num_rows($result) < 1) {
    form_destroy();
    die('Error 56801. -Programmer.');
  } else {
    $user_array = mysql_fetch_array($result);
    $cost = $user_array['cost'];
    $shopId = $user_array['shopId'];
    $timedate = $user_array['timedate'];
    $mileage = $user_array['mileage'];
    // We would perform a stripslashes here if it was needed.
  }

  /*
  Since I want to show the user the actual business name instead of the shop id,
  I'll need to dig it up.
  */
  $query = "SELECT businessName
            FROM shops
            WHERE id = '$shopId'";
  $result = mysql_query($query);
  if (!$result OR mysql_num_rows($result) < 1) {
    form_destroy();
    die('Query failed. Err: 278596710. May want sleep(). -Programmer.');
  }
  $bnsName = mysql_result($result, 0, 0);
  /*
  retrieve data about the associated maintnance items
  */
  $miLabelArr = midsForThis($srId);
  /*
  Okay, now we build the display tables.
  */
  /*
  First we build a table string for the service record information.
  */
  $srDisplayArr[0][0] = $bnsName;
  $srDisplayArr[0][1] = $timedate;
  $srDisplayArr[0][2] = $cost;
  $srDisplayArr[0][3] = $mileage;

  $cap = 'Service Record';
  $nOfCols = 4;
  $tblHeader = array('shop', 'date time', 'cost', 'mileage');
  $display_SR = makeTable($cap, $nOfCols, $tblHeader, $srDisplayArr);
  /*
  Second we build a table string for the maintenance item labels.
  */
  $cap = 'Maintenance Items';
  $nOfCols = 1;
  $tblHeader = array('label');
  $display_mItems = makeTable($cap, $nOfCols, $tblHeader, $miLabelArr);

  site_header('Process An Invoice');
  $userform_str = <<<EOUSERFORMSTR

<h2>Step Ten</h2>

<p>If you see all the service record data field values and maintenance item
values which correlate to what you specifed before then this means the data
has been saved in the database. REMINDER: <em class="highlight">update time next, mile next
and time desired</em>
for all the maintenance items which were taken care of by this service.</p>

$display_SR
$display_mItems

EOUSERFORMSTR;
  echo $userform_str;
  site_footer();
  form_destroy();
  return;
}



function midsForThis($srId_in) {
/*
This function takes a service record ID and returns an array containing all the
maintenance item labels associated with it.

BE AWARE: the array returned must be two dimensional in order for it to be compatible
with the function that displays tables. That is why it's neccessary to have the extra
[0] you see in the while statement below.

If there are no maintenance item labels
associated with it then the function will return an empty array. If the database query
fails then the script will abort. If no service record ID was passed to the
function it will abort.
*/
  $miArr = array();
  if (!isset($srId_in) OR $srId_in == "") {
    form_destroy();
    die('Error 47821346879. -Programmer.');
  }
  /*
  Query to find all maintenance item labels associated with service record ID.
  */
  $query = "SELECT maintenItems.label
            FROM servRecToMaintenItem INNER JOIN maintenItems
            ON servRecToMaintenItem.maintenItemId = maintenItems.id
            WHERE servRecToMaintenItem.servRecId = '$srId_in'";
  $result = mysql_query($query);
  if (!$result) {
    form_destroy();
    die('Query failed. Err: 367790875. May want sleep(). -Programmer.');
  }
  if ( mysql_num_rows($result) < 1) {
    return $miArr;
  }
  while ($row = mysql_fetch_row($result)) {
    $miArr[][0] = $row[0];
  }
  return $miArr;
}



function presentErrorStatus() {
/*
This function brings on the presentation which follows a call to validateSave()
which did not result in data being saved . At this point in time the variables
$_SESSION['isanomaly'] and $_SESSION['requiremet'] have been assigned values
and are ready to be used here to help us enlighten the user.

Remind the user that an anomaly can mean that the user's specification of the service
record indicates that the record already exists or it can mean that data entered is
non-conforming. This function will destroy both the session id and the
session variables.
*/

  if (isset($_POST['cancel'])) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  /*
  Handle the situation where we have arrived here as a result of the user
  wandering back to the script after giving up in the middle of running it
  earlier when presented with a form.
  */
  if (isset($_POST['submitToken'])) {
    $submitToken = $_POST['submitToken'];
  } else {
    $submitToken = "";
  }
  if ($submitToken != $_SESSION['PAI_submitToken']) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  $messIsa = '<p class="errmsg">An anomaly occured. Nothing got saved.</p>';
  $messReq = '<p class="errmsg">Required fields were not filled. Nothing got saved</p>';

  if ($_SESSION['isanomaly'] == TRUE and $_SESSION['requiremet'] == FALSE) {
    $messageStr = $messIsa . "\n" . $messReq;
  } elseif ($_SESSION['isanomaly'] == TRUE) {
    $messageStr = $messIsa;
  } elseif ($_SESSION['requiremet'] == FALSE) {
    $messageStr = $messReq;
  } else {
    $messageStr = '<p class="errmsg">Unknown error (5150). -Programmer.</p>';
  }

  site_header('Process An Invoice');
  $userform_str = <<<EOUSERFORMSTR

<h2>Step Ten</h2>

$messageStr

<p>You have reached this page because no data has been stored and your session
is terminated. An error message should appear above. If the error message says an
anomaly has occured this means either the service record already existed or the
data you entered didn't conform. Do not use the browser's back button to try to
fix things.</p>

EOUSERFORMSTR;
  echo $userform_str;
  site_footer();
  form_destroy();
  return;
}

?>