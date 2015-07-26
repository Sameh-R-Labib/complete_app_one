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


site_header('Base Conversion Math Functions');

$page_str = <<<EOPAGESTR5

<p>The default base in PHP for reading in or printing out numbers is 10.
In addition, you can instruct PHP to read octal numbers in base 8 (by
starting the number with a leading 0) or hexadecimal numbers in base 16
(by starting the number with a 0x).</p>

<p>Once numbers are read in, of course, they are represented in binary
format in memory, and all the basic arithmetic and mathematical
calculations are carried out internally in base 2. PHP also has a number
of functions for translating between different bases:</p>

<table class="events" width="678">
  <caption>Base Conversion Functions</caption>
  <tr>
    <th>Function</th>
    <th>Behavior</th>
  </tr>
  <tr>
    <td>BinDec()</td>
    <td>Takes a single string argument representing a binary (base 2)
    integer, and returns a string representation of that number in base 10.</td>
  </tr>
  <tr>
    <td>DecBin()</td>
    <td>Like BinDec(), but converts from base 10 to base 2.</td>
  </tr>
  <tr>
    <td>OctDec()</td>
    <td>Like BinDec(), but converts from base 8 to base 10</td>
  </tr>
  <tr>
    <td>DecOct()</td>
    <td>Like BinDec(), but converts from base 10 to base 8.</td>
  </tr>
  <tr>
    <td>HexDec()</td>
    <td>Like BinDec(), but converts from base 16 to base 10.</td>
  </tr>
  <tr>
    <td>DecHex()</td>
    <td>Like BinDec(), but converts from base 10 to base 16.</td>
  </tr>
  <tr>
    <td>baseconvert()</td>
    <td>Takes a string argument (the integer to be converted) and
    two integer arguments (the original base, and the desired base).
    Returns a string representing the converted number — digits higher
    than 9 (from 10 to 35) are represented by the letters a–z. Both the
    original and desired bases must be in the range 2–36.</td>
  </tr>
</table>

<p>All the base-conversion functions are special-purpose, converting from one
particular base to another, except for base_convert(), which accepts an
arbitrary start base and destination base. Here's an example of base_convert()
in action:</p>

<pre>
function display_bases(&#36;start_string, &#36;start_base) {
  for (&#36;new_base = 2; &#36;new_base <= 36; &#36;new_base++)
    {
      &#36;converted =
        base_convert(&#36;start_string, &#36;start_base, &#36;new_base);
        print("&#36;start_string in base &#36;start_base
               is &#36;converted in base &#36;new_base&lt;BR>");
    }
}

display_bases("1jj", 20);
</pre>

<p>This code yields the browser output:</p>

<pre>
1jj in base 20 is 1100011111 in base 2
1jj in base 20 is 1002121 in base 3
1jj in base 20 is 30133 in base 4
1jj in base 20 is 11144 in base 5
1jj in base 20 is 3411 in base 6
1jj in base 20 is 2221 in base 7
1jj in base 20 is 1437 in base 8
1jj in base 20 is 1077 in base 9
1jj in base 20 is 799 in base 10
1jj in base 20 is 667 in base 11
1jj in base 20 is 567 in base 12
1jj in base 20 is 496 in base 13
1jj in base 20 is 411 in base 14
1jj in base 20 is 384 in base 15
1jj in base 20 is 31f in base 16
1jj in base 20 is 2d0 in base 17
1jj in base 20 is 287 in base 18
1jj in base 20 is 241 in base 19
1jj in base 20 is 1jj in base 20
1jj in base 20 is 1h1 in base 21
1jj in base 20 is 1e7 in base 22
1jj in base 20 is 1bh in base 23
1jj in base 20 is 197 in base 24
1jj in base 20 is 16o in base 25
1jj in base 20 is 14j in base 26
1jj in base 20 is 12g in base 27
1jj in base 20 is 10f in base 28
1jj in base 20 is rg in base 29
1jj in base 20 is qj in base 30
1jj in base 20 is po in base 31
1jj in base 20 is ov in base 32
1jj in base 20 is o7 in base 33
1jj in base 20 is nh in base 34
1jj in base 20 is mt in base 35
1jj in base 20 is m7 in base 36
</pre>

<p>Notice that although all the base-conversion functions take string arguments
and return string values, you can use decimal numerical arguments and rely on
PHP's type conversion (but see the cautionary note that follows). In other
words, both DecBin("1234") and DecBin(1234) will yield the same result.</p>

<h2>caution</h2>

<hr />

<p>Don't confuse the read formats of numbers with their representations as
strings for the purposes of base conversion. For example, although 10 in base
16 is the number 16 in base 10, the expression HexDec(0x10) evaluates to the
string "22". Why? There are really three conversions happening: when 0x10 is
read (converts from hex to internal binary), when the argument is auto-converted
(from internal binary number to the decimal string "16"), and in the operation
of the function (from assumed base 16 to decimal "22"). If you want just one
conversion, the desired expression is HexDec("10").</p>

<hr />

<h2>note</h2>

<hr />

<p>The base conversion functions expect their string arguments to be integers,
not floating-point numbers. That means you can’t use these functions to convert
a binary 10.1 to a decimal 2.5.</p>

<hr />


EOPAGESTR5;
echo $page_str;

site_footer();
?>