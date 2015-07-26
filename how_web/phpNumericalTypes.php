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


site_header('PHP Numerical Types');

$page_str = <<<EOPAGESTR5

<p>PHP has only two numerical types: integer (also known as long),
and double (aka float), which correspond to the largest numerical
types in the C language. PHP does automatic conversion of numerical
types, so they can be freely intermixed in numerical expressions and
the "right thing" will typically happen. PHP also converts strings
to numbers where necessary.</p>

<p>In situations where you want a value to be interpreted as a
particular numerical type, you can force a typecast by prepending
the type in parentheses, such as:</p>

<pre>
(double) &#36;my_var
(integer) &#36;my_var
</pre>

<p>Or you can use the functions intval() and doubleval(), which convert
their arguments to integers and doubles, respectively.</p>

<p>Integers can actually be read in three formats, which correspond to
bases: decimal (base 10), octal (base 8), and hexadecimal (base 16).
Decimal format is the default, octal integers are specified with a
leading 0, and hexadecimals have a leading 0x. Note that the read
format affects only how the integer is converted as it is read.
Internally, of course, these numbers are represented in binary
format.</p>

<p>The typical read format for doubles is -X.Y, where the - optionally
specifies a negative num- ber, and both X and Y are sequences of digits
between 0 and 9. The X part may be omitted if the number is between –1.0
and 1.0, and the Y part can also be omitted. Leading or trailing zeros
have no effect.</p>

<p>In addition, doubles can be specified in scientific notation, by
adding the letter e and a desired integral power of 10 to the end of
the previous format — for example, 2.2e-3 would correspond to 2.2 ×
10-3. The floating-point part of the number need not be restricted to a
range between 1.0 and 10.0.</p>

<p>Notice that, just as with octal and hexadecimal integers, the read
format is irrelevant once PHP has finished reading in the numbers — the
preceding variables retain no memory of whether they were originally
specified in scientific notation. In printing the values, PHP is making
its own decisions to print the more extreme values in scientific
notation, but this has nothing to do with the original read format.</p>

<h2>tip</h2>

<hr />

<p>If you need finer control of printing, see the printf function.</p>

<hr />

EOPAGESTR5;
echo $page_str;

site_footer();
?>