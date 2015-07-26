<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/*

*/




function form_destroy() {
/*
Reset all $_SESSION variables for this form to their default values.
*/
  $_SESSION['USSP_mode'] = 'stageOne';
  $_SESSION['USSP_prepend'] = "";
  $_SESSION['USSP_submitToken'] = "";

  return;
}




function explainPurposeOfForm() {
  $page_str = <<<EOPAGESTR
<p>The purpose of this form:</p>

<ul>
  <li>Register a prepend.</li>
  <li>Or, find out if it has been registered.</li>
</ul>

EOPAGESTR;
  echo $page_str;
  return;
}




function explainSyntaxOfPrepend() {
  $page_str = <<<EOPAGESTR
<p>Syntax of a prepend:</p>

<ul>
  <li>Case insensitive (use uppercase)</li>
  <li>Alphanumerics only.</li>
  <li>Fifteen (15) characters or less.</li>
</ul>

EOPAGESTR;
  echo $page_str;
  return;
}




function presentInputFormForPrependString() {
/*
Comments.
*/
  /*
  Manage protection from aborted form since code uses sessions.
  In other words the code which validates the values received from a
  submitted form needs to know that it is not executing after the user
  has come back after a previously abandoned instance of the script
  (another possibility is the user may have attempted to run two
  instance of the script).
  */
  $submitToken = time();
  $_SESSION['USSP_submitToken'] = $submitToken;

  $php_self = $_SERVER['PHP_SELF'];
  $page_str = <<<EOPAGESTR
<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Possible Value:</legend>
  <div>
    <label for="prepend_str" class="fixedwidth">prepend string</label>
    <input type="text" name="prepend_str" id="prepend_str" value="" size="15" maxlength="15"/>
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
  return;
}




function alreadyUsed() {
/*
Returns TRUE if already used. Otherwise, returns FALSE.
*/
  /*
  Handle the situation where we have arrived here as a result of the user
  wandering back to the script after giving up in the middle of running it
  earlier when presented with a form (or running multiple instances).
  */
  if (isset($_POST['submitToken'])) {
    $submitToken = $_POST['submitToken'];
  } else {
    $submitToken = "";
  }
  if ($submitToken != $_SESSION['USSP_submitToken']) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  if (isset($_POST['prepend_str'])) {
    $prepend = $_POST['prepend_str'];
    $_SESSION['USSP_prepend'] = $prepend;
  } else {
    form_destroy();
    die('You probably bailed last time. Start over. Err: 581649. -Programmer.');
  }
  
  /*
  Validate.
  */
  if (get_magic_quotes_gpc()) {
    $prepend = stripslashes($prepend);
  }
  $prepend = trim($prepend);
  if (strlen($prepend) > 15) {
    form_destroy();
    die('Error 390089933322. -Programmer.');
  }
  if (strlen($prepend) < 1) {
    form_destroy();
    die('You did not supply a value. Error 4515885548778. -Programmer.');
  }
  if (!ctype_alnum($prepend)) {
    form_destroy();
    die('The string is not alfanumeric. Error 228897598711. -Programmer.');
  }

  $query = "SELECT prepend
            FROM kds_prependsAlreadyUsed
            WHERE
            prepend = '$prepend'";
  $result = mysql_query($query);
  if (!$result) {
    form_destroy();
    die('Query in function failed 0465555655548. -Programmer.');
  }
  if ( mysql_num_rows($result) >= 1) {
    return TRUE;
  } else {
    return FALSE;
  }
}




function informAlreadyUsed() {
  $prepend = $_SESSION['USSP_prepend'];
  $page_str = <<<EOPAGESTR
<p>The prepend string ( $prepend ) which you specifed is already registered.
That means it is already being used by an existing script. You may try to submit
a different string.</p>

EOPAGESTR;
  echo $page_str;
  return;
}




function presentForm_NextOrCancel() {
/**/
  /*
  Manage protection from aborted form since code uses sessions.
  In other words the code which validates the values received from a
  submitted form needs to know that it is not executing after the user
  has come back after a previously abandoned instance of the script
  (another possibility is the user may have attempted to run two
  instance of the script).
  */
  $submitToken = time();
  $_SESSION['USSP_submitToken'] = $submitToken;

  $php_self = $_SERVER['PHP_SELF'];
  $page_str = <<<EOPAGESTR
<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Please Choose:</legend>
  <div>
    <input type="hidden" name="submitToken" value="$submitToken">
  </div>
  <div class="buttonarea">
    <input type="submit" name="cancel" value="Cancel"/>
    <input type="submit" name="submit" value="Try Again"/>
  </div>
  </fieldset>
</form>
EOPAGESTR;
  echo $page_str;
  return;
}




function informAvailable() {
  $prepend = $_SESSION['USSP_prepend'];
  $page_str = <<<EOPAGESTR
<p>The prepend string ( $prepend ) which you specifed is available. You may
register and use it.</p>

EOPAGESTR;
  echo $page_str;
  return;
}




function presentForm_DoYouWantToRegisterIt() {
/**/
  /*
  Manage protection from aborted form since code uses sessions.
  In other words the code which validates the values received from a
  submitted form needs to know that it is not executing after the user
  has come back after a previously abandoned instance of the script
  (another possibility is the user may have attempted to run two
  instance of the script).
  */
  $submitToken = time();
  $_SESSION['USSP_submitToken'] = $submitToken;

  $php_self = $_SERVER['PHP_SELF'];
  $page_str = <<<EOPAGESTR
<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Please Choose:</legend>
  <div>
    <input type="hidden" name="submitToken" value="$submitToken">
  </div>
  <div class="buttonarea">
    <input type="submit" name="cancel" value="Cancel"/>
    <input type="submit" name="registerIt" value="Register It"/>
  </div>
  </fieldset>
</form>
EOPAGESTR;
  echo $page_str;
  return;
}




function wantToRegisterIt() {
/*
Returns TRUE if the user indicated he/she wants to register the new prepend string.
Otherwise, returns FALSE.
*/
  /*
  Handle the situation where we have arrived here as a result of the user
  wandering back to the script after giving up in the middle of running it
  earlier when presented with a form (or running multiple instances).
  */
  if (isset($_POST['submitToken'])) {
    $submitToken = $_POST['submitToken'];
  } else {
    $submitToken = "";
  }
  if ($submitToken != $_SESSION['USSP_submitToken']) {
    form_destroy();
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['PHP_SELF'];
    header("Location: http://$host$uri");
    exit;
  }

  if (isset($_POST['registerIt'])) {
    return TRUE;
  } else {
    return FALSE;
  }
}




function insertPrepend() {
  $prepend = $_SESSION['USSP_prepend'];
  $query = "INSERT INTO kds_prependsAlreadyUsed
            SET prepend='$prepend'";
  $result = mysql_query($query);
  if (!$result || mysql_affected_rows() < 1) {
    form_destroy();
    die('Error adding new record. 0030919. -Programmer.');
  }
  return;
}




function presentConfirmation() {
  $prepend = $_SESSION['USSP_prepend'];
  $page_str = <<<EOPAGESTR
<p>The prepend string ( $prepend ) which you specified has just been successfully
registered.</p>

EOPAGESTR;
  echo $page_str;
  return;
}

?>