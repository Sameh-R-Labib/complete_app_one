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


site_header('Exponents & Logarithms');

$page_str = <<<EOPAGESTR5

<p>PHP includes the standard exponential and logarithmic functions, in both base 10
and base e varieties. Unlike with exp() and the base e, there is no single-argument
function to raise 10 to a given power, but in its place you can use the two-argument
function pow() with 10 as the first argument.</p>

<table class="events" width="678">
  <caption>Exponential Functions</caption>
  <tr>
    <th>Function</th>
    <th>Behavior</th>
  </tr>
  <tr>
    <td>pow()</td>
    <td>Takes two numerical arguments and returns the first argument raised to the
    power of the second. The value of pow(&#36;x, &#36;y)is x<sup>y</sup>.</td>
  </tr>
  <tr>
    <td>exp()</td>
    <td>Takes a single argument and raises e to that power. The value of exp(&#36;x)
    is e<sup>x</sup>.</td>
  </tr>
  <tr>
    <td>log()</td>
    <td>The "natural log" function. Takes a single argument and returns its base e logarithm.
If e<sup>y</sup> = x, then the value of log(&#36;x) is y.</td>
  </tr>
  <tr>
    <td>log10()</td>
    <td>Takes a single argument and returns its base-10 logarithm. If 10<sup>y</sup> = x,
    then the value of log10(&#36;x) is y.</td>
  </tr>
</table>

EOPAGESTR5;
echo $page_str;

site_footer();
?>