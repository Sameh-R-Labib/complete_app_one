<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/* This file is for inclusion in addPart.php. */


require_once('generalFormFuncs.php');



function presentLabelPartNumberForm() {
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
   4. Present input fields for label and partNumber
*/

  global $status_message, $submitCounter;

  /*
  Construct form:
  */

  site_header('Add Part');

  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];

  if ( !isset($status_message) || $status_message == "" ) {
    $message_str = "";
  } else {
    $message_str = "<p class=\"errmsg\">$status_message</p>";
  }

  $userform_str = <<<EOUSERFORMSTR

$message_str

<p>This page allows you to insert or update a part record in the database table
for parts. This table is used by other scripts (including the ones for maintenance.)
Please, if you are updating a part record, make sure the label and part number you
supply on this form are the same as the ones in the record. Otherwise you will be
inserting or updating a record other than the intended one.</p>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Specify a Part: <span class="formcomment">*All fields required</span></legend>
  <div>
    <label for="label" class="fixedwidth">label</label>
    <input type="text" name="label" id="label" value="" size="50" maxlength="50"/>
  </div>
  <div>
    <label for="partNumber" class="fixedwidth">part number</label>
    <input type="text" name="partNumber" id="partNumber" value="" size="30" maxlength="30"/>
  </div>
  <div>
    <input type="hidden" name="mode" value="stageTwo">
    <input type="hidden" name="submitCounter" value="$submitCounter">
  </div>
  <div class="buttonarea">    
    <input type="submit" name="submit" value="Retrieve part data"/>
  </div>
  </fieldset>
</form>

EOUSERFORMSTR;
  echo $userform_str;
  site_footer();
  return;
}



function validLabelPartNumber() {
/* Verifies that the user supplied both a label and
part number. Also, verifies that the supplied string values
are of appropriate length and content. Also, this function
will assign appropriately the global values for $label and
$partNumber.

This function needs to have the $label, $partNumber values backslashed
appropriately for possible insertion into database before return to
main program.
*/

  global $label, $partNumber;

  if ( isset($_POST['submit']) AND (($_POST['submit'] == "Edit part data")
      OR ($_POST['submit'] == "Retrieve part data")) ) {

    // Initialize $label and $partNumber.
    if ( isset($_POST['label']) ) {
      $label = $_POST['label'];
    } else {
      $label = "";
    }
    if ( isset($_POST['partNumber']) ) {
      $partNumber = $_POST['partNumber'];
    } else {
      $partNumber = "";
    }

    // If magic quotes is on I'll stripslashes.
    if ( get_magic_quotes_gpc() ) {
      $label = stripslashes($label);
      $partNumber = stripslashes($partNumber);
    }

    // Trim white space.
    $label = trim($label);
    $partNumber = trim($partNumber);

    // Test the values and return truth.
    if ( strlen($label) > 50 || strlen($partNumber) > 30 ) { return false; }
    if ( strlen($label) < 1 || strlen($partNumber) < 1 ) { return false; }

    // addslashes
    $label = addslashes($label);
    $partNumber = addslashes($partNumber);

  } else {
    die('Script aborted #12580. -Programmer.');
  }

  return true;
}



function labelPartNumberInTable() {
/* Indicates whether the part whose label and partNumber are
available in the global $label and $partNumber are found in the table. */


  global $label, $partNumber;


  $query = "SELECT id
            FROM maintenVehicleParts
            WHERE label = '$label' AND partNumber = '$partNumber'";
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
Assumption: A record containing the label and partNumber for this
vehicle already exists. Assumption: $label and $partNumber are
global variables which contain valid values. */

  // $isAnomaly - whether any form string is too long
  $isAnomaly = false;

  // Have required fields been filled out?
  $requireMet = true;

  global $status_message;

  // All form variables are global.
  global $label, $manufacturer, $retailer, $partNumber, $price, $quantityHave;

  if ( isset($_POST['submit']) and $_POST['submit'] == "Edit part data" ) {
    // Transfer POSTS to regular vars
    if ( isset($_POST['manufacturer']) ) {
      $manufacturer = $_POST['manufacturer'];
    } else {
      $manufacturer = "";
    }
    if ( isset($_POST['retailer']) ) {
      $retailer = $_POST['retailer'];
    } else {
      $retailer = "";
    }
    if ( isset($_POST['price']) ) {
      $price = $_POST['price'];
    } else {
      $price = "";
    }
    if ( isset($_POST['quantityHave']) ) {
      $quantityHave = $_POST['quantityHave'];
    } else {
      $quantityHave = "";
    }

    if ( !get_magic_quotes_gpc() ) {
      $label = addslashes($label);
      $manufacturer = addslashes($manufacturer);
      $retailer = addslashes($retailer);
      $partNumber = addslashes($partNumber);
      $price = addslashes($price);
      $quantityHave = addslashes($quantityHave);
    }

    // Trim white space.
    $label = trim($label);
    $manufacturer = trim($manufacturer);
    $retailer = trim($retailer);
    $partNumber = trim($partNumber);
    $price = trim($price);
    $quantityHave = trim($quantityHave);

    // Verify string length and deal with anomalies

    // The string length should not be longer than the
    // MAXLENGTH of the FORM field unless slashes were added.
    // Therefore make allowance for this for strings which may
    // have slashes added.
    if ( strlen($label) > 50 ) { $isAnomaly = true; }
    if ( strlen($manufacturer) > 34 ) { $isAnomaly = true; }
    if ( strlen($retailer) > 34 ) { $isAnomaly = true; }
    if ( strlen($partNumber) > 34 ) { $isAnomaly = true; }
    if ( strlen($price) > 16 ) { $isAnomaly = true; }
    if ( strlen($quantityHave) > 10 ) { $isAnomaly = true; }

    // Verify numeric data is numeric.
    if ( strlen($price) > 0 and !is_numeric($price)) {$isAnomaly = true; }
    if ( strlen($quantityHave) > 0 and !is_numeric($quantityHave)) {$isAnomaly = true; }

    // Find out if required fields were supplied
    if ( strlen($label) < 1 || strlen($partNumber) < 1 ) {
      $requireMet = false;
    }

    // If required fields were inputed then update database.
    if ( $requireMet == true and $isAnomaly == false ) {

      // Send data to db
      $query = "UPDATE maintenVehicleParts
                SET manufacturer = '$manufacturer',
                    retailer = '$retailer',
                    price = '$price',
                    quantityHave = '$quantityHave'
                WHERE label = '$label' AND partNumber = '$partNumber'";
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
/* Puts the data from an existent part record into the
variables which will populate an update form. It is assumed
that $label and $partNumber correspond to a part record which
exists already. Also, this function makes sure that all form
variables get initialized with values and defined as global.
Otherwise, it aborts.
*/

  // All form variables are global.
  global $label, $manufacturer, $retailer, $partNumber, $price, $quantityHave;

  $query = "SELECT manufacturer, retailer, price, quantityHave
            FROM maintenVehicleParts
            WHERE label = '$label' AND partNumber = '$partNumber'";
  $result = mysql_query($query);
  if (!$result || mysql_num_rows($result) < 1) {
    die('Error 56801. -Programmer.');
  } else {
    $user_array = mysql_fetch_array($result);
    
    $manufacturer = $user_array['manufacturer'];
    $retailer = $user_array['retailer'];
    $price = $user_array['price'];
    $quantityHave = $user_array['quantityHave'];
    
    // Text and Textarea fields have had backslashes
    // added to escape single quotes ('), double
    // quotes ("), backslashes (\) and NULL before
    // insertion into the database. Therefore, we must
    // undo this before displaying these strings.
    $manufacturer = stripslashes($manufacturer);
    $retailer = stripslashes($retailer);
    
    // Even though $label and $partNumber were
    // not retrieved
    $label = stripslashes($label);
    $partNumber = stripslashes($partNumber);

  }

  return;
}



function createNewRecord() {
/* Inserts a new part record populating it with the
valid label and partNumber strings supplied by the user. */

  global $label, $partNumber;

  $query = "INSERT INTO maintenVehicleParts (label, partNumber)
            VALUES ('$label', '$partNumber')";
  $result = mysql_query($query);
  if (!$result || mysql_affected_rows() < 1) {
    die('Error adding new record. 00099. -Programmer.');
  } else {
    return;
  }
}



function presentUpdateForm() {
/* Presents the form for supplying (or changing) field
values for an existing part record. Also, presents
the $status_message. The $mode, $label and $partNumber
global variables must contain valid values (which will
further more be passed along when the form is submitted.)
The $label and $partNumber values can't be changed here (but
are displayed.)
*/

  global $mode, $status_message, $submitCounter;

  // All form variables are global.
  global $label, $manufacturer, $retailer, $partNumber, $price, $quantityHave;

  // mode must be valid
  $modeIsNotValid = true;
  if ( $mode == 'stageOne') { $modeIsNotValid = false; }
  if ( $mode == 'stageTwo') { $modeIsNotValid = false; }
  if ( $mode == 'stageThree') { $modeIsNotValid = false; }
  if ($modeIsNotValid) { die('Error 11102. -Programmer.'); }

  // The main program has a more rigorous validation test.
  if ((strlen($label) < 1) OR (strlen($partNumber) < 1)) {
    die('Error 22001. -Programmer.');
  }


  /*
  Construct form:
  */

  site_header('Add Part');


  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];

  if ( !isset($status_message) || $status_message == "" ) {
    $message_str = "";
  } else {
    $message_str = "<p class=\"errmsg\">$status_message</p>";
  }

  $userform_str = <<<EOUSERFORMSTR

$message_str

<p>This page allows you to update a part record in the database table
for parts. This table is used by other scripts (including
the ones for maintenance.)</p>

<p>label: $label<br/>
part number: $partNumber</p>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Part data:</legend>
  <div>
    <label for="manufacturer" class="fixedwidth">manufacturer</label>
    <input type="text" name="manufacturer" id="manufacturer" value="$manufacturer" size="20" maxlength="30"/>
  </div>
  <div>
    <label for="retailer" class="fixedwidth">retailer</label>
    <input type="text" name="retailer" id="retailer" value="$retailer" size="20" maxlength="30"/>
  </div>

  <div>
    <label for="price" class="fixedwidth">price</label>
    <input type="text" name="price" id="price" value="$price" size="12" maxlength="12"/>
  </div>

  <div>
    <label for="quantityHave" class="fixedwidth">quantity on hand</label>
    <input type="text" name="quantityHave" id="quantityHave" value="$quantityHave" size="6" maxlength="6"/>
  </div>
  <div>
    <input type="hidden" name="mode" value="stageThree">
    <input type="hidden" name="submitCounter" value="$submitCounter">
    <input type="hidden" name="label" value="$label">
    <input type="hidden" name="partNumber" value="$partNumber">
  </div>
  <div class="buttonarea">    
    <input type="submit" name="submit" value="Edit part data"/>
  </div>
  </fieldset>
</form>

EOUSERFORMSTR;
  echo $userform_str;

  site_footer();
  
  return;

}

?>