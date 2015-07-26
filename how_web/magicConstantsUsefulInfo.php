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


site_header('Magic Constants');

$page_str = <<<EOPAGESTR

<p>PHP provides a large number of predefined constants to any script which it runs.
Many of these constants, however, are created by various extensions, and will only
be present when those extensions are available, either via dynamic loading or
because they have been compiled in.</p>

<p>There are seven magical constants that change depending on where they are used.
For example, the value of __LINE__ depends on the line that it's used on in your
script. These special constants are case-insensitive and are as follows:</p>

<p>The current line number of the file.</p>

<pre>
__LINE__
</pre>

<p>The full path and filename of the file. If used inside an include, the name of
the included file is returned. Since PHP 4.0.2, __FILE__ always contains an
absolute path with symlinks resolved whereas in older versions it contained
relative path under some circumstances.</p>

<pre>
__FILE__
</pre>

<p>The directory of the file. If used inside an include, the directory of the
included file is returned. This is equivalent to dirname(__FILE__). This directory
name does not have a trailing slash unless it is the root directory. (Added in
PHP 5.3.0.)</p>

<pre>
__DIR__
</pre>

<p>The function name. (Added in PHP 4.3.0) As of PHP 5 this constant returns the
function name as it was declared (case-sensitive). In PHP 4 its value is always
lowercased.</p>

<pre>
__FUNCTION__
</pre>

<p>The class name. (Added in PHP 4.3.0) As of PHP 5 this constant returns the
class name as it was declared (case-sensitive). In PHP 4 its value is always
lowercased.</p>

<pre>
__CLASS__
</pre>

<p>The class method name. (Added in PHP 5.0.0) The method name is returned as
it was declared (case-sensitive).</p>

<pre>
__METHOD__
</pre>

<p>The name of the current namespace (case-sensitive). This constant is
defined in compile-time (Added in PHP 5.3.0)</p>

<pre>
__NAMESPACE__
</pre>

<p>See also get_class(), get_object_vars(), file_exists() and function_exists().</p>

EOPAGESTR;
echo $page_str;

site_footer();
?>