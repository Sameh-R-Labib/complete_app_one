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


site_header('Trigonometry');

$page_str = <<<EOPAGESTR5

<p>PHP offers the standard set of basic trigonometric functions as well as the constant
M_PI, an approximation of pi as a double that prints as 3.1415926535898. This constant
can be used anywhere you would use the literal number itself, and it is also
interchangeable with the pi() function. (For other constants derived from pi, see the
"Mathematical Constants" page.)</p>

<table class="events" width="678">
  <caption>Trigonometric Functions</caption>
  <tr>
    <th>Function</th>
    <th>Behavior</th>
  </tr>
  <tr>
    <td>pi()</td>
    <td>Takes no arguments and returns an approximation of pi (3.1415926535898). Can
    be used interchangeably with the constant M_PI.</td>
  </tr>
  <tr>
    <td>Sin()</td>
    <td>Takes a numerical argument in radians and returns the sine of the argument as
    a double.</td>
  </tr>
  <tr>
    <td>Cos()</td>
    <td>Takes a numerical argument in radians and returns the cosine of the argument
    as a double.</td>
  </tr>
  <tr>
    <td>Tan()</td>
    <td>Takes a numerical argument in radians and returns the tangent of the argument
    as a double.</td>
  </tr>
  <tr>
    <td>Asin()</td>
    <td>Takes a numerical argument and returns the arcsine of the argument in radians.
    Inputs must be between –1.0 and 1.0 [inputs outside that range will return a result
    of NAN (for "not a number")]. Results are in the range –pi / 2 to pi / 2.</td>
  </tr>
  <tr>
    <td>Acos()</td>
    <td>Takes a numerical argument and returns the arccosine of the argument in radians.
    Inputs must be between –1.0 and 1.0 [inputs outside that range will return a result
    of NAN (for "not a number")]. Results are in the range 0 to pi.</td>
  </tr>
  <tr>
    <td>Atan()</td>
    <td>Takes a numerical argument and returns the arctangent of the argument in radians.
    Results are in the range –pi / 2 to pi / 2.</td>
  </tr>
  <tr>
    <td>Atan2()</td>
    <td>A variant of atan() that takes two arguments. Atan(&#36;y,	&#36;x) is identical to
    atan(&#36;y/&#36;x) when &#36;x is positive, but the quadrant of atan2's result depends on the
    signs of both &#36;y and &#36;x. Range of the result is from –pi to pi.</td>
  </tr>
</table>

<h2>Trigonometry in One Paragraph</h2>

<hr />

<p>Imagine a circle with a radius of 1, centered at 0,0 in the coordinate plane. Start at the
right-hand edge (at position (1,0)), and trace a certain distance along the circle
counterclockwise. For example, a distance of 2 pi would take you once around the circle and
back to your starting point. Clockwise travel counts as a negative distance. For any such
distance, the sine function tells you the y-value of the coordinate you arrive at, the cosine
function tells you the x-value of that coordinate, and the tangent function is a ratio of the
two, from which you can infer the slope of the line tangent to the circle at that point. The
functions arccosine, arcsine, and arctangent are in some sense inverses of their corresponding
functions — they map back from an x, y, or y/x ratio to the distance of a circular trip that
would arrive at that x-coordinate, y-coordinate, or ratio thereof. Because adding a multiple of
2 pi to any distance brings you around to the same point again, these inverse functions might
have an infinite number of answers per input, making them ill-defined—instead, they are restricted
to a range corresponding to one particular trip around half of the circle and so have well-defined
results.</p>

<hr />

EOPAGESTR5;
echo $page_str;

site_footer();
?>