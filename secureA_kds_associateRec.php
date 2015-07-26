<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
Secure A kds_associate Record
-----------------------------
The task of this script is to work with the user towards inserting or
deleting records in the kds_kdaToUserType.
*/

session_start();

require_once('includes/login_funcs.php');
require_once('includes/db_vars.php');
include_once('includes/header_footer.php');
require_once('includes/secureA_kds_associateRec_funcs.php');

// User must be logged in and be someone special.
if (!user_isloggedin() || !user_type_cookieIsSet()) {
  form_destroy();
  $host = $_SERVER['HTTP_HOST'];
  header("Location: http://{$host}/web/login.php");
  exit;
}
if ($_COOKIE['user_type'] != 1) {
  form_destroy();
  die('Script aborted #3098. -Programmer.');
}

// Cancel if requested.
if (isset($_POST['cancel'])) {
  form_destroy();
  $host = $_SERVER['HTTP_HOST'];
  $uri = $_SERVER['PHP_SELF'];
  header("Location: http://$host$uri");
  exit;
}

if (isSet($_SESSION['SAKAR_mode'])) {
  $mode = $_SESSION['SAKAR_mode'];
} else {
  $mode = 'stageOne';
}


if ($mode == 'stageOne') {
  site_header('Secure A kds_associate Record');
  explain_the_script();
  explain_phrase_input();
  present_phrase_input_form();
  site_footer();
  $_SESSION['SAKAR_mode'] = 'for each node - secure it';
} elseif ($mode == 'for each node - secure it') {
  /*
  Establish $prevMode.
  */
  if (isset($_SESSION['SAKAR_previous_mode'])) {
    $prevMode = $_SESSION['SAKAR_previous_mode'];
  } else {
    $prevMode = "";
  }
  if ($prevMode != 'proccess choose do over form') {
    validateInputPhrases(); // Puts it into SESSION,
  }
  /*
  From this point on:
  All code accesses phrases through SESSION variables.
  */
  validatePhrasesMakeExistingNodes();
  site_header('Secure A kds_associate Record');
  explainSecuringForm();
  gatherInfoForSecuringForm();
  presentSecuringForm();
  site_footer();
  $_SESSION['SAKAR_mode'] = 'proccess securing form';
} elseif ($mode == 'proccess securing form') {
  validateSecuringForm();
  /*
  Make sure we don't try to insert or delete if it is not neccessary.
  */
  insertNewUserTypesForNodes();
  deleteUserTypesForNodes();
  site_header('Secure A kds_associate Record');
  confirmWhatScriptThinksItDid();
  presentChoiceToUseSamePhrasesOver();
  site_footer();
  $_SESSION['SAKAR_mode'] = 'proccess choose do over form';
} elseif ($mode == 'proccess choose do over form') {
  if (isSet($_POST['submit'])) {
    resetSomeSessionVars();
    $_SESSION['SAKAR_previous_mode'] = 'proccess choose do over form';
    $_SESSION['SAKAR_mode'] = 'for each node - secure it';
    site_header('Secure A kds_associate Record');
    presentSubmitButton();
    site_footer();
  } else {
    // The code which handles Cancel should have prevented this.
    form_destroy();
    die('Error: why are we here? 965405227 -Programmer.');
  }
} else {
  form_destroy();
  die('The script died for reason # 294875654. -Programmer.');
}

?>