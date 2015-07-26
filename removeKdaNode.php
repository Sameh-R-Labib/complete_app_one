<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
REMOVE KDA NODE
---------------
This script helps me unclutter the navigation system by allowing me to
remove nodes which are no longer needed or wanted.
*/

session_start();

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

if (isSet($_SESSION['RKN_mode'])) {
  $mode = $_SESSION['RKN_mode'];
} else {
  $mode = 'stageOne';
}


if ($mode == 'stageOne') {
  site_header('Remove KDA Node');
  explain_the_script();
  explain_phrase_input();
  present_phrase_input_form();
  site_footer();
  $_SESSION['RKN_mode'] = 'process the form';
} elseif ($mode == 'process the form') {
  processTheForm();
  $_SESSION['RKN_NodeIsKDA'] = nodeIsKda();// nodeIsKda() will error out
                                           // if node is not a node at all.
                                           // Also, gets $_SESSION['RKN_nodeId'].
  $node_is_KDA = $_SESSION['RKN_NodeIsKDA'];
  if ($node_is_KDA) {
    site_header('Remove KDA Node');
    askTheUserToMakeSure();
    presentForm_DeleteOrNot();
    site_footer();
    $_SESSION['RKN_mode'] = 'delete or not';
  } else {
    $_SESSION['RKN_removed_kda_rec'] = "NO";
    $_SESSION['RKN_nodeHasChildren'] = nodeHasChildren();
    $node_has_children = $_SESSION['RKN_nodeHasChildren'];
    if ($node_has_children) {
      site_header('Remove KDA Node');
      informHasChildren_canNotRemove();
      site_footer();
      form_destroy();
    } else {
      deleteSecurityRecords();
      deleteAssociateRecord();
      site_header('Remove KDA Node');
      informWasRemoved();
      site_footer();
      form_destroy();
    }
  }

} elseif ($mode == 'delete or not') {
  deleteSecurityRecords();
  deleteAssociateRecord();
  deleteKdaRecord();
  site_header('Remove KDA Node');
  informWasRemoved();
  site_footer();
  form_destroy();
} else {
  form_destroy();
  die('The script died for reason # 294875654. -Programmer.');
}




function form_destroy() {
/*
Re-set all $_SESSION variables for this form.
*/
  $_SESSION['RKN_mode'] = 'stageOne';
  $_SESSION['RKN_NodeIsKDA'] = "";
  $_SESSION['RKN_nodeHasChildren'] = "";
  $_SESSION['RKN_phrase_array'] = array();
  $_SESSION['RKN_nodeId'] = NULL;
  $_SESSION['RKN_removed_security_recs'] = "NO";
  $_SESSION['RKN_removed_associate_rec'] = "NO";
  $_SESSION['RKN_removed_kda_rec'] = "NO";
  $_SESSION['RKN_submitToken'] = "";
}




function explain_the_script() {
  $page_str = <<<EOPAGESTR

<p>A node is an association table record. What makes us call it a node
is the fact that it is used to form a link which leads to one of two things:
A. another bunch of links, or B. a kda script.
In this script you will specify a node.This script allows you to remove this
node which is no longer needed. Type A nodes must NOT have subdirectories in
order for them to qualify for removal. When a type B node is removed its
kda table record will also be removed. For both types of nodes the security
table records associated with it will also be removed.</p>

EOPAGESTR;
  echo $page_str;
  return;
}




function explain_phrase_input() {
  $page_str = <<<EOPAGESTR

<p>The form below is layed out as if you were specifying a KDA record. However,
you will be using it to specify a node. A node contains a composite and a nextPhrase
field. You will specify those using this form. You will specify phrases (the last
of which is the nextPhrase value). It doesn't matter which input fields you use
as long as the phrases are in correct order and you reserve the kda id field
for id values only.</p>

EOPAGESTR;
  echo $page_str;
  return;
}




function present_phrase_input_form() {
/*
Present form which allows input of the node described above in
explain_phrase_input().
*/
  /*
  Manage protection from aborted form since code uses sessions.
  In other words the code which validates the values received from a
  submitted form needs to know that it is not executing after the user
  has come back after a previously abandoned instance of the script.
  */
  $submitToken = time();
  $_SESSION['RKN_submitToken'] = $submitToken;

  $php_self = $_SERVER['PHP_SELF'];
  $page_str = <<<EOPAGESTR

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Specify the Node:</legend>
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




function processTheForm() {
/*
Take the (up to 11 and at least 1) strings submitted by the form and:
  - Put them into variables.
  - Validate them.
  - Pack the ones that exist into an array.
  - Make that array into a SESSION variable.
  - Compose the composite and nextPhrase.
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
  if ($submitToken != $_SESSION['RKN_submitToken']) {
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

  $_SESSION['RKN_phrase_array'] = $tempArr;

  if (empty($tempArr)) {
    form_destroy();
    die('You need to supply at least one phrase (132116976). -Programmer.');
  }
  
  composeCompositeAndNextPhrase();
  return;
}




function composeCompositeAndNextPhrase() {
/*
This function takes $_SESSION['RKN_phrase_array'] and uses it to compose the following:
  - $_SESSION['RKN_composite']
  - $_SESSION['RKN_nextPhrase']
*/
  // how many elements in $_SESSION['RKN_phrase_array']?
  $phrase_array = $_SESSION['RKN_phrase_array'];
  $n = count($phrase_array);
  $composite = "";
  $nextPhrase = "";
  for ($i=0; $i<=($n-2); $i++) {
    $composite .= ":::" . $phrase_array[$i];
  }
  $nextPhrase = $phrase_array[$i];
  $_SESSION['RKN_composite'] = $composite;
  $_SESSION['RKN_nextPhrase'] = $nextPhrase;
  return;
}




function nodeIsKda() {
/*
Returns TRUE if node is a KDA node. Otherwise, returns FALSE.
If not a node then EXIT().
Gets $_SESSION['RKN_nodeId'].
*/
  $composite  = $_SESSION['RKN_composite'];
  $nextPhrase = $_SESSION['RKN_nextPhrase'];

  $composite = addslashes($composite);
  $nextPhrase = addslashes($nextPhrase);

  $query = "SELECT id, isComplete
            FROM kds_associate
            WHERE composite = '$composite' AND nextPhrase = '$nextPhrase'";
  $result = mysql_query($query);
  if (!$result) {
    form_destroy();
    die('Query failed. Err: 8812237. -Programmer.');
  }
  if (mysql_num_rows($result) < 1) {
    form_destroy();
    die('Node not found. Err: 532635217. -Programmer.');
  }
  
  $_SESSION['RKN_nodeId'] = mysql_result($result, 0, 0);
  $isComplete = mysql_result($result, 0, 1);;
  
  if ($isComplete == 1) {
    return TRUE;
  } else {
    return FALSE;
  }
}




function askTheUserToMakeSure() {
  $page_str = <<<EOPAGESTR

<p>Please make sure the node for this KDA is no longer needed so
access to it is not lost.</p>

EOPAGESTR;
  echo $page_str;
  return;
}




function presentForm_DeleteOrNot() {
  /*
  Manage protection from aborted form since code uses sessions.
  In other words the code which validates the values received from a
  submitted form needs to know that it is not executing after the user
  has come back after a previously abandoned instance of the script.
  */
  $submitToken = time();
  $_SESSION['RKN_submitToken'] = $submitToken;
  $php_self = $_SERVER['PHP_SELF'];
  $page_str = <<<EOPAGESTR

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>Do you want to delete it?</legend>
  <div>
    <input type="hidden" name="submitToken" value="$submitToken">
  </div>
  <div class="buttonarea">
    <input type="submit" name="cancel" value="No/Cancel"/>
    <input type="submit" name="submit" value="Yes/Delete"/>
  </div>
  </fieldset>
</form>

EOPAGESTR;
  echo $page_str;
  return;
}




function nodeHasChildren() {
/*
Returns TRUE if the node makes a link to other node links.
Otherwise, returns FALSE.

The way this will be done is:
  - We will combine the composite and nextPhrase for the current
    node to form a new composite.
  - We will see if there are any kds_associate records which contain
    this new composite.
  - If the new composite is found then the node has children. Return
    TRUE. Otherwise, return FALSE.
*/
  $composite  = $_SESSION['RKN_composite'];
  $nextPhrase = $_SESSION['RKN_nextPhrase'];
  $composite = addslashes($composite);
  $nextPhrase = addslashes($nextPhrase);
  $newComposite = $composite . ":::" . $nextPhrase;
  $query = "SELECT id
            FROM kds_associate
            WHERE composite='$newComposite'";
  $result = mysql_query($query);
  if (!$result) {
    form_destroy();
    die('Query failed. Err: 91434186. -Programmer.');
  }
  if (mysql_num_rows($result) < 1) {
    return FALSE;
  } else {
    return TRUE;
  }
}




function informHasChildren_canNotRemove() {
  $page_str = <<<EOPAGESTR

<p class="errmsg">This node has children. It can't be removed.</p>

EOPAGESTR;
  echo $page_str;
  return;
}




function informWasRemoved() {
  $m = $_SESSION['RKN_removed_associate_rec'];
  $n = $_SESSION['RKN_removed_security_recs'];
  $o =   $_SESSION['RKN_removed_kda_rec'];
  $page_str = <<<EOPAGESTR

<p>$m node, $n security record(s) and $o kda record(s) have been
removed.</p>

EOPAGESTR;
  echo $page_str;
  return;
}





function deleteSecurityRecords() {
/*
This function will delete any record in the kds_kdaToUserType table
which has an associateId value of $_SESSION['RKN_nodeId']. Also,
it records the quantity of those records which were removed into
$_SESSION['RKN_removed_security_recs'].
*/
  $associateId = $_SESSION['RKN_nodeId'];
  $query = "DELETE FROM kds_kdaToUserType
            WHERE associateId='$associateId'";
  $result = mysql_query($query);
  if (!$result) {
    form_destroy();
    die('Delete failed. Err: 8514115463. -Programmer.');
  }
  $_SESSION['RKN_removed_security_recs'] = mysql_affected_rows(); 
  return;
}




function deleteAssociateRecord() {
  $id = $_SESSION['RKN_nodeId'];
  $query = "DELETE FROM kds_associate
            WHERE id='$id'";
  $result = mysql_query($query);
  if (!$result) {
    form_destroy();
    die('Delete failed. Err: 795847693216. -Programmer.');
  }
  $_SESSION['RKN_removed_associate_rec'] = mysql_affected_rows(); 
  return;
}




function deleteKdaRecord() {
  $id = $_SESSION['RKN_nextPhrase'];
  $query = "DELETE FROM kds_kda
            WHERE id='$id'";
  $result = mysql_query($query);
  if (!$result) {
    form_destroy();
    die('Delete failed. Err: 624871594. -Programmer.');
  }
  $_SESSION['RKN_removed_kda_rec'] = mysql_affected_rows(); 
  return;
}

?>