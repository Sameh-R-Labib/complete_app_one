<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
This is the include file for the following script:
Secure A kds_associate Record
*/




function form_destroy() {
/*
Unset all $_SESSION variables for this form.
*/
  $_SESSION['SAKAR_mode'] = 'stageOne';
  $_SESSION['SAKAR_previous_mode'] = "";
  $_SESSION['SAKAR_phrase_array'] = array();
  $_SESSION['SAKAR_series'] = array();
  $_SESSION['SAKAR_userTypes'] = array();
  $_SESSION['SAKAR_submitToken'] = "";

  return;
}




function explain_the_script() {
  $page_str = <<<EOPAGESTR

<p>A node is an association table record which specifies the "next phrase".
The next phrase may be an actual phrase or a kda id for a script. When a
user navigates the website, it is the nodes which form the links presented
to him/her. In this script you will specify a deep node. After that the
form will allow you to specify which user type can view this node's link
while navigating. Also, you will do the same for all the shallower nodes.</p>

EOPAGESTR;
  echo $page_str;
  return;
}




function explain_phrase_input() {
  $page_str = <<<EOPAGESTR
<p>The deepest possible node is one that has four subject phrases,
six title phrases, and a "next phrase" (id of kda).
Specify the deepest node you want
to secure and you'll also be able to secure the less deap ones
associated with it.</p>

EOPAGESTR;
  echo $page_str;
  return;
}




function present_phrase_input_form() {
/*
Present form which allows input of the deap node described above in
explain_phrase_input().
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
  $_SESSION['SAKAR_submitToken'] = $submitToken;

  $php_self = $_SERVER['PHP_SELF'];
  $page_str = <<<EOPAGESTR

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Deap Node:</legend>
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
    <label for="kda_id" class="fixedwidth">KDA ID</label>
    <input type="text" name="kda_id" id="kda_id" value="" size="10" maxlength="10"/>
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




function validateInputPhrases() {
/*
Take the (up to 11 and at least 1) strings submitted by the form and:
  - Put them into variables.
  - Validate them.
  - Pack the ones that exist into an array.
  - Make that array into a SESSION variable.
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
  if ($submitToken != $_SESSION['SAKAR_submitToken']) {
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
  if (isset($_POST['kda_id'])) {
    $kda_id = $_POST['kda_id'];
  } else {
    $kda_id = "";
  }


  if ( get_magic_quotes_gpc() ) {
    $sp_1 = stripslashes($sp_1);
    $sp_2 = stripslashes($sp_2);
    $sp_3 = stripslashes($sp_3);
    $sp_4 = stripslashes($sp_4);
    $tp_1 = stripslashes($tp_1);
    $tp_2 = stripslashes($tp_2);
    $tp_3 = stripslashes($tp_3);
    $tp_4 = stripslashes($tp_4);
    $tp_5 = stripslashes($tp_5);
    $tp_6 = stripslashes($tp_6);
  }
  
  $sp_1 = trim($sp_1);
  $sp_2 = trim($sp_2);
  $sp_3 = trim($sp_3);
  $sp_4 = trim($sp_4);
  $tp_1 = trim($tp_1);
  $tp_2 = trim($tp_2);
  $tp_3 = trim($tp_3);
  $tp_4 = trim($tp_4);
  $tp_5 = trim($tp_5);
  $tp_6 = trim($tp_6);
  $kda_id = trim($kda_id);
  
  if ( strlen($kda_id) > 0 and !is_numeric($kda_id)) {
    form_destroy();
    die('The KDA ID must be numeric. err: 55555550. -Programmer.');
  }

  if (strlen($sp_1) > 36 OR strlen($sp_2) > 36 OR strlen($sp_3) > 36 OR strlen($sp_4) > 36) {
    form_destroy();
    die('Err 7980365455. -Programmer.');
  }
  if (strlen($tp_1) > 60 OR strlen($tp_2) > 60 OR strlen($tp_3) > 60 OR strlen($tp_4) > 60
    OR strlen($tp_5) > 60 OR strlen($tp_6) > 60 OR strlen($kda_id) > 10) {
    form_destroy();
    die('Err 7877777455. -Programmer.');
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
  Make sure TP strings do no contain ':::'.
  */
  if (strpos($tp_1, ':::') !== FALSE OR strpos($tp_2, ':::') !== FALSE
  OR strpos($tp_3, ':::') !== FALSE OR strpos($tp_4, ':::') !== FALSE
  OR strpos($tp_5, ':::') !== FALSE OR strpos($tp_6, ':::') !== FALSE) {
    form_destroy();
    die("You are not allowed to have ':::' in your phrase string (518). -Programmer.");
  }

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
  if (!empty($kda_id)) {
    $tempArr[] = $kda_id;
  }

  $_SESSION['SAKAR_phrase_array'] = $tempArr;

  if (empty($tempArr)) {
    form_destroy();
    die('You need to supply at least one phrase (132116976). -Programmer.');
  }

  return;
}




function validatePhrasesMakeExistingNodes() {
/*
Instructions for writing this function:
Use $_SESSION['SAKAR_phrase_array'] to get phrases.
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
  $phrases = $_SESSION['SAKAR_phrase_array'];

  foreach ($phrases as $phrase) {
    $temp = array();
    $temp['composite'] = $composite;
    $temp['nextPhrase'] = $phrase;
    $series[] = $temp;
    $composite = $composite . ":::" . $phrase;
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

  $_SESSION['SAKAR_series'] = $series;
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
  $_SESSION['SAKAR_series']  -- An array of elements each is composite/nextPhrase
                                for all sub-nodes.
  $_SESSION['SAKAR_phrase_array']  -- An array of all phrases and kda id.
What info do we need?
  - The user types in the system.
  - Which user types have already been assigned to each element/node.
What are we going to do?
  $_SESSION['SAKAR_series'] will acquire an array of user types (id only) for
  each element.
  $_SESSION['SAKAR_userTypes'] will be an array of user type elements.
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
  $_SESSION['SAKAR_userTypes'] = $userType_inSystem;
  /*
  Get user types for each element of $series. This info is stored in the database
  table: kds_kdaToUserType which correlates each node id with a user type id.
  Since we don't have node ids (but instead have composite/nextPhrase), we will
  be using an SQL join.
  */
  $series = $_SESSION['SAKAR_series'];
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
  $_SESSION['SAKAR_series'] = $series;
  return;
}




function presentSecuringForm() {
/*
Present form which presents all the sub-nodes along with a choice of
which user types they can be authorized for. The choice will be
presented as check boxes. The user types that are already authorized
will appear as already checked.
*/
  $series = $_SESSION['SAKAR_series'];
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

  /*
  Manage protection from aborted form since code uses sessions.
  In other words the code which validates the values received from a
  submitted form needs to know that it is not executing after the user
  has come back after a previously abandoned instance of the script
  (another possibility is the user may have attempted to run two
  instance of the script).
  */
  $submitToken = time();
  $_SESSION['SAKAR_submitToken'] = $submitToken;


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
  $userType_inSystem = $_SESSION['SAKAR_userTypes'];

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
  $series = $_SESSION['SAKAR_series'];

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
  if ($submitToken != $_SESSION['SAKAR_submitToken']) {
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

  $_SESSION['SAKAR_series'][$nodeKey_IN]['userTypeIdsSubmitted'] = $userTypeIdsSubmitted;
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
  $series = $_SESSION['SAKAR_series'];
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
  $series = $_SESSION['SAKAR_series'];
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
  $page_str = <<<EOPAGESTR

<p>The script believes it has made user type assignments
for the nodes according to what you have selected. If you
want to confirm then repeat this script using the same
nodes as before by clicking on the appropriate button.</p>

EOPAGESTR;
  echo $page_str;
  return;
}




function presentChoiceToUseSamePhrasesOver() {
  /*
  Manage protection from aborted form since code uses sessions.
  In other words the code which validates the values received from a
  submitted form needs to know that it is not executing after the user
  has come back after a previously abandoned instance of the script
  (another possibility is the user may have attempted to run two
  instance of the script).
  */
  $submitToken = time();
  $_SESSION['SAKAR_submitToken'] = $submitToken;

  $php_self = $_SERVER['PHP_SELF'];
  $page_str = <<<EOPAGESTR

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Continue?</legend>
  <div>
    <input type="hidden" name="submitToken" value="$submitToken">
  </div>
  <div class="buttonarea">
    <input type="submit" name="cancel" value="No/Cancel"/>
    <input type="submit" name="submit" value="Yes/Continue"/>
  </div>
  </fieldset>
</form>

EOPAGESTR;
  echo $page_str;
  return;
}




function resetSomeSessionVars() {
  $_SESSION['SAKAR_series'] = array();
  $_SESSION['SAKAR_userTypes'] = array();
  return;
}




function presentSubmitButton() {
  $php_self = $_SERVER['PHP_SELF'];
  $page_str = <<<EOPAGESTR

<p>The script is now ready and waiting for one more click.</p>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Continue?</legend>
  <div class="buttonarea">
    <input type="submit" name="cancel" value="No/Cancel"/>
    <input type="submit" name="submit" value="Yes/Continue"/>
  </div>
  </fieldset>
</form>

EOPAGESTR;
  echo $page_str;
  return;
}

?>