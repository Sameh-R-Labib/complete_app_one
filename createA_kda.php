<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
CREATE A KDA
------------
This script is for creating a new KDA (Know Do Article).
*/

session_start();



require_once('includes/login_funcs.php');
require_once('includes/db_vars.php');
include_once('includes/header_footer.php');
require_once('includes/createA_kda_funcs.php');

// User must be logged in and be someone special.
if (!user_isloggedin() || !user_type_cookieIsSet()) {
  // Redirect to login page.
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

if (isSet($_SESSION['CAK_mode'])) {
  $mode = $_SESSION['CAK_mode'];
} else {
  $mode = 'stageOne';
}


if ($mode == 'stageOne') {
  /*
  Inform the user that only the website owner can use this form.
  Inform that this form is for creating a new record for a KDA (Know
  Do Article) and establishing the neccessary associations to find it.
  Inform: Tell user to consider existing names of
  KDAs before deciding what KDA name to create. Certain types of duplication
  will cause the database to reject the new name.
  Inform: The actual KDA is created by the website owner using other means.
  Inform: instruct to enter up to four SP. Explain what the SPs are.
  Inform: Sequence order of phrases should be heirarchical.
  Provide the user with a form which allows him/her to specify up to
  four subject phrases (SP).
  */
  $_SESSION['CAK_mode'] = 'stageTwo';
  presentFormSP();
} elseif ($mode == 'stageTwo') {
  /*
  Get the SPs. Validate.
  Deal with blank phrases interspersed among the phrases.
  Store them in $_SESSION.
  Provide the user with a form which allows him/her to specify up to
  six topic phrases (TP).
  Inform: Try to make sequence order of phrases heirarchical.
  */
  $_SESSION['CAK_mode'] = 'stageThree';
  getValidateSPs();
  presentFormTP();
} elseif ($mode == 'stageThree') {
  /*
  Get the TPs. Validate.
  Deal with blank phrases interspersed among the phrases.
  Store them in $_SESSION.
  Provide the user with a form which allows him/her to specify a
  file name, directory path and short title for the KDA.
  Inform that at least a / must be present in the path string.
  */
  $_SESSION['CAK_mode'] = 'stageFour';
  getValidateTPs();
  fileNameDirPathSTitle();
} elseif ($mode == 'stageFour') {
  /*
  Get the file name, directory path, and short title.
  Validate.
  Make sure at least a / is present in the path string.
  Store them in $_SESSION.
  Create the KDA record.
  Make connections between all phrases as described in the design and
  flowchart.
  "ALL THE VALUES" <=> both kda record and values in the records which
  make connections.
  Retrieve from the database "ALL THE VALUES" we just created and the id
  of the KDA. (When searching for the KDA record use all known field values).
  Present "ALL THE VALUES" (including the id of the KDA) retrieved as a
  confirmation. Inform the user that if what he/she sees is what was entered
  (or agrees with it) then be confident the job is done.
  */
  getFileNameDirPathSTitle();
  createKDA();
  makeConnections();
  reloadKdaRecord();
  reloadConnections();
  presentConfirm();
  form_destroy();
} else {
  form_destroy();
  die('The script died for reason # 294875654. -Programmer.');
}

?>