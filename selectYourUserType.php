<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

// This script allows the user to view a radio button form field.
// The field's choices will include all the user types which were
// previously "created" for this particular user when he/she used
// the "Create A New user_type" script. This will establish the
// selected user_type as default and current user_type.

include_once('includes/login_funcs.php');
require_once('includes/db_vars.php');
include_once('includes/header_footer.php');

$user_type = '';

if (!user_isloggedin()) {
  echo '<p>You are not logged in, or this is not your user profile.</p>';
} else {
  $user_name = $_COOKIE['user_name'];
  
  // Have required fields been filled out?
  $requireMet = true;

  // Initialize $status_message
  $status_message = "";
  
  // Initialize $select_user_type_form_str
  $select_user_type_form_str = "";
  
  // Get all user types which were created for the user and put
  // them into array $all_created_userTypes. Abort on failure or
  // if no array elements can be returned. Note: the array must
  // include the id and label of each user_type element.
  $all_created_userTypes = alreadyCreated_userTypes($user_name);
  
  if ($all_created_userTypes == false) {
    $status_message = "You do not have any created user types. Please navigate to" .
    " the Create A New user_type page. Then, come back to this one.";
  } else {
    if ( isset($_POST['submit']) && $_POST['submit'] == "Select Your user_type" ) {
      // If the form was submitted then store the Selected
      // user_type in the user table and set user_type tokens.
      // More is accomplished by the code below.
    
      // Transfer POSTS to regular vars
      // Note: $user_type = the user_type selected.
      // This will be an integer corresponding to the
      // id number of the user_type.
      if ( isset($_POST['user_type']) ) {
        // Abort if user type is spoofed.
        abortIfUT_spoofed($all_created_userTypes);
        $user_type = $_POST['user_type'];

      } else {
        $user_type = 0;
        $requireMet = false;
      }
    
      // If required fields were inputed and the user is not using a spoofed
      // form user_type id then update database and set tokens.
      if ( $requireMet == true && isCreated($user_name, $user_type) ) {
    
        // Store the selected default user_type into the database.
        // Abort on failure.
        user_type_storeDefault($user_name, $user_type);
    
        // Set tokens. Abort on failure.
        user_type_set_tokens($user_type);
        
        // We also need to set this variable correctly
        // now so the page we display now will have the
        // correct user_type cookie value
        $_COOKIE['user_type'] = $user_type;
        
        // Get out of here quick to avoid menue anomalies.
        header("Location: index.php");
        exit;
      
      } elseif ( $requireMet == false ) {
        // Increment the counter
        $submitCounter = $_POST['submitCounter'] + 1;
        if ( $submitCounter == 4 ) {
          die('Script aborted for undisclosed reason.');
        }
        $status_message =  'Error -- you did not complete a required field.';
      }
    } else {
      // Initialize counter
      $submitCounter = 1;
    }

    // Make sure script dies if user_type_make_radio_button()
    // fails or will not produce a radio button string.
    $user_type_radio_button = user_type_make_radio_button($all_created_userTypes);
  
    // If the radio button field is not an empty string then
    // define the string for the form. Make sure it has a hidden
    // counter variable so that the form never gets submitted
    // more than three times. Call the form string
    // $select_user_type_form_str.  
    if ( isset($user_type_radio_button) && strlen($user_type_radio_button) > 0 ) {
      // Superglobals don't work with heredoc
      $php_self = $_SERVER['PHP_SELF'];
      $select_user_type_form_str = <<<EOSUTFSTR
<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>User Types: <span class="formcomment">*Required</span></legend>
  <div>
$user_type_radio_button
  </div>
  <div>
    <input type="hidden" name="submitCounter" value="$submitCounter">
  </div>
  <div class="buttonarea">
    <input type="submit" name="submit" value="Select Your user_type">
  </div>
  </fieldset>
</form>

EOSUTFSTR;
    }
  }
  
  
  // Define $message_str based on the value of $status_message
  // and how you want HTML to represent it.
  if ( $status_message == "" || !isset($status_message) ) {
    $message_str = "";
  } else {
    $message_str = "<p class=\"errmsg\">$status_message</p>";
  }
  

  
  // --------------
  // Construct Page
  // --------------

  site_header('Select Your user_type');

  $userform_str = <<<EOUSERFORMSTR

$message_str

<p>Select a User Type to make it your default and current User Type.
This will cause you to see different
links on the left side menue bar.</p>

$select_user_type_form_str

EOUSERFORMSTR;
  echo $userform_str;

  site_footer();
  
}

?>