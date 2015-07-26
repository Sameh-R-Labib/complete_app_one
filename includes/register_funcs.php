<?php

// A file with the database host, user, password, and
// selected database
$jeffWiggleLabib = "Wake up Jeff 59990!";
include_once('db_vars.php');

// A string used for md5 encryption. You could move it
// to a file outside the web tree for more security.
$supersecret_hash_padding = 'Halleluyah choir at the mall. Fond memories.';

function user_register() {
  // This function will only work with superglobal arrays,
  // because I'm not passing in any values or declaring globals
  global $supersecret_hash_padding;
  // Are all vars present and passwords match?
  if (strlen($_POST['user_name']) <=25 && strlen($_POST['password1']) <= 25 &&
    ($_POST['password1'] == $_POST['password2']) && strlen($_POST['email']) <= 50 &&
    validate_email()) {
    // Validate username and password
    if (account_namevalid() && (strlen($_POST['password1']) >= 6)) {
      $user_name = strtolower($_POST['user_name']);
      $user_name = trim($user_name);
      // Don't need to escape, because single quotes aren't allowed
      $email = $_POST['email'];
      // Don't allow duplicate usernames or emails
      $query = "SELECT id
              FROM user
              WHERE user_name = '$user_name' OR email = '$email'";
      $result = mysql_query($query);
      if ($result && mysql_num_rows($result) > 0) {
        $feedback = 'ERROR -- Username or email address already exists';
        return $feedback;
      } else {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $password = md5($_POST['password1']);
        $user_ip = $_SERVER['REMOTE_ADDR'];
        // Create a new hash to insert into the db and the confirmation email
        $hash = md5($email.$supersecret_hash_padding);
        $query = "INSERT INTO user (user_name, first_name,
          last_name, password, email, remote_addr, confirm_hash,
          is_confirmed, date_created)
          VALUES ('$user_name', '$first_name', '$last_name',
          '$password', '$email', '$user_ip', '$hash', '0',
          NOW())";
        $result = mysql_query($query);
        if (!$result) {
          $feedback = 'ERROR -- Database error';
          return $feedback;
        } else {
          // Send the confirmation email
          $encoded_email = urlencode($_POST['email']);
          $mail_body = <<<EOMAILBODY
Thank you for registering at www.gxsam11.net. Click this link
to confirm your registration:

http://www.gxsam11.net/web/confirm.php?hash=$hash&email=$encoded_email

Once you see a confirmation message, you will be logged into
www.gxsam11.net
EOMAILBODY;
          mail($email, 'www.gxsam11.net registration confirmation',
            $mail_body, 'From: noreply@gxsam11.net');
          // Give a successful registration message
          $feedback = 'YOU HAVE SUCCESSFULLY REGISTERED.
            You will receive a confirmation email soon';
          return $feedback;
        }
      }
    } else {
      $feedback = 'ERROR -- Username or password is invalid';
      return $feedback;
    }
  } else {
    $feedback = 'ERROR -- Please fill in all fields correctly';
    return $feedback;
  }
}



function account_namevalid() {
  // parameter for use with strspan
$span_str = "abcdefghijklmnopqrstuvwxyz" .
    "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-";
  // must have at least one character
  if (strspn($_POST['user_name'], $span_str) == 0) {
    return false;
  }
  // must contain all legal characters
  if (strspn($_POST['user_name'], $span_str) != strlen($_POST['user_name'])) {
    return false;
  }
  // min and max length
  if (strlen($_POST['user_name']) < 5) {
    return false;
  }
  if (strlen($_POST['user_name']) > 25) {
    return false;
  }
  // illegal names
  $rex = '^(root|bin|daemon|adm|lp|sync|shutdown|halt|' .
    'mail|news|uucp|operator|games|mysql|httpd|nobody|' .
    'dummy|www|cvs|shell|ftp|irc|debian|ns|download)$';
  if (eregi($rex, $_POST['user_name'])) {
    return false;
  }
  if (eregi("^(anoncvs_)", $_POST['user_name'])) {
    return false;
  }

return true;
}



function validate_email() {
  return (ereg(
'^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+'.
'@'.
'[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+\.'.
'[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$',
$_POST['email']));
}



function user_confirm() {
  // This function will only work with superglobal arrays,
  // because Im not passing in any values or declaring globals
  global $supersecret_hash_padding;
  // Verify that they didnt tamper with the email address
  $new_hash = md5($_GET['email'].$supersecret_hash_padding);
  if ($new_hash && ($new_hash == $_GET['hash'])) {
    $query = "SELECT user_name
              FROM user
              WHERE confirm_hash = '$new_hash'";
    $result = mysql_query($query);
    if (!$result || mysql_num_rows($result) < 1) {
      $feedback = 'ERROR -- Hash not found';
      return $feedback;
    } else {
      // Confirm the email and set account to active
      $email = $_GET['email'];
      $hash = $_GET['hash'];
    $query = "UPDATE user SET email='$email',
is_confirmed='1' WHERE confirm_hash='$hash'";
      $result = mysql_query($query);
      return 1;
    }
  } else {
    $feedback = 'ERROR -- Values do not match';
    return $feedback;
  }
}

?>