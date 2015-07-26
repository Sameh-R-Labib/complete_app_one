<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

// A file with the database host, user, password, and
// selected database
$jeffWiggleLabib = "Wake up Jeff 59990!";
include_once('db_vars.php');

// A string used for md5 encryption. You could move it to
// a file
// outside the web tree for more security.
$supersecret_hash_padding = 'Halleluyah choir at the mall. Fond memories.';

 
$LOGGED_IN = false;
unset($LOGGED_IN);

$UT_COOKIEISSET = false;
unset($UT_COOKIEISSET);


function user_isloggedin() {
  // This function will only work with superglobal arrays,
  // because I'm not passing in any values or declaring globals
  global $supersecret_hash_padding, $LOGGED_IN;

  // Have we already run the hash checks?
  // If so, return the pre-set var
  if (isSet($LOGGED_IN)) {
    return $LOGGED_IN;
  }

  if ( isset($_COOKIE['user_name']) && isset($_COOKIE['id_hash'])) {
    $hash = md5($_COOKIE['user_name'].$supersecret_hash_padding);
    if ($hash == $_COOKIE['id_hash']) {
      return true;
    } else {
      return false;
    }
  } else {
    return false;
  }
}


function user_type_cookieIsSet() {
  global $supersecret_hash_padding, $UT_COOKIEISSET;

  // Have we already run the hash checks?
  // If so, return the pre-set var
  if (isSet($UT_COOKIEISSET)) {
    return $UT_COOKIEISSET;
  }

  if ( isset($_COOKIE['user_type']) && isset($_COOKIE['user_type_hash'])) {
    $hash = md5($_COOKIE['user_type'].$supersecret_hash_padding);
    if ($hash == $_COOKIE['user_type_hash']) {
      return true;
    } else {
      return false;
    }
  } else {
    return false;
  }
}


function user_login() {
  // This function will only work with superglobal arrays,
  // because I'm not passing in any values or declaring globals
  if (!$_POST['user_name'] || !$_POST['password']) {
    $feedback = 'ERROR -- Missing username or password';
    return $feedback;
  } else {
    $user_name = strtolower($_POST['user_name']);
    // Don't need to trim because extra spaces should fail
    // for this
    // Don't need to addslashes because single quotes
    // aren't allowed
    if ( isLocked($user_name) ) {
      $feedback = 'Your account is locked. Please contact site owner.';
      return $feedback;
    }
    $password = $_POST['password'];
    // Don't need to addslashes because we'll be hashing it
    $crypt_pwd = md5($password);
    $query = "SELECT user_name, is_confirmed
              FROM user
              WHERE user_name = '$user_name'
              AND password='$crypt_pwd'";
    $result = mysql_query($query);
    if (!$result || mysql_num_rows($result) < 1){

      $feedback = 'ERROR -- User not found or password ' .
        'incorrect. ';
      $feedback .= killMultiLoginAtm($user_name);
      return $feedback;
    } else {
      if (mysql_result($result, 0, 'is_confirmed') == '1') {
         user_set_tokens($user_name);
         return 1;
      } else {
         $feedback = 'ERROR -- You may not have confirmed ' .
           'your account yet';
         return $feedback;
      }
    }
  }
}




function isLocked($user_name_in) {
/*
Assume: A record with user name exists and is unique.
Returns TRUE if user account is locked. Otherwise returns FALSE.
*/

  $query = "SELECT mylock
            FROM user
            WHERE user_name = '$user_name_in'";
  $result = mysql_query($query);
  if (!$result || mysql_num_rows($result) < 1){
    die("Error: AAEEE04J - Programmer.");
  } else {
    $isLocked = mysql_result($result, 0, 'mylock');
    if ( $isLocked >= 1 ) {
      return TRUE;
    } else {
      return FALSE;
    }
  }
}




function killMultiLoginAtm($user_name_in) {
/*
Assumption: A login failure just occured.

This function updates the user table fields for keeping track of
attacks where a user keeps trying to guess the password. Each user
record will have the following fields: qtyFLogins, lastFLogin, mylock.

qtyFLogin = number of failed login attempts within past two hours
lastFLogin = time of last failed login
mylock = contains 1 if locked ... 0 otherwise.

How it works: 1. Get lastFLogin time
              2. Was last fail within 2 hrs?
              3. If no  then set qtyFLogin = 1 AND lastFLogin = current time.
              4. If yes then get qtyFLogin
                   A. Is qtyFLogin < 4?
                   B. If no then return feedback string and lock the account.
                   C. If yes then increment qtyFLogins AND lastFLogin = current time.

Returns a feedback string IF the account got locked.
*/


  // When was the last failed login attempt?
  $query = "SELECT qtyFLogin, lastFLogin
            FROM user
            WHERE user_name = '$user_name_in'";
  $result = mysql_query($query);
  /* if query is successful then get the targeted value */
  if (!$result || mysql_num_rows($result) < 1){
    // The user name was not found in the table
    // so this is not considered an attempt. Either
    // that or the query just failed for some other reason.
    $feedback = 'The information you entered is not valid.';
    return $feedback; 
  } else {
    $timeOfLFLA = mysql_result($result, 0, 'lastFLogin');
    $howMany = mysql_result($result, 0, 'qtyFLogin');
  }

  // How much time has passed since then?
  $query = "SELECT TIMESTAMPDIFF(MINUTE,'$timeOfLFLA',NOW())";
  $result = mysql_query($query);
  if (!$result || mysql_num_rows($result) < 1){
    die("Function failed code 515812.\n - Programmer.");
  } else {
    $duration = mysql_result($result, 0, 0);
  }

  if ( $duration > 120 ) {
    // This is NOT an attack. Set qtyFLogin field to 1.
    // Set lastFLogin to current time.
    $query = "UPDATE user
              SET qtyFLogin = '1',
                  lastFLogin = NOW()
              WHERE user_name = '$user_name_in'";
    $result = mysql_query($query);
    if (!$result) {
      die("Error: 998758T - Programmer.");
    }
  } else {
    // This COULD BE an attack.
  
    // So, is it?
    
    // See how many how many failed login attempts there have been.
    // Value was retrieved earlier into $howMany.
    
    if ( $howMany < 4 ) {
      // Not yet but could be.
      
      // Update the user table with an incremented value for howMany
      // and set the value of lastFLogin field to current time.
      $howMany += 1;
      $query = "UPDATE user
                SET qtyFLogin = '$howMany',
                    lastFLogin = NOW()
                WHERE user_name = '$user_name_in'";
      $result = mysql_query($query);
      if (!$result) {
        die("Error: HH90SYR - Programmer.");
      }
    } else {
      // Yes, it is. Lock their account. And, return an appropriate
      // feedback string.
      
      // Lock user account.
      $query = "UPDATE user
                SET mylock = '1'
                WHERE user_name = '$user_name_in'";
      $result = mysql_query($query);
      if (!$result) {
        die("Error: LWQ0031 - Programmer.");
      }
      $feedback = 'Please contact site owner to re-activate account. ';
      return $feedback;
    }
  }
}



function user_logout() {
  setcookie('user_name',      '', (time()+57600), '/', '', 0);
  setcookie('id_hash',        '', (time()+57600), '/', '', 0);
  setcookie('user_type',      '', (time()+57600), '/', '', 0);
  setcookie('user_type_hash', '', (time()+57600), '/', '', 0);
}



function user_set_tokens($user_name_in) {
  global $supersecret_hash_padding;
  if (!$user_name_in) {
    $feedback = 'ERROR -- No username';
    return false;
  }
  $user_name = strtolower($user_name_in);
  $id_hash = md5($user_name.$supersecret_hash_padding);

  setcookie('user_name', $user_name, (time()+57600), '/', '', 0);
  setcookie('id_hash', $id_hash, (time()+57600), '/', '', 0);
}



function user_type_set_tokens($user_type_in) {
// This function sets the user_type cookies in the user's browser
// to $user_type_in (which is an available_user_types id) in a
// similar way to how user_set_tokens() does for the the user_name.
// Make sure function aborts on failure.
  global $supersecret_hash_padding;
  if (!$user_type_in) {
    die("Function user_type_set_tokens failed. \n- Programmer.");
  }
  $user_type_hash = md5($user_type_in.$supersecret_hash_padding);
  setcookie('user_type', $user_type_in, (time()+57600), '/', '', 0);
  setcookie('user_type_hash', $user_type_hash, (time()+57600), '/', '', 0);
}



function user_type_default($user_name_in) {
// This function finds out the value of user_type_default field,
// for a specified username, and returns it. We assume this field
// contains NULL or a valid available_user_types id for that
// particular user. If the function fails then it will cause
// an abort. Make sure the returned user_type is a valid one.
  $query = "SELECT user_type_default
            FROM user
            WHERE user_name = '$user_name_in'";
  $result = mysql_query($query);
  if (!$result || mysql_num_rows($result) < 1){
    die("Function user_type_default failed. \n- Programmer.");
  } else {
    $user_type_id = mysql_result($result, 0, 'user_type_default');
    return $user_type_id;
  }
}




function user_type_storeDefault($user_name_in, $user_type_in) {
// $user_type_in is an id.
// user_type_storeDefault makes this id the default for the user.
// Abort on failure.
  $query = "UPDATE user
            SET user_type_default = '$user_type_in'
            WHERE user_name = '$user_name_in'";
  $result = mysql_query($query);
  if (!$result) {
    die("Function user_type_storeDefault failed to save data. \n- Programmer.");
  }
}



function user_type_set_default($user_name_in) {
// If user's default user_type is NULL then:
//   1. set it to system default
//   2. set user's user_type browser tokens to system default.
//   3. Create it by adding it to the user_types_foreach table.
//   4. Remove it from $user_type_field_options array and set array
//      value to FALSE if it has become empty.
// If default is not NULL then don't do
// anything. On failure abort everything.

  // Global variable:
  global $user_type_field_options;

  // Get value of user_type_default from user table.
  $userTypeDefault = user_type_default($user_name_in);
  
  if ( $userTypeDefault == NULL ) {
    // 1. Set the user's default user_type to School Bus Driver
    //    Not Employed by My Company. If this fails abort.
    $query = "UPDATE user
              SET user_type_default = '2'
              WHERE user_name = '$user_name_in'";
    $result = mysql_query($query);
    if (!$result || mysql_affected_rows() != 1) {
      die("Function user_type_set_default failed to save data. \n- Programmer.");
    }
  
    // 2. Set the user's user_type tokens to School Bus Driver
    //    Not Employed by My Comany. If this fails abort.
    user_type_set_tokens(2);
    
    // 3. Create the system default user_type for this user by adding
    //    a row to the table user_types_foreach.
    user_type_store($user_name_in, 2);
    
    // 4. Remove the array element which corresponds to the system
    //    default user_type which we just created from the array
    //    $user_type_field_options and set $user_type_field_options
    //    to FALSE if its array value ends up having zero elements.
    $user_type_field_options = removeUserType_justAdded(2, $user_type_field_options);
  }
}



function user_type_store($user_name_in, $user_type_in) {
// This function adds a row to the user_types_foreach table.
// The row being added will store the user_id for $user_name_in
// along with the user_type_id $user_type_in. Returns true when
// successful. If for any reason this function fails then abort.

  // Derive $user_id from $user_name_in.
  $query = "SELECT id
            FROM user
            WHERE user_name ='$user_name_in'";
  $result = mysql_query($query);
  if (!$result || mysql_num_rows($result) < 1){
    die("Function user_type_store failed getting id from table user.\n" .
    " - Programmer.");
  } else {
    $user_id = mysql_result($result, 0, 'id');
  }
  
  // Add row to table user_types_foreach containing
  // user_id and user_type_id.
  $query = "INSERT INTO user_types_foreach (
            user_id ,
            user_type_id
            )
            VALUES (
            '$user_id', '$user_type_in'
            )";
  $result = mysql_query($query);
  if (!$result) {
    die("Function user_type_store failed inserting row into table.\n" .
    " - Programmer.");
  } else {
    return true;
  }
}



function user_type_generate_eligibles($user_name_in) {
  // This function returns an array of arrays. The top level
  // array will be indexed 0 through n-1. Each element of
  // the top level array will contain an array having two
  // indices: id and label which specify a user_type.
  // The top level array is a collection of user types which
  // the user CAN CREATE using the createANewUser.php script.
  // The user types which the user CAN CREATE satisfy two
  // criteria: 1. The user is eligible to have them
  // 2. The user does NOT have them yet.
  // This function returns FALSE if no user types can be
  // returned. It will abort if something goes wrong.
  
  // Initialize index used in array of all user types.
  $id = 1;
  
  // Get all user types from database. Place them in
  // $userType_inSystem[]. If this array is empty then
  // $id will still be 1 after the loop. Otherwise, $id
  // will have value one more than the highest index of
  // the array.
  $query = "SELECT id, label
            FROM available_user_types";
  $result = mysql_query($query);
  while ($row = mysql_fetch_array($result)) {
    $userType_inSystem[$id] = $row;
    $id += 1;
  }
  
  if ($id == 1) {
    die("Something is wrong. Func user_type_generate_eligibles".
    " was unable to get any user types from database. -Programmer.");
  }
  
  // There is at least one elemnt in $userType_inSystem.
  // From this point on I assume all 4 user types have been
  // stored in $userType_inSystem. I also assume I know what
  // they are.
  
  // Reset the index for $userType_inSystem[].
  // Set it to 1 to consider possiblity our user is eligible
  // for user type having id == 1.
  $id = 1;
  
  // eligibleType[] will hold the user types our user is
  // found to be eligible for.
  
  // Initialize $indexfor_eligibleType.
  $indexfor_eligibleType = 0;


  // Generate and return array $alreadyCreated_UserType_id[].
  // There will be one element for each user_type in the system.
  // The index correlates to the id.
  // The value is a boolean which indicates whether the
  // user_type has already been created for the user.
  // This function will abort if it fails to get the
  // user_type data from the database.
  $alreadyCreated_UserType_id = alreadyCreatedUT_id($user_name_in);


  // Has $userType_inSystem[1] already been created?
  if ($alreadyCreated_UserType_id[$id] == false) {
    // Is our user the Administrator?
    if ($user_name_in == 'administrator') {
      $eligibleType[$indexfor_eligibleType] = $userType_inSystem[$id];
      $indexfor_eligibleType += 1;
    }
  }
  
  // Considering possibility our user is eligible for
  // user type having id == 2.
  $id = 2;


  // Has $userType_inSystem[2] already been created?
  if ($alreadyCreated_UserType_id[$id] == false) {
  
    // Is our user a Driver or Assistant for MD HC Public Schools?
    // I am allowing all users to be a Driver or Assistant for
    // MD HC Public Schools. Therefore yes.
    $eligibleType[$indexfor_eligibleType] = $userType_inSystem[$id];
    $indexfor_eligibleType += 1;
  }
  
  // Considering possibility our user is eligible for
  // user type having id == 3.
  $id = 3;


  // Has $userType_inSystem[3] already been created?
  if ($alreadyCreated_UserType_id[$id] == false) {
    // Is our user a Driver for SAMEH R LABIB, LLC?
    // Only users whose email address is found in the set of my
    // known driver email addresses will be eligible.
    if (isSRL_driver($user_name_in)) {
      $eligibleType[$indexfor_eligibleType] = $userType_inSystem[$id];
      $indexfor_eligibleType += 1;
    }
  }
  
  // Considering possibility our user is eligible for
  // user type having id == 4.
  $id = 4;

  // Has $userType_inSystem[4] already been created?
  if ($alreadyCreated_UserType_id[$id] == false) {  
    // Is our user a School Bus Contractor for MD HC Public Schools?
    // Only users whose email address is found in the set of
    // known School Bus Contractors for MD HC Public Schools
    // email addresses will be eligible.
    if (isMDHCPS_contractor($user_name_in)) {
      $eligibleType[$indexfor_eligibleType] = $userType_inSystem[$id];
    }
  }
  
  // Is $eligibleType[] an empty array? If it is then return
  // FALSE. Otherwise, return that array.
  if (isSet($eligibleType) && sizeof($eligibleType) > 0) {
    return $eligibleType;
  } else {
    return FALSE;
  }
}



function isMDHCPS_contractor($user_name_in) {
// Returns TRUE if our user is a School Bus Contractor for MD HC
// Public Schools. Otherwise, returns FALSE. Abort if something
// goes wrong.

  // Get the email address.
  $query = "SELECT email
            FROM user
            WHERE user_name ='$user_name_in'";
  $result = mysql_query($query);
  if (!$result || mysql_num_rows($result) < 1){
    die("Function isMDHCPS_contractor failed getting email from table user.\n" .
    " - Programmer.");
  } else {
    $email = mysql_result($result, 0, 'email');
  }
  
  // Find out if our user is a School Bus Contractor for MD HC
  // Public Schools. TRUE if $email is found in table called
  // md_hcps_contractor.
  $query = "SELECT email
            FROM md_hcps_contractor
            WHERE email = '$email'";
  $result = mysql_query($query);
  if (!$result || mysql_num_rows($result) < 1) {
    if (!$result) {
      die("Function isMDHCPS_contractor failed where it finds out".
      " if the email address is in the table.\n");
    }
    return FALSE;
  } else {
    return TRUE;
  }
}



function isSRL_driver($user_name_in) {
// Returns TRUE if our user works for SAMEH R LABIB, LLC.
// Otherwise, returns FALSE. Abort if something goes wrong.

  // Get the email address.
  $query = "SELECT email
            FROM user
            WHERE user_name ='$user_name_in'";
  $result = mysql_query($query);
  if (!$result || mysql_num_rows($result) < 1){
    die("Function isSRL_driver failed getting email from table user.\n" .
    " - Programmer.");
  } else {
    $email = mysql_result($result, 0, 'email');
  }
  
  // Find out if our user works for SAMEH R LABIB, LLC.
  // TRUE if $email is found in table called
  // srl_driver.
  $query = "SELECT email
            FROM srl_driver
            WHERE email = '$email'";
  $result = mysql_query($query);
  if (!$result || mysql_num_rows($result) < 1) {
    if (!$result) {
      die("Function isSRL_driver failed where it finds out".
      " if the email address is in the table.\n");
    }
    return FALSE;
  } else {
    return TRUE;
  }
}



function user_type_make_radio_button($user_type_field_options_in) {
// Returns a string containing the radio button form field
// using the given array of user types. If function fails
// kill the script. The input array is of the type generated by
// the function user_type_generate_eligibles($user_name_in).

  // If $user_type_field_options_in is not set or is not an array or it is an
  // array which has no elements then abort.
  if (!IsSet($user_type_field_options_in) || !is_array($user_type_field_options_in)
    || sizeof($user_type_field_options_in) < 1) {
    die("Function user_type_make_radio_button failed because it was not passed ".
    "an array.\n - Programmer.");
  }
  
  // Loop which generates the radio button.
  $user_type_button = "";
  reset($user_type_field_options_in);
  while ($array_cell = each($user_type_field_options_in))
  {
    $temp_1 = $array_cell['value']['id'];
    $temp_2 = $array_cell['value']['label'];
    $user_type_button .=
    "    <input type=\"radio\" name=\"user_type\" id=\"$temp_1\" value=\"$temp_1\" />" .
    "    <label for=\"$temp_1\">$temp_2</label>" .
    "<br/>\n";
  }
  
  // Return the radio button string.
  return $user_type_button;
}


function abortIfUT_spoofed($all_created_userTypesIN) {
// This function takes an array of user types like the one generated
// by the function alreadyCreated_userTypes and aborts if the user type
// id $user_typeIN is not part of the array. Also, this function will
// abort if $user_typeIN is a string which is too long.
  if (strlen($_POST['user_type']) > 3) {
    die("Error 091424. -Programmer.");
  }

  // If $all_created_userTypesIN is not set or is not an array or it is an
  // array which has no elements then abort.
  if (!IsSet($all_created_userTypesIN) || !is_array($all_created_userTypesIN)
    || sizeof($all_created_userTypesIN) < 1) {
    die("Function abortIfUT_spoofed failed because it was not passed ".
    "an array.\n - Programmer.");
  }

  $user_typeIN = $_POST['user_type'];
  
  // Abort if user type not found in our array.
  
  // Iterate through the array $all_created_userTypesIN.
  // We will return if $user_typeIN is found.
  // However, we will abort if not found.
  reset($all_created_userTypesIN);
  while($array_cell = each($all_created_userTypesIN))
  {
    if ($array_cell['value']['id'] == $user_typeIN) {
      return;
    }
  }
  die("Error 2940177. -Programmer.");
}


function removeUserType_justAdded($user_typeIN, $user_type_field_optionsIN) {
// We need to do the following statement so that the user type
// which was just created will not be presented again when the form
// is presented this time. We do this instead of running the function
// user_type_generate_eligibles($user_name) over again.
// This function will return the modified array if it still has at
// least one element. Otherwise, the function will return FALSE.
// An abort will occur if any failure or anomaly occures.

  // If $user_type_field_optionsIN is not set or is not an array or it is an
  // array which has no elements then abort.
  if (!IsSet($user_type_field_optionsIN) || !is_array($user_type_field_optionsIN)
    || sizeof($user_type_field_optionsIN) < 1) {
    die("Function removeUserType_justAdded failed because it was not passed ".
    "an array.\n - Programmer.");
  }
  
  $new_array = array();
  $index = 0;

  // Iterate through the array $user_type_field_optionsIN.
  reset($user_type_field_optionsIN);
  while($array_cell = each($user_type_field_optionsIN))
  {
    if ($array_cell['value']['id'] != $user_typeIN) {
      $new_array[$index] = $array_cell['value'];
      $index += 1;
    }
  }
  
  // Is $new_array[] an empty array? If it is then return
  // FALSE. Otherwise, return that array.
  if ( sizeof($new_array) > 0) {
    return $new_array;
  } else {
    return FALSE;
  }
}


function alreadyCreated_userTypes($user_nameIN) {
// This function was originaly made for selectYourUserType.php.
// Mainly it returns an array of user type elements. User type
// elements consist of id and label elements. This function
// will return these user type elements which the user has already
// created. This function will abort if no user types have been
// created for the user since this situation should not happen.
// Every user should have successfully run the createANewUserType.php
// script and therefore should at least have created the system default
// user type for themselves.

  // Generate and return array $alreadyCreated_UserType_id[].
  // There will be one element for each user_type in the system.
  // The index correlates to the id.
  // The value is a boolean which indicates whether the
  // user_type has already been created for the user.
  // This function will abort if it fails to get the
  // user_type data from the database.
  $alreadyCreated_UserType_id = alreadyCreatedUT_id($user_nameIN);
  
  
  // Here I need code which reads all the user types into an array.
  
  // Initialize index used in array of all user types.
  $id = 1;
  
  // Get all user types from database. Place them in
  // $userType_inSystem[]. If this array is empty then
  // $id will still be 1 after the loop. Otherwise, $id
  // will have value one more than the highest index of
  // the array.
  $query = "SELECT id, label
            FROM available_user_types
            LIMIT 0 , 30";
  $result = mysql_query($query);
  while ($row = mysql_fetch_array($result)) {
    $userType_inSystem[$id] = $row;
    $id += 1;
  }
  
  if ($id == 1) {
    die("Error from function alreadyCreated_userTypes. No user type".
    "data retrieved. -Programmer");
  }

  
  // Generate and return array $alreadyCreated_UserType_id[].
  // There will be one element for each user_type in the system.
  // The index correlates to the id.
  // The value is a boolean which indicates whether the
  // user_type has already been created for the user.
  // This function will abort if it fails to get the
  // user_type data from the database.
  $alreadyCreated_UserType_id = array();
  $alreadyCreated_UserType_id = alreadyCreatedUT_id($user_nameIN);
  
  
  // Loop that generates returned array.
  
  $new_array = array();
  $index = 0;

  // Iterate through the array $userType_inSystem.
  reset($userType_inSystem);
  while($array_cell = each($userType_inSystem))
  {
    if ($alreadyCreated_UserType_id[$array_cell['value']['id']]) {
      $new_array[$index] = $array_cell['value'];
      $index += 1;
    }
  }
  
  // Is $new_array[] an empty array? If it is then return
  // FALSE. Otherwise, return that array.
  if ( sizeof($new_array) >= 1) {
    return $new_array;
  } else {
    return FALSE;
  }
}


function alreadyCreatedUT_id($user_name_in) {
// Generate and return array $alreadyCreated_UserType_id[]
// used in function user_type_generate_eligibles() which is
// used in the createANewUserType.php script.
// There will be one element for each user_type in the system.
// The index correlates to the id.
// The value is a boolean which indicates whether the
// user_type has already been created for the user.
// This function will abort if it fails to get the
// user_type data from the database.

  $returnArr = array();
  
  $numberOfUserTypes = numOfUserTypes();
  
  for ($index = 1; $index <= $numberOfUserTypes; $index++) {
    $returnArr[$index] = isCreated($user_name_in, $index);
  }
  
  if (count($returnArr) >= 1) {
    return $returnArr;
  } else {
    die("Error from function alreadyCreatedUT_id. No array".
    "was generated. -Programmer");
  }
}


function numOfUserTypes() {
// Returns the number of user types in the system.
// Otherwise aborts.
  $query = "SELECT COUNT(*)
            FROM available_user_types";
  $result = mysql_query($query);
  if (!$result) {
    die("Function numOfUserTypes failed where it finds out".
    " the number of rows in the table.\n");
  }
  $numberOfRows = mysql_result($result, 0, 0);
  return $numberOfRows;
}


function isCreated($user_nameIN, $userTypeID_IN) {
// This function returns a boolean reflecting the createdness
// status of a particular user type id for a particular user.
// Abort on error accessing the database.

  // Derive $user_id from $user_nameIN.
  $query = "SELECT id
            FROM user
            WHERE user_name ='$user_nameIN'";
  $result = mysql_query($query);
  if (!$result || mysql_num_rows($result) < 1){
    die("Function isCreated failed getting id from table user.\n" .
    " - Programmer.");
  } else {
    $user_id = mysql_result($result, 0, 'id');
  }

  // Find out if the user id user type combination
  // exists in the table user_types_foreach. Then,
  // return boolean.
  $query = "SELECT *
            FROM user_types_foreach
            WHERE user_id = '$user_id'
            AND user_type_id = '$userTypeID_IN'
            LIMIT 0, 30";
  $result = mysql_query($query);
  if (!$result) {
    die("Function isCreated failed accessing database table." .
    " - Programmer.");
  }
  if (mysql_num_rows($result) < 1) {
    return FALSE;
  } else {
    return TRUE;
  }
}
?>