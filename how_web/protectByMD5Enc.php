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


site_header('Protect by MD5 Encryption');

$page_str = <<<EOPAGESTR

<p>Simple Example:</p>

<pre>
// One-way encrypt it
&#36;crypt_pass = md5(&#36;password);

// Put the temp password in the db
&#36;query = "update user set password = '&#36;crypt_pass'
  where email = '&#36;as_email'";
&#36;result = mysql_query(&#36;query) or die('Cannot complete update');
</pre>

<h2>Hashing using MD5</h2>

<p>MD5 is a string-processing algorithm that is used to produce a digest or signature of
whatever string it is given. The algorithm boils its input string down into a fixed-length
string of 32 hexadecimal values (0,1,2, . . . 9,a,b, . . . f). MD5 has some very useful
properties:</p>

<ul>
  <li>MD5 always produces the same output string for any given input string.</li>
  <li>The fixed-length results of applying MD5 are very evenly spread over the range of
      possible values.</li>
  <li>There is no known way to efficiently produce an input string corresponding to a given
      MD5 output string or to produce two inputs that yield the same output.</li>
</ul>

<p>PHP’s implementation of MD5 is available in the function md5(), which takes a string as
input and produces the 32-character digest as output. For example, evaluating this:</p>

<pre>
print("md5 of 'Tim' is " . md5('Tim') . "&lt;BR&gt;");
print("md5 of 'tim' is " . md5('tim') . "&lt;BR&gt;");
print("md5 of 'time' is " . md5('time') . "&lt;BR&gt;");
</pre>

<p>gives us the browser output:</p>

<pre>
md5 of Tim is dc2054afd537ddc98afd9347136494ac
md5 of tim is b15d47e99831ee63e3f47cf3d4478e9a
md5 of time is 07cc694b9b3fc636710fa08b6922c42b
</pre>

<p>Although the input strings seem close to each other in some sense, there is no apparent
similarity in the output strings. And since the range of possible output values is so huge
(16 to the 32nd power), the chances that any two distinct strings will collide by producing
the same MD5 value is vanishingly small.</p>

<p>The characteristics of MD5 make it useful for a wide variety of tasks, including:</p>

<ul>
  <li>Checksumming a message or file. If you are worried about errors that might happen in
  transfer, you can transmit an MD5 digest, along with the message, and run the message
  through MD5 again after transfer. If the two versions of the digest do not match, then
  something is amiss.</li>
  <li>Detecting if a file’s contents have changed. Similar to checksumming, MD5 is often used
  in this way by search engines as a check on whether a Web page has changed, making
  re-indexing necessary. It is cheaper to store the MD5 digest than the entire original
  file.</li>
  <li>Encrypting passwords. You might store an MD5'ed password in your database, and compare
  the result of MD5'ing an entered password against that entry.</li>
</ul>

<p>In addition to the md5() function, PHP offers md5_file(), which takes a filename as
argument and returns an MD5 hash of the file's contents.</p>

EOPAGESTR;
echo $page_str;

site_footer();
?>