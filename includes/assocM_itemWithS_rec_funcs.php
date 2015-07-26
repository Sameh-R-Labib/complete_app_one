<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/* This file is for inclusion in assocPartMaintItem.php. */



function presentServIdMaintIdForm() {
/*
Return: nothing
Action: display page including message
Effect: nothing
*/

  global $status_message, $submitCounter;

  /*
  Construct form:
  */

  site_header('Associate Maintenance Item with Service Record');

  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];

  if ( !isset($status_message) || $status_message == "" ) {
    $message_str = "";
  } else {
    $message_str = "<p class=\"errmsg\">$status_message</p>";
  }

  $userform_str = <<<EOUSERFORMSTR

$message_str

<p>This page allows you to associate a maintenance item with a service record.</p>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Form: <span class="formcomment">*All fields required</span></legend>
  <div>
    <label for="servRecId" class="fixedwidth">service ID (int)</label>
    <input type="text" name="servRecId" id="servRecId" value="" size="10" maxlength="10"/>
  </div>
  <div>
    <label for="maintenItemId" class="fixedwidth">maintenance ID</label>
    <input type="text" name="maintenItemId" id="maintenItemId" value="" size="10" maxlength="10"/>
  </div>
  <div>
    <input type="hidden" name="submitCounter" value="$submitCounter">
  </div>
  <div class="buttonarea">    
    <input type="submit" name="submit" value="Associate Maintenance Item with Service Record"/>
  </div>
  </fieldset>
</form>

EOUSERFORMSTR;
  echo $userform_str;
  site_footer();
  return;
}



function validServIdMaintId() {
/*
Return: boolean
Action: are the two values valid strings?
Effect: status message gets set if FALSE, $servRecId and $maintenItemId get assigned
*/

  global $servRecId, $maintenItemId, $status_message;

  // $isAnomaly - whether any form string is too long
  $isAnomaly = false;

  // Have required fields been filled out?
  $requireMet = true;

  if ( isset($_POST['submit']) AND $_POST['submit'] ==
           "Associate Maintenance Item with Service Record" ) {

    // Initialize $itemId and $partId.
    if ( isset($_POST['servRecId']) ) {
      $servRecId = $_POST['servRecId'];
    } else {
      $servRecId = "";
    }
    if ( isset($_POST['maintenItemId']) ) {
      $maintenItemId = $_POST['maintenItemId'];
    } else {
      $maintenItemId = "";
    }

    // If magic quotes is on I'll stripslashes.
    if ( get_magic_quotes_gpc() ) {
      $servRecId = stripslashes($servRecId);
      $maintenItemId = stripslashes($maintenItemId);
    }

    // Trim white space.
    $servRecId = trim($servRecId);
    $maintenItemId = trim($maintenItemId);

    // Test the values and return truth.
    if ( strlen($servRecId) > 12 || strlen($maintenItemId) > 12 ) { $isAnomaly = true; }
    if ( strlen($servRecId) < 1 || strlen($maintenItemId) < 1 ) { $requireMet = false; }

    // Verify numeric data is numeric.
    if ( strlen($servRecId) > 0 and !is_numeric($servRecId)) { $isAnomaly = true; }
    if ( strlen($maintenItemId) > 0 and !is_numeric($maintenItemId)) { $isAnomaly = true; }

    // addslashes - no need

  } else {
    die('Script aborted #12580. -Programmer.');
  }

  if ( $requireMet == false ) {
    $status_message =  'Error -- you did not complete a required field.';
    if ( $isAnomaly == true ) {
    $status_message .= ' Also, one or more fields was ' .
        'supplied with non-conforming data. No data was saved.';
    return false;
    }
  } elseif ( $isAnomaly == true ) {
    $status_message = 'One or more fields was ' .
        'supplied with non-conforming data. No data was saved.';
    return false;
  }

  return true;
}



function isAlreadyInDatabase() {
/*
Return: boolean
Action: is the pair of values in the table
Effect: status msg set if func ret TRUE
*/

  global $servRecId, $maintenItemId, $status_message;

  $query = "SELECT servRecId
            FROM servRecToMaintenItem
            WHERE servRecId = '$servRecId' AND maintenItemId = '$maintenItemId'";
  $result = mysql_query($query);

  if (!$result) {
    die('Query failed. Err: 880037. -Programmer.');
  }

  if ( mysql_num_rows($result) < 1) {
    return false;
  } else {
    $status_message =  'The data you supplied had previously been stored.';
    return true;
  }
}



function insertIntoDatabase() {
/*
Return: nothing
Action: insert values
Effect: status message gets set confirming insertion occured, else die
*/

  global $servRecId, $maintenItemId, $status_message;

  $query = "INSERT INTO servRecToMaintenItem (servRecId, maintenItemId)
            VALUES ('$servRecId', '$maintenItemId')";
  $result = mysql_query($query);
  if (!$result) {
    $status_message =  'Error -- No data was stored.';
    return;
  } else {
    $status_message =  'The data was successfully stored.';
    return;
  }
}

?>