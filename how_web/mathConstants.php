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


site_header('Math Constants');

$page_str = <<<EOPAGESTR5

<p>The general naming scheme is M_&lt;constant-name>. In cases where
the constant is a ratio (x/y), the name is M_X_Y, and in cases where
there is an operation on a number, the name is M_OPERNUM (for
example, M_SQRT2).</p>

<table class="events" width="678">
  <caption>Mathematical Constants</caption>
  <tr>
    <th>Constant</th>
    <th>Description</th>
  </tr>
  <tr>
    <td>M_PI</td>
    <td>Pi</td>
  </tr>
  <tr>
    <td>M_PI_2</td>
    <td>pi/2</td>
  </tr>
  <tr>
    <td>M_PI_4</td>
    <td>pi/4</td>
  </tr>
  <tr>
    <td>M_1_PI</td>
    <td>1/pi</td>
  </tr>
  <tr>
    <td>M_2_PI</td>
    <td>2/pi</td>
  </tr>
  <tr>
    <td>M_2_SQRTPI</td>
    <td>2/sqrt(pi)</td>
  </tr>
  <tr>
    <td>M_E</td>
    <td>the constant e</td>
  </tr>
  <tr>
    <td>M_SQRT2</td>
    <td>sqrt(2)</td>
  </tr>
  <tr>
    <td>M_SQRT1_2</td>
    <td>1/sqrt(2)</td>
  </tr>
  <tr>
    <td>M_LOG2E</td>
    <td>log base 2 of e</td>
  </tr>
  <tr>
    <td>M_LOG10E</td>
    <td>log base 10 of e</td>
  </tr>
  <tr>
    <td>M_LN2</td>
    <td>log base e of 2</td>
  </tr>
  <tr>
    <td>M_LN10</td>
    <td>log base e of 10</td>
  </tr>
</table>

EOPAGESTR5;
echo $page_str;

site_footer();
?>