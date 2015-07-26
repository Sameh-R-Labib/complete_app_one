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


site_header('Understanding PHP File Permissions');

$page_str = <<<EOPAGESTR5

<p>Many PHP users, who have a developer orientation rather than any sysadmin experience,
unfortunately do not take the time to understand Unix filesystem permissions. You really
need to have a firm grasp of the basics to make good decisions about using many of the
functions in this section. If you already do, feel free to skip the rest of this section.</p>

<p>Unfortunately, most explanations of the subject are quite general and user’s eyes can
easily glaze over in a hail of rwxes and three-digit numbers. So we're going to break it down
for you into two simple default rules specifically for PHP users.</p>

<ul>
  <li>Unless you have a good reason to do otherwise, your PHP files should all be set to 644
  (rw-r--r--).</li>
  <li>Unless you have a good reason to do otherwise, your PHP-enabled directories should all be
  set to 751 (rwxr-x--x).</li>
</ul>

<p>For some reason, many users seem to believe that PHP files need to be executable. This is
only true for files that you write with the intention of their being called on the command
line (for example, ./myscript.php). Files that will be run through a Web server only
need to be readable by the Web server user (usually Nobody, or some other user with very
limited permissions). It’s rather inconvenient to make the files not writable by you, which is
why our default recommendation is 644 (rw-r--r--) rather than 444 (r--r--r--), but this is a
matter of convenience only — on a production system, where you shouldn’t be altering code
anyway, you might very well want to set them to 444. Your PHP scripts will run perfectly fine
at 444 (read-only).</p>

<p>Directory permissions are also very often misunderstood. Many users seem to believe that
directories need to be readable for files to run. Actually the read directory permission means
a user can list the contents of that directory (via the ls command, for instance). The execute
directory permission is closer to what we think of as readable. For your PHP scripts to run,
the directory needs only to be world-executable (751 or rwxr-x--x). Do not make the directory
writable by others unless you know what you’re doing.</p>

<p>This Web page gives a good short explanation of Unix file permissions:
www.freeos.com/articles/3127/.</p>

<br />

<hr />

<h2>Here is what I do:</h2>

<p>For web root and below as a general rule my directories are rwx--x--x and files are
rw-r--r--; For below the web root where I want httpd to be able to write my directories
are rwx--xrwx and files are rw-r--rw-. Keep in mind that in those latter directories (where
httpd writes and owns the files it creates when my scripts are run) I fall under the
categor of other. That means my scripts will need to use the chmod function after creating
a file. See example:</p>

<pre>
&#36;docRoot = &#36;_SERVER["DOCUMENT_ROOT"];
&#36;locatorStr = &#36;docRoot . "/web/how_web/" . "aTestOfChmod.php";

if (file_exists(&#36;locatorStr)) {
  echo "No file written. File already exists.&lt;br />&#92;n";
} else {
  touch(&#36;locatorStr);
  echo "File was created.&lt;br />&#92;n";
  if (chmod(&#36;locatorStr, 0646)) {
    echo "Successful at chmod.";
  } else {
    echo "Not successful at chmod.";
  }
}

clearstatcache();
</pre>

<p>Also, I will need to make sure that files which I manually create inside the directories
where httpd does editing end up having rw-r--rw- permissions. I'll use the File Manager
which my webhost supplies to accomplish this. Hopefully, I won't be creating any such
files manually.</p>

EOPAGESTR5;
echo $page_str;

site_footer();
?>