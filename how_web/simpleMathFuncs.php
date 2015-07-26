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


site_header('Simple Math Functions');

$page_str = <<<EOPAGESTR5

<p>The next step up in sophistication from the arithmetic operators consists
of miscellaneous functions that perform tasks like converting between the two
numerical types and finding the minimum and maximum of a set of numbers.</p>

<table class="events" width="678">
  <caption></caption>
  <tr>
    <th>Function</th>
    <th>Description</th>
  </tr>
  <tr>
    <td>floor()</td>
    <td>Takes a single argument (typically a double) and returns the largest
integer that is less than or equal to that argument.</td>
  </tr>
  <tr>
    <td>ceil()</td>
    <td>Short for ceiling — takes a single argument (typically a double) and
returns the smallest integer that is greater than or equal to that
argument.</td>
  </tr>
  <tr>
    <td>round()</td>
    <td>Takes a single argument (typically a double) and returns the nearest
integer. If the fractional part is exactly 0.5, it returns the nearest even
number.</td>
  </tr>
  <tr>
    <td>abs()</td>
    <td>Short for absolute value — if the single numerical argument is negative,
the corresponding positive number is returned; if the argument is positive,
the argument itself is returned.</td>
  </tr>
  <tr>
    <td>min()</td>
    <td>Takes any number of numerical arguments (but at least one) and returns the
smallest of the arguments.</td>
  </tr>
  <tr>
    <td>max()</td>
    <td>Takes any number of numerical arguments (but at least one) and returns the
largest of the arguments.</td>
  </tr>
</table>

<p>For example, the result of the following expression:</p>

<pre>
min(3, abs(-3), max(round(2.7), ceil(2.3), floor(3.9)))
</pre>

<p>is 3, because the value of every function call is also 3.</p>

EOPAGESTR5;
echo $page_str;

site_footer();
?>