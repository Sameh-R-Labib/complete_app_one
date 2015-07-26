<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
NAVIGATE
--------
This script is for navigating the KDS article archeive.
*/

require_once('includes/login_funcs.php');
require_once('includes/db_vars.php');
include_once('includes/header_footer.php');

global $allMatches;  // See getAllMatches function.
global $resultLinks; // makeNav_link function generates elements of this array

/*
Make sure user has a system assigned user type.
*/
if (!user_type_cookieIsSet()) {
  // Make user a Visitor.
  user_type_set_tokens(5);
  $_COOKIE['user_type'] = 5; // code in this script won't read the cookie in
                             // time so we define it here.
}

/*
Assign $composite its value. NOTE:
    1. Strings coming back from the database are automatically being stripslashed.
    2. Addslashes is automatic if get_magic_quotes_gps is true.
*/
if (isset($_GET['composite'])) {
    $composite = $_GET['composite'];
} else {
  $composite = "";
}

/*
$composite is the string we will use to find the kds_associate records.
*/

getAllMatches($composite);

// $allMatches now has its values.

$resultLinks = array(); // Links to display as results.

foreach ($allMatches as $record) {
  $id = $record['id'];
  // $nextPhrase has no backslash escaping for apostrophe, backslash, doublequotes
  // or NULL.
  $nextPhrase = $record['nextPhrase'];
  $isComplete = $record['isComplete'];
  if (isAllowed($id)) {
    if ($isComplete == 1) {
      // Assemble link for KDA and store it in the array of links.
      $resultLinks[] = makeKDA_link($nextPhrase);
    } else {
      // Assemble link that initiates further navigation using this record.
      $resultLinks[] = makeNav_link($composite, $nextPhrase);
    }
  }
}

$path = makePath($composite);
$results = "";
$noResults = "";

if (!empty($resultLinks)) {
  /*
  Form presentation for Navigation Results ($results).
  */
  $results = makeResults(); // passes resultLinks as global instead of
                            // as parameter.
} else {
  /*
  Form two presentation for Message saying: Navigation yields no results
  ($noResults).
  */
  $noResults = '<p class="errmsg">No Results!</p>';
}

displayPage($path, $results, $noResults);




function getAllMatches($composite_IN) {
/*
Get all kds_associate records which have a composite field value of
$composite. Assign them to $allMatches.
*/
  global $allMatches;
  if (!get_magic_quotes_gpc()) {
    $composite_IN = addslashes($composite_IN);
  }
  $query = "SELECT id, nextPhrase, isComplete
            FROM kds_associate
            WHERE composite='$composite_IN'";
  $result = mysql_query($query);
  if (!$result) {
    die('Query failed. Err: 68398994. -Programmer.');
  }
  $allMatches = array();
  while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
    $allMatches[] = $row;
  }
  return;
}




function isAllowed($id_IN) {
/*
This function answers the question:
Is the user/user_type allowed to have the link for this node/record?
*/
  if (!isset($id_IN) OR empty($id_IN)) {
    die('Err 686776222. -Programmer.');
  }

  /*
  Return TRUE if there is a record in kds_kdaToUserType which
  has associateId == $id_IN AND userTypeId == the user_type id
  of the user. Otherwise, return FALSE.
  */
  $userTypeId = $_COOKIE['user_type'];
  $query = "SELECT userTypeId
            FROM kds_kdaToUserType
            WHERE associateId = '$id_IN' AND userTypeId = '$userTypeId'";
  $result = mysql_query($query);
  if (!$result) {
    die('Query failed. Err: 3591524586. -Programmer.');
  }
  if (mysql_num_rows($result) < 1) {
    return FALSE;
  } else {
    return TRUE;
  }
}




function makeKDA_link($kdaId) {
/*
This function takes a KDA id and returns an HTML a-link element string
for it.
*/
  /*
  What are the things we need:
    - scriptFileName
    - scriptFileDir
    - shortTitle
  */
  $query = "SELECT scriptFileName, scriptFileDir, shortTitle
            FROM kds_kda
            WHERE id='$kdaId'";
  $result = mysql_query($query);
  if (!$result) {
    die('Query failed. Err: 1215895096. -Programmer.');
  }
  if (mysql_num_rows($result) != 1) {
    die('Query failed. Err: 7805485985. -Programmer.');
  }
  $row = mysql_fetch_array($result, MYSQL_ASSOC);
  $scriptFileName = $row['scriptFileName'];
  $scriptFileDir = $row['scriptFileDir'];
  $shortTitle = $row['shortTitle'];
  $shortTitle = stripslashes($shortTitle);
  $host = $_SERVER['HTTP_HOST'];
  $string = "<a href=\"http://$host$scriptFileDir$scriptFileName\">" .
            "$shortTitle ($kdaId)</a>";
  return $string;

}




function makeNav_link($composite_IN, $nextPhrase_IN) {
/*
This function takes a composite and nextPhrase, makes a new composite
out of them and then makes a link string. This link string will link
to the same script but with the new composite as a GET argument. The
return value will be this string.
*/
  /*
  We don't want the new composite to have had any addslashes already applied
  in any way at all.
  */
  if (get_magic_quotes_gpc()) {
    $composite_IN = stripslashes($composite_IN);
  }

  /*
  $nextPhrase_IN had no addslahes because the slashes are being automatically
  striped off by PHP after retrieval from the database. I don't know anything
  else about this at this time.
  */

  $new_composite = $composite_IN . ":::" . $nextPhrase_IN;
  $php_self = $_SERVER['PHP_SELF'];
  // urlencode the $new_composite since the string will be used in a URL.
  $new_composite = urlencode($new_composite);
  $string = "<a href=\"$php_self?composite=$new_composite\">$nextPhrase_IN</a>";
  return $string;
}




function makePath($composite_IN) {
/*
This function will return a string. This string is the HTML code which
produces the path div (actually a p tag) on the page.

What do we have already?
  - a composite which contains all the phrases.
  - the last phrase in the composite will not become a link
    since it represents the current page.
  - NOTE: We must use stripslashes since the composite came
    from the database.
*/

  // We want a composite with no added slashes.
  if (get_magic_quotes_gpc()) {
    $composite_NO_SLASHES = stripslashes($composite_IN);
  } else {
    $composite_NO_SLASHES = $composite_IN;
  }

  $string = ""; // return value.
  if (empty($composite_NO_SLASHES)) {
    return $string;
  }
  /*
  Break down the composite to get the phrases.
  NOTE: explode also gives the empty string on the left of :::
  We don't want that.
  */
  $phrases = explode(":::", $composite_NO_SLASHES);
  
  // get rid of first element
  unset($phrases[0]);
  
  // We will build $matrix to hold things.
  foreach ($phrases as $phraseStr) {
    $matrix[]['phraseNoSlashes'] = $phraseStr;
  }

  // We'll need this later.
  $lastMatrixElem = end($matrix);
  reset($matrix);

  /*
  For each except the last element of $matrix we need to add a
  string for the hrefVal.
  */

  global $tempComposite;
  $tempComposite = "";

  foreach ($matrix as $matrixKey => $matrixElem) {
    if ($matrixElem !== $lastMatrixElem) {
      $phrase = $matrixElem['phraseNoSlashes'];
      $matrix[$matrixKey]['hrefVal'] = getMatrixHrefVal($phrase);
    }
  }

  // Make the return string.
  $string = "";
  reset($matrix);
  foreach ($matrix as $matrixElem) {
    $phrase = $matrixElem['phraseNoSlashes'];
    if ($matrixElem !== $lastMatrixElem) {
      $hrefVal = $matrixElem['hrefVal'];
      $string .= "  &gt; <a href=\"$hrefVal\">$phrase</a>\n";
    } else {
      $string .= "  &gt; $phrase";
    }
  }

  return $string;
}




function getMatrixHrefVal($phrase_IN) {
/*
Side-effect: the current phrase will be appended to $tempComposite.
Returns string whose value is the characters which form the href
for the link to the listing page corresponding to $phrase_IN.
*/
  global $tempComposite;
  if (!isset($phrase_IN)) {
    die('Err: 1531714. -Programmer.');
  }
  $php_self = $_SERVER['PHP_SELF'];
  $tempComposite .= ":::" . $phrase_IN;
  $composite_urlEncoded = urlencode($tempComposite);
  $string = "$php_self?composite=$composite_urlEncoded";
  return $string;
}




function makeResults() {
/*
Takes the global $resultLinks and forms the presentation string for
navigation results and returns it.
*/
  global $resultLinks; // An array of strings whose value is
  $string = "<p>";
  reset($resultLinks);
  foreach ($resultLinks as $htmlLink) {
    $string .= "$htmlLink<br/>\n";
  }
  $string .= "</p>\n\n";
  return $string;
}




function displayPage($path_IN, $results_IN, $noResults_IN) {
  site_header('Navigate');
  $page_str = <<<EOPAGESTR

<div id="pagetop">
<p>$path_IN &nbsp;</p>
</div> <!-- end of pagetop div -->
$results_IN
$noResults_IN

EOPAGESTR;
  echo $page_str;
  site_footer();
  return;
}

?>