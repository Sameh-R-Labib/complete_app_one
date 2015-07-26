<?php

require_once('register_funcs.php');

function user_change_password() {
  $feedback = "";
  // Do new passwords match?
  if ($_POST['new_password1'] && ($_POST['new_password1'] ==
    $_POST['new_password2'])) {
    // Is password long enough?
    if (strlen($_POST['new_password1']) >= 6) {
      // Is the old password correct?
      if (strlen($_POST['old_password']) > 1) {
        $change_user_name = strtolower($_COOKIE['user_name']);
        $old_password = $_POST['old_password'];
         $crypt_pass = md5($old_password);
        $new_password1 = $_POST['new_password1'];
        $query = "SELECT *
                  FROM user
                  WHERE user_name = '$change_user_name'
                  AND password = '$crypt_pass'";
        $result = mysql_query($query);
        if (!$result || mysql_num_rows($result) < 1) {
          $feedback = 'ERROR -- User not found or bad password';
          return $feedback;
        } else {
          $crypt_newpass = md5($new_password1);
          $query = "UPDATE user
                    SET password = '$crypt_newpass'
                    WHERE user_name = '$change_user_name'
                    AND password = '$crypt_pass'";
          $result = mysql_query($query);
          if (!$result || mysql_affected_rows() < 1) {
            $feedback = 'ERROR -- Problem updating password';
            return $feedback;
          } else {
            return 1;
          }
        }
      } else {
        $feedback = 'ERROR -- Please enter old password';
        return $feedback;
      }
    } else {
      $feedback = 'ERROR -- New password not long enough';
      return $feedback;
    }
  } else {
    $feedback = 'ERROR -- Your passwords do not match';
    return $feedback;
  }
}



function user_change_email() {
  global $supersecret_hash_padding;
  if (validate_email()) {
    $hash = md5($_POST['email'].$supersecret_hash_padding);

    // Send out a new confirm email with a new hash
    $user_name = strtolower($_COOKIE['user_name']);
    $password1 = $_POST['password1'];
    $crypt_pass = md5($password1);
    $query = "UPDATE user
              SET confirm_hash = '$hash',
                  is_confirmed = 0
              WHERE user_name = '$user_name'
              AND password = '$crypt_pass'";
    $result = mysql_query($query);
    if (!$result || mysql_affected_rows() < 1) {
      $feedback = 'ERROR -- Wrong password';
      return $feedback;
    } else {
      // Send the confirmation email
      $email = $_POST['email'];
      $encoded_email = urlencode($_POST['email']);
      $mail_body = <<< EOMAILBODY
Thank you for registering at gxsam11.net. Click this link to
confirm your registration:

http://gxsam11.net/web/confirm.php?hash=$hash&email=$encoded_email

Once you see a confirmation message, you will be logged
into gxsam11.net
EOMAILBODY;

       mail($email, 'gxsam11.net Registration Confirmation',
         $mail_body, 'From: noreply@gxsam11.net');
       // Logout the user
       user_logout();
       return 1;
    }
  } else {
    $feedback = 'ERROR- New email address is invalid';
    return $feedback;
  }
}

?>