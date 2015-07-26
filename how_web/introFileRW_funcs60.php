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


site_header('Intro. to File Read Write');

$page_str = <<<EOPAGESTR5

<p>This is a supremely useful set of functions, particularly for data sets too small or
scattered to merit the use of a database. File reading is pretty safe unless you keep
unencrypted passwords lying around, but file writing can be quite unsafe.</p>

<h2>tip</h2>

<hr />

<p>Remember that although the Web server (and client-side languages such as JavaScript) can
only act on files located under the document root, PHP can access files at any location in
the file system—including those above or entirely outside the Web server document root—as
long as the file permissions and include_path are set correctly. For instance, if your Web
server document root is located at /usr/local/apache/htdocs, Apache will be able to serve
only files from this directory and its subdirectories, but PHP can open, read, and write to
files in /usr/local, /home/php, /export/home/httpd, or any other directory that you make
readable and includable by the PHP and/or Web server user.</p>

<hr />

<p><br />A file manipulation session might involve the following steps:</p>

<ol>
  <li>Open the file for read/write.</li>
  <li>Read in the file.</li>
  <li>Close the file (may happen later).</li>
  <li>Perform operations on the file contents.</li>
  <li>Write results out.</li>
</ol>

<p>Each step has a corresponding PHP filesystem function.</p>

<p>This archetypal example illustrates some subtleties of the syntax for manipulating file
contents:</p>

<pre>
&#36;fd = fopen(&#36;filename, "r+")
  or die("Can't open file &#36;filename");
&#36;fstring = fread(&#36;fd, filesize(&#36;filename));
&#36;fout = fwrite(&#36;fd, &#36;fstring);
fclose(&#36;fd);
</pre>

<p>The effect of this particular example will be to double the file — in other words,
the end result will be a file with the original contents of the file written out twice.
This function will not overwrite the file, as you might expect. In the following sections,
we walk you through this archetypal file manipulation session, step by step.</p>

EOPAGESTR5;
echo $page_str;

site_footer();
?>