<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/* This file is for inclusion in assocPartMaintItem.php. */



function presentItemIdPartIdForm() {
/*
Return: nothing
Action: display page including message
Effect: nothing
*/

  global $status_message, $submitCounter;

  /*
  Construct form:
  */

  site_header('Associate Part with Maintenance Item');

  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];

  if ( !isset($status_message) || $status_message == "" ) {
    $message_str = "";
  } else {
    $message_str = "<p class=\"errmsg\">$status_message</p>";
  }

  $userform_str = <<<EOUSERFORMSTR

$message_str

<p>This page allows you to associate a service part with a maintenance item.</p>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Form: <span class="formcomment">*All fields required</span></legend>
  <div>
    <label for="itemId" class="fixedwidth">item ID (number)</label>
    <input type="text" name="itemId" id="itemId" value="" size="12" maxlength="12"/>
  </div>
  <div>
    <label for="partId" class="fixedwidth">part ID (number)</label>
    <input type="text" name="partId" id="partId" value="" size="12" maxlength="12"/>
  </div>
  <div>
    <input type="hidden" name="submitCounter" value="$submitCounter">
  </div>
  <div class="buttonarea">    
    <input type="submit" name="submit" value="Associate Part with Maintenance Item"/>
  </div>
  </fieldset>
</form>

EOUSERFORMSTR;
  echo $userform_str;
  site_footer();
  return;
}



function validPartIdMaintId() {
/*
Return: boolean
Action: are the two values valid strings?
Effect: status message gets set if FALSE, $partId and $itemId get assigned
*/

  global $itemId, $partId, $status_message;

  // $isAnomaly - whether any form string is too long
  $isAnomaly = false;

  // Have required fields been filled out?
  $requireMet = true;

  if ( isset($_POST['submit']) AND $_POST['submit'] == "Associate Part with Maintenance Item" ) {

    // Initialize $itemId and $partId.
    if ( isset($_POST['itemId']) ) {
      $itemId = $_POST['itemId'];
    } else {
      $itemId = "";
    }
    if ( isset($_POST['partId']) ) {
      $partId = $_POST['partId'];
    } else {
      $partId = "";
    }

    // If magic quotes is on I'll stripslashes.
    if ( get_magic_quotes_gpc() ) {
      $itemId = stripslashes($itemId);
      $partId = stripslashes($partId);
    }

    // Trim white space.
    $itemId = trim($itemId);
    $partId = trim($partId);

    // Test the values and return truth.
    if ( strlen($itemId) > 12 || strlen($partId) > 12 ) { $isAnomaly = true; }
    if ( strlen($itemId) < 1 || strlen($partId) < 1 ) { $requireMet = false; }

    // Verify numeric data is numeric.
    if ( strlen($itemId) > 0 and !is_numeric($itemId)) { $isAnomaly = true; }
    if ( strlen($partId) > 0 and !is_numeric($partId)) { $isAnomaly = true; }

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

  global $itemId, $partId, $status_message;

  $query = "SELECT itemId
            FROM itemToPart
            WHERE itemId = '$itemId' AND partId = '$partId'";
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

  global $itemId, $partId, $status_message;

  $query = "INSERT INTO itemToPart (itemId, partId)
            VALUES ('$itemId', '$partId')";
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