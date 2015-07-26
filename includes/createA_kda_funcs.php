<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
This file is for inclusion in createA_kda.php.
*/

function form_destroy() {
/*
Reset all $_SESSION variables for this form to their default values.
*/
  $_SESSION['CAK_mode'] = 'stageOne';
  $_SESSION['CAK_SP_array'] = array();
  $_SESSION['CAK_TP_array'] = array();
  $_SESSION['CAK_scriptFileName'] = "";
  $_SESSION['CAK_scriptFileDir'] = "";
  $_SESSION['CAK_shortTitle'] = "";
  $_SESSION['CAK_id'] = NULL;
  $_SESSION['CAK_kdaFromDB'] = array();
  $_SESSION['CAK_connection'] = array();
  $_SESSION['CAK_submitToken'] = "";

  return;
}


function presentFormSP() {
/*
Display the following paragraph:
  This form may only be used by the owner of this website. This form is
  for creating a KDA (Know Do Article) record and it's associations.
  Please, give it some thought before filling out the phrase fields.
  Duplication of all beginnings of phrases may cause the record not to
  be created by the database. In this step you have the opportunity to
  supply up to four subject phrases (SP). Order them hierarchically.
Display a form having four text input boxes.
*/
  /*
  Manage protection from aborted form since code uses sessions.
  In other words the code which validates the values received from a
  submitted form needs to know that it is not executing after the user
  has come back after a previously abandoned instance of the script.
  */
  $submitToken = time();
  $_SESSION['CAK_submitToken'] = $submitToken;

  /*
  Construct page:
  */
  site_header('Create A KDA');
  
  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];

  $page_str = <<<EOPAGESTR

<h2>Step One</h2>

<p>This form may only be used by the owner of this website. This form is
for creating a KDA (Know Do Article) record and it's associations.
Please, give it some thought before filling out the phrase fields.
Duplication of all beginnings of phrases may cause the record not to
be created by the database. In this step you have the opportunity to
supply up to four subject phrases (SP). Order them hierarchically.
NOTE: You are not allowed to have ':::' in your phrase string.</p>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Subject Phrases:</legend>
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




function getValidateSPs() {
/*
Get the SPs. Validate. Make sure strings not too long.
Make sure strings do not contain ':::'.
Stripslashes if get_magic_quotes_gpc() is TRUE.
Trim white-space.
Deal with blank phrases interspersed among the phrases.
Store them in $_SESSION.
*/
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
  if ($submitToken != $_SESSION['CAK_submitToken']) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

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

  /*
  If get_magic_quotes_gpc() is TRUE then addslashes() is automatically applied to text
  strings. Do I need to do something now to deal with this?
  
  Well these strings are going to be inserted into the database eventually. However,
  when I put them in $_SESSION or validate them I'm better off not having addslashes()
  applied.
  */
  if ( get_magic_quotes_gpc() ) {
    $sp_1 = stripslashes($sp_1);
    $sp_2 = stripslashes($sp_2);
    $sp_3 = stripslashes($sp_3);
    $sp_4 = stripslashes($sp_4);
  }

  // Trim white space.
  $sp_1 = trim($sp_1);
  $sp_2 = trim($sp_2);
  $sp_3 = trim($sp_3);
  $sp_4 = trim($sp_4);

  // Make sure strings not too long.
  if (strlen($sp_1) > 36 OR strlen($sp_2) > 36 OR strlen($sp_3) > 36 OR strlen($sp_4) > 36) {
    form_destroy();
    die('Err 7980365455. -Programmer.');
  }
  
  /*
  Make sure SP strings do no contain ':::'.
  */
  if (strpos($sp_1, ':::') !== FALSE OR strpos($sp_2, ':::') !== FALSE
  OR strpos($sp_3, ':::') !== FALSE OR strpos($sp_4, ':::') !== FALSE) {
    form_destroy();
    die("You are not allowed to have ':::' in your phrase string (611). -Programmer.");
  }
  
  /*
  I'll store the SP values in an array and put the array in a $_SESSION variable.
  Also, I'll weed out empty strings.
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

  /*
  We should have at least one SP.
  */
  if (empty($tempArr)) {
    form_destroy();
    die('You need to supply at least one SP (132116976). -Programmer.');
  }

  $_SESSION['CAK_SP_array'] = $tempArr;
  return;
}




function presentFormTP() {
/*
Display the folowing paragraph:
  Here you are doing pretty much the same thing. However, these are topic phrases
  (TP). Try to make the sequence order of phrases heirarchical.
Provide the user with a form which allows him/her to specify up to
six TP.
*/
  /*
  Manage protection from aborted form since code uses sessions.
  In other words the code which validates the values received from a
  submitted form needs to know that it is not executing after the user
  has come back after a previously abandoned instance of the script.
  */
  $submitToken = time();
  $_SESSION['CAK_submitToken'] = $submitToken;

  /*
  Construct page:
  */
  site_header('Create A KDA');
  
  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];

  $page_str = <<<EOPAGESTR

<h2>Step Two</h2>

<p>Here you are doing pretty much the same thing. However, these are topic phrases
(TP). Try to make the sequence order of phrases heirarchical.</p>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Topic Phrases:</legend>
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




function getValidateTPs() {
/*
GEt the TPs. Validate. Make sure strings not too long.
Make sure strings do not contain ':::'.
Stripslashes if get_magic_quotes_gpc() is TRUE.
Trim white-space.
Deal with blank phrases interspersed among the phrases.
Store them in $_SESSION.
*/
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
  if ($submitToken != $_SESSION['CAK_submitToken']) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

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

  /*
  If get_magic_quotes_gpc() is TRUE then addslashes() is automatically applied to text
  strings. Do I need to do something now to deal with this?
  
  Well these strings are going to be inserted into the database eventually. However,
  when I put them in $_SESSION or validate them I'm better off not having addslashes()
  applied.
  */
  if ( get_magic_quotes_gpc() ) {
    $tp_1 = stripslashes($tp_1);
    $tp_2 = stripslashes($tp_2);
    $tp_3 = stripslashes($tp_3);
    $tp_4 = stripslashes($tp_4);
    $tp_5 = stripslashes($tp_5);
    $tp_6 = stripslashes($tp_6);
  }

  // Trim white space.
  $tp_1 = trim($tp_1);
  $tp_2 = trim($tp_2);
  $tp_3 = trim($tp_3);
  $tp_4 = trim($tp_4);
  $tp_5 = trim($tp_5);
  $tp_6 = trim($tp_6);

  // Make sure strings not too long.
  if (strlen($tp_1) > 60 OR strlen($tp_2) > 60 OR strlen($tp_3) > 60 OR strlen($tp_4) > 60
    OR strlen($tp_5) > 60 OR strlen($tp_6) > 60) {
    form_destroy();
    die('Err 7980365455. -Programmer.');
  }
  
  /*
  Make sure TP strings do no contain ':::'.
  */
  if (strpos($tp_1, ':::') !== FALSE OR strpos($tp_2, ':::') !== FALSE
  OR strpos($tp_3, ':::') !== FALSE OR strpos($tp_4, ':::') !== FALSE
  OR strpos($tp_5, ':::') !== FALSE OR strpos($tp_6, ':::') !== FALSE) {
    form_destroy();
    die("You are not allowed to have ':::' in your phrase string (518). -Programmer.");
  }
  
  /*
  I'll store the TP values in an array and put the array in a $_SESSION variable.
  Also, I'll weed out empty strings.
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

  /*
  We should have at least one TP.
  */
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
  $SP_arr = $_SESSION['CAK_SP_array'];
  $lastSP = end($SP_arr);
  $firstTP = $tempArr[0];
  if (!lastSPandFirstTP_areGood($lastSP, $firstTP)) {
    form_destroy();
    die('Your choice of last SP or first TP is not good. See comment in code. -Programmer.');
  }

  $_SESSION['CAK_TP_array'] = $tempArr;
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




function fileNameDirPathSTitle() {
/*
Provide the user with a form which allows him/her to specify a
file name, directory path and short title for the KDA.
Inform that at least a / must be present in the path string.
*/
  /*
  Manage protection from aborted form since code uses sessions.
  In other words the code which validates the values received from a
  submitted form needs to know that it is not executing after the user
  has come back after a previously abandoned instance of the script.
  */
  $submitToken = time();
  $_SESSION['CAK_submitToken'] = $submitToken;

  /*
  Construct page:
  */
  site_header('Create A KDA');
  
  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];

  $page_str = <<<EOPAGESTR

<h2>Step Three</h2>

<p>Speify the file name, path and short name for the KDA file. Note: You must enter a directory
value of '/' at the very least. The directory path is everything in a URL that comes after the
domain name and before the file name. For example: assuming the URL is
http://www.gxsam11.net/bublebath/rubberduckie.php then the directory path is /bublebath/</p>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Script File:</legend>
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




function getFileNameDirPathSTitle() {
/*
Get the file name, directory path, and short title.
Validate.
Make sure at least a / is present in the path string.
Actually, the first character has to be a /. And, the
last character has to be a /.
Store them in $_SESSION.
*/
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
  if ($submitToken != $_SESSION['CAK_submitToken']) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

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

  $_SESSION['CAK_scriptFileName'] = $scriptFileName;
  $_SESSION['CAK_scriptFileDir'] = $scriptFileDir;
  $_SESSION['CAK_shortTitle'] = $shortTitle;
  return;
}




function createKDA() {
/*
If this "same KDA record" already exists then kill the script.
Otherwise, create the KDA record.

NOTE: When I say "same KDA record" I'm talking about the phrases only.
*/

  $scriptFileName = $_SESSION['CAK_scriptFileName'];
  $scriptFileDir = $_SESSION['CAK_scriptFileDir'];
  $shortTitle = $_SESSION['CAK_shortTitle'];

  $SP_arr =  $_SESSION['CAK_SP_array'];
  $TP_arr =  $_SESSION['CAK_TP_array'];

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

  $sp = $_SESSION['CAK_SP_array'];
  $tp = $_SESSION['CAK_TP_array'];

  $id = getKdaRecordId();
  /*
  We will need this later so:
  */
  $_SESSION['CAK_id'] = $id;

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




function getKdaRecordId() {
/*
Helper for makeConnections(). Returns the kds_kda record id for the record
which was created by the script during this session.
*/
  $scriptFileName = $_SESSION['CAK_scriptFileName'];
  $scriptFileDir = $_SESSION['CAK_scriptFileDir'];
  $shortTitle = $_SESSION['CAK_shortTitle'];

  $SP_arr =  $_SESSION['CAK_SP_array'];
  $TP_arr =  $_SESSION['CAK_TP_array'];
  
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




function reloadKdaRecord() {
/*
Thus far the only kda field values (other than id) we have in $_SESSION
came from the user. Now, we will load them from the database so that we
can present them later as part of the confermation message.
*/
  $id = $_SESSION['CAK_id'];

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
  $_SESSION['CAK_kdaFromDB'] = mysql_fetch_array($result, MYSQL_ASSOC);
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

  $sp = $_SESSION['CAK_SP_array'];
  $tp = $_SESSION['CAK_TP_array'];
  $id = $_SESSION['CAK_id'];

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

  $_SESSION['CAK_connection'] = $conn_ection;
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




function presentConfirm() {
/*
Present "ALL THE VALUES" (including the id of the KDA) retrieved as a
confirmation. Inform the user that if what he/she sees is what was entered
(or agrees with it) then be confident the job is done.
*/
  $valuesStr = "";

  /*
  Place the id of the kds_kda record in $valuesStr.
  */
  $id = $_SESSION['CAK_id'];
  $valuesStr .= "<p>kda id: $id</p>\n\n";

  /*
  Place the values of the kds_kda record in $valuesStr.
  */
  $kda = $_SESSION['CAK_kdaFromDB'];
  
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
  $connection = $_SESSION['CAK_connection'];
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
  site_header('Create A KDA');

  $page_str = <<<EOPAGESTR

<h2>Step Four</h2>

<p>Don't forget to secure each new KDA composite.
The following values have been retrieved for confirmation:</p>

$valuesStr

EOPAGESTR;
  echo $page_str;
  site_footer();
  return;
}

?>