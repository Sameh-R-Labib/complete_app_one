<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
This script is for associating a service part with a maintenance item.

*/

require_once('includes/login_funcs.php');
require_once('includes/db_vars.php');
include_once('includes/header_footer.php');
require_once('includes/assocPartMaintItem_funcs.php');

// User must be logged in and be someone special.
if (!user_isloggedin() || !user_type_cookieIsSet()) {
  // Redirect to login page.
  $host = $_SERVER['HTTP_HOST'];
  header("Location: http://{$host}/web/login.php");
  exit;
}
if ($_COOKIE['user_type'] != 1) {
  die('Script aborted #3098. -Programmer.');
}

// Manage the submit counter.
if (!isSet($_POST['submitCounter'])) {
  $submitCounter = 1;
} else {
  // Increment the counter
  $submitCounter = $_POST['submitCounter'] + 1;
  if ( $submitCounter == 12 ) {
    die('Script aborted for undisclosed reason. -Programmer.');
  }
}

$status_message = "";

if ( isset($_POST['submit']) AND ($_POST['submit'] ==
                            "Associate Part with Maintenance Item") ) {

  if (validPartIdMaintId()) {       // status message gets set if FALSE

    if (!isAlreadyInDatabase()) {   // status msg set if func ret TRUE
      insertIntoDatabase();         // status message gets set
    }
  }
}

presentItemIdPartIdForm();

?>