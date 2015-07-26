<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/****************************************************
* createANewUserType.php                            *
* This script is for displaying a form which allows *
* the user to select a user_type from the ones in   *
* which they are eligible for. This user_type will  *
* then be available for their future sessions. The  *
* user will be able to select it from amongst all   *
* the other user types which have been established  *
* in the same manner. They will use a script called *
* selectYourUserType.php to accomplish the latter.  *
* We call the process accomplished by               *
* creatANewUserType.php: Creating A New user_type.  *
* We call the process accomplished by               *
* selectYourUserType.php: Select Your user_type.    *
*                                                   *
* The script submits to itself, and displays a      *
* message and possibly a form each time you submit. *
* If the user has a NULL stored in the database as  *
* the default user_type then a standard default     *
* type will be stored in place of the NULL. This    *
* user_type will also become the session user_type. *
* Later on the user can use selectYourUserType.php  *
* to select which user type they want as the        *
* default and session user_type.                    *
*                                                   *
* Features:                                         *
*  - Makes sure user is logged-in by username & P/W *
*  - Only lists user_type choices which are         *
*    necessary to present.                          *
*  - It won't present a form for users trying to    *
*    submit forms without making a selection more   *
*    than three times.                              *
*  - Inform the user about what the script is for.  *
*  - Inform the user about how to use the script.   *
*  - The script can be run over-and-over until all  *
*    eligible user types are created.               *
*****************************************************/

include_once('includes/login_funcs.php');
require_once('includes/db_vars.php');
include_once('includes/header_footer.php');

if (!user_isloggedin()) {
  echo '<p>You are not logged in, or this is not your user profile.</p>';
} else {
  $user_name = $_COOKIE['user_name'];

  
  // Generate the array $user_type_field_options[$index]
  // The $index starts at 0 and goes to n-1.
  // Each element of the array is an array with two
  // key/value pairs. The later array's two keys are:
  // id and label. Note that only the user types which the
  // the user is both eligible for and has not yet created
  // will be included in the array.
  // If the function which generates the
  // array user_type_field_options can't find any new user_types
  // for the user then it will return FALSE. If the function
  // fails for any reason then kill the scripts execution.
  $user_type_field_options = user_type_generate_eligibles($user_name);
  

  // Have required fields been filled out?
  // That is what $requireMet is for. Here we
  // initialize it.
  $requireMet = true;

  // Initialize $status_message
  $status_message = "";
  
  // Initialize $create_user_type_form_str
  $create_user_type_form_str = "";


  if ( isset($_POST['submit']) && $_POST['submit'] == "Create A New user_type" ) {
    // If the form was submitted then store the newly created
    // user_type in the table which associates user ids with
    // user_types.
  

    // Transfer POSTS to regular vars
    // Note: $user_type = the user_type selected.
    // This will be an integer corresponding to the
    // id number of the user_type.
    if ( isset($_POST['user_type']) ) {
      abortIfUT_spoofed($user_type_field_options);
      $user_type = $_POST['user_type'];
    } else {
      $user_type = 0;
      $requireMet = false;
    }


    // If required fields were inputed then update database
    if ( $requireMet == true ) {

      // If user's default user_type is NULL then:
      //   1. set it to system default
      //   2. set user's user_type browser tokens to system default.
      //   3. Create it by adding it to the user_types_foreach table.
      //   4. Remove it from $user_type_field_options array and set array
      //      value to FALSE if it has become empty.
      // If default is not NULL then don't do
      // anything. On failure abort everything.
      user_type_set_default($user_name);
      
      
      // If $user_type == the system default user_type then
      // do not store it because it has already been stored.
      if ( $user_type != 2 ) {
        // Store the being-created user_type into the database.
        // On success assign $status_message: Successfuly edited
        // user data. On failure assign $status_message: Problem
        // with storing your choice.
        if ( user_type_store($user_name, $user_type) ) {
          $status_message = 'Successfuly edited user data.';
        
          // We need to do the following statement so that the user type
          // which was just created will not be presented again when the form
          // is presented this time. We do this instead of running the function
          // user_type_generate_eligibles($user_name) over again.
          // This new function will return a value of FALSE if there is no
          // array to be returned.
          $user_type_field_options =
            removeUserType_justAdded($user_type, $user_type_field_options);
        } else {
          $status_message = 'Problem with storing your choice.';
        }
      }
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


  // Create the radio button field string if the user was
  // found to be eligible for any more user types. Otherwise,
  // generate the appropriate message string and an empty
  // string for the button field variable.
  if ( $user_type_field_options == false ) {
    $status_message .=  ' The system was unable to find any more user types' .
    ' which you are eligible for.';
    $user_type_radio_button = "";
  } else {
    // Make sure script dies if user_type_make_radio_button() fails.
    $user_type_radio_button = user_type_make_radio_button($user_type_field_options);
  }


  // If the radio button field is not an empty string then
  // define the string for the form. Make sure it has a hidden
  // counter variable so that the form never gets submitted
  // more than three times. Call the form string
  // $create_user_type_form_str.  
  if ( $user_type_radio_button ) {
    // Superglobals don't work with heredoc
    $php_self = $_SERVER['PHP_SELF'];
    $create_user_type_form_str = <<<EOCUTFSTR
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
    <input type="submit" name="submit" value="Create A New user_type">
  </div>
  </fieldset>
</form>

EOCUTFSTR;
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

  site_header('Create A New user_type');

  $userform_str = <<<EOUSERFORMSTR

$message_str

<P>If you see a form below then choose a User Type.
Then, submit the form. This action will permit you later on to select
this User Type for your browser session and therefore see different
links on the left side menue bar. If you feel a particular User Type
is wrongly not being offered to you then please contact the webmaster.</P>

<P>Note: If previously you did not have a default user_type then after
making a selection and submitting the form this script
will (1)set your default user_type to the standard one.
(2)ceate the standard one even if it wasn't the one you seleced and
(3)will make the standard user_type your current user_type.</P>

$create_user_type_form_str

EOUSERFORMSTR;
  echo $userform_str;

  site_footer();

}

?>