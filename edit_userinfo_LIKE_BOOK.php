<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/**********************************************
* This file displays the change non-sensitive *
* user data form. It submits to itself, and   *
* displays a message each time you submit.    *
***********************************************/

// A file with the database host, user, password, and
// selected database
require_once('includes/db_vars.php');
include_once('includes/header_footer.php');
include_once('includes/login_funcs.php');

if (!user_isloggedin()) {
  echo '<P>You are not logged in, or this is not your user profile.</P>';
} else {
  $user_name = $_COOKIE['user_name'];

  // To reduce calls to strlen()
  if (isset($_POST['submit']) && $_POST['submit'] == "Edit user data") {
    $lenStrGender = strlen($_POST['gender']);
    $lenStrPrivPr = strlen($_POST['priv_profile']);
  }

  if (isset($_POST['submit']) && $_POST['submit'] == "Edit user data" &&
    $lenStrGender == 1 && $lenStrPrivPr == 1) {

    // I don't want to use $_POST array in
    // the query string for consistency and
    // possibly more.
    $gender = $_POST['gender'];
    $priv_profile = $_POST['priv_profile'];

    // Send data to db

    // NOTES:
    // The PHP directive  magic_quotes_gpc is on by default,
    // and it essentially runs addslashes() on all GET, POST,
    // and COOKIE data. Do not use addslashes() on strings
    // that have already been escaped with magic_quotes_gpc
    // as you'll then do double escaping. The function
    // get_magic_quotes_gpc() may come in handy for checking
    // this. The following code takes care of this problem:
    if (!get_magic_quotes_gpc()) {
      // Here we are adding slashes
      $as_photo_url = addslashes($_POST['photo_url']);
      $as_homepage_url = addslashes($_POST['homepage_url']);
      $as_fav_link1 = addslashes($_POST['fav_link1']);
      $as_fav_link2 = addslashes($_POST['fav_link2']);
      $as_fav_link3 = addslashes($_POST['fav_link3']);
      $as_location = addslashes($_POST['location']);
      $as_country = addslashes($_POST['country']);
    } else {
      // Here we assign the POST vars to vars with var names
      // that are consistent with the rest of our code
      $as_photo_url = $_POST['photo_url'];
      $as_homepage_url = $_POST['homepage_url'];
      $as_fav_link1 = $_POST['fav_link1'];
      $as_fav_link2 = $_POST['fav_link2'];
      $as_fav_link3 = $_POST['fav_link3'];
      $as_location = $_POST['location'];
      $as_location = $_POST['country'];
    }

    // Im not bothering to check the stringlength of these
    // because Im URL-encoding them

    // Possible reasons why we are urlencoding:
    // 1. To make sure length of string is okay.
    // 2. If user used inapropriate characters they will be taken care of.
    // 3. Make shure there is no ' or " characters which would mess up the query.
    // 4. Make sure no other characters in the string will mess up the query.
    // The PHP manual says: urlencode function is convenient when encoding a string to
    // be used in a query part of a URL, as a convenient way to pass variables to
    // the next page. Here "query" refers to GET like query. Also note they are talking
    // about encoding only the part of the URL used for query (not the http://domain.com?)
    // part.

    $ue_photo_url = urlencode($as_photo_url);
    $ue_homepage_url = urlencode($as_homepage_url);
    $ue_fav_link1 = urlencode($as_fav_link1);
    $ue_fav_link2 = urlencode($as_fav_link2);
    $ue_fav_link3 = urlencode($as_fav_link3);

    $query = "UPDATE user
              SET photo = '$ue_photo_url',
                  homepage = '$ue_homepage_url',
                  link1 = '$ue_fav_link1',
                  link2 = '$ue_fav_link2',
                  link3 = '$ue_fav_link3',
                  location = '$as_location',
                  country = '$as_country',
                  gender = '$gender',
                  priv_profile = '$priv_profile'
              WHERE user_name = '$user_name'";
    $result = mysql_query($query);
    if (!$result) {
      $status_message = 'Problem with user data entry';
    } else {
      $status_message = 'Successfully edited user data';
    }

  // You don't want to be evaluating $lenStrGender or $lenStrPrivPr
  // unless they've been defined or assigned a value. That is why
  // I'm using isset and checking the other one before doing OR.
  } elseif ((isset($_POST['submit']) && $_POST['submit'] == "Edit user data") &&
      ($lenStrGender > 1 || $lenStrPrivPr > 1)) {
    // Bad user, smack on wrist
    $status_message = 'You\'re trying to do something very ' .
      'odd with this form. Stop it now.';
  }

  // Get previously-existing data
  $query = "SELECT photo, homepage, link1, link2, link3, ' .
    'location, country, gender, priv_profile
            FROM user
            WHERE user_name = '$user_name'";

  $result = mysql_query($query);
  // Shall we have an error message if no data comes back?

  if (!$result || mysql_num_rows($result) < 1) {
    // This SHOULD execute if NO row was read from db.
    $status_message .= 'No record was retrieved. Login and try again.';
    $photo_url = "";
    $homepage_url = "";
    $fav_link1 = "";
    $fav_link2 = "";
    $fav_link3 = "";
    $location = "";
    $country = "";
    $gender = "";
    $priv_profile = "";
  } else {
    // This should not execute if NO row was read from db.
    $user_array = mysql_fetch_array($result);
    $photo_url = urldecode($user_array['photo']);
    $photo_url = stripslashes($photo_url);
    $homepage_url = urldecode($user_array['homepage']);
    $homepage_url = stripslashes($homepage_url);
    $fav_link1 = urldecode($user_array['link1']);
    $fav_link1 = stripslashes($fav_link1);
    $fav_link2 = urldecode($user_array['link2']);
    $fav_link2 = stripslashes($fav_link2);
    $fav_link3 = urldecode($user_array['link3']);
    $fav_link3 = stripslashes($fav_link3);
    $location = stripslashes($user_array['location']);
    $country = $user_array['country'];
    $gender = $user_array['gender'];
    $priv_profile = $user_array['priv_profile'];
  }

  // Construct the multiple field types
  if ($gender == "M") {
    $gender_button_str = '<INPUT TYPE="RADIO" NAME="gender" VALUE="M" CHECKED>M
      <INPUT TYPE="RADIO" NAME="gender" VALUE="F">F';
  } elseif ($gender == "F") {
    $gender_button_str = '<INPUT TYPE="RADIO" NAME="gender" VALUE="M">M
      <INPUT TYPE="RADIO" NAME="gender" VALUE="F" CHECKED>F';
  } else {
    $gender_button_str = '<INPUT TYPE="RADIO" NAME="gender" VALUE="M">M
      <INPUT TYPE="RADIO" NAME="gender" VALUE="F">F';
  }
  if ($priv_profile == 1) {
    $priv_profile_str = '<INPUT TYPE="RADIO" NAME="priv_profile" VALUE="1" CHECKED>Yes
                <INPUT TYPE="RADIO" NAME="priv_profile" VALUE="0">No';
  } elseif ($priv_profile == 0) {
    $priv_profile_str = '<INPUT TYPE="RADIO" NAME="priv_profile" VALUE="1">Yes
      <INPUT TYPE="RADIO" NAME="priv_profile" VALUE="0" CHECKED>No';
  } else {
    $priv_profile_str = '<INPUT TYPE="RADIO" NAME="priv_profile" VALUE="1">Yes
      <INPUT TYPE="RADIO" NAME="priv_profile" VALUE="0">No';
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
    $message_str = "<P><FONT COLOR=\"#ff0000\">$status_message</FONT></P>";
  }

  $userform_str = <<<EOUSERFORMSTR
<TABLE ALIGN="CENTER" WIDTH="621">
<TR>
  <TD ROWSPAN="10"><IMG WIDTH="15" HEIGHT="1" SRC="../images/spacer.gif"></TD>
<TD WIDTH="606"></TD>
</TR>
<TR>
 <TD>

<P CLASS="bold">USER PROFILE<BR><BR>
<A HREF="changeemail.php">Change your email address</A><BR><BR>
<A HREF="changepass.php">Change your password</A>

$message_str

<FORM ACTION="$php_self" METHOD="POST">
<P CLASS="left">
<B>Photo URL</B> (i.e. http://www.my.com/foto.jpg)<BR>
<INPUT TYPE="TEXT" NAME="photo_url" VALUE="$photo_url" SIZE="40" MAXLENGTH="65">
</P>
<P CLASS="left">
<B>Homepage URL </B>(e.g. http://www.my.com/page.html)<BR>
<INPUT TYPE=TEXT NAME=homepage_url VALUE="$homepage_url" SIZE="40" MAXLENGTH="65">
</P>
<P CLASS="bold">
Favorite links<BR>
<INPUT TYPE="TEXT" NAME="fav_link1" VALUE="$fav_link1" SIZE="40" MAXLENGTH="65">
</P>
<P>
<INPUT TYPE="TEXT" NAME="fav_link2" VALUE="$fav_link2" SIZE="40" MAXLENGTH="65">
</P>
<P>
<INPUT TYPE="TEXT" NAME="fav_link3" VALUE="$fav_link3" SIZE="40" MAXLENGTH="65">
</P>
<P CLASS="left">
<B>Location</B> (City, State)<BR>
<INPUT TYPE="TEXT" NAME="location" VALUE="$location" SIZE="35" MAXLENGTH="50">
</P>
<P CLASS="left">
<B>Country<BR>
<INPUT TYPE="TEXT" NAME="country" VALUE="$country" SIZE="35" MAXLENGTH="50">
</P>
<P CLASS="bold">
Gender<BR>
$gender_button_str
</P>
<P CLASS="bold">
Make user profile private?<BR>
$priv_profile_str
</P>
<P>
<INPUT TYPE="SUBMIT" NAME="submit" VALUE="Edit user data">
</FORM>

  </TD>
</TR>
</TABLE>
EOUSERFORMSTR;
  echo $userform_str;

  site_footer();

}

?>