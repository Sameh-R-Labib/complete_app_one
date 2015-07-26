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


site_header('File Read');

$page_str = <<<EOPAGESTR5

<p>The fread() function takes a file-pointer identifier and a file size in bytes as its
arguments. If the file size given is not sufficient to read in the whole file, you will have
mysterious problems (unless you're passing in a smaller file size on purpose, which is useful
when reading huge files in chunks). Unless you have a reason to do otherwise (such as a huge,
unwieldy file), it's best just to let PHP fill in the file size itself, by using the filesize()
function with the name of the file (or a variable) as the argument:</p>

<pre>
&#36;fstring = fread(&#36;fd, filesize(&#36;filename));
</pre>

<p>A common error is to type filesize(&#36;fd) rather than filesize(&#36;filename).</p>

<p>This is an extremely useful function because it allows you to turn any file into a string,
which can then be manipulated with PHP’s large variety of useful string functions. Any string
can also be broken up into an array through use of a function like file() or explode(), which
gives you access to the large arsenal of PHP array-manipulation functions.</p>

<p>If you wish to send the entire contents of a file to standard output (meaning, for most PHP
installations, echoing it to the Web browser window), use readfile() instead. This function has
file opening built in, so you need not use a separate function to open the file first. The
readfile() function is equivalent to the combination of fopen() and fpassthru().</p>

<p>Beginning with PHP4.3.0, a new function called file_get_contents() was made available. This
function returns the entire contents of a file as a string, including the fopen(). It is
equivalent to fopen() and fread(), or to readfile() except returning the contents as a string
rather than straight to standard output.</p>

<p>If you wish to read in and perform operations on a file line-by-line, you can use fgets()
instead of fread(). Beginning in PHP4.2.0, if you do not specify a line length as the second
argument to fgets(), the function will default to 1024 bytes per line.</p>

<pre>
&#36;fd = fopen("samplefile.inc", "r") or die('Cannot find file');
while (!feof(&#36;fd)) {
  &#36;line = fgets(&#36;fd, 4096);
  if (&#36;line === &#36;targetline)
    {echo "A match was found!";}
}
fclose(&#36;fd);
</pre>

<p>If you would rather read the file in as an array, you can use the function file() instead.
You might want to do this if you’re reading one of the many types of data files that use
newlines to indicate rows — such as a spreadsheet saved to tab-delimited text format. This
creates an array, each element of which is a line from the original file including an ending
newline character. The function file() does not require a separate file open or file close
step. A single operation using file(), such as:</p>

<pre>
&#36;line_array = file(&#36;filename);
</pre>

<p>is the equivalent of this:</p>

<pre>
&#36;fd = fopen(&#36;filename, "r") or die("Can't open file &#36;filename");
&#36;fstring = fread(&#36;fd, filesize(&#36;filename));
&#36;line_array = explode("&#92;n", &#36;fstring);
</pre>

<h2>caution</h2>
<hr />
<p>The file() function will work correctly only when PHP recognizes newlines. Hopefully PHP
will handle newlines from other operating systems correctly — current Windows and Unix
versions of PHP seem to identify newline characters from the other operating system — but we
cannot guarantee that this will be true of every case.</p>
<hr />

<br />

<p>Finally, if you'd like to read in a file character by character, you can use the fgetc()
function. This will return a character from the file pointer, until the end-of-file. In
practice, this function is not used very much, because it's so inefficient to read in a file
one character at a time. You'd probably use fgetc() only in situations where you wanted to
test the first or second character in the file.</p>

<h2>Constructing file downloads by using fpassthru()</h2>

<p>Besides reading in a file for manipulation by PHP, you can use fpassthru() in
combination with the PHP header() construct to assemble and send file downloads. For
instance, let’s say you keep lots of tab-delimited data lying around in files, and
occasionally you need to let someone download some data from them. Your users are
typical businesspeople, not techies, so you know they use IE and would prefer the
data as an Excel spreadsheet. So you give the user an HTML form that he or she can
use to ask for the data from a particular day, and when it submits you assemble a
download and send it like so:</p>

<pre>
&lt;?php

// This example assumes there is one data file per day,
// and your form lets the user specify the date they want to
// see.

&#36;file = &#36;_POST['date'].'.txt';
&#36;fp = &#36;fopen(&#36;file, "r");
header("Content-Type:application/xls");
header("Content-Disposition:attachment;
filename=&#36;_POST['date'].xls");
// Notice we changed the file name and type
header("Content-Transfer-Encoding:binary");
fpassthru(&#36;fp);
&#63;&gt;
</pre>

<p>Caution: File downloads in PHP are surprisingly tricky because every browser implements the
file download behavior differently—even different versions of the same browser can have
different behaviors. The preceding method works fine in IE 6.0, but in Mozilla 1.0 the file
will claim to be of type application.xls but will download as 20020526.xls.php. Most of the
methods necessary to get a perfect file download are hacks and involve tricking the browser
into thinking it's downloading the file directly—for instance by tacking /&#36;_POST['data'].xls
onto the end of the URL (for example, http://example.com/sample.php/20020526.xls). Also, if
you saved the script above as data.xls, and jiggered your Web server into parsing .xls files
as PHP, you could get a great download in just about every browser. No single perfect method
exists for every browser, but this is one situation where you can't just go by what you read
in the PHP online manual.</p>

EOPAGESTR5;
echo $page_str;

site_footer();
?>