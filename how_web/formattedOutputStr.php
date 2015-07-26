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


site_header('Formatted Output');

$page_str = <<<EOPAGESTR5

<p>The workhorse constructs for printing and output are print and echo. The standard way
to print the value of variables to output is to include them in a doubly quoted string
(which will interpolate their values) and then give that string to print or echo.</p>

<p>If you need even more tightly formatted output, use printf() and sprintf().
The two functions take identical arguments: a special format string and then any number
of other arguments, which will be spliced into the right places in the format string to
make the result.</p>

<p>The only difference between printf() and sprintf() is that printf() sends the
resulting string directly to output, whereas sprintf() returns the result string as its
value.</p>

<p>The complicated bit about these functions is the format string. Every character that
you put in the string will show up literally in the result, except the % character and
characters that immediately follow it. The % character signals the beginning of a
conversion specification, which indicates how to print one of the arguments that follow
the format string.</p>

<p>After the %, there are five elements that make up the conversion specification, some
of which are optional: padding, alignment, minimum width, precision, and type.</p>

<table class="events" width="678">
  <caption></caption>
  <tr>
    <th>Conversion Specification</th>
    <th>Description</th>
  </tr>
  <tr>
    <td>The single (optional) padding character is either a 0 or a space ( ).</td>
    <td>This character is used to fill any space that would otherwise be unused but that
    you have insisted (with the minimum width argument) be filled with something. If this
    padding character is not given, the default is to pad with spaces.</td>
  </tr>
  <tr>
    <td>The optional alignment character (-).</td>
    <td>indicates whether the printed value should be left- or right-justified. If
    present, the value will be left-justified; if absent, it will be right-justified.</td>
  </tr>
  <tr>
    <td>An optional minimum width number</td>
    <td>that indicates how many spaces this value should take up, at a minimum. (If more
    spaces are needed to print the value, it will overflow beyond its bounds.)</td>
  </tr>
  <tr>
    <td>An optional precision specifier</td>
    <td>is written as a dot (.) followed by a number. It indicates how many decimal
    points of precision a double should print with. (This has no effect on printing
    things other than doubles.)</td>
  </tr>
  <tr>
    <td>A single character indicating how the type of the value should be interpreted.</td>
    <td>The f character indicates printing as a double, the s character indicates
    printing as a string, and then the rest of the possible characters (b, c, d, o, x, X)
    mean that the value should be interpreted as an integer and printed in various
    formats. Those formats are b for binary, c for printing the character with the
    corresponding ASCII values, o for octal, x for hexadecimal (with lower case letters)
    and X for hexadecimal with uppercase letters.</td>
  </tr>
</table>

<p>Here's an example of printing the same double in several different ways:</p>

<pre>
&lt;pre>
&lt;&#63;php
&#36;value = 3.14159;
printf("%f,%10f,%-010f,%2.2f&#92;n",
        &#36;value, &#36;value, &#36;value, &#36;value);
&#63;>
&lt;/pre>
</pre>

<p>gives us:</p>

<pre>
3.141590, 3.141590,3.141590000000000, 3.14
</pre>

EOPAGESTR5;
echo $page_str;

site_footer();
?>