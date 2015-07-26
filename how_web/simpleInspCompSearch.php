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


site_header('Simple Inspection Comparison Search of Strings');

$page_str = <<<EOPAGESTR5

<table class="events" width="678">
  <caption></caption>
  <tr>
    <th>Function</th>
    <th>Description</th>
  </tr>
  <tr>
    <td>strlen()</td>
    <td>Takes a single string argument and returns its length as an integer.</td>
  </tr>
  <tr>
    <td>strpos()</td>
    <td>Takes two string arguments: a string to search, and the string being searched for. Returns
    the (0-based) position of the beginning of the first instance of the string if found, and a
    false value otherwise. It also takes a third optional integer argument, specifying the position
    at which the search should begin.</td>
  </tr>
  <tr>
    <td>strrpos()</td>
    <td>Like strpos(), except that it searches backward from the end of the string, rather than
    forward from the beginning. The search string must only be one character long, and there is no
    optional position argument.</td>
  </tr>
  <tr>
    <td>strcmp()</td>
    <td>Takes two strings as arguments and returns 0 if the strings are exactly equivalent. If
    strcmp() encounters a difference, it returns a negative number if the first different byte is a
    smaller ASCII value in the first string, and a positive number if the smaller byte is found in
    the second string.</td>
  </tr>
  <tr>
    <td>strcasecmp()</td>
    <td>Identical to strcmp(), except that lowercase and uppercase versions of the same letter
    compare as equal.</td>
  </tr>
  <tr>
    <td>strstr()</td>
    <td>Searches its first string argument to see if its second string argument is contained in it.
    Returns the substring of the first string that starts with the first instance of the second
    argument, if any is found — otherwise, it returns false.</td>
  </tr>
  <tr>
    <td>strchr()</td>
    <td>Identical to strstr().</td>
  </tr>
  <tr>
    <td>stristr()</td>
    <td>Identical to strstr() except that the comparison is case independent.</td>
  </tr>
  <tr>
    <td>count_chars()</td>
    <td>Takes a single string argument and an integer mode argument from 0 to 4. Returns a
    report about frequencies of characters in the string argument, as either an array or a
    string.</td>
  </tr>
  <tr>
    <td>strspn()</td>
    <td>Takes two string arguments and returns the length of the initial substring of the
    first argument that is composed entirely of characters found in its second argument.</td>
  </tr>
  <tr>
    <td>strcspn()</td>
    <td>Takes two string arguments and returns the length of the initial substring of the
    first argument that is composed entirely of characters that are not found in its second
    argument.</td>
  </tr>
  <tr>
    <td>chr()</td>
    <td>Returns a one-character string containing the character specified by ascii.</td>
  </tr>
  <tr>
    <td>ord()</td>
    <td>Returns the ASCII value of the first character of string.</td>
  </tr>
</table>

EOPAGESTR5;
echo $page_str;

site_footer();
?>