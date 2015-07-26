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


site_header('Access Characters from a String');

$page_str = <<<EOPAGESTR5

<p>Unlike some programming languages, PHP has no distinct character type different from the string
type. In general, functions that would take character arguments in other languages expect strings
of length 1 in PHP.</p>

<p>You can retrieve the individual characters of a string by including the number of the
character, starting at 0, enclosed in curly braces immediately following a string variable. These
characters will actually be one-character strings. For example, the following code:</p>

<pre>
&#36;my_string = "Doubled";
for (&#36;index = 0; &#36;index < 7; &#36;index++) {
  &#36;string_to_print = &#36;my_string{&#36;index};
  print("&#36;string_to_print&#36;string_to_print");
}
</pre>

<p>gives the browser output:</p>

<pre>
DDoouubblleedd
</pre>

<p>with each character of the string being printed twice per loop.</p>

<h2>caution</h2>

<p>In earlier versions of PHP, it was customary to retrieve individual characters of a string using
square brackets to enclose the index rather than curly braces (for example, &#36;my_string[3] rather
than &#36;my_string{3}). While you can still use the square (array-like) brackets to do this, this
usage is now deprecated, and the curly brace syntax is encouraged.</p>

<h2>Are strings immutable?</h2>

<p>In some programming languages (such as C), it is common to manipulate strings by directly
changing them—that is, storing new characters into the middle of an existing string, replacing old
characters. Other languages (like Java) try to keep the programmer out of certain kinds of trouble
by making string classes that are immutable (or unchangeable)—you can make new strings by creating
modified copies of old ones, but once you have made a string, you are not allowed to change it by
directly changing the characters that make it up.</p>

<p>Where does PHP fit in? As it turns out, PHP strings can be changed, but the most common practice
seems to be to treat strings as immutable.</p>
<p>Strings can be changed by treating them as character arrays and assigning directly into them,
like this:</p>

<pre>
&#36;my_string = "abcdefg";
&#36;my_string[5] = "X";
print(&#36;my_string . "<BR>");
</pre>

<p>which will give the browser output:</p>

<pre>
abcdeXg
</pre>

<p>This modification method seems to be undocumented, however, and shows up nowhere in the online
manual, even though the corresponding extraction method (now updated to use curly braces) is
highlighted. Also, almost all PHP string-manipulation functions return modified copies of their
string arguments rather than making direct changes, which seems to indicate that this is the style
that the PHP designers prefer. Our advice is not to use this direct-modification method to change
strings, unless you know what you are doing and there is some large benefit in terms of memory
savings.</p>

EOPAGESTR5;
echo $page_str;

site_footer();
?>