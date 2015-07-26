<?php

/**********************************************
* This file displays the change non-sensitive *
* user data form. It submits to itself, and   *
* displays a message each time you submit.    *
***********************************************/

// A file with the database host, user, password, and
// selected database
include_once('includes/login_funcs.php');
require_once('includes/db_vars.php');
include_once('includes/header_footer.php');

if (!user_isloggedin()) {
  echo '<p>You are not logged in, or this is not your user profile.</p>';
} else {
  $user_name = $_COOKIE['user_name'];

  // Initialize $isAnomaly. It represents whether any form
  // string is too long indicating hacking attempt
  $isAnomaly = false;

  // Have required fields been filled out?
  $requireMet = true;

  // Since I use .= when assigning to $status_message
  // I don't want to get a Notice from
  // the compiler that $status_message is undefined
  $status_message = "";


  if ( isset($_POST['submit']) and $_POST['submit'] == "Edit user data" ) {

    // Transfer POSTS to regular vars
    if ( isset($_POST['wantPublished']) ) {
      $wantPublished = $_POST['wantPublished'];
    } else {
      $wantPublished = "";
    }
    if ( isset($_POST['availability']) ) {
      $availability = $_POST['availability'];
    } else {
      $availability = "";
    }
    if ( isset($_POST['cdlAB']) ) {
      $cdlAB = $_POST['cdlAB'];
    } else {
      $cdlAB = "";
    }
    if ( isset($_POST['cdlP']) ) {
      $cdlP = $_POST['cdlP'];
    } else {
      $cdlP = "";
    }
    if ( isset($_POST['cdlS']) ) {
      $cdlS = $_POST['cdlS'];
    } else {
      $cdlS = "";
    }
    if ( isset($_POST['aBrakes']) ) {
      $aBrakes = $_POST['aBrakes'];
    } else {
     $aBrakes  = "";
    }
    if ( isset($_POST['lerners']) )
    {
      $learners = $_POST['learners'];
    } else {
      $learners = "";
    }
    if ( isset($_POST['dCar']) ) {
      $dCar = $_POST['dCar'];
    } else {
      $dCar = "";
    }
    if ( isset($_POST['eDr']) ) {
      $eDr = $_POST['eDr'];
    } else {
      $eDr = "";
    }
    if ( isset($_POST['hC_Certified']) ) {
      $hC_Certified = $_POST['hC_Certified'];
    } else {
      $hC_Certified = "";
    }
    if ( isset($_POST['lContractor']) ) {
      $lContractor = $_POST['lContractor'];
    } else {
      $lContractor = "";
    }
    if ( isset($_POST['commentStr']) ) {
      $commentStr = $_POST['commentStr'];
    } else {
      $commentStr = "";
    }
    if ( isset($_POST['phone1_ac']) ) {
      $phone1_ac = $_POST['phone1_ac'];
    } else {
      $phone1_ac = "";
    }
    if ( isset($_POST['phone1_3d']) ) {
      $phone1_3d = $_POST['phone1_3d'];
    } else {
      $phone1_3d = "";
    }
    if ( isset($_POST['phone1_4d']) ) {
      $phone1_4d = $_POST['phone1_4d'];
    } else {
      $phone1_4d = "";
    }
    if ( isset($_POST['phone2_ac']) ) {
      $phone2_ac = $_POST['phone2_ac'];
    } else {
      $phone2_ac = "";
    }
    if ( isset($_POST['phone2_3d']) ) {
      $phone2_3d = $_POST['phone2_3d'];
    } else {
      $phone2_3d = "";
    }
    if ( isset($_POST['phone2_4d']) ) {
      $phone2_4d = $_POST['phone2_4d'];
    } else {
      $phone2_4d = "";
    }
    if ( isset($_POST['fax_ac']) ) {
      $fax_ac = $_POST['fax_ac'];
    } else {
      $fax_ac = "";
    }
    if ( isset($_POST['fax_3d']) ) {
      $fax_3d = $_POST['fax_3d'];
    } else {
      $fax_3d = "";
    }
    if ( isset($_POST['fax_4d']) ) {
      $fax_4d = $_POST['fax_4d'];
    } else {
      $fax_4d = "";
    }
    if ( isset($_POST['streetAd1']) ) {
      $streetAd1 = $_POST['streetAd1'];
    } else {
      $streetAd1 = "";
    }
    if ( isset($_POST['streetAd2']) ) {
      $streetAd2 = $_POST['streetAd2'];
    } else {
      $streetAd2 = "";
    }
    if ( isset($_POST['cityAdres']) ) {
      $cityAdres = $_POST['cityAdres'];
    } else {
      $cityAdres = "";
    }
    if ( isset($_POST['stateAdre']) ) {
      $stateAdre = $_POST['stateAdre'];
    } else {
      $stateAdre = "";
    }
    if ( isset($_POST['zipCodeAd']) ) {
      $zipCodeAd = $_POST['zipCodeAd'];
    } else {
      $zipCodeAd = "";
    }


    // NOTES:
    // Data being placed in a database needs to have characters
    // in the following set escaped: {',",.}. That is why
    // one way or another we will escape them.
    // The PHP directive  magic_quotes_gpc is on by default,
    // and it essentially runs addslashes() on all GET, POST,
    // and COOKIE data. Do not use addslashes() on strings
    // that have already been escaped with magic_quotes_gpc
    // as you'll then do double escaping. The function
    // get_magic_quotes_gpc() may come in handy for checking
    // this. The following code takes care of this problem:
    if ( !get_magic_quotes_gpc() ) {
      $lContractor = addslashes($lContractor);
      $commentStr = addslashes($commentStr);
      $streetAd1 = addslashes($streetAd1);
      $streetAd2 = addslashes($streetAd2);
      $cityAdres = addslashes($cityAdres);
      $stateAdre = addslashes($stateAdre);
    }


    // Verify string length and deal with anomalies

    // The string length should not be longer than the
    // MAXLENGTH of the FORM field
    if ( strlen($wantPublished) > 1 ) { $isAnomaly = true; }
    if ( strlen($availability) > 1 ) { $isAnomaly = true; }
    if ( strlen($cdlAB) > 1 ) { $isAnomaly = true; }
    if ( strlen($cdlP) > 1 ) { $isAnomaly = true; }
    if ( strlen($cdlS) > 1 ) { $isAnomaly = true; }
    if ( strlen($aBrakes) > 1 ) { $isAnomaly = true; }
    if ( strlen($learners) > 1 ) { $isAnomaly = true; }
    if ( strlen($dCar) > 1 ) { $isAnomaly = true; }
    if ( strlen($eDr) > 1 ) { $isAnomaly = true; }
    if ( strlen($hC_Certified) > 1 ) { $isAnomaly = true; }
    if ( strlen($lContractor) > 30 ) { $isAnomaly = true; }
    if ( strlen($commentStr) > 85 ) { $isAnomaly = true; }
    if ( strlen($phone1_ac) > 3 ) { $isAnomaly = true; }
    if ( strlen($phone1_3d) > 3 ) { $isAnomaly = true; }
    if ( strlen($phone1_4d) > 4 ) { $isAnomaly = true; }
    if ( strlen($phone2_ac) > 3 ) { $isAnomaly = true; }
    if ( strlen($phone2_3d) > 3 ) { $isAnomaly = true; }
    if ( strlen($phone2_4d) > 4 ) { $isAnomaly = true; }
    if ( strlen($fax_ac) > 3 ) { $isAnomaly = true; }
    if ( strlen($fax_3d) > 3 ) { $isAnomaly = true; }
    if ( strlen($fax_4d) > 4 ) { $isAnomaly = true; }
    if ( strlen($streetAd1) > 30 ) { $isAnomaly = true; }
    if ( strlen($streetAd2) > 30 ) { $isAnomaly = true; }
    if ( strlen($cityAdres) > 20 ) { $isAnomaly = true; }
    if ( strlen($stateAdre) > 20 ) { $isAnomaly = true; }
    if ( strlen($zipCodeAd) > 10 ) { $isAnomaly = true; }
    
    // Run some more validation on checkbox field variables
    if ( strlen($wantPublished) > 0 and !is_numeric($wantPublished)) {$isAnomaly = true; }
    if ( strlen($cdlAB) > 0 and !is_numeric($cdlAB)) {$isAnomaly = true; }
    if ( strlen($cdlP) > 0 and !is_numeric($cdlP)) {$isAnomaly = true; }
    if ( strlen($cdlS) > 0 and !is_numeric($cdlS)) {$isAnomaly = true; }
    if ( strlen($aBrakes) > 0 and !is_numeric($aBrakes)) {$isAnomaly = true; }
    if ( strlen($learners) > 0 and !is_numeric($learners)) {$isAnomaly = true; }
    if ( strlen($dCar) > 0 and !is_numeric($dCar)) {$isAnomaly = true; }
    if ( strlen($eDr) > 0 and !is_numeric($eDr)) {$isAnomaly = true; }
    
    if ( strlen($wantPublished) > 0 and $wantPublished > 1 ) {$isAnomaly = true; }
    if ( strlen($cdlAB) > 0 and $cdlAB > 1 ) {$isAnomaly = true; }
    if ( strlen($cdlP) > 0 and $cdlP > 1 ) {$isAnomaly = true; }
    if ( strlen($cdlS) > 0 and $cdlS > 1 ) {$isAnomaly = true; }
    if ( strlen($aBrakes) > 0 and $aBrakes > 1 ) {$isAnomaly = true; }
    if ( strlen($learners) > 0 and $learners > 1 ) {$isAnomaly = true; }
    if ( strlen($dCar) > 0 and $dCar > 1 ) {$isAnomaly = true; }
    if ( strlen($eDr) > 0 and $eDr > 1 ) {$isAnomaly = true; }

    // Find out if required fields were filled out
    if ( strlen($availability) != 1 || strlen($hC_Certified) != 1 ) {
      $requireMet = false;
    }

    // If required fields were inputed then update database
    if ( $requireMet == true and $isAnomaly == false ) {

      // Send data to db

      $query = "UPDATE user
                SET wantPublished = '$wantPublished',
                    availability = '$availability',
                    cdlAB = '$cdlAB',
                    cdlP = '$cdlP',
                    cdlS = '$cdlS',
                    aBrakes = '$aBrakes',
                    learners = '$learners',
                    dCar = '$dCar',
                    eDr = '$eDr',
                    hC_Certified = '$hC_Certified',
                    lContractor = '$lContractor',
                    commentStr = '$commentStr',
                    phone1_ac = '$phone1_ac',
                    phone1_3d = '$phone1_3d',
                    phone1_4d = '$phone1_4d',
                    phone2_ac = '$phone2_ac',
                    phone2_3d = '$phone2_3d',
                    phone2_4d = '$phone2_4d',
                    fax_ac = '$fax_ac',
                    fax_3d = '$fax_3d',
                    fax_4d = '$fax_4d',
                    streetAd1 = '$streetAd1',
                    streetAd2 = '$streetAd2',
                    cityAdres = '$cityAdres',
                    stateAdre = '$stateAdre',
                    zipCodeAd = '$zipCodeAd'
                WHERE user_name = '$user_name'";
      $result = mysql_query($query);
      if (!$result) {
        $status_message = 'Problem with user data entry';
      } else {
        $status_message = 'Successfully edited user data';
      }
    } elseif ( $requireMet == false ) {
      $status_message =  'Error -- you did not complete a required field.';
      if ( $isAnomaly == true ) {
        $status_message .= ' You\'re trying to do something very ' .
          'odd with this form. Stop it now.';
      }
    } elseif ( $isAnomaly == true ) {
      $status_message = 'You\'re trying to do something very ' .
        'odd with this form. Stop it now.';
    }
  }

  // Get previously-existing data
  $query = "SELECT wantPublished, availability, cdlAB, cdlP, cdlS, aBrakes,
learners, dCar, eDr, hC_Certified, lContractor, commentStr, phone1_ac,
phone1_3d, phone1_4d, phone2_ac, phone2_3d, phone2_4d, fax_ac, fax_3d, fax_4d,
streetAd1, streetAd2, cityAdres, stateAdre, zipCodeAd
            FROM user
            WHERE user_name = '$user_name'";

  $result = mysql_query($query);
  // Shall we have an error message if no data comes back?

  if (!$result || mysql_num_rows($result) < 1) {
    // This SHOULD execute if NO row was read from db.
    $status_message .= ' No record was retrieved. Login and try again.';

    // Assign empty strings to variables.
    $wantPublished = "";
    $availability = "";
    $cdlAB = "";
    $cdlP = "";
    $cdlS = "";
    $aBrakes = "";
    $learners = "";
    $dCar = "";
    $eDr = "";
    $hC_Certified = "";
    $lContractor = "";
    $commentStr = "";
    $phone1_ac = "";
    $phone1_3d = "";
    $phone1_4d = "";
    $phone2_ac = "";
    $phone2_3d = "";
    $phone2_4d = "";
    $fax_ac = "";
    $fax_3d = "";
    $fax_4d = "";
    $streetAd1 = "";
    $streetAd2 = "";
    $cityAdres = "";
    $stateAdre = "";
    $zipCodeAd = "";
  } else {
    // This should not execute if NO row was read from db.
    $user_array = mysql_fetch_array($result);

    // Assign variables data from database
    $wantPublished = $user_array['wantPublished'];
    $availability = $user_array['availability'];
    $cdlAB = $user_array['cdlAB'];
    $cdlP = $user_array['cdlP'];
    $cdlS = $user_array['cdlS'];
    $aBrakes = $user_array['aBrakes'];
    $learners = $user_array['learners'];
    $dCar = $user_array['dCar'];
    $eDr = $user_array['eDr'];
    $hC_Certified = $user_array['hC_Certified'];
    $lContractor = $user_array['lContractor'];
    $commentStr = $user_array['commentStr'];
    $phone1_ac = $user_array['phone1_ac'];
    $phone1_3d = $user_array['phone1_3d'];
    $phone1_4d = $user_array['phone1_4d'];
    $phone2_ac = $user_array['phone2_ac'];
    $phone2_3d = $user_array['phone2_3d'];
    $phone2_4d = $user_array['phone2_4d'];
    $fax_ac = $user_array['fax_ac'];
    $fax_3d = $user_array['fax_3d'];
    $fax_4d = $user_array['fax_4d'];
    $streetAd1 = $user_array['streetAd1'];
    $streetAd2 = $user_array['streetAd2'];
    $cityAdres = $user_array['cityAdres'];
    $stateAdre = $user_array['stateAdre'];
    $zipCodeAd = $user_array['zipCodeAd'];

    // Text and Textarea fields have had backslashes
    // added to escape single quotes ('), double
    // quotes ("), and periods (.) before insertion
    // into the database. Therefore, we must
    // undo this before displaying these strings.
    $lContractor = stripslashes($lContractor);
    $commentStr = stripslashes($commentStr);
    $streetAd1 = stripslashes($streetAd1);
    $streetAd2 = stripslashes($streetAd2);
    $cityAdres = stripslashes($cityAdres);
    $stateAdre = stripslashes($stateAdre);
  }

  // Construct the multiple field type controls 

  // Construct wantPublished checkbox control
  if ( $wantPublished == "1" ) {
    $wantPubBox =
      '<div><input type="checkbox" name="wantPublished" id="wantPublished" value="1" checked="checked"/>' .
      ' <label for="wantPublished">Yes, I want this information published.</label></div>';
  } else {
    $wantPubBox =
      '<div><input type="checkbox" name="wantPublished" id="wantPublished" value="1"/>' .
      ' <label for="wantPublished">Yes, I want this information published.</label></div>';
  }

  // Construct cdlAB checkbox control
  if ( $cdlAB == "1" ) {
    $cdlAB_Box =
      '<div><input type="checkbox" name="cdlAB" id="cdlAB" value="1" checked="checked"/>' .
      ' <label for="cdlAB">CDL B (or better)</label></div>';
  } else {
    $cdlAB_Box =
      '<div><input type="checkbox" name="cdlAB" id="cdlAB" value="1"/>' .
      ' <label for="cdlAB">CDL B (or better)</label></div>';
  }

  // Construct cdlP checkbox control
  if ( $cdlP == "1" ) {
    $cdlP_Box =
      '<div><input type="checkbox" name="cdlP" id="cdlP" value="1" checked="checked"/>' .
      ' <label for="cdlP">P</label></div>';
  } else {
    $cdlP_Box =
      '<div><input type="checkbox" name="cdlP" id="cdlP" value="1"/>' .
      ' <label for="cdlP">P</label></div>';
  }

  // Construct cdlS checkbox control
  if ( $cdlS == "1" ) {
    $cdlS_Box =
      '<div><input type="checkbox" name="cdlS" id="cdlS" value="1" checked="checked"/>' .
      ' <label for="cdlS">S</label></div>';
  } else {
    $cdlS_Box =
      '<div><input type="checkbox" name="cdlS" id="cdlS" value="1"/>' .
      ' <label for="cdlS">S</label></div>';
  }

  // Construct aBrakes checkbox control
  if ( $aBrakes == "1" ) {
    $aBrakes_Box =
      '<div><input type="checkbox" name="aBrakes" id="aBrakes" value="1" checked="checked"/>' .
      ' <label for="aBrakes">Air Brakes</label></div>';
  } else {
    $aBrakes_Box =
      '<div><input type="checkbox" name="aBrakes" id="aBrakes" value="1"/>' .
      ' <label for="aBrakes">Air Brakes</label></div>';
  }

  // Construct learners checkbox control
  if ( $learners == "1" ) {
    $learners_Box =
      '<div><input type="checkbox" name="learners" id="learners" value="1" checked="checked"/>' .
      ' <label for="learners">Learners</label></div>';
  } else {
    $learners_Box =
      '<div><input type="checkbox" name="learners" id="learners" value="1"/>' .
      ' <label for="learners">Learners</label></div>';
  }

  // Construct dCar checkbox control
  if ( $dCar == "1" ) {
    $dCar_Box =
      '<div><input type="checkbox" name="dCar" id="dCar" value="1" checked="checked"/>' .
      ' <label for="dCar">Driven car over 5 years</label></div>';
  } else {
    $dCar_Box =
      '<div><input type="checkbox" name="dCar" id="dCar" value="1"/>' .
      ' <label for="dCar">Driven car over 5 years</label></div>';
  }

 // Construct eDr checkbox control
  if ( $eDr == "1" ) {
    $eDr_Box =
      '<div><input type="checkbox" name="eDr" id="eDr" value="1" checked="checked"/>' .
      ' <label for="eDr">Excellent driving record</label></div>';
  } else {
    $eDr_Box =
      '<div><input type="checkbox" name="eDr" id="eDr" value="1"/>' .
      ' <label for="eDr">Excellent driving record</label></div>';
  }

  // Construct availability radio control
  if ( $availability == "S" ) {
    $availability_button =
      '<div><input type="radio" name="availability" id="availSub" value="S" checked="checked"/>' .
      ' <label for="availSub">Substitute</label><br/>' .
      '<input type="radio" name="availability" id="availReg" value="R"/>' .
      ' <label for="availReg">Regular</label><br/>' .
      '<input type="radio" name="availability" id="availAssist" value="A"/>' .
      ' <label for="availAssist">Assistant</label><br/>' .
      '<input type="radio" name="availability" id="availNone" value="N"/>' .
      ' <label for="availNone">None</label></div>';
  } elseif ( $availability == "R" ) {
    $availability_button =
      '<div><input type="radio" name="availability" id="availSub" value="S"/>' .
      ' <label for="availSub">Substitute</label><br/>' .
      '<input type="radio" name="availability" id="availReg" value="R" checked="checked"/>' .
      ' <label for="availReg">Regular</label><br/>' .
      '<input type="radio" name="availability" id="availAssist" value="A"/>' .
      ' <label for="availAssist">Assistant</label><br/>' .
      '<input type="radio" name="availability" id="availNone" value="N"/>' .
      ' <label for="availNone">None</label></div>';
  } elseif ( $availability == "A" ) {
    $availability_button =
      '<div><input type="radio" name="availability" id="availSub" value="S"/>' .
      ' <label for="availSub">Substitute</label><br/>' .
      '<input type="radio" name="availability" id="availReg" value="R"/>' .
      ' <label for="availReg">Regular</label><br/>' .
      '<input type="radio" name="availability" id="availAssist" value="A" checked="checked"/>' .
      ' <label for="availAssist">Assistant</label><br/>' .
      '<input type="radio" name="availability" id="availNone" value="N"/>' .
      ' <label for="availNone">None</label></div>';
  } elseif ( $availability == "N" ) {
    $availability_button =
      '<div><input type="radio" name="availability" id="availSub" value="S"/>' .
      ' <label for="availSub">Substitute</label><br/>' .
      '<input type="radio" name="availability" id="availReg" value="R"/>' .
      ' <label for="availReg">Regular</label><br/>' .
      '<input type="radio" name="availability" id="availAssist" value="A"/>' .
      ' <label for="availAssist">Assistant</label><br/>' .
      '<input type="radio" name="availability" id="availNone" value="N" checked="checked"/>' .
      ' <label for="availNone">None</label></div>';
  } else {
    $availability_button =
      '<div><input type="radio" name="availability" id="availSub" value="S"/>' .
      ' <label for="availSub">Substitute</label><br/>' .
      '<input type="radio" name="availability" id="availReg" value="R"/>' .
      ' <label for="availReg">Regular</label><br/>' .
      '<input type="radio" name="availability" id="availAssist" value="A"/>' .
      ' <label for="availAssist">Assistant</label><br/>' .
      '<input type="radio" name="availability" id="availNone" value="N"/>' .
      ' <label for="availNone">None</label></div>';
  }

  // Construct hC_Certified radio control
  if ( $hC_Certified == "Y" ) {
    $hC_Certified_button =
      '<div><input type="radio" name="hC_Certified" id="hcYcert" value="Y" checked="checked"/>' .
      ' <label for="hcYcert">Certified</label><br/>' .
      '<input type="radio" name="hC_Certified" id="hcRcert" value="R"/>' .
      ' <label for="hcRcert">Recently Certified</label><br/>' .
      '<input type="radio" name="hC_Certified" id="hcNcert" value="N"/>' .
      ' <label for="hcNcert">Never Certified (Not Recently - 5 yrs.)</label></div>';
  } elseif ( $hC_Certified == "R" ) {
    $hC_Certified_button =
      '<div><input type="radio" name="hC_Certified" id="hcYcert" value="Y"/>' .
      ' <label for="hcYcert">Certified</label><br/>' .
      '<input type="radio" name="hC_Certified" id="hcRcert" value="R" checked="checked"/>' .
      ' <label for="hcRcert">Recently Certified</label><br/>' .
      '<input type="radio" name="hC_Certified" id="hcNcert" value="N"/>' .
      ' <label for="hcNcert">Never Certified (Not Recently - 5 yrs.)</label></div>';
  } elseif ( $hC_Certified == "N" ) {
    $hC_Certified_button =
      '<div><input type="radio" name="hC_Certified" id="hcYcert" value="Y"/>' .
      ' <label for="hcYcert">Certified</label><br/>' .
      '<input type="radio" name="hC_Certified" id="hcRcert" value="R"/>' .
      ' <label for="hcRcert">Recently Certified</label><br/>' .
      '<input type="radio" name="hC_Certified" id="hcNcert" value="N" checked="checked"/>' .
      ' <label for="hcNcert">Never Certified (Not Recently - 5 yrs.)</label></div>';
  } else {
    $hC_Certified_button =
      '<div><input type="radio" name="hC_Certified" id="hcYcert" value="Y"/>' .
      ' <label for="hcYcert">Certified</label><br/>' .
      '<input type="radio" name="hC_Certified" id="hcRcert" value="R"/>' .
      ' <label for="hcRcert">Recently Certified</label><br/>' .
      '<input type="radio" name="hC_Certified" id="hcNcert" value="N"/>' .
      ' <label for="hcNcert">Never Certified (Not Recently - 5 yrs.)</label></div>';
  }



  // --------------
  // Construct form
  // --------------

  site_header('User data edit page');

  // Superglobals don't work with heredoc
  $php_self = $_SERVER['PHP_SELF'];

  if ( !isset($status_message) || $status_message == "" ) {
    $message_str = "";
  } else {
    $message_str = "<p class=\"errmsg\">$status_message</p>";
  }

  $userform_str = <<<EOUSERFORMSTR

$message_str

<p>This site provides you with the
opportunity to make your
services available to contractors in Howard County.
Please note that this site is offered as is. The
owner of this site is not responsible for any
consiquence of you using it.</p>

<form action="$php_self" method="post" class="loginform">
  <fieldset>
  <legend>User Information Form</legend>
  <div>
    <p><b>Do you want to publish at this time?</b><br/>
      In other words: Do you want others to see this information at this time?</p>
$wantPubBox
  </div>
  <div>
    <p><b>Availability</b> <span class="formcomment">*required</span></p>
$availability_button
  </div>
  <div>
    <p><b>MVA CDL Status</b></p>
$cdlAB_Box
$cdlP_Box
$cdlS_Box
$aBrakes_Box
$learners_Box
$dCar_Box
$eDr_Box
  </div>
  <div>
    <p><b>Howard County Certified</b> <span class="formcomment">*required</span></p>
$hC_Certified_button
  </div>
  <div>
    <label for="lContractor" class="fixedwidth">Most recent contractor</label>
    <input type="text" name="lContractor"id="lContractor" value="$lContractor" size="21" maxlength="30"/>
  </div>
  <div>
    <label for="commentStr" class="fixedwidth">Comment</label>
    <input type="text" name="commentStr" id="commentStr" value="$commentStr" size="41" maxlength="85"/>
  </div>
  <div>
    <label for="phone1_ac" class="fixedwidth">Phone 1</label>
    <input type="text" name="phone1_ac" id="phone1_ac" value="$phone1_ac" size="3" maxlength="3"/> -
    <input type="text" name="phone1_3d" id="phone1_3d" value="$phone1_3d" size="3" maxlength="3"/> -
    <input type="text" name="phone1_4d" id="phone1_4d" value="$phone1_4d" size="4" maxlength="4"/>
  </div>
  <div>
    <label for="phone2_ac" class="fixedwidth">Phone 2</label>
    <input type="text" name="phone2_ac" id="phone2_ac" value="$phone2_ac" size="3" maxlength="3"/> -
    <input type="text" name="phone2_3d" id="phone2_3d" value="$phone2_3d" size="3" maxlength="3"/> -
    <input type="text" name="phone2_4d" id="phone2_4d" value="$phone2_4d" size="4" maxlength="4"/>
  </div>
  <div>
    <label for="fax_ac" class="fixedwidth">Fax</label>
    <input type="text" name="fax_ac" id="fax_ac" value="$fax_ac" size="3" maxlength="3"/> -
    <input type="text" name="fax_3d" id="fax_3d" value="$fax_3d" size="3" maxlength="3"/> -
    <input type="text" name="fax_4d" id="fax_4d" value="$fax_4d" size="4" maxlength="4"/>
  </div>
  <div>
    <label for="streetAd1" class="fixedwidth">Street Address</label>
    <input type="text" name="streetAd1" id="streetAd1" value="$streetAd1" size="24" maxlength="30"/>
  </div>
  <div>
    <label for="streetAd2" class="fixedwidth">Address Line 2</label>
    <input type="text" name="streetAd2" id="streetAd2" value="$streetAd2" size="24" maxlength="30"/>
  </div>
  <div>
    <label for="cityAdres" class="fixedwidth">City</label>
    <input type="text" name="cityAdres" id="cityAdres" value="$cityAdres" size="20" maxlength="20"/>
  </div>
  <div>
    <label for="stateAdre" class="fixedwidth">State</label>
    <input type="text" name="stateAdre" id="stateAdre" value="$stateAdre" size="15" maxlength="20"/>
  </div>
  <div>
    <label for="zipCodeAd" class="fixedwidth">Zip</label>
    <input type="text" name="zipCodeAd" id="zipCodeAd" value="$zipCodeAd" size="10" maxlength="10"/>
  </div>
  <div class="buttonarea">    
    <input type="submit" name="submit" value="Edit user data"/>
  </div>
  </fieldset>
</form>

EOUSERFORMSTR;
  echo $userform_str;

  site_footer();

}

?>