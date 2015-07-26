<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once('../includes/login_funcs.php');
require_once('../includes/db_vars.php');
require_once('../includes/header_footer.php');

// User must be logged in and be someone special.
if (!user_isloggedin() || !user_type_cookieIsSet()) {
  $host = $_SERVER['HTTP_HOST'];
  header("Location: http://{$host}/web/login.php");
  exit;
}
if ($_COOKIE['user_type'] != 1) {
  die('Script aborted #3098. -Programmer.');
}


site_header('User_Register Function');

$page_str = <<<EOPAGESTR5

<p>This function comes from /web/includes/register_funcs.php.
Here is its flowchart:</p>

<div>
  <img src="user_register_FUNC.jpg" width="585" height="2200" alt="user_register() flowchart" />
</div>

<p>Here is the code:</p>

<pre>
function user_register() {
  // This function will only work with superglobal arrays,
  // because I'm not passing in any values or declaring globals
  global &#36;supersecret_hash_padding;
  // Are all vars present and passwords match?
  if (strlen(&#36;_POST['user_name']) &lt;=25 && strlen(&#36;_POST['password1']) &lt;= 25 &&
    (&#36;_POST['password1'] == &#36;_POST['password2']) && strlen(&#36;_POST['email']) &lt;= 50 &&
    validate_email()) {
    // Validate username and password
    if (account_namevalid() && (strlen(&#36;_POST['password1']) >= 6)) {
      &#36;user_name = strtolower(&#36;_POST['user_name']);
      &#36;user_name = trim(&#36;user_name);
      // Don't need to escape, because single quotes aren't allowed
      &#36;email = &#36;_POST['email'];
      // Don't allow duplicate usernames or emails
      &#36;query = "SELECT id
              FROM user
              WHERE user_name = '&#36;user_name' OR email = '&#36;email'";
      &#36;result = mysql_query(&#36;query);
      if (&#36;result && mysql_num_rows(&#36;result) > 0) {
        &#36;feedback = 'ERROR -- Username or email address already exists';
        return &#36;feedback;
      } else {
        &#36;first_name = &#36;_POST['first_name'];
        &#36;last_name = &#36;_POST['last_name'];
        &#36;password = md5(&#36;_POST['password1']);
        &#36;user_ip = &#36;_SERVER['REMOTE_ADDR'];
        // Create a new hash to insert into the db and the confirmation email
        &#36;hash = md5(&#36;email.&#36;supersecret_hash_padding);
        &#36;query = "INSERT INTO user (user_name, first_name,
          last_name, password, email, remote_addr, confirm_hash,
          is_confirmed, date_created)
          VALUES ('&#36;user_name', '&#36;first_name', '&#36;last_name',
          '&#36;password', '&#36;email', '&#36;user_ip', '&#36;hash', '0',
          NOW())";
        &#36;result = mysql_query(&#36;query);
        if (!&#36;result) {
          &#36;feedback = 'ERROR -- Database error';
          return &#36;feedback;
        } else {
          // Send the confirmation email
          &#36;encoded_email = urlencode(&#36;_POST['email']);
          &#36;mail_body = &lt;&lt;&lt;EOMAILBODY
Thank you for registering at www.gxsam11.net. Click this link
to confirm your registration:

http://www.gxsam11.net/web/confirm.php?hash=&#36;hash&email=&#36;encoded_email

Once you see a confirmation message, you will be logged into
www.gxsam11.net
EOMAILBODY;
          mail(&#36;email, 'www.gxsam11.net registration confirmation',
            &#36;mail_body, 'From: noreply@gxsam11.net');
          // Give a successful registration message
          &#36;feedback = 'YOU HAVE SUCCESSFULLY REGISTERED.
            You will receive a confirmation email soon';
          return &#36;feedback;
        }
      }
    } else {
      &#36;feedback = 'ERROR -- Username or password is invalid';
      return &#36;feedback;
    }
  } else {
    &#36;feedback = 'ERROR -- Please fill in all fields correctly';
    return &#36;feedback;
  }
}
</pre>

EOPAGESTR5;
echo $page_str;

site_footer();
?>