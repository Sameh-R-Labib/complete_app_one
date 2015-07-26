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


site_header('File Open');

$page_str = <<<EOPAGESTR5

<p>It's essentially mandatory to assign the result of fopen() to a variable (traditionally
&#36;fd for file descriptor, or &#36;fp for file pointer).</p>

<h2>caution</h2>

<hr />

<p>Note that fopen() does not return an integer on success. In fact, it returns a string that
says Resource id #n, where n is the number of the currently opened stream. Do not attempt to
test the success of your file open by using is_int() or is_numeric(). Use die() instead.</p>

<hr />

<p><br />If it's successful in opening the file, PHP will return a resource ID, which it requires
for further operations such as fread or fwrite. Otherwise, the value will be false.</p>

<h2>caution</h2>

<hr />

<p>The system makes only a certain number of file descriptors available, which is a good
argument for closing files as soon as you can. If you anticipate a large demand and have
access to system settings, you may increase the number. However, if you fail to close a file
descriptor, PHP will do it for you when the script ends.</p>

<hr />

<p><br />Files may be opened in any of six modes (similar to permissions levels). If you try
to do mode-inappropriate things, you will be denied. The modes are:</p>

<ul>
  <li>Read-only ("r").</li>
  <li>Read and write if the file exists already ("r+"): will write to the beginning of the
  file, doubling original contents of the file if you read the file in as a string, edit it,
  and then write the string out to the file.</li>
  <li>Write-only ("w") will create a file of this name, if one doesn’t already exist, and will
  erase the contents of any file of this name before writing! You cannot use this mode to read
  a file, only to write one.</li>
  <li>Write and read even if the file doesn’t exist already ("w+") will create a file of this
  name, if one doesn’t already exist, and will erase the contents of any file of this name
  before writing!</li>
  <li>Write-only to the end of a file whether it exists or not ("a").</li>
  <li>Read and write to the end of a file whether it exists or not ("a+"), "doubling" original
  contents of the file if you read the file in as a string, edit it, and then write the string
  out to the file.</li>
</ul>

<p>You need to be very sure you have read in the contents of any pre-existing file before
using w or w+ on it. Your chance of losing data with the other modes is much less.</p>

<h2>tip</h2>

<hr />

<p>Since version 4.3.2 of PHP, a formerly optional parameter, b, has been made the default
operating mode for fopen(). This means all files, on platforms where the distinction is
supported, are opened as binary. The result is that (among other, finer points) no
translation of the line-ending characters occurs between Windows and Unix-like platforms.
You can still circumvent this measure by making the letter t the last character of your
permissions, in effect forcing the translation to occur.</p>

<hr />

<p><br />There are four main types of file connections that can be opened: HTTP, FTP, standard
I/O, and filesystem.</p>

<h2>tip</h2>

<hr />

<p>Some users have reported problems with the "+" modes. Many of these problems actually
appear to be caused by slightly faulty understanding of the six modes. When in doubt, try
opening in separate read and write modes. See the section on file-writing later in this
chapter.</p>

<hr />

<br />

<h2>HTTP fopen</h2>

<p>An HTTP fopen() tries to open an HTTP 1.0 connection to a file of a type which would
normally be served by a Web server (such as HTML, PHP, ASP, and so on). PHP actually fakes
out the Web server into thinking the request is coming from a normal Web browser surfing the
Net rather than a file-open operation.</p>

<p>You should be able to use forward slashes like this on either Unix or Windows, since the
addresses are URLs rather than filepaths:</p>

<pre>
&#36;fd = fopen("http://www.example.com/openfile.html/", "r");
</pre>

<p>Remember that technically a URL without a trailing slash is malformed, but through incorrect
usage most Web servers will automatically rewrite the URL with the slash and try redirecting
it. Versions of PHP before 4.0.5 did not support redirects, so all HTTP fopen() requests would
fail without the trailing slash. After 4.0.5, the trailing slash became optional.</p>

<h2>note-to-self</h2>

<hr />

<p>I think the author is misguided with the example above since that trailing forward slash is
following a file name not a directory name. It makes no sense to have a trailing forward slash
after a file name under any circumstance.</p>

<hr />

<p><br />Remember that you need not necessarily use an HTTP connection just because you’re looking
at an HTML file. If you have filesystem access, you can open from the filesystem instead and
treat the file as a text file. The HTTP fopen() alternative is mostly useful for getting HTML
pages from remote Web servers — as when you try to "scrape" data from an HTML page. The effect
will be much like viewing an HTML page and saving the source code.</p>

<p>PHP versions older than 4.3.0 were unable to make HTTPS fopens. Now, you can accomplish
this simply by using "https://" rather than "http://".</p>

<p>HTTP fopen()s are read-only. You will not be able to write to a remote HTML file using
this type of file manipulation.</p>

<h2>FTP fopen</h2>

<p>An FTP fopen() attempts to establish an FTP connection to a remote server by pretending to
be an FTP client. This is the trickiest of the four options because you need to use an FTP
username and password in addition to the hostname and path.</p>

<pre>
&#36;fd = fopen("ftp://username:password@example.com/openfile.txt/", "r");
</pre>

<p>The FTP server must support passive mode for this method to work correctly. Also, FTP file
opens can only be read or write, not both at once, and writes can only be to new files, not to
existing ones.</p>

<p>PHP has many specific FTP functions, sufficient to implement a complete FTP client in PHP.
If you want to do anything except a simple FTP file download, you should probably use them
instead. See the PHP manual at www.php.net/manual/en/ref.ftp.php.</p>

<h2>Standard I/O fopen</h2>

<p>Standard I/O read/writes are indicated by php://stdin, php://stdout, or php://stderr
(depending on the desired stream). The standard I/O fopen() comes into play mostly when PHP is
used on the command line or as a system scripting language, à la Perl, because standard
I/O is usually associated with terminal windows. This usage is so rare in PHP that we have
only seen one real-life example of any length.</p>

<p>A command-line script using a standard I/O fopen looks like this:</p>

<pre>
#! /usr/local/bin/php
&lt;?php
&#36;fp = fopen("php://stdin", "r");
while (!feof(&#36;fp)) {
  echo fgets(&#36;fp, 4096);
}
echo "&#92;n";
?&gt;
</pre>

<p>You would run it like this from the command line:</p>

<pre>
echo "goo goo ga ga" | ./stdin_test.php
</pre>

<h2>Filesystem fopen</h2>

<p>The most common and useful way to use fopen() is from the filesystem. Unless specifically
directed otherwise, PHP will attempt to open from the filesystem.</p>

<p>On Windows systems, you can choose to use the Windows format with backslashes if desired
— but remember to escape them:</p>

<pre>
&#36;fp = fopen("c:&#92;&#92;php&#92;&#92;phpdocs&#92;&#92;navbar.inc", "r");
</pre>

<p>You can use forward slashes from both Windows and Unix. You should not use a trailing slash
for filesystem fopen() calls.</p>

<h2>tip</h2>

<hr />

<p>Remember that your files, and potentially your directories, need to be readable or writable
by the PHP (or Web server, if module) process UID rather than by you as a system user. If you
share a server, this means any of the other legitimate PHP users may be able to read and/or
write to your files.</p>

<hr />

EOPAGESTR5;
echo $page_str;

site_footer();
?>