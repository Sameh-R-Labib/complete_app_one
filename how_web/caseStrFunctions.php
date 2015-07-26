<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once('../includes/login_funcs.php');
require_once('../includes/db_vars.php');
require_once('../includes/header_footer.php');

// User must be logged in and be someone special.
if (!user_isloggedin() || !user_type_cookieIsSet()) {
  // Redirect to login page.
  $host = $_SERVER['HTTP_HOST'];
  header("Location: http://{$host}/web/login.php");
  exit;
}
if ($_COOKIE['user_type'] != 1) {
  die('Script aborted #3098. -Programmer.');
}


site_header('Case');

$page_str = <<<EOPAGESTR5

<p>These functions change lowercase to uppercase and vice versa. The first two
capitalize/decapitalize entire strings, whereas the second two operate only on first
letters of words.</p>

<table class="events" width="678">
  <caption></caption>
  <tr>
    <th>Function</th>
    <th>Description</th>
  </tr>
  <tr>
    <td>strtolower()</td>
    <td>The strtolower() function returns an all-lowercase string. It doesn’t matter if
    the original is all uppercase or mixed.</td>
  </tr>
  <tr>
    <td>strtoupper()</td>
    <td>The strtoupper() function returns an all-uppercase string, regardless of whether
    the original was all lowercase or mixed.</td>
  </tr>
  <tr>
    <td>ucfirst()</td>
    <td>The ucfirst() function capitalizes only the first letter of a string.</td>
  </tr>
  <tr>
    <td>ucwords()</td>
    <td>The ucwords() function capitalizes the first letter of each word in a string.</td>
  </tr>
</table>

<h2>note</h2>

<p>Neither ucwords() nor ucfirst() converts anything into lowercase. Each makes only the
appropriate leading letters into uppercase. If there are inappropriate capital letters in
the middle of words, they will not be corrected.</p>

EOPAGESTR5;
echo $page_str;

site_footer();
?>