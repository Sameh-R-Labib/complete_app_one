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


site_header('Date & Time Functions');

$page_str = <<<EOPAGESTR5

<p>The fastest way to get a time is to use the function time(). This will return the Unix
timestamp for your locale, which will look something like 101906652. If you plan to pass this
timestamp to another function or program, this is the best format. Alternatively, you can then
use one of the functions in the next section to format the timestamp into something a bit more
human-readable.</p>

<p>You could also use microtime() to return the current time in seconds and microseconds
since the Unix epoch. This can be supremely helpful for utilities that are designed to
measure performance. The format is 0.74321900 961906846, where the first part is microseconds
and the second is the Unix timestamp. If you’re trying to (for instance) measure the
performance of different parts of your Web page, you really just want the microseconds part,
which can be cut out like so:</p>

<pre>
&lt;&#63;php &#36;stampmebaby = microtime();
&#36;chunks = explode(" ", &#36;stampmebaby);
&#36;microseconds = &#36;chunks[0];
echo &#36;microseconds;
&#63;&gt;
</pre>

<p>A function used to return date information is getdate(&#36;timestamp). When used with the
argument time(), as in getdate(time()), it returns an associative array with the following
numeric elements derived from the Unix timestamp:</p>

<table class="events" width="678">
  <caption>Table 23-3</caption>
  <tr>
    <th>Index</th>
    <th>Value</th>
  </tr>
  <tr>
    <td>seconds</td>
    <td>0 to 59</td>
  </tr>
  <tr>
    <td>minutes</td>
    <td>0 to 59</td>
  </tr>
  <tr>
    <td>hours</td>
    <td>0 to 23</td>
  </tr>
  <tr>
    <td>mday</td>
    <td>Numeric representation of the day of the month (1 to 31).</td>
  </tr>
  <tr>
    <td>wday</td>
    <td>Numeric representation of the day of the week &mdash; 0 (for Sunday) through 6
    (for Saturday)</td>
  </tr>
  <tr>
    <td>mon</td>
    <td>1 through 12</td>
  </tr>
  <tr>
    <td>year</td>
    <td>A full numeric representation of a year, 4 digits.</td>
  </tr>
  <tr>
    <td>yday</td>
    <td>Numeric representation of the day of the year &mdash; 0 through 365</td>
  </tr>
  <tr>
    <td>weekday</td>
    <td>A full textual representation of the day of the week.</td>
  </tr>
  <tr>
    <td>month</td>
    <td>A full textual representation of a month, such as January or March.</td>
  </tr>
  <tr>
    <td>0</td>
    <td>Seconds since the Unix Epoch, similar to the values returned by time() and used by
    date(). System Dependent, typically -2147483648 through 2147483647.</td>
  </tr>
</table>

<p>You can also use the getdate() function with a Unix timestamp other than that
representing the current time.</p>

<p>If you want to get the time and format it in one step, you can use date() instead of
getdate(). In the absence of a Unix timestamp argument, date() will default to the current
local date. This has the advantage of allowing nicer formatting, as we will explain in the
next subsection. The function strftime() will also format the current Unix timestamp for
you (as we explain in the next subsection) unless another is specified.</p>

<h2>If you've already determined the date/time/timestamp</h2>

<p>The functions in this section come into play if you already have a timestamp and merely
wish to format the information more finely. For instance, you may like to express your dates
European style (2000.20.04) rather than American (4/20/2000).</p>

<p>The main method to format a timestamp is using date(&#36;format...&#36;formatn[, &#36;timestamp]).
You pass a series of codes indicating your formatting preferences, plus an optional
timestamp. For instance:</p>

<pre>
date('Y-m-d');
</pre>

<p>returns a string like 2002-05-27. You can choose a date with two-zero day identifiers or
strictly numeric date identifiers, 12- or 24-hour format, or abbreviated month name. (See the
PHP manual for all the options.) An analogous function is gmdate(&#36;format...&#36;formatn
[, &#36;timestamp]), which will return a Greenwich Mean Date.</p>

<p>The function strftime(&#36;format...&#36;formatn[, &#36;timestamp]) is similar but specializes in
formatting the time rather than the date; gmstrftime(&#36;format...&#36;formatn [, &#36;timestamp])
returns the time in formatted Greenwich Mean Time.</p>

<p>The function mktime() allows you to convert any date into a timestamp. It’s subtly
different in the order of arguments from the Unix command of the same name, so pay attention.
The function gmmktime() gives the Greenwich alternative to your own time zone.</p>

<p>Finally, checkdate(&#36;month, &#36;day, &#36;year) allows you to quickly ensure that a particular
date is a valid one. This is great for leap-year questions.</p>

EOPAGESTR5;
echo $page_str;

site_footer();
?>