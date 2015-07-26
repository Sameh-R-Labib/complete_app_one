<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);


/*
This file is for inclusion in assocPIDwithMID.php. This script along
with the main script make use of sessions and a mode variable. The mode
variable will be passed around in session space as $_SESSION['APWM_mode'].
*/


require_once('table_funcs.php'); // remove line if no table function used




function form_destroy() {
/*
Reset all $_SESSION variables for this form to their default values.
*/
  $_SESSION['APWM_mode'] = 'stageOne';
  $_SESSION['APWM_PrevMode'] = "";
  $_SESSION['APWM_vclID'] = "";
  $_SESSION['APWM_MID'] = "";
  $_SESSION['APWM_PID'] = NULL;
  $_SESSION['APWM_submitToken'] = "";

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
  has come back after a previously abandoned instance of the script.
  */
  $submitToken = time();
  $_SESSION['APWM_submitToken'] = $submitToken;

  /*
  Construct page:
  */
  site_header('Assoc. Part w/ MaintenItem');
  
  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];

  $page_str = <<<EOPAGESTR

<h2>Step One</h2>

<p>This script allows you to correlate a part with a maintenance item.
Specify the vehicle for which this maintenance item relates.</p>

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



function selectMID() {
/*
This function exports the vehicle id submitted by the form of stageOne into
session space. Kill the script if vehicle id was a spoof.

Also, this function presents a selection box of the maintenance items for the
vehicle.
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
  if ($submitToken != $_SESSION['APWM_submitToken']) {
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
    $_SESSION['APWM_vclID'] = $_POST['theID'];
    $vclID = $_SESSION['APWM_vclID'];
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
  has come back after a previously abandoned instance of the script.
  */
  $submitToken = time();
  $_SESSION['APWM_submitToken'] = $submitToken;

  /*
  Construct page:
  */
  site_header('Assoc. Part w/ MaintenItem');
  
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



function inputPNtoFind() {
/*
If code execution arrived at this function via stageTwo then this function exports
the maintenance item id submitted by the form of stageTwo into session space. Kill
the script if maintenance item id was a spoof.

Present a text box for the user to fill up with a part number string. Instruct the
user on what constitutes the part number.
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
  if ($submitToken != $_SESSION['APWM_submitToken']) {
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
  if ( isset($_POST['theID']) AND $_SESSION['APWM_PrevMode'] == 'stageTwo') {
    $_SESSION['APWM_MID'] = $_POST['theID'];
    $mID = $_SESSION['APWM_MID'];
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

  /*
  Manage protection from aborted form since code uses sessions.
  In other words the code which validates the values received from a
  submitted form needs to know that it is not executing after the user
  has come back after a previously abandoned instance of the script.
  */
  $submitToken = time();
  $_SESSION['APWM_submitToken'] = $submitToken;

  /*
  Construct page:
  */
  site_header('Assoc. Part w/ MaintenItem');
  
  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];

  $page_str = <<<EOPAGESTR

<h2>Step Three</h2>

<p>Supply a part number string which you believe matches one in the system.
It can have spaces and the extra portion which gets prepended.</p>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Specify</legend>
  <div>
    <label for="partNumber" class="fixedwidth">part number</label>
    <input type="text" name="partNumber" id="partNumber" value="" size="30" maxlength="30"/>
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

EOPAGESTR;
  echo $page_str;
  site_footer();
  return;
}



function searchForStatedPN() {
/*
Capture the part number string which the user supplied in the form
generated by stageThree. If entry is a spoof then kill the script.
Return value of this function will be a boolean indicating whether
the the part number string was found in the parts table. Also, this
function needs to put the part number ID into session space if the
part number was found.
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
  if ($submitToken != $_SESSION['APWM_submitToken']) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  if (isset($_POST['partNumber'])) {
    $PN = $_POST['partNumber'];
    if (strlen($PN) > 32) {
      form_destroy();
      die('err: 99899893334. -Programmer.');
    }
  } else {
    form_destroy();
    die('err: 4362343334. -Programmer.');
  }

  /*
  We have to make sure $PN is ready for an SQL query.
  */
  if (!get_magic_quotes_gpc()) {
    $PN = addslashes($PN);
  }

  $query = "SELECT id
            FROM maintenVehicleParts
            WHERE partNumber = '$PN'";
  $result = mysql_query($query);

  if (!$result) {
    form_destroy();
    die('query failed #07079890');
  }

  if ( mysql_num_rows($result) < 1) {
    return FALSE;
  }

  $_SESSION['APWM_PID'] = mysql_result($result, 0, 0);
  return TRUE;
}



function goodJob() {
/*
The reason this function was created was to provide a Submit button.
However, we will also use it to inform the user that the part number
he supplied was good.
*/

  if (isset($_POST['cancel'])) {
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
  $_SESSION['APWM_submitToken'] = $submitToken;

  /*
  Construct page:
  */
  site_header('Assoc. Part w/ MaintenItem');
  
  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];

  $page_str = <<<EOPAGESTR

<h2>Step Four</h2>

<p>The part number you supplied was good. You may proceed!</p>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Click Away!</legend>
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



function tryAgainOrAddNew() {
/*
This function follows the case where the part number was not found.
So, here we ask the user how he wants to proceed:
    A. Would he like to try typing in the part number again?
    B. Would he like to add a new part before resuming?
The form will present a select box.
*/

  if (isset($_POST['cancel'])) {
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
  $_SESSION['APWM_submitToken'] = $submitToken;

  /*
  Construct page:
  */
  site_header('Assoc. Part w/ MaintenItem');
  
  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];

  $page_str = <<<EOPAGESTR

<h2>Step Four</h2>

<p>The part number you supplied was NOT good. Please, indicate how
you would like to proceed.</p>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Specify</legend>
  <div>
    <label for="how">how to proceed:</label>
    <select name="how" id="how">
      <option value ="try">try typing in the part number again</option>
      <option value ="add">add a new part before resuming</option>
    </select>
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

EOPAGESTR;
  echo $page_str;
  site_footer();
  return;
}



function processTheAnswer() {
/*
This function reads the answer to the question posed in stageFour by
the function tryAgainOrAddNew(). Based on the answer processTheAnswer()
will return the appropriate mode string value.
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
  if ($submitToken != $_SESSION['APWM_submitToken']) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  if (isset($_POST['how']))
    if ($_POST['how'] == "try") {
      return "stageThree";
    } elseif ($_POST['how'] == "add") {
      return "stageSix";
    } else {
      form_destroy();
      die('err: 0795175390');
  } else {
    form_destroy();
    die('err: 2397175390');
  }
}



function instructToAddPart() {
/*
Present a form telling the user to open up a new tab in the browser,
add the new part and then come back to this script's browser tab.
*/

  if (isset($_POST['cancel'])) {
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
  $_SESSION['APWM_submitToken'] = $submitToken;

  /*
  Construct page:
  */
  site_header('Assoc. Part w/ MaintenItem');
  $php_self = $_SERVER['PHP_SELF'];
  $page_str = <<<EOPAGESTR

<h2>Step Five</h2>

<p>Open up a new browser tab, add the part and then come back.</p>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Click when you are ready:</legend>
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



function validateSave() {
/*
Verify that we have a part id and maintenance item id then
add their correlation to the system.
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
  if ($submitToken != $_SESSION['APWM_submitToken']) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  if (isset($_SESSION['APWM_PID']) AND isset($_SESSION['APWM_MID'])) {
    $partId = $_SESSION['APWM_PID'];
    $itemId = $_SESSION['APWM_MID'];
    $query = "INSERT INTO itemToPart (itemId, partId)
              VALUES ('$itemId', '$partId')";
    $result = mysql_query($query);
    if (!$result OR mysql_affected_rows() < 1) {
      form_destroy();
      die('err: 2345854525356');
    } else {
      return;
    }
  } else {
    form_destroy();
    die('err: 234215365390');
  }
}



function displayConfirmation() {
/*
Reveal to the user the PID and MID values which were saved and
cancel the session.
*/

  /*
  Construct page:
  */
  site_header('Assoc. Part w/ MaintenItem');
  $PID = $_SESSION['APWM_PID'];
  $MID = $_SESSION['APWM_MID'];
  $page_str = <<<EOPAGESTR

<h2>Step Six</h2>

<p>Congratulations! The part id $PID and the maintenance item id
$MID have been successfully associated with each other.</p>

EOPAGESTR;
  echo $page_str;
  site_footer();
  form_destroy();
  return;
}

?>