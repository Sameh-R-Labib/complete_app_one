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


site_header('Randomness Math Functions');

$page_str = <<<EOPAGESTR5

<p>The random number generators in PHP should realy be called
pseudo-random number generators because any number generated
by a computer can never be truely random.</p>

<p>There are two random number generators (invoked with rand() and mt_rand(),
respectively), each with the same three associated functions: a seeding
function, the random-number function itself, and a function that retrieves
the largest integer that might be returned by the generator.</p>

<p>The particular pseudo-random function that is used by rand() may depend
on the particular libraries that PHP was compiled with. By contrast, the
mt_rand() generator always uses the same random function (the Mersenne
Twister), and the author of mt_rand()'s online documentation argues that
it is also faster and "more random" (in a cryptographic sense) than rand().
We have no reason to believe that this is not correct, so we prefer mt_rand()
to rand().</p>

<table class="events" width="678">
  <caption>Random Number Functions</caption>
  <tr>
    <th>Function</th>
    <th>Description</th>
  </tr>
  <tr>
    <td>srand()</td>
    <td>Takes a single positive integer argument and seeds the random number
generator with it.</td>
  </tr>
  <tr>
    <td>rand()</td>
    <td>If called with no arguments, returns a "random" number between 0 and
RAND_MAX (which can be retrieved with the function getrandmax()). The
function can also be called with two integer arguments to restrict the
range of the number returned — the first argument is the minimum and the
second is the maximum (inclusive).</td>
  </tr>
  <tr>
    <td>getrandmax()</td>
    <td>Returns the largest number that may be returned by rand(). This is
limited to 32768 on Windows platforms.</td>
  </tr>
  <tr>
    <td>mt_srand()</td>
    <td>Like srand(), except that it seeds the "better" random number
generator.</td>
  </tr>
  <tr>
    <td>mt_rand()</td>
    <td>Like rand(), except that it uses the "better" random number
generator.</td>
  </tr>
  <tr>
    <td>mt_getrandmax()</td>
    <td>Returns the largest number that may be returned by mt_rand().</td>
  </tr>
</table>

<h2>note</h2>

<hr />

<p>On some PHP versions and some platforms, you can apparently get
seemingly random numbers from rand() and mt_rand() without seeding
first—this should not be relied upon, however, both for reasons of
portability and because the unseeded behavior is not guaranteed.</p>

<hr />

<h2>Seeding the generator</h2>

<p>The typical way to seed either of the PHP random-number generators
(using mt_srand() or srand()) looks like this:</p>

<pre>
mt_srand((double)microtime()*1000000);
</pre>

<p>This sets the seed of the generator to be the number of microseconds
that have elapsed since the last whole second. (Yes, the typecast to
double is necessary here, because microtime() returns a string, which
would be treated as an integer in the multiplication but for the cast.)
Please use this seeding statement even if you don't understand it —
just place it in any PHP page, once only, before you use the
corresponding mt_rand() or rand() functions, and it will ensure that
you have a varying starting point and therefore random sequences that
are different every time. This particular seeding technique has been
thought through by people who understand the ins and outs of
pseudo-random number generation and is probably better than any attempt
an individual programmer might make to try something trickier.</p>

<p>Here's some representative code that uses the pseudo-random functions:</p>

<pre>
print("Seeding the generator&lt;BR>");
mt_srand((double)microtime() * 1000000);
print("With no arguments: " . mt_rand() . "&lt;BR>");
print("With no arguments: " . mt_rand() . "&lt;BR>");
print("With no arguments: " . mt_rand() . "&lt;BR>");
print("With two arguments: " .
       mt_rand(27, 31) . "&lt;BR>");
print("With two arguments: " .
       mt_rand(27, 31) . "&lt;BR>");
print("With two arguments: " .
       mt_rand(27, 31) . "&lt;BR>");
</pre>

<p>with the browser output:</p>

<pre>
Seeding the generator
With no arguments: 992873415
With no arguments: 656237128
With no arguments: 1239053221
With two arguments: 28
With two arguments: 31
With two arguments: 29
</pre>

<p>Obviously, if you run exactly this code, you will get numbers that
differ from those in the output shown here, because the point of seeding
the generator this way is to ensure that different executions produce
different sequences of numbers. Note, we seeded it once but ran it
six times yielding different values.</p>

<h2>tip</h2>

<hr />

<p>Although the random-number functions only return integers,
it is easy to convert a random integer in a given range to a
corresponding floating-point number (say, one between 0.0 and
1.0 inclusive) with an expression like rand() / getrandmax().
You can then scale and shift the range as desired (to, say,
a number between 100.0 and 120.0) with an expression like 100.0
+ 20.0 * (rand() / getrandmax()).</p>

<hr />

EOPAGESTR5;
echo $page_str;

site_footer();
?>