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


site_header('Substring and String Replacement');

$page_str = <<<EOPAGESTR5

<table class="events" width="678">
  <caption></caption>
  <tr>
    <th>Function</th>
    <th>Description</th>
  </tr>
  <tr>
    <td>substr()</td>
    <td>
    <p>Returns a subsequence of its initial string argument, as specified by the second
    (position) argument and optional third (length) argument. The substring starts at the
    indicated position and continues for as many characters as specified by the length
    argument or until the end of the string, if there is no length argument.</p>
    <p>A negative position argument means that the start character is located by counting
    backward from the end, whereas a negative length argument means that the end of the
    substring is found by counting back from the end, rather than forward from the start
    position.</p>
    </td>
  </tr>
  <tr>
    <td>chop(), or rtrim()</td>
    <td>Returns its string argument with trailing (right-hand side) whitespace removed.
    Whitespace is &nbsp;&nbsp;, &#92;n, &#92;r, &#92;t, and &#92;0.</td>
  </tr>
  <tr>
    <td>ltrim()</td>
    <td>Returns its string argument with leading (left-hand side) whitespace removed.</td>
  </tr>
  <tr>
    <td>Trim()</td>
    <td>Returns its string argument with both leading and trailing whitespace removed.</td>
  </tr>
  <tr>
    <td>Str_replace()</td>
    <td>Used to replace target substrings with another string. Takes three string
    arguments: a substring to search for, a string to replace it with, and the containing
    string. Returns a copy of the containing string with all instances of the first
    argument replaced by the second argument.</td>
  </tr>
  <tr>
    <td>Substr_replace()</td>
    <td>
    <p>Puts a string argument in place of a position-specified substring. Takes up to
    four arguments: the string to operate on, the string to replace with, the start
    position of the substring to replace, and the length of the string segment to be
    replaced. Returns a copy of the first argument with the replacement string put in
    place of the specified substring.</p>
    <p>If the length argument is omitted, the entire tail of the first string argument is
    replaced. Negative position and length arguments are treated as in substr().</p>
    </td>
  </tr>
</table>

<h2>note</h2>

<p>There functions strstr(), strchr() and stristr() are also substring functions.
These are covered on the page titled: Simple Inspection Comparison Search of Strings.</p>

EOPAGESTR5;
echo $page_str;

site_footer();
?>