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


site_header('Tests on Numbers');

$page_str = <<<EOPAGESTR5

<p>PHP offers a handful of functions for doing tests on numbers. Despite
PHP's type looseness, it's a good idea to employ some of these tests in
your code to help anticipate what sorts of results you will get; and how
best to handle them.</p>

<table class="events" width="678">
  <caption>Functions that test values:</caption>
  <tr>
    <th>Function</th>
    <th>Description</th>
    <th>Parameters</th>
    <th>Return Values</th>
  </tr>
  <tr>
    <td>
      <p>is_numeric</p>
    </td>
    <td>
      <p>bool is_numeric ( mixed &#36;var )</p>
      <p>Finds whether the given variable is numeric. Numeric strings
      consist of optional sign, any number of digits, optional decimal
      part and optional exponential part. Thus +0123.45e6 is a valid
      numeric value. Hexadecimal notation (0xFF) is allowed too but only
      without sign, decimal and exponential part.</p>
    </td>
    <td>
      <p>var</p>
      <blockquote>The variable being evaluated.</blockquote>
    </td>
    <td>
      <p>Returns TRUE if var is a number or a numeric string, FALSE otherwise.</p>
    </td>
  </tr>
  <tr>
    <td>
      <p>is_int</p>
    </td>
    <td>
      <p>bool is_int ( mixed &#36;var )</p>
      <p>Finds whether the type of the given variable is integer.</p>
      <p>note: To test if a variable is a number or a numeric string (such as form
      input, which is always a string), you must use is_numeric().</p>
    </td>
    <td>
      <p>var</p>
      <blockquote>The variable being evaluated.</blockquote>
    </td>
    <td>
      <p>Returns TRUE if var is an integer, FALSE otherwise.</p>
    </td>
  </tr>
  <tr>
    <td>
      <p>is_float</p>
    </td>
    <td>
      <p>bool is_float ( mixed &#36;var )</p>
      <p>Finds whether the type of the given variable is float.</p>
      <p>note: To test if a variable is a number or a numeric string (such as form
      input, which is always a string), you must use is_numeric().</p>
    </td>
    <td>
      <p>var</p>
      <blockquote>The variable being evaluated.</blockquote>
    </td>
    <td>
      <p>Returns TRUE if var is a float, FALSE otherwise.</p>
    </td>
  </tr>
  <tr>
    <td>
      <p>is_finite</p>
    </td>
    <td>
      <p>Finds whether a value is a legal finite number</p>
      <p>bool is_finite ( float &#36;val )</p>
      <p>Checks whether val is a legal finite on this platform.</p>
    </td>
    <td>
     <p>val</p>
     <blockquote>The value to check.</blockquote>
    </td>
    <td>
      <p>TRUE if val is a legal finite number within the allowed range for a PHP
    float on this platform, else FALSE.</p>
    </td>
  </tr>
  <tr>
    <td>
      <p>is_infinite</p>
    </td>
    <td>
      <p>Finds whether a value is infinite.</p>
      <p>bool is_infinite ( float &#36;val )</p>
      <p>Returns TRUE if val is infinite (positive or negative), like the result
      of log(0) or any value too big to fit into a float on this platform.</p>
    </td>
    <td>
      <p>val</p>
      <blockquote>The value to check.</blockquote>
    </td>
    <td>
      <p>TRUE if val is infinite, else FALSE.</p>
    </td>
  </tr>
  <tr>
    <td>
      <p>is_nan</p>
    </td>
    <td>
      <p>bool is_nan ( float &#36;val )</p>
      <p>Checks whether val is 'not a number', like the result of acos(1.01).</p>
    </td>
    <td>
     <p>val</p>
     <blockquote>The value to check.</blockquote>
    </td>
    <td>
      <p>Returns TRUE if val is 'not a number', else FALSE.</p>
    </td>
  </tr>
</table>


EOPAGESTR5;
echo $page_str;

site_footer();
?>