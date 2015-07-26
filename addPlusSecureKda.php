<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
Create Plus Secure a KDA
------------------------
*/

session_start();

// to avoid warning when adding to this string
if (!isset($_SESSION['CPSAK_status_message'])) {
  $_SESSION['CPSAK_status_message'] ="";
}

require_once('includes/login_funcs.php');
require_once('includes/db_vars.php');
include_once('includes/header_footer.php');

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





/* ====================================================
Action Starts Here!
*/

if (isSet($_SESSION['CPSAK_mode'])) {
  $mode = $_SESSION['CPSAK_mode'];
} else {
  $mode = 'stageOne';
}



if ($mode == 'stageOne') {
  /*
  Collect: all phrases, file name, dir path, short title
  */
  presentBigForm();
  $_SESSION['CPSAK_mode'] = 'validate big form';
} elseif ($mode == 'validate big form') {
  readAndValidateBigForm();
  createKDA();
  makeConnections();
  reloadKdaRecord();
  reloadConnections();
  presentConfirm_Create();
  $_SESSION['CPSAK_mode'] = 'Secure Starts Here';
} elseif ($mode == 'Secure Starts Here') {
  makeSureWeHaveAllInfo();
  validatePhrasesMakeExistingNodes();
  site_header('Create Plus Secure a KDA');
  explainSecuringForm();
  gatherInfoForSecuringForm();
  presentSecuringForm();
  site_footer();
  $_SESSION['CPSAK_mode'] = 'proccess securing form';
} elseif ($mode == 'proccess securing form') {
  validateSecuringForm();
  /*
  Make sure we don't try to insert or delete if it is not neccessary.
  */
  insertNewUserTypesForNodes();
  deleteUserTypesForNodes();
  createPageString();
  saveFileToServer();
  site_header('Create Plus Secure a KDA');
  confirmWhatScriptThinksItDid();
  presentDownloadForm();
  site_footer();
  $_SESSION['CPSAK_mode'] = 'download to PC';
} elseif ($mode == 'download to PC') {
  sendFileToBrowser();
//  recommendWhatToDoNext();
  form_destroy();
} else {
  form_destroy();
  die('The script died for reason # 294875654. -Programmer.');
}


// ====================================================


function form_destroy() {
  $_SESSION['CPSAK_mode'] = 'stageOne';
  $_SESSION['CPSAK_submitToken'] = "";
  $_SESSION['CPSAK_SP_array'] = array();
  $_SESSION['CPSAK_TP_array'] = array();
  $_SESSION['CPSAK_scriptFileName'] = "";
  $_SESSION['CPSAK_scriptFileDir'] = "";
  $_SESSION['CPSAK_shortTitle'] = "";
  $_SESSION['CPSAK_id'] = NULL;
  $_SESSION['CPSAK_kdaFromDB'] = array();
  $_SESSION['CPSAK_connection'] = array();
  $_SESSION['CPSAK_ArrOfPhrasesPlusId'] = array();
  $_SESSION['CPSAK_series'] = array();
  $_SESSION['CPSAK_userTypes'] = array();
  $_SESSION['CPSAK_pageStr'] = "";
  $_SESSION['CPSAK_status_message'] = "";

  return;
}




function presentBigForm() {
  $submitToken = time();
  $_SESSION['CPSAK_submitToken'] = $submitToken;

  site_header('Create Plus Secure a KDA');
  $php_self = $_SERVER['PHP_SELF'];
  $page_str = <<<EOPAGESTR

<h2>Step One</h2>

<p>This form provides minimal guidance. If this is not good for you
try using the two other scripts which will do the same thing.</p>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Form</legend>
  <p>Subject Phrases:</p>
  <div>
    <label for="sp_1" class="fixedwidth">SP 1</label>
    <input type="text" name="sp_1" id="sp_1" value="" size="36" maxlength="36"/>
  </div>
  <div>
    <label for="sp_2" class="fixedwidth">SP 2</label>
    <input type="text" name="sp_2" id="sp_2" value="" size="36" maxlength="36"/>
  </div>
  <div>
    <label for="sp_3" class="fixedwidth">SP 3</label>
    <input type="text" name="sp_3" id="sp_3" value="" size="36" maxlength="36"/>
  </div>
  <div>
    <label for="sp_4" class="fixedwidth">SP 4</label>
    <input type="text" name="sp_4" id="sp_4" value="" size="36" maxlength="36"/>
  </div>
  <div>
    <input type="hidden" name="submitToken" value="$submitToken">
  </div>
  <p>Topic Phrases:</p>
  <div>
    <label for="sp_1" class="fixedwidth">TP 1</label>
    <input type="text" name="tp_1" id="tp_1" value="" size="36" maxlength="60"/>
  </div>
  <div>
    <label for="tp_2" class="fixedwidth">TP 2</label>
    <input type="text" name="tp_2" id="tp_2" value="" size="36" maxlength="60"/>
  </div>
  <div>
    <label for="tp_3" class="fixedwidth">TP 3</label>
    <input type="text" name="tp_3" id="tp_3" value="" size="36" maxlength="60"/>
  </div>
  <div>
    <label for="tp_4" class="fixedwidth">TP 4</label>
    <input type="text" name="tp_4" id="tp_4" value="" size="36" maxlength="60"/>
  </div>
  <div>
    <label for="tp_5" class="fixedwidth">TP 5</label>
    <input type="text" name="tp_5" id="tp_5" value="" size="36" maxlength="60"/>
  </div>
  <div>
    <label for="tp_6" class="fixedwidth">TP 6</label>
    <input type="text" name="tp_6" id="tp_6" value="" size="36" maxlength="60"/>
  </div>
  <p>Script File:</p>
  <div>
    <label for="scriptFileName" class="fixedwidth">File Name</label>
    <input type="text" name="scriptFileName" id="scriptFileName" value="" size="36" maxlength="36"/>
  </div>
  <div>
    <label for="scriptFileDir" class="fixedwidth">Directory Path</label>
    <input type="text" name="scriptFileDir" id="scriptFileDir" value="" size="36" maxlength="90"/>
  </div>
  <div>
    <label for="shortTitle" class="fixedwidth">Short Title</label>
    <input type="text" name="shortTitle" id="shortTitle" value="" size="36" maxlength="90"/>
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




function readAndValidateBigForm() {
  if (isset($_POST['submitToken'])) {
    $submitToken = $_POST['submitToken'];
  } else {
    $submitToken = "";
  }
  if ($submitToken != $_SESSION['CPSAK_submitToken']) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }


  // Get subject phrases.
  if (isset($_POST['sp_1'])) {
    $sp_1 = $_POST['sp_1'];
  } else {
    $sp_1 = "";
  }
  if (isset($_POST['sp_2'])) {
    $sp_2 = $_POST['sp_2'];
  } else {
    $sp_2 = "";
  }
  if (isset($_POST['sp_3'])) {
    $sp_3 = $_POST['sp_3'];
  } else {
    $sp_3 = "";
  }
  if (isset($_POST['sp_4'])) {
    $sp_4 = $_POST['sp_4'];
  } else {
    $sp_4 = "";
  }

  if ( get_magic_quotes_gpc() ) {
    $sp_1 = stripslashes($sp_1);
    $sp_2 = stripslashes($sp_2);
    $sp_3 = stripslashes($sp_3);
    $sp_4 = stripslashes($sp_4);
  }

  $sp_1 = trim($sp_1);
  $sp_2 = trim($sp_2);
  $sp_3 = trim($sp_3);
  $sp_4 = trim($sp_4);

  if (strlen($sp_1) > 36 OR strlen($sp_2) > 36 OR strlen($sp_3) > 36 OR strlen($sp_4) > 36) {
    form_destroy();
    die('Err 7980365455. -Programmer.');
  }
  
  if (strpos($sp_1, ':::') !== FALSE OR strpos($sp_2, ':::') !== FALSE
  OR strpos($sp_3, ':::') !== FALSE OR strpos($sp_4, ':::') !== FALSE) {
    form_destroy();
    die("You are not allowed to have ':::' in your phrase string (611). -Programmer.");
  }

  /*
  I'll store the SP values in an array and I'll weed out empty strings.
  */
  $tempArr = array();

  if (!empty($sp_1)) {
    $tempArr[] = $sp_1;
  }
  if (!empty($sp_2)) {
    $tempArr[] = $sp_2;
  }
  if (!empty($sp_3)) {
    $tempArr[] = $sp_3;
  }
  if (!empty($sp_4)) {
    $tempArr[] = $sp_4;
  }

  if (empty($tempArr)) {
    form_destroy();
    die('You need to supply at least one SP (132116976). -Programmer.');
  }

  // EXPORTING
  $_SESSION['CPSAK_SP_array'] = $tempArr;



  // Get topic phrases
  if (isset($_POST['tp_1'])) {
    $tp_1 = $_POST['tp_1'];
  } else {
    $tp_1 = "";
  }
  if (isset($_POST['tp_2'])) {
    $tp_2 = $_POST['tp_2'];
  } else {
    $tp_2 = "";
  }
  if (isset($_POST['tp_3'])) {
    $tp_3 = $_POST['tp_3'];
  } else {
    $tp_3 = "";
  }
  if (isset($_POST['tp_4'])) {
    $tp_4 = $_POST['tp_4'];
  } else {
    $tp_4 = "";
  }
  if (isset($_POST['tp_5'])) {
    $tp_5 = $_POST['tp_5'];
  } else {
    $tp_5 = "";
  }
  if (isset($_POST['tp_6'])) {
    $tp_6 = $_POST['tp_6'];
  } else {
    $tp_6 = "";
  }

  if ( get_magic_quotes_gpc() ) {
    $tp_1 = stripslashes($tp_1);
    $tp_2 = stripslashes($tp_2);
    $tp_3 = stripslashes($tp_3);
    $tp_4 = stripslashes($tp_4);
    $tp_5 = stripslashes($tp_5);
    $tp_6 = stripslashes($tp_6);
  }

  $tp_1 = trim($tp_1);
  $tp_2 = trim($tp_2);
  $tp_3 = trim($tp_3);
  $tp_4 = trim($tp_4);
  $tp_5 = trim($tp_5);
  $tp_6 = trim($tp_6);

  if (strlen($tp_1) > 60 OR strlen($tp_2) > 60 OR strlen($tp_3) > 60 OR strlen($tp_4) > 60
    OR strlen($tp_5) > 60 OR strlen($tp_6) > 60) {
    form_destroy();
    die('Err 7980365455. -Programmer.');
  }

  if (strpos($tp_1, ':::') !== FALSE OR strpos($tp_2, ':::') !== FALSE
  OR strpos($tp_3, ':::') !== FALSE OR strpos($tp_4, ':::') !== FALSE
  OR strpos($tp_5, ':::') !== FALSE OR strpos($tp_6, ':::') !== FALSE) {
    form_destroy();
    die("You are not allowed to have ':::' in your phrase string (518). -Programmer.");
  }

  /*
  I'll store the TP values in an array and I'll weed out empty strings.
  */
  $tempArr = array();

  if (!empty($tp_1)) {
    $tempArr[] = $tp_1;
  }
  if (!empty($tp_2)) {
    $tempArr[] = $tp_2;
  }
  if (!empty($tp_3)) {
    $tempArr[] = $tp_3;
  }
  if (!empty($tp_4)) {
    $tempArr[] = $tp_4;
  }
  if (!empty($tp_5)) {
    $tempArr[] = $tp_5;
  }
  if (!empty($tp_6)) {
    $tempArr[] = $tp_6;
  }

  if (empty($tempArr)) {
    form_destroy();
    die('You need to supply at least one TP (577980665). -Programmer.');
  }

  /*
  Make sure the first TP does not match any of SP in our KDS.
  Also, make sure the last SP does not match any tp_1 in our KDS.
  This is to help in our effort to make sure that the sets of
  phrases associated with a KDA are unique. The database already
  has an index which helps do this. However, this extra step will
  help eliminate a loophole in our algorithm which occurs if the user
  enters a phrase as TP which should have been an SP or vice versa.
  */
  $SP_arr = $_SESSION['CPSAK_SP_array'];
  $lastSP = end($SP_arr);
  $firstTP = $tempArr[0];
  if (!lastSPandFirstTP_areGood($lastSP, $firstTP)) {
    form_destroy();
    die('Your choice of last SP or first TP is not good. See comment in code. -Programmer.');
  }

  // EXPORTING
  $_SESSION['CPSAK_TP_array'] = $tempArr;


  // Get script file information.
  if (isset($_POST['scriptFileName'])) {
    $scriptFileName = $_POST['scriptFileName'];
  } else {
    $scriptFileName = "";
  }
  if (isset($_POST['scriptFileDir'])) {
    $scriptFileDir = $_POST['scriptFileDir'];
  } else {
    $scriptFileDir = "";
  }
  if (isset($_POST['shortTitle'])) {
    $shortTitle = $_POST['shortTitle'];
  } else {
    $shortTitle = "";
  }

  if ( get_magic_quotes_gpc() ) {
    $scriptFileName = stripslashes($scriptFileName);
    $scriptFileDir = stripslashes($scriptFileDir);
    $shortTitle = stripslashes($shortTitle);
  }

  $scriptFileName = trim($scriptFileName);
  $scriptFileDir = trim($scriptFileDir);
  $shortTitle = trim($shortTitle);

  /*
  Validate the scriptFileName by making sure:
    1. It's not too long.
    2. It's not too short.
  */
  $length = strlen($scriptFileName);
  if ($length > 36 OR $length < 1) {
    form_destroy();
    die('Problem with string length. Err 9899986988. -Programmer.');
  }
  /*
  Validate the scriptFileDir by making sure:
    1. It's not too long or short.
    2. It starts with a '/' and ends with a '/'.
  */
  $length = strlen($scriptFileDir);
  if ($length > 90 OR $length < 1) {
    form_destroy();
    die('Problem with string length. Err 3643348226. -Programmer.');
  }

  $beginning = strpos($scriptFileDir, '/');
  $temp = strrpos($scriptFileDir, '/');
  $length = strlen($scriptFileDir);
  $end =  $temp - ($length - 1);

  if ($beginning !== 0 OR $end !== 0) {
    form_destroy();
    die("The path string does not start and end with a '/' (2087). -Programmer.");
  }
  /*
  Validate the shortTitle by making sure:
    1. It's not too long.
    2. It's not too short.
  */
  $length = strlen($shortTitle);
  if ($length > 90 OR $length < 1) {
    form_destroy();
    die('Problem with string length. Err 050403020133. -Programmer.');
  }

  // EXPORTING
  $_SESSION['CPSAK_scriptFileName'] = $scriptFileName;
  $_SESSION['CPSAK_scriptFileDir'] = $scriptFileDir;
  $_SESSION['CPSAK_shortTitle'] = $shortTitle;

  return;
}




function lastSPandFirstTP_areGood($lastSP_IN, $firstTP_IN) {
  $lastSP_IN = addslashes($lastSP_IN);
  $firstTP_IN = addslashes($firstTP_IN);
  $query = "SELECT id
            FROM kds_kda
            WHERE sp_1='$firstTP_IN' OR sp_2='$firstTP_IN'
            OR sp_3='$firstTP_IN' OR sp_4='$firstTP_IN'";
  $result = mysql_query($query);
  if (!$result) {
    die('Query failed. Err: 649315986. -Programmer.');
  }
  if (mysql_num_rows($result) >= 1) {
    return FALSE;
  }
  $query = "SELECT id
            FROM kds_kda
            WHERE tp_1='$lastSP_IN'";
  $result = mysql_query($query);
  if (!$result) {
    die('Query failed. Err: 252547711. -Programmer.');
  }
  if (mysql_num_rows($result) >= 1) {
    return FALSE;
  }
  return TRUE;
}




function createKDA() {
/*
If this "same KDA record" already exists then kill the script.
Otherwise, create the KDA record.

NOTE: When I say "same KDA record" I'm talking about the phrases only.
*/

  $scriptFileName = $_SESSION['CPSAK_scriptFileName'];
  $scriptFileDir = $_SESSION['CPSAK_scriptFileDir'];
  $shortTitle = $_SESSION['CPSAK_shortTitle'];

  $SP_arr =  $_SESSION['CPSAK_SP_array'];
  $TP_arr =  $_SESSION['CPSAK_TP_array'];

  if (isset($SP_arr[0])) {
    $sp_1 = $SP_arr[0];
  } else {
    $sp_1 = "";
  }

  if (isset($SP_arr[1])) {
    $sp_2 = $SP_arr[1];
  } else {
    $sp_2 = "";
  }

  if (isset($SP_arr[2])) {
    $sp_3 = $SP_arr[2];
  } else {
    $sp_3 = "";
  }

  if (isset($SP_arr[3])) {
    $sp_4 = $SP_arr[3];
  } else {
    $sp_4 = "";
  }

  if (isset($TP_arr[0])) {
    $tp_1 = $TP_arr[0];
  } else {
    $tp_1 = "";
  }

  if (isset($TP_arr[1])) {
    $tp_2 = $TP_arr[1];
  } else {
    $tp_2 = "";
  }

  if (isset($TP_arr[2])) {
    $tp_3 = $TP_arr[2];
  } else {
    $tp_3 = "";
  }

  if (isset($TP_arr[3])) {
    $tp_4 = $TP_arr[3];
  } else {
    $tp_4 = "";
  }

  if (isset($TP_arr[4])) {
    $tp_5 = $TP_arr[4];
  } else {
    $tp_5 = "";
  }

  if (isset($TP_arr[5])) {
    $tp_6 = $TP_arr[5];
  } else {
    $tp_6 = "";
  }

  $sp_1 = addslashes($sp_1);
  $sp_2 = addslashes($sp_2);
  $sp_3 = addslashes($sp_3);
  $sp_4 = addslashes($sp_4);
  $tp_1 = addslashes($tp_1);
  $tp_2 = addslashes($tp_2);
  $tp_3 = addslashes($tp_3);
  $tp_4 = addslashes($tp_4);
  $tp_5 = addslashes($tp_5);
  $tp_6 = addslashes($tp_6);
  $scriptFileName = addslashes($scriptFileName);
  $scriptFileDir = addslashes($scriptFileDir);
  $shortTitle = addslashes($shortTitle);

  $query = "SELECT id
            FROM kds_kda
            WHERE sp_1 = '$sp_1' AND sp_2 = '$sp_2' AND sp_3 = '$sp_3' AND sp_4 = '$sp_4'
              AND tp_1 = '$tp_1' AND tp_2 = '$tp_2' AND tp_3 = '$tp_3' AND tp_4 = '$tp_4'
              AND tp_5 = '$tp_5' AND tp_6 = '$tp_6'";
  $result = mysql_query($query);
  if (!$result) {
    form_destroy();
    die('Query failed. Err 094979020133. -Programmer.');
  }
  if (mysql_num_rows($result) >= 1) {
    form_destroy();
    die('KDA record already exists. Err 9790737873. -Programmer.');
  }

  /*
  Create the record.
  */
  $query = "INSERT INTO kds_kda (sp_1, sp_2, sp_3, sp_4, tp_1, tp_2, tp_3, tp_4, tp_5, tp_6,
              scriptFileName, scriptFileDir, shortTitle)
            VALUES ('$sp_1', '$sp_2', '$sp_3', '$sp_4', '$tp_1', '$tp_2', '$tp_3', '$tp_4',
              '$tp_5', '$tp_6', '$scriptFileName', '$scriptFileDir', '$shortTitle')";
  $result = mysql_query($query);
  if (!$result || mysql_affected_rows() < 1) {
    form_destroy();
    die('Error adding new record. 00099. -Programmer.');
  }

  return;
}




function makeConnections() {
/*
Make connections between all phrases as described in the design and flowchart.
This means we need to put all the values into the database table called
kds_associate which would make it possible for the KDA record to be searched for
and have its id found.
*/

  $sp = $_SESSION['CPSAK_SP_array'];
  $tp = $_SESSION['CPSAK_TP_array'];

  $id = getKdaRecordId();
  /*
  We will need this later so:
  */
  $_SESSION['CPSAK_id'] = $id;

  if (empty($sp[0])) {
    form_destroy();
    die('Subject phrases missing 467778777. -Programmer.');
  }

  if (empty($tp[0])) {
    form_destroy();
    die('Topic phrases missing 4165728647. -Programmer.');
  }


  if (!isset($id)) {
    form_destroy();
    die('id is not set Error 498179. -Programmer.');
  }


  if (!really_is_int($id)) {
    form_destroy();
    die('id is not integer Error 2438179. -Programmer.');
  }


  if ($id < 0) {
    form_destroy();
    die('id is negative Error 59648179. -Programmer.');
  }

  /*
  Put all the phrases into one array.
  */
  $arr = array_merge($sp, $tp);
  
  /*
  Add slashes to phrases.
  */
  foreach ($arr as $phraseKey => $phrase) {
    $arr[$phraseKey] = addslashes($phrase);
  }
  
  /*
  Make a string called $composite.
  */
  $composite = "";

  /*
  Make sure the appropriate records exist in order for associations to exist.
  */
  foreach ($arr as $phrase) {
    insertIfNotExist($composite, $phrase);
    $composite = $composite . ":::" . $phrase;
  }


  insertAndCompleteIfNotExist($composite, $id);
  return;
}




function getKdaRecordId() {
/*
Helper for makeConnections(). Returns the kds_kda record id for the record
which was created by the script during this session.
*/
  $scriptFileName = $_SESSION['CPSAK_scriptFileName'];
  $scriptFileDir = $_SESSION['CPSAK_scriptFileDir'];
  $shortTitle = $_SESSION['CPSAK_shortTitle'];

  $SP_arr =  $_SESSION['CPSAK_SP_array'];
  $TP_arr =  $_SESSION['CPSAK_TP_array'];
  
  if (isset($SP_arr[0])) {
    $sp_1 = $SP_arr[0];
  } else {
    $sp_1 = "";
  }

  if (isset($SP_arr[1])) {
    $sp_2 = $SP_arr[1];
  } else {
    $sp_2 = "";
  }

  if (isset($SP_arr[2])) {
    $sp_3 = $SP_arr[2];
  } else {
    $sp_3 = "";
  }

  if (isset($SP_arr[3])) {
    $sp_4 = $SP_arr[3];
  } else {
    $sp_4 = "";
  }

  if (isset($TP_arr[0])) {
    $tp_1 = $TP_arr[0];
  } else {
    $tp_1 = "";
  }

  if (isset($TP_arr[1])) {
    $tp_2 = $TP_arr[1];
  } else {
    $tp_2 = "";
  }

  if (isset($TP_arr[2])) {
    $tp_3 = $TP_arr[2];
  } else {
    $tp_3 = "";
  }

  if (isset($TP_arr[3])) {
    $tp_4 = $TP_arr[3];
  } else {
    $tp_4 = "";
  }

  if (isset($TP_arr[4])) {
    $tp_5 = $TP_arr[4];
  } else {
    $tp_5 = "";
  }

  if (isset($TP_arr[5])) {
    $tp_6 = $TP_arr[5];
  } else {
    $tp_6 = "";
  }

  $sp_1 = addslashes($sp_1);
  $sp_2 = addslashes($sp_2);
  $sp_3 = addslashes($sp_3);
  $sp_4 = addslashes($sp_4);
  $tp_1 = addslashes($tp_1);
  $tp_2 = addslashes($tp_2);
  $tp_3 = addslashes($tp_3);
  $tp_4 = addslashes($tp_4);
  $tp_5 = addslashes($tp_5);
  $tp_6 = addslashes($tp_6);
  $scriptFileName = addslashes($scriptFileName);
  $scriptFileDir = addslashes($scriptFileDir);
  $shortTitle = addslashes($shortTitle);

  $query = "SELECT id
            FROM kds_kda
            WHERE
            sp_1 = '$sp_1'
            AND sp_2 = '$sp_2'
            AND sp_3 = '$sp_3'
            AND sp_4 = '$sp_4'
            AND tp_1 = '$tp_1'
            AND tp_2 = '$tp_2'
            AND tp_3 = '$tp_3'
            AND tp_4 = '$tp_4'
            AND tp_5 = '$tp_5'
            AND tp_6 = '$tp_6'
            AND scriptFileName = '$scriptFileName'
            AND scriptFileDir = '$scriptFileDir'
            AND shortTitle = '$shortTitle'";
  $result = mysql_query($query);
  if (!$result OR mysql_num_rows($result) < 1) {
    form_destroy();
    die('Unable to get value. Err: 21195384114. -Programmer.');
  }

  $the_id = mysql_result($result, 0, 0);
  return $the_id;
}




function really_is_int($val)
{
    if(func_num_args() !== 1)
        exit(__FUNCTION__.'(): not passed 1 arg');

    return ($val !== true) && ((string)abs((int) $val)) === ((string) ltrim($val, '-0'));
}




function insertIfNotExist($composite_IN, $phrase_IN) {
/*
Helper for makeConnections().
*/

  /*
  Does the record already exist?
  If yes then return.
  */
  $query = "SELECT id
            FROM kds_associate
            WHERE composite = '$composite_IN' AND nextPhrase = '$phrase_IN'";
  $result = mysql_query($query);
  if (!$result) {
    form_destroy();
    die('Query in function failed 073987690984. -Programmer.');
  }

  if ( mysql_num_rows($result) >= 1) {
    return;
  } else {
    /*
    Insert then return.
    */
    $query = "INSERT INTO kds_associate (composite, nextPhrase)
              VALUES ('$composite_IN', '$phrase_IN')";
    $result = mysql_query($query);
    if (!$result || mysql_affected_rows() < 1) {
      form_destroy();
      $errStr = mysql_error();
      echo $errStr;
      die('Error adding record. Try changing phrase beginnings. -Programmer.');
    } else {
      return;
    }
  }
}




function insertAndCompleteIfNotExist($composite_IN, $id_IN) {
/*
Helper for makeConnections().
*/

  /*
  Does the record already exist?
  If yes then return.
  */
  $query = "SELECT id
            FROM kds_associate
            WHERE
            composite = '$composite_IN'
            AND nextPhrase = '$id_IN'
            AND isComplete = 1";
  $result = mysql_query($query);
  if (!$result) {
    form_destroy();
    die('Query in function failed 073987690984. -Programmer.');
  }
  if ( mysql_num_rows($result) >= 1) {
    return;
  }

  /*
  Insert then return.
  */
  $query = "INSERT INTO kds_associate (composite, nextPhrase, isComplete)
            VALUES ('$composite_IN', '$id_IN', 1)";
  $result = mysql_query($query);
  if (!$result || mysql_affected_rows() < 1) {
    form_destroy();
    die('Error adding new record. 877758898300. -Programmer.');
  } else {
    return;
  }
}




function reloadKdaRecord() {
/*
Thus far the only kda field values (other than id) we have in $_SESSION
came from the user. Now, we will load them from the database so that we
can present them later as part of the confermation message.
*/
  $id = $_SESSION['CPSAK_id'];

  $query = "SELECT
sp_1, sp_2, sp_3, sp_4, tp_1, tp_2, tp_3, tp_4, tp_5, tp_6, scriptFileName,
scriptFileDir, shortTitle
            FROM kds_kda
            WHERE id = $id";
  $result = mysql_query($query);
  if (!$result OR mysql_num_rows($result) < 1) {
    form_destroy();
    die('Unable to get kda values. Err: 743687478377. -Programmer.');
  }
  $_SESSION['CPSAK_kdaFromDB'] = mysql_fetch_array($result, MYSQL_ASSOC);
  return;
}




function reloadConnections() {
/*
Now, we will load the connections/associations from the database so that we
can present them later as part of the confermation message.
*/

  /*
  I learned an important programming lesson while debugging:
  - Declare a global variable as global EVERYWHERE it will be used.
  - Just because I declared the variable as global in the helper
    function for this function DOES NOT mean changes will be reflected
    back here.
  */
  global $conn_ection;

  $sp = $_SESSION['CPSAK_SP_array'];
  $tp = $_SESSION['CPSAK_TP_array'];
  $id = $_SESSION['CPSAK_id'];

  if (empty($sp[0])) {
    form_destroy();
    die('Subject phrases missing 4278777. -Programmer.');
  }

  if (empty($tp[0])) {
    form_destroy();
    die('Topic phrases missing 4228647. -Programmer.');
  }

  if (empty($id)) {
    form_destroy();
    die('Error 592179. -Programmer.');
  }

  /*
  Put all the phrases into one array.
  */
  $arr = array_merge($sp, $tp);
  
  /*
  Add slashes to phrases.
  */
  foreach ($arr as $phraseKey => $phrase) {
    $arr[$phraseKey] = addslashes($phrase);
  }
  
  /*
  Make a string called $composite.
  */
  $composite = "";


  /*
  Get all connections.
  */
  $conn_ection = array(); // each element: composite, nextPhrase, isComplete.
  foreach ($arr as $phrase) {
    // $conn_ection will be a global.
    getConnection($composite, $phrase, 0);
    $composite = $composite . ":::" . $phrase;
  }

  getConnection($composite, $id, 1);

  $_SESSION['CPSAK_connection'] = $conn_ection;
  return;
}




function getConnection($composite_IN, $next_IN, $isComplete_IN) {
/*
Helper for reloadConnections.
*/
  global $conn_ection;

  /*
  Use the input to get the values from the database.
  */
  $query = "SELECT composite, nextPhrase, isComplete
            FROM kds_associate
            WHERE
            composite = '$composite_IN'
            AND nextPhrase = '$next_IN'
            AND isComplete = $isComplete_IN";
  $result = mysql_query($query);
  if (!$result OR mysql_num_rows($result) < 1) {
    form_destroy();
    die('Query in function failed 07211393125484. -Programmer.');
  }

  $conn_ection[] = mysql_fetch_array($result, MYSQL_ASSOC);
  return;
}




function presentConfirm_Create() {
/*
Present "ALL THE VALUES" (including the id of the KDA) retrieved as a
confirmation. Inform the user that if what he/she sees is what was entered
(or agrees with it) then be confident the job is done.
*/
  $valuesStr = "";

  /*
  Place the id of the kds_kda record in $valuesStr.
  */
  $id = $_SESSION['CPSAK_id'];
  $valuesStr .= "<p>kda id: $id</p>\n\n";

  /*
  Place the values of the kds_kda record in $valuesStr.
  */
  $kda = $_SESSION['CPSAK_kdaFromDB'];
  
  // stripslashes
  foreach ($kda as $key => $val) {
    $kda["$key"] = stripslashes($val);
  }
  
  if (isset($kda['sp_1'])) {
    $sp_1 = $kda['sp_1'];
  } else {
    $sp_1 = "";
  }
  if (isset($kda['sp_2'])) {
    $sp_2 = $kda['sp_2'];
  } else {
    $sp_2 = "";
  }
  if (isset($kda['sp_3'])) {
    $sp_3 = $kda['sp_3'];
  } else {
    $sp_3 = "";
  }
  if (isset($kda['sp_4'])) {
    $sp_4 = $kda['sp_4'];
  } else {
    $sp_4 = "";
  }
  if (isset($kda['tp_1'])) {
    $tp_1 = $kda['tp_1'];
  } else {
    $tp_1 = "";
  }
  if (isset($kda['tp_2'])) {
    $tp_2 = $kda['tp_2'];
  } else {
    $tp_2 = "";
  }
  if (isset($kda['tp_3'])) {
    $tp_3 = $kda['tp_3'];
  } else {
    $tp_3 = "";
  }
  if (isset($kda['tp_4'])) {
    $tp_4 = $kda['tp_4'];
  } else {
    $tp_4 = "";
  }
  if (isset($kda['tp_5'])) {
    $tp_5 = $kda['tp_5'];
  } else {
    $tp_5 = "";
  }
  if (isset($kda['tp_6'])) {
    $tp_6 = $kda['tp_6'];
  } else {
    $tp_6 = "";
  }
  if (isset($kda['scriptFileName'])) {
    $scriptFileName = $kda['scriptFileName'];
  } else {
    $scriptFileName = "";
  }
  if (isset($kda['scriptFileDir'])) {
    $scriptFileDir = $kda['scriptFileDir'];
  } else {
    $scriptFileDir = "";
  }
  if (isset($kda['shortTitle'])) {
    $shortTitle = $kda['shortTitle'];
  } else {
    $shortTitle = "";
  }
  $valuesStr .= "<p>SP 1: $sp_1<br/>\n";
  $valuesStr .= "SP 2: $sp_2<br/>\n";
  $valuesStr .= "SP 3: $sp_3<br/>\n";
  $valuesStr .= "SP 4: $sp_4<br/>\n";
  $valuesStr .= "TP 1: $tp_1<br/>\n";
  $valuesStr .= "TP 2: $tp_2<br/>\n";
  $valuesStr .= "TP 3: $tp_3<br/>\n";
  $valuesStr .= "TP 4: $tp_4<br/>\n";
  $valuesStr .= "TP 5: $tp_5<br/>\n";
  $valuesStr .= "TP 6: $tp_6<br/>\n";
  $valuesStr .= "File: $scriptFileName<br/>\n";
  $valuesStr .= "Dir: $scriptFileDir<br/>\n";
  $valuesStr .= "Short Title: $shortTitle";
  $valuesStr .= "</p>\n\n";

  /*
  Place the connection values into $valuesStr.
  */
  $valuesStr .= "<p>";
  $connection = $_SESSION['CPSAK_connection'];
  foreach ($connection as $row) {
    $temp1 = $row['composite'];
    $temp2 = $row['nextPhrase'];
    $temp3 = $row['isComplete'];
    $temp1 = stripslashes($temp1);
    $temp2 = stripslashes($temp2);
    $valuesStr .= "Composite: $temp1<br/>\n";
    $valuesStr .= "Next Phrase: $temp2<br/>\n";
    $valuesStr .= "Is Complete: $temp3<br/>\n<br/>\n";
  }
  $valuesStr .= "</p>\n\n";


  /*
  Construct page:
  */
  site_header('Create Plus Secure a KDA');
  $php_self = $_SERVER['PHP_SELF'];

  $submitToken = time();
  $_SESSION['CPSAK_submitToken'] = $submitToken;

  $page_str = <<<EOPAGESTR

<h2>Step Two</h2>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Do you want to proceed?</legend>
  <div>
    <input type="hidden" name="submitToken" value="$submitToken">
  </div>
  <div class="buttonarea">
    <input type="submit" name="cancel" value="Cancel"/>
    <input type="submit" name="submit" value="Continue"/>
  </div>
  </fieldset>
</form>

<p>The following values have been retrieved for confirmation:</p>

$valuesStr

EOPAGESTR;
  echo $page_str;
  site_footer();
  return;
}




function makeSureWeHaveAllInfo() {
/*
Well, what kind of information do we need in order to be able to secure
the nodes which make up the KDA?
  We need an array which has all the phrases plus the KDA id as the last
  element.
*/
  $sp = $_SESSION['CPSAK_SP_array'];
  $tp = $_SESSION['CPSAK_TP_array'];
  $id = $_SESSION['CPSAK_id'];
  $theArrayWeWant = array_merge($sp, $tp);
  $theArrayWeWant[] = $id;

  $_SESSION['CPSAK_ArrOfPhrasesPlusId'] = $theArrayWeWant;
  return;     
}




function validatePhrasesMakeExistingNodes() {
/*
Instructions for writing this function:
Use $_SESSION['CPSAK_phrase_array'] to get phrases.
You see, the array of phrases makes up a series of composite/nextPhrase
pairs. This function will go through each pair in the series to make
sure it is found in the kds_associate database table. If any pair in the
series is not found in the table then the script will error out.
Oh! By-the-way. We will need this series of pairs later. So, save it in
a session variable. Also, we will need the kds_associate.id for later on.
This will be taken care of by elementIsFound function.
*/
  /*
  Create an array called $series which looks like this:
  $series[0] has two elements $series[0]['composite']
                          and $series[0]['nextPhrase'].
  $series[1] has two elements $series[1]['composite']
                          and $series[1]['nextPhrase'].
  and so on.
  */
  global $series;
  $series = array();
  $composite = "";
  $nextPhrase = "";
  $phrases = $_SESSION['CPSAK_ArrOfPhrasesPlusId'];

  foreach ($phrases as $phrase) {
    $temp = array();
    $temp['composite'] = $composite;
    $temp['nextPhrase'] = $phrase;
    $series[] = $temp;
    $composite .= ":::" . $phrase;
  }

  /*
  Go through $series and kill the script if any of its elements are not found
  in any row of the kds_associate database table.
  */
  foreach ($series as $key => $element) {
    if (!elementIsFound($key, $element)) {
      form_destroy();
      die('One of the subnodes was not found. -Programmer.');
    }
  }

  $_SESSION['CPSAK_series'] = $series;
  return;
}




function elementIsFound($key_IN, $element_IN) {
/*
Return TRUE if the element of the series is found in kds_associate.
Otherwise, return FALSE. Also, this function has a side effect. It
will add a member called associateId to the element of series.
This function is called from validatePhrasesMakeExistingNodes.
*/
  global $series;
  $composite = $element_IN['composite'];
  $composite = addslashes($composite);
  $nextPhrase = $element_IN['nextPhrase'];
  $nextPhrase = addslashes($nextPhrase);
  $query = "SELECT id
            FROM kds_associate
            WHERE composite = '$composite' AND nextPhrase = '$nextPhrase'";
  $result = mysql_query($query);
  if (!$result) {
    form_destroy();
    die('Query failed. Err: 8812237. -Programmer.');
  }
  if (mysql_num_rows($result) < 1) {
    return FALSE;
  } else {
    $series[$key_IN]['associateId'] = mysql_result($result, 0, 0);
    return TRUE;
  }
}




function explainSecuringForm() {
  $page_str = <<<EOPAGESTR
<p>Specify which user types will have access to each node.</p>

EOPAGESTR;
  echo $page_str;
  return;
}




function gatherInfoForSecuringForm() {
/*
Instructions to self:
What info do we have so far?
  $_SESSION['CPSAK_series']  -- An array of elements each is composite/nextPhrase
                                for all sub-nodes.
  $_SESSION['CPSAK_phrase_array']  -- An array of all phrases and kda id.
What info do we need?
  - The user types in the system.
  - Which user types have already been assigned to each element/node.
What are we going to do?
  $_SESSION['CPSAK_series'] will acquire an array of user types (id only) for
  each element.
  $_SESSION['CPSAK_userTypes'] will be an array of user type elements.
*/
  /*
  Get all user type info from the database.
  */
  $userType_inSystem = array();
  $query = "SELECT id, label
            FROM available_user_types";
  $result = mysql_query($query);
  if (!$result) {
    form_destroy();
    die('Query failed. Err: 3912137. -Programmer.');
  }
  if (mysql_num_rows($result) < 1) {
    form_destroy();
    die("Unable to get any user types from database. 21157244 -Programmer.");
  }
  while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
    $userType_inSystem[] = $row;
  }
  $_SESSION['CPSAK_userTypes'] = $userType_inSystem;
  /*
  Get user types for each element of $series. This info is stored in the database
  table: kds_kdaToUserType which correlates each node id with a user type id.
  Since we don't have node ids (but instead have composite/nextPhrase), we will
  be using an SQL join.
  */
  $series = $_SESSION['CPSAK_series'];
  foreach ($series as $elemKey => $elemVal) {
    $composite = $elemVal['composite'];
    $composite = addslashes($composite);
    $nextPhrase = $elemVal['nextPhrase'];
    $nextPhrase = addslashes($nextPhrase);
    $query = "SELECT kds_kdaToUserType.userTypeId
              FROM kds_associate INNER JOIN kds_kdaToUserType
              ON kds_associate.id = kds_kdaToUserType.associateId
              WHERE kds_associate.composite = '$composite'
              AND kds_associate.nextPhrase = '$nextPhrase'";
    $result = mysql_query($query);
    if (!$result) {
      form_destroy();
      die('Query failed. Err: 26585662221. -Programmer.');
    }
    $series[$elemKey]['userTypeIds'] = array();
    while ($row = mysql_fetch_row($result)) {
      $series[$elemKey]['userTypeIds'][] = $row[0];
    }
  }
  $_SESSION['CPSAK_series'] = $series;
  return;
}




function presentSecuringForm() {
/*
Present form which presents all the sub-nodes along with a choice of
which user types they can be authorized for. The choice will be
presented as check boxes. The user types that are already authorized
will appear as already checked.
*/
  $series = $_SESSION['CPSAK_series'];
  global $formContent;
  $formContent = "";
  
  /*
  Generate the string $formContent.
  */
  foreach ($series as $nodeKey => $nodeElem) {
    $composite = $nodeElem['composite'];
    $nextPhrase = $nodeElem['nextPhrase'];
    $userTypeIds = $nodeElem['userTypeIds'];
    addNodeHeaderStringToFormContent($composite, $nextPhrase);
    addCheckBoxesForNodeToFormContent($nodeKey, $userTypeIds);
  }


  $submitToken = time();
  $_SESSION['CPSAK_submitToken'] = $submitToken;


  $php_self = $_SERVER['PHP_SELF'];
  $page_str = <<<EOPAGESTR

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Authorize Node Users by User Type</legend>
$formContent
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
  return;
}




function addNodeHeaderStringToFormContent($composite_IN, $nextPhrase_IN) {
/*
Make something modeled after this:
    <p><b>MVA CDL Status</b></p>
However, the paragraph content will look like the kind of node specifiers
that get presented to a web user. You know the kind that has these
> symbols separating all the node elements. Then, append it to the global
form string.
*/
  global $formContent;

  $formContent .= "    <p><b>";
  /*
  Append $composite_IN but with the ":::" replaced with " > ".
  */
  $composite_IN = str_replace(":::", " > ", $composite_IN);
  $formContent .= $composite_IN . " > " . $nextPhrase_IN;
  $formContent .= "</b></p>\n";
  return;
}




function addCheckBoxesForNodeToFormContent($nodeKey_IN, $userTypeIds_IN) {
/*
Make something modeled after this:
    <div>
      <input type="checkbox" name="cdlAB" id="cdlAB" value="1"/>
      <label for="cdlAB">School Bus Contractor</label>
    </div>
    <div>
      <input type="checkbox" name="dCar" id="dCar" value="1"/>
      <label for="dCar">Driven car over 5 years</label>
    </div>
Then append it to the global form string.
*/
  global $formContent;
  $userType_inSystem = $_SESSION['CPSAK_userTypes'];

  foreach ($userType_inSystem as $ut_value) {
    $ut_id = $ut_value['id'];
    $ut_label = $ut_value['label'];
    // Create a checkbox for that node/userType and add it to the
    // form content string.
    if (shouldBeUnchecked($ut_id, $userTypeIds_IN)) {
      $formContent .= "    <div>\n";
      $formContent .= "      <input type=\"checkbox\" name=\"box[$nodeKey_IN][$ut_id]\" " .
                          "id=\"box[$nodeKey_IN][$ut_id]\" value=\"1\"/>\n";
      $formContent .= "      <label for=\"box[$nodeKey_IN][$ut_id]\">$ut_label</label>\n";
      $formContent .= "    </dive>\n";
    } else {
      $formContent .= "    <div>\n";
      $formContent .= "      <input type=\"checkbox\" name=\"box[$nodeKey_IN][$ut_id]\" " .
                          "id=\"box[$nodeKey_IN][$ut_id]\" value=\"1\" checked=\"checked\"/>\n";
      $formContent .= "      <label for=\"box[$nodeKey_IN][$ut_id]\">$ut_label</label>\n";
      $formContent .= "    </dive>\n";
    }
  }

  return;
}




function shouldBeUnchecked($id, $arrOfIds) {
/*
The check box should be unchecked if $id is not found in $arrOfIds.
This function returns TRUE if $id is not found in $arrOfIds.
This function returns FALSE if $id is found in in $arrOfIds.
*/
  $isFound = FALSE;
  foreach ($arrOfIds as $idNodeHas) {
    if ($id == $idNodeHas) {
      $isFound = TRUE;
    }
  }
  if ($isFound) {
    return FALSE;
  } else {
    return TRUE;
  }
}




function validateSecuringForm() {
/*
The purpose of this function is to:
  - Take the post values and put them in standard variables.
  - Validate the values received from the form.
  - Put values into session variable.
The value of $_POST['box'] should be an array with two indices.
The first index specifies which sub-node in series we are talking about.
The second index specifies which user type id we are talking about.
The value of the array element specifies whether the user checked its
box or not. A value of one indicates checked.
*/
  $series = $_SESSION['CPSAK_series'];

  if (isset($_POST['submitToken'])) {
    $submitToken = $_POST['submitToken'];
  } else {
    $submitToken = "";
  }
  if ($submitToken != $_SESSION['CPSAK_submitToken']) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  if (isset($_POST['box'])) {
    /*
    For each element of series create an array which holds a list of
    user type ids which were submitted. This array will become the
    'userTypeIdsSubmitted' member of the series element.
    */
    foreach ($series as $nodeKey => $nodeElem) {
      if (isset($_POST['box'][$nodeKey]) AND is_array($_POST['box'][$nodeKey])) {
        $submittedIdCkBoxes = $_POST['box'][$nodeKey];
        createArrOfSubmittedIds($submittedIdCkBoxes, $nodeKey);
      }
    }
  } else {
    form_destroy();
    die('No checkbox form values submitted. -Programmer.');
  }

  return;
}




function createArrOfSubmittedIds($ckBoxes, $nodeKey_IN) {
/*
This function is handed an array $ckBoxes.
$ckBoxes is numerically indexed.
Each index corresponds to a user type id in the system.
Each value (if it exists) is either a one (1) or an empty string "".

The task of this function is to build the 'userTypeIdsSubmitted' member
of the series element having key value $nodeKey_IN.

What is the 'userTypeIdsSubmitted' member of the series element having
key value $nodeKey_IN?
It is a numerically indexed array.
Indexing goes from 0 to n-1. Where n is the number of elements.
Each value is a user type id that was checked in the form.

Make sure the ids being stored into the newly created array are unique
and are actual user types in the system. ACTUALLY THIS WILL BE ASSURED
A LITTLE FARTHER DOWN THE LINE. HOWEVER WE NEED TO DO SOME VALIDATION.
*/

  $userTypeIdsSubmitted = array();

  foreach ($ckBoxes as $userId => $ckBoxVal) {
    if (!empty($ckBoxVal) AND $ckBoxVal == 1) {
      $userTypeIdsSubmitted[] = $userId;
    }
  }

  $_SESSION['CPSAK_series'][$nodeKey_IN]['userTypeIdsSubmitted'] = $userTypeIdsSubmitted;
  return;
}




function insertNewUserTypesForNodes() {
/*
Each element of series has the following two members:
['userTypeIds']           -- ones that are already assigned to this node.
['userTypeIdsSubmitted']  -- ones selected by the user of the form.

Here, we want to see if the user selected any user types which were not
already assigned to this node. And, if they are, then insert a record into
the table kds_kdaToUserType having the associateId value and userTypeId
value which establishes this relationship.
*/
  $series = $_SESSION['CPSAK_series'];
  foreach ($series as $nodeElem) {
    $userTypeIds = $nodeElem['userTypeIds'];
    $userTypeIdsSubmitted = $nodeElem['userTypeIdsSubmitted'];
    $associateId = $nodeElem['associateId'];
    foreach ($userTypeIdsSubmitted as $idVal) {
      if (!in_array($idVal, $userTypeIds)) {
        // Insert row.
        $query = "INSERT INTO kds_kdaToUserType (associateId, userTypeId)
                  VALUES ('$associateId', '$idVal')";
        $result = mysql_query($query);
        if (!$result OR mysql_affected_rows() <1) {
          form_destroy();
          die('Insert failed 772168175. -Programmer.');
        }
      }
    }
  }
  return;
}




function deleteUserTypesForNodes() {
/*
Each element of series has the following two members:
['userTypeIds']           -- ones that are already assigned to this node.
['userTypeIdsSubmitted']  -- ones selected by the user of the form.

Here, we want to see if the array ['userTypeIds'] has ids that are missing
from array ['userTypeIdsSubmitted']. And, if there are, then delete the
records in the table kds_kdaToUserType which correspond to them.
*/
  $series = $_SESSION['CPSAK_series'];
  foreach ($series as $nodeElem) {
    $userTypeIds = $nodeElem['userTypeIds'];
    $userTypeIdsSubmitted = $nodeElem['userTypeIdsSubmitted'];
    $associateId = $nodeElem['associateId'];
    foreach ($userTypeIds as $idVal) {
      if (!in_array($idVal, $userTypeIdsSubmitted)) {
        // delete row.
        $query = "DELETE FROM kds_kdaToUserType
                  WHERE associateId = '$associateId'
                  AND userTypeId = '$idVal'";
        $result = mysql_query($query);
        if (!$result OR mysql_affected_rows() <1) {
          form_destroy();
          die('Delete failed 344168175. -Programmer.');
        }
      }
    }
  }
  return;
}




function confirmWhatScriptThinksItDid() {
  if (isset($_SESSION['CPSAK_status_message'])) {
    $status_message = $_SESSION['CPSAK_status_message'];
  } else {
    $status_message = "";
  }

  if ( empty($status_message) ) {
    $message_str = "";
  } else {
    $message_str = "<p class=\"errmsg\">$status_message</p>";
  }

  $page_str = <<<EOPAGESTR

$message_str

<p>The script believes it has made user_type privilege assignments
for the nodes according to what you have selected. If you
want to confirm then do so by either navigating to the KDA while
logged in as the user_type which you gave privilege to
or by running the script which does securing.</p>

EOPAGESTR;
  echo $page_str;
  return;
}




function recommendWhatToDoNext() {
  $shortTitle = $_SESSION['CPSAK_shortTitle'];
  $dir = $_SESSION['CPSAK_scriptFileDir'];
  $fileName = $_SESSION['CPSAK_scriptFileName'];
  $page_str = <<<EOPAGESTR

<p>Assuming you are going to add the script file now, do the following:</p>

<ul>
  <li>Open TextWrangler</li>
  <li>Open template for KDA.</li>
  <li>Change title to: $shortTitle</li>
  <li>Do Save As.</li>
  <li>Change directory to: $dir</li>
  <li>Change file name to: $fileName</li>
  <li>Click Save to save locally.</li>
  <li>Do Save Copy to ftp.</li>
  <li>Test navigation link.</li>
  <li>Export database to file for backup.</li>
</ul>

EOPAGESTR;
  echo $page_str;

  return;
}




function createPageString() {
/*
Create the page string and make a session variable out of it.
This is a page string for the KDA being created.
*/
  // These are the variables we will need here.

  $shortTitle = $_SESSION['CPSAK_shortTitle'];
  
  $page_str = '<?php'. "\n";

  $temp_str = <<<EOPAGESTR
error_reporting(E_ALL);
ini_set("display_errors", 1);

\$docRoot = \$_SERVER["DOCUMENT_ROOT"];

require_once("\$docRoot/web/includes/login_funcs.php");
require_once("\$docRoot/web/includes/db_vars.php");
require_once("\$docRoot/web/includes/header_footer.php");

// User must be logged in and be someone special.
if (!user_isloggedin() || !user_type_cookieIsSet()) {
  // Redirect to login page.
  \$host = \$_SERVER['HTTP_HOST'];
  header("Location: http://{\$host}/web/login.php");
  exit;
}
if (\$_COOKIE['user_type'] != 1) {
  die('Script aborted #3098. -Programmer.');
}


site_header("$shortTitle");

\$page_str = <<<EOPAGESTR5

<p>Sample</p>

<pre>
Sample
</pre>

EOPAGESTR5;
echo \$page_str;

site_footer();


EOPAGESTR;
  $page_str .= $temp_str;
  $page_str .= '?>';
  $_SESSION['CPSAK_pageStr'] = $page_str;
  return;
}




function saveFileToServer() {
/*
First it checks to make shure the file doesn't already exist. Then,
it writes the file to the directory where it belongs.
*/
  $pageStr = $_SESSION['CPSAK_pageStr'];
  $docRoot = $_SERVER["DOCUMENT_ROOT"];
  $scriptFileDir = $_SESSION['CPSAK_scriptFileDir'];
  $scriptFileName = $_SESSION['CPSAK_scriptFileName'];
  $locatorStr = $docRoot . $scriptFileDir . $scriptFileName;

  if (file_exists($locatorStr)) {
    $_SESSION['CPSAK_status_message'] .= "No file written. File already exists.<br />\n";
    clearstatcache();
    return;
  } else {
    touch($locatorStr);
    if (!chmod($locatorStr, 0646)) {
      $_SESSION['CPSAK_status_message'] .= "Not successful at chmod!<br />\n";
    }
    $fp = fopen($locatorStr, "w");
    $fout = fwrite($fp, $pageStr);
    if ($fout != strlen($pageStr)) {
      $_SESSION['CPSAK_status_message'] .= "File write failed!<br />\n";
    }
    fclose($fp);
  }
  clearstatcache();
  return;
}




function presentDownloadForm() {
/*
Outputs an HTML string for a form which asks:
Do you want to "Continue" or "Quit"?
And informs that if user continues the file download will begin.
*/

  $submitToken = time();
  $_SESSION['CPSAK_submitToken'] = $submitToken;

  $php_self = $_SERVER['PHP_SELF'];

  $userform_str = <<<EOUSERFORMSTR

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>What do you want to do?</legend>
  <p>If you continue, a file download will be initiated so you can have a copy locally.
  Also, remember to save a copy of the database to your computer;
  Also, do an incremental backup;
  Also, test the navigation to the file.</p>
  <div>
    <input type="hidden" name="submitToken" value="$submitToken">
  </div>
  <div class="buttonarea">
    <input type="submit" name="cancel" value="Quit"/>
    <input type="submit" name="submit" value="Continue"/>
  </div>
  </fieldset>
</form>

EOUSERFORMSTR;
  echo $userform_str;
  return;
}




function sendFileToBrowser() {
/*
This function uses the technique from the book which showed me how to send
a file to the browser in such a way that it will become a download.
*/
  $docRoot = $_SERVER["DOCUMENT_ROOT"];
  $scriptFileDir = $_SESSION['CPSAK_scriptFileDir'];
  $scriptFileName = $_SESSION['CPSAK_scriptFileName'];
  $locatorStr = $docRoot . $scriptFileDir . $scriptFileName;

  if (isset($_POST['submitToken'])) {
    $submitToken = $_POST['submitToken'];
  } else {
    $submitToken = "";
  }
  if ($submitToken != $_SESSION['CPSAK_submitToken']) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  $fp = fopen($locatorStr, "r");
  header("Content-Type:text/plain;charset=utf-8");
  header("Content-Disposition:attachment;filename=$scriptFileName");
  header("Content-Transfer-Encoding:binary");
  fpassthru($fp);
  fclose($fp);

  return;
}

?>