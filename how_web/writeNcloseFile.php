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


site_header('File Write & File Close');

$page_str = <<<EOPAGESTR5

<p>Note: For file writing via PHP, directory permissions must be set to at least 703.</p>

<p>File writing is pretty straightforward if you've successfully opened in the correct mode
for your intended purpose. The function fwrite() takes arguments of a file pointer and a
string, with an optional length in bytes, which should not be used unless you have a specific
reason to do so. It returns the number of characters written.</p>

<pre>
&#36;fout = fwrite(&#36;fp, &#36;fstring);
if (&#36;fout != strlen(&#36;fstring)){
  echo "file write failed!";
}
</pre>

<p>The function fputs() is identical to fwrite() in every way. They are simply aliases for one
another, but fputs() is the C-style function name.</p>

<p>Keep in mind that opening a file in w or w+ modes will result in the complete and utter
obliteration of any file contents. These modes are meant for clean overwrites only. If you
want to write to the beginning or end of a file, use r+ or a+, respectively.</p>

<p>Probably the most common error with PHP file-writing modes involves using a Web interface
(in other words, an HTML form) to edit a text file. If you want to open a file, read in and
view the contents, then write an edited version to the same filename, you cannot depend on w+
mode. The w modes erase the contents of the file immediately upon opening it — thus, although
you can fread() from a w+ file, there will be no text to read until after you write to it. To
get around this issue, you need to open once in read mode and once in write mode, as in the
following example:</p>

<pre>
&lt;&#63;php
if (IsSet(&#36;_POST[‘submitted'])) {
  &#36;fd = fopen(&#36;filename, "w+")
    or die("Can't open file &#36;filename");
  &#36;fout = fwrite(&#36;fd, &#36;_POST[‘newstring']);
  fclose(&#36;fd);
}
&#36;fd = fopen(&#36;filename, "r") or die("Can't open file &#36;filename");
&#36;initstring = fread(&#36;fd, filesize(&#36;filename));
fclose(&#36;fd); echo "&lt;HTML&gt;";
echo "&lt;FORM METHOD='POST' ACTION=&#92;"&#36;_SERVER[‘PHP_SELF']&#92;"&gt;";
echo "&lt;INPUT TYPE='text' SIZE=50 NAME='newstring' VALUE=&#92;"&#36;initstring&#92;"&gt;";
echo "&lt;INPUT TYPE='HIDDEN' NAME='submitted' VALUE=1&gt;";
echo "&lt;INPUT TYPE='SUBMIT'&gt;";
echo "&lt;/FORM&gt;"; echo "&lt;/HTML&gt;";
&#63;&gt;
</pre>

<p>Let us reiterate that file writing is not at all a good idea unless you can control your
environment very tightly! In other words, a well-hardened intranet server might be
appropriate, but file writing on a production Web site can be a security risk. For more
information, see Chapter 29.</p>

<p>As we explain in Chapter 30, in PHP there is now a very easy mechanism to disable the
capability to file-write. This is a great idea especially if your site is entirely
database-driven, in which case you don't have any legitimate need to write to the filesystem
with PHP anyway. To disable file writing, simply add fwrite to the list of disabled functions in php.ini:</p>

<pre>
disabled_functions = "fwrite"
</pre>

<p>If you don't use php.ini and need to set this value in Apache httpd.conf, remember that it
requires a php_admin_value flag (rather than php_value):</p>

<pre>
php_admin_value disabled_functions="fwrite";
</pre>

<h2>File close</h2>

<p>File closing is straightforward:</p>

<pre>
fclose(&#36;fd);
</pre>

<p>Unlike fopen(), the result of fclose() does not need to be assigned to a variable. File
closing may seem like a waste of time; but your system has only so many file descriptors
available, and you may run out if you do not close your files. On the other hand, PHP will
close all open files when your script ends; and at least one version of PHP3 had a buggy
fclose() function which would crash the server. You know your own setup best, and you can
make the call.</p>

EOPAGESTR5;
echo $page_str;

site_footer();
?>