<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

/*****************************************************
* New user confirmation page. Should only get here *
* from an email link. *
*****************************************************/


require_once('includes/register_funcs.php');
include_once('includes/header_footer.php');

site_header('Account Confirmation');

// Initialize variables
$feedback_str = "";
$worked = 0;
$noconfirm = "";
$confirm = "";

if (!IsSet($_GET['hash']) || !IsSet($_GET['email'])) {
  $feedback_str = "<p class=\"errmsg\">ERROR -- Bad link</p>";
} else {
  if ( $_GET['hash'] && $_GET['email'] ) {
    $worked = user_confirm();
  }
}


if ($worked != 1) {
  $noconfirm = '<p class="errmsg">Something went wrong. ' .
    'Send email to gxsam11@gxsam11.net for help. If you ' .
    'came to this page directly, please go to login.php ' .
    'instead.</p>';
} else {
   $confirm = '<p>You are now confirmed. <a ' .
     'href="login.php">Log in</a> to start browsing the ' .
     'site.</p>';
}

$page = <<<EOPAGE

  $feedback_str
  $noconfirm
  $confirm

EOPAGE;
echo $page;

site_footer();

?>