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


site_header('Regular Expression');

$page_str = <<<EOPAGESTR5

<p>Regular expressions are patterns that match to strings. They are used not only to
create complex true/false tests on strings, but also to extract substrings and make
complex substitutions.</p>

<p>Regular expressions (or regex, pronounced with a soft g by your authors, but with no
consensus pronunciation) are patterns for string matching, with special wildcards that
can match entire portions of the target string. There are two broad classes of regular
expression that PHP works with: POSIX (extended) regex and Perl-compatible regex. The
differences mostly have to do with syntax, although there are some functional
differences, too.</p>

<p>POSIX-style regular expressions are ultimately descended from the regex
pattern-matching machinery used in Unix command-line shells; Perl-compatible regex is a
more direct imitation of regular expressions in Perl.</p>

<h2>note</h2>

<hr />

<p>I will be learning the Perl-Compatible style regex since the other one is slower
and depricated.</p>

<hr />

<p><br />Perl-compatible regex patterns are always bookended by one particular character, which
must be the same at beginning and end, indicating the beginning and end of the pattern.
By convention, this is most often the / character, although you can use a different
character if you so desire. The Perl-compatible pattern:</p>

<pre>
/pattern/
</pre>

<p>matches any string that has the string (or substring) pattern in it. To make things
slightly more complicated, these patterns are typically strings, and PHP needs its own
quotes to recognize such strings. So if you are putting a pattern into a variable for
later use, you might well do this:</p>

<pre>
&#36;my_pattern = '/pattern/';
</pre>

<p>This variable would now be suitable for passing off to a Perl-compatible regex
function that expects a pattern as argument.</p>

<table class="events" width="678">
  <caption>Common Perl-Compatible Pattern Constructs</caption>
  <tr>
    <th>Construct</th>
    <th>Interpretation</th>
  </tr>
  <tr>
    <td>Simple literal character matches</td>
    <td>If the character involved is not special, Perl will match characters in sequence.
    The example pattern /abc/ matches any string that has the substring 'abc' in it.</td>
  </tr>
  <tr>
    <td>Character class matches: [<list of characters>]</td>
    <td>Will match a single instance of any of the characters between the brackets. For
    example, /[xyz]/ matches a single character, as long as that character is either x,
    y, or z. A sequence of characters (in ASCII order) is indicated by a hyphen, so that
    a class matching all digits is [0-9].</td>
  </tr>
  <tr>
    <td>Predefined character class abbreviations</td>
    <td>The patterns &#92;d will match a single digit (from the character class [0-9]), and
    the pattern &#92;s matches any whitespace character.</td>
  </tr>
  <tr>
    <td>Multiplier patterns</td>
    <td>
    <p>Any pattern followed by * means: "Match this pattern 0 or more times."</p>
    <p>Any pattern followed by ? means: "Match this pattern exactly once."</p>
    <p>Any pattern followed by + means: "Match this pattern 1 or more times."</p>
    </td>
  </tr>
  <tr>
    <td>Anchoring characters</td>
    <td>The caret character ^ at the beginning of a pattern means that the pattern must
    start at the beginning of the string; the &#36; character at the end of a pattern means
    that the pattern must end at the end of the string. The caret character at the
    beginning of a character class [^abc] means that the set is the complement of the
    characters listed (that is, any character that is not in the list).</td>
  </tr>
  <tr>
    <td>Escape character '&#92;'</td>
    <td>
    <p>Any character that has a special meaning to regex can be treated as a simple
    matching character by preceding it with a backslash. The special characters that
    might need this treatment are:</p>
    <p>.&#92;+*&#63;[]^&#36;(){}=!<>|:</p>
    </td>
  </tr>
  <tr>
    <td>Parentheses</td>
    <td>A parenthesis grouping around a portion of any pattern means: "Add the substring
    that matches this pattern to the list of substring matches."</td>
  </tr>
</table>

<p>Take, as an example, the following pattern:</p>

<pre>
/phone number&#92;s+(&#92;d&#92;d&#92;d&#92;d&#92;d&#92;d&#92;d)/
</pre>

<p>It matches any string that contains the literal phrase phone number, followed by some
number of spaces (but at least one), followed by exactly seven digits (no spaces, no
dash). In addition, because of the parentheses, the seven-digit number is saved and
returned in an array containing substring matches if it is called from a function that
returns such things.</p>

<table class="events" width="678">
  <caption>Perl-Compatible Regular Expression Functions</caption>
  <tr>
    <th>Function</th>
    <th>Behavior</th>
  </tr>
  <tr>
    <td>preg_match()</td>
    <td>Takes a regex pattern as first argument, a string to match against as second
    argument, and an optional array variable for returned matches. Returns 0 if no
    matches are found, and 1 if a match is found. If a match is successful, the array
    variable contains the entire matching substring as its first element, and subsequent
    elements contain portions matching parenthesized portions of the pattern. As of PHP
    4.3.0, an optional flag of PREG_OFFSET_CAPTURE is also available. This flag causes
    preg match to return into the specified array a two-element array for each match,
    consisting of the match itself and the offset where the match occurs.</td>
  </tr>
  <tr>
    <td>preg_match_all()</td>
    <td>Like preg_match(), except that it makes all possible successive matches of the
    pattern in the string, rather than just the first. The return value is the number of
    matches successfully made. The array of matches is not optional (If you want a
    true/false answer, use preg_match()). The structure of the array returned depends
    on the optional fourth argument (either the constant PREG_PATTERN_ORDER, or
    PREG_SET_ORDER, defaulting to the former). (See further discussion following the
    table.) PREG_OFFSET_CAPTURE is also available with this function.</td>
  </tr>
  <tr>
    <td>preg_split()</td>
    <td>Takes a pattern as first argument and a string to match as second argument.
    Returns an array containing the string divided into substrings, split along boundary
    strings matching the pattern. (Analogous to the POSIX-style function split().) An
    optional third argument (limit) controls how many elements to split before returning
    the list; -1 means no limit. An optional flag in the fourth position can be
    PREG_SPLIT_NO_EMPTY causing the function to return only non-empty pieces,
    PREG_SPLIT_DELIM_CAPTURE causing any parenthesized expression in the delimiter
    pattern to be returned, or PREG_SPLIT_OFFSET_CAPTURE, which does the same as
    PREG_OFFSET_CAPTURE.</td>
  </tr>
  <tr>
    <td>preg_replace()</td>
    <td>Takes a pattern, a replacement string, and a string to modify. Returns the result
    of replacing every matching portion of the modifiable string with the replacement
    string. An optional limit argument determines how many replacements will occur (as in
    preg_split()).</td>
  </tr>
  <tr>
    <td>preg_replace_callback()</td>
    <td>Like preg_replace(), except that the second argument is the name of a callback
    function, rather than a replacement string. This function should return the string
    that is to be used as a replacement.</td>
  </tr>
  <tr>
    <td>preg_grep()</td>
    <td>Takes a pattern and an array and returns an array of the elements of the input
    array that matched the pattern. Surviving values of the new array have the same keys
    as in the input array.</td>
  </tr>
  <tr>
    <td>preg_quote()</td>
    <td>A special-purpose function for inserting escape characters into strings that are
    intended for use as regex patterns. The only required argument is a string to escape;
    the return value is that string with every special regex character preceded by a
    backslash.</td>
  </tr>
</table>

<p>The most widely used of these functions are probably preg_match() and preg_match_all().
The first is best for simply answering whether a pattern matches a string, and the latter
is best for either counting matches or collecting portions that match.</p>

<p>The optional fourth argument to preg_match_all() requires a little more explanation. The
array that contains the returned matches is going to be two levels deep, with one level
being the iteration of the match (the first match, the second, and so on), and the other
level being the position of the match in the pattern. (The entire match is always first,
followed by any parenthesized subpatterns in order.) The question is: Which level is on
top? Will the array be a list of positions, each of which contains a list of iterations, or
the other way around? If the argument is PREG_PATTERN_ORDER, the first element will
contain all matches of the entire pattern, the second element will contain all matches of
the first parenthesized pattern, and so forth.
If the argument is PREG_SET_ORDER, the first argument will be all the substrings from the
first match (first the total match, then parenthesized bits in order), the second element
will contain all the substrings from the second match, and so on.</p>

EOPAGESTR5;
echo $page_str;

site_footer();
?>